<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\SmsService;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // Display all bookings
    public function index()
    {
        $bookings = Booking::with('user', 'vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('layouts.authorities.adminBooking', compact('bookings'));
    }

    // Confirm a booking
    // Confirm a booking (first payment OR balance payment)
public function confirm(Booking $booking)
{
    $meta = $booking->payment_meta ?? [];
    $isBalancePayment = ($meta['payment_for'] ?? null) === 'balance';

    if ($isBalancePayment) {

        // Mark payment as fully paid
        $booking->payment_status = 'Paid';

        // Make sure paid_amount is at least the total
        if (($booking->paid_amount ?? 0) < $booking->total_amount) {
            $booking->paid_amount = $booking->total_amount;
        }

        // If status was left as "Payment Submitted", push it back to Ongoing
        // (or just keep whatever it currently is if you prefer)
        if ($booking->booking_status === 'Payment Submitted') {
            $booking->booking_status = 'Ongoing';
        }

    } else {

        $booking->booking_status = 'Confirmed';
        $booking->payment_status = 'Paid';

        // Vehicle becomes unavailable once confirmed
        if ($booking->vehicle) {
            $booking->vehicle->availability = false;
            $booking->vehicle->save();
        }
    }

    $booking->save();

    // ===== SMS notification (kept, but with branch text) =====
    if ($booking->user && $booking->user->phone) {
        $vehicleName = $booking->vehicle
            ? $booking->vehicle->Brand . ' ' . $booking->vehicle->Model
            : 'vehicle';

        if ($isBalancePayment) {
            $message = "Hello {$booking->user->name}, your remaining balance for booking #{$booking->booking_id} "
                     . "has been verified. The booking is now fully paid. Thank you!";
        } else {
            $message = "Hello {$booking->user->name}, your booking #{$booking->booking_id} for {$vehicleName} from "
                     . Carbon::parse($booking->pickup_datetime)->format('M d, Y H:i')
                     . " to "
                     . Carbon::parse($booking->return_datetime)->format('M d, Y H:i')
                     . " has been confirmed. Thank you for choosing us!";
        }

        $this->smsService->sendMessage($booking->user->phone, $message);
    }

    return back()->with(
        'success',
        $isBalancePayment
            ? 'Balance payment verified (booking is now fully paid).'
            : 'Booking confirmed and SMS sent to customer!'
    );
}


    // Reject a booking
    public function reject(Request $request, Booking $booking, SmsService $smsService)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'additional_note' => 'nullable|string|max:255'
        ]);

        $booking->booking_status = 'Rejected';
        $booking->payment_status = 'Rejected';
        $booking->save();

        // Send SMS
        $vehicleName = $booking->vehicle ? ($booking->vehicle->Brand . ' ' . $booking->vehicle->Model) : 'Unknown Vehicle';
        $customerName = $booking->user->name;

        $message = "Hi {$customerName}, your booking #{$booking->booking_id} for vehicle {$vehicleName} was rejected due to: {$request->reason}.";
        if ($request->additional_note) {
            $message .= " Note: {$request->additional_note}";
        }

        $smsService->sendMessage($booking->user->phone, $message);

        return redirect()->route('admin.bookings')->with('success', 'Booking rejected and SMS sent.');
    }

    // Delete a rejected booking
    public function destroy(Booking $booking)
    {
        if ($booking->booking_status !== 'Rejected') {
            return redirect()->route('admin.bookings')->with('error', 'Only rejected bookings can be deleted.');
        }

        $booking->delete();
        return redirect()->route('admin.bookings')->with('success', 'Booking deleted successfully.');
    }

    // Mark booking as ongoing
    public function markOngoing(Booking $booking)
    {
        $booking->booking_status = 'Ongoing';
        $booking->save();

        if ($booking->vehicle) {
            $booking->vehicle->availability = false;
            $booking->vehicle->save();
        }

        return back()->with('success', 'Booking marked as Ongoing. Vehicle is now unavailable.');
    }

    // Mark booking as completed
    public function markCompleted(Booking $booking)
    {
        // Optional safety: don’t complete if not fully paid
        if (($booking->paid_amount ?? 0) < $booking->total_amount) {
            return back()->with('error', 'Cannot complete: customer still has an outstanding balance.');
        }

        $booking->booking_status = 'Completed';
        $booking->payment_status = 'Paid';   // ✅ mark payment fully paid too
        $booking->save();

        if ($booking->vehicle) {
            $booking->vehicle->availability = true;
            $booking->vehicle->save();
        }

        return back()->with('success', 'Booking marked as Completed and fully paid. Vehicle is now available again.');
    }

    public function approveRefund(Request $request, Booking $booking)
    {
        if ($booking->refund_status !== 'pending') {
            return back()->with('error', 'No pending refund for this booking.');
        }

        $maxRefund = (float) ($booking->paid_amount ?? 0);

        $data = $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $maxRefund,
        ]);

        $refundAmount = (float) $data['refund_amount'];

        // update refund fields
        $booking->refund_status = 'approved';
        $booking->refund_amount = $refundAmount;

        // mark payment as refunded (business choice)
        $booking->payment_status = 'Refunded';
        // optional: cancel & free vehicle
        $booking->booking_status = 'Cancelled';
        if ($booking->vehicle) {
            $booking->vehicle->availability = true;
            $booking->vehicle->save();
        }

        $booking->save();

        // TODO: actually send money back via payment gateway, or mark in finance system

        return back()->with('success', 'Refund approved and booking cancelled.');
    }

    public function rejectRefund(Booking $booking)
    {
        if ($booking->refund_status !== 'pending') {
            return back()->with('error', 'No pending refund for this booking.');
        }

        $booking->refund_status = 'rejected';
        $booking->save();

        return back()->with('success', 'Refund request rejected.');
    }
}

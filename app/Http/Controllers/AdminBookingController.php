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

    public function confirm(Booking $booking)
    {
        $meta = $booking->payment_meta ?? [];

        $paymentFor  = $meta['payment_for']    ?? null;
        $paymentOpt  = $meta['payment_option'] ?? null;

        $isBalancePayment      = $paymentFor === 'balance';
        $isReservationDeposit  = $paymentFor === 'reservation' && $paymentOpt === 'deposit';

        if ($isBalancePayment) {

            // User just paid the remaining balance
            $booking->payment_status = 'Paid';

            if (($booking->paid_amount ?? 0) < $booking->total_amount) {
                $booking->paid_amount = $booking->total_amount;
            }

            if ($booking->booking_status === 'Payment Submitted') {
                $booking->booking_status = 'Ongoing';
            }

        } elseif ($isReservationDeposit) {

            $booking->booking_status = 'Confirmed';  
            $booking->payment_status = 'Paid';      

            if (($booking->paid_amount ?? 0) < $booking->security_deposit) {
                $booking->paid_amount = $booking->security_deposit;
            }

            if ($booking->vehicle) {
                $booking->vehicle->availability = false;
                $booking->vehicle->save();
            }

        } else {

            $booking->booking_status = 'Confirmed';
            $booking->payment_status = 'Paid';

            if (($booking->paid_amount ?? 0) < $booking->total_amount) {
                $booking->paid_amount = $booking->total_amount;
            }

            if ($booking->vehicle) {
                $booking->vehicle->availability = false;
                $booking->vehicle->save();
            }
        }

        $booking->save();

        if ($booking->user && $booking->user->phone) {
            $vehicleName = $booking->vehicle
                ? $booking->vehicle->Brand . ' ' . $booking->vehicle->Model
                : 'vehicle';

            if ($isBalancePayment) {
                $message = "Hello {$booking->user->name}, your remaining balance for booking #{$booking->booking_id} "
                        . "has been verified. The booking is now fully paid. Thank you!";
            } elseif ($isReservationDeposit) {
                $message = "Hello {$booking->user->name}, your reservation deposit for booking #{$booking->booking_id} "
                        . "has been verified. The booking is confirmed, with a remaining balance of "
                        . '₱' . number_format(max(0, $booking->total_amount - $booking->paid_amount), 2) . ".";
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
                : ($isReservationDeposit
                    ? 'Reservation deposit verified (booking confirmed with remaining balance).'
                    : 'Booking confirmed and SMS sent to customer!')
        );
    }

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
        $total = (float) $booking->total_amount;
        $paid  = (float) ($booking->paid_amount ?? 0);

        if ($paid <= 0 && $booking->payment_status === 'Paid') {
            $paid = $total;
            $booking->paid_amount = $total;
            $booking->save();
        }

        if ($paid < $total) {
            return back()->with('error', 'Cannot complete: customer still has an outstanding balance.');
        }

        $booking->booking_status = 'Completed';
        $booking->payment_status = 'Paid';
        $booking->save();

        if ($booking->vehicle) {
            $booking->vehicle->availability = true;
            $booking->vehicle->save();
        }

        return back()->with('success', 'Booking marked as Completed and fully paid. Vehicle is now available again.');
    }

    public function approveRefund(Booking $booking)
    {

        if ($booking->refund_status !== 'pending') {
            return back()->with('error', 'No pending refund for this booking.');
        }

        if (!$booking->refund_requested_at) {
            $booking->refund_requested_at = now();
        }

        $paidAmount = (float) ($booking->getOriginal('paid_amount') ?? $booking->paid_amount ?? 0);

        if ($paidAmount <= 0) {
            return back()->with('error', 'This booking has no recorded payment to refund.');
        }

        $deductionPerMinute = 1.0; 

        $minutesUsed = $booking->refund_minutes_used;
        $deduction   = $booking->refund_deduction;

        if (empty($minutesUsed) || $minutesUsed < 0 || $deduction === null || $deduction <= 0) {
            $now = now();

            $start = $booking->pickup_datetime ?? $booking->updated_at;

            if ($start && ! $start instanceof Carbon) {
                $start = Carbon::parse($start);
            }

            $minutesUsed = 0;
            if ($start && $now->gt($start)) {
                $minutesUsed = $start->diffInMinutes($now);
            }

            $deduction = min($paidAmount, $minutesUsed * $deductionPerMinute);
        }

        $refundAmount = $booking->refund_amount ?? max(0, $paidAmount - $deduction);

        $booking->refund_status  = 'approved';
        $booking->refund_amount  = $refundAmount;
        $booking->booking_status = 'Cancelled';

        if ($booking->vehicle) {
            $booking->vehicle->availability = true;
            $booking->vehicle->save();
        }

        $booking->refund_minutes_used = (int) $minutesUsed;
        $booking->refund_deduction    = (float) $deduction;

        $booking->save();

        return back()->with(
            'success',
            'Refund approved. Customer will receive ₱' . number_format($refundAmount, 2) .
            " ({$minutesUsed} minutes used, deduction ₱" . number_format($deduction, 2) . ")."
        );
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

    public function show(string $booking_id)
    {
        $booking = Booking::with(['user', 'vehicle'])->where('booking_id', $booking_id)->firstOrFail();

        return view('layouts.authorities.showBookings', compact('booking'));
    }
}

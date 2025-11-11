<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\SmsService;

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
    public function confirm(Booking $booking)
    {
        $booking->booking_status = 'Confirmed';
        $booking->payment_status = 'Paid';
        $booking->save();

        // Send SMS
        if ($booking->user->phone) {
            $vehicleName = $booking->vehicle ? $booking->vehicle->Brand . ' ' . $booking->vehicle->Model : 'Unknown Vehicle';
            $message = "Hello {$booking->user->name}, your booking #{$booking->booking_id} for vehicle {$vehicleName} from " .
                \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y H:i') . " to " .
                \Carbon\Carbon::parse($booking->return_datetime)->format('M d, Y H:i') . " has been confirmed. Thank you for choosing us!";

            $this->smsService->sendMessage($booking->user->phone, $message);
        }

        return redirect()->back()->with('success', 'Booking confirmed and SMS sent to customer!');
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
        $booking->booking_status = 'Completed';
        $booking->save();

        if ($booking->vehicle) {
            $booking->vehicle->availability = true;
            $booking->vehicle->save();
        }

        return back()->with('success', 'Booking marked as Completed. Vehicle is now available again.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Show logged-in user's reservations.
     */
    public function index()
{
    $reservations = Booking::with('vehicle')
        ->where('user_id', Auth::id())
        ->whereIn('booking_status', [
            'Awaiting Payment',
            'Under Review',
            'Payment Submitted',
            'Confirmed',
            'Ongoing',
        ])
        ->orderBy('pickup_datetime')
        ->get();

    return view('layouts.pages.reservations', compact('reservations'));
}


    /**
     * Handle payment for a reservation (deposit or full).
     */
    public function pay(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->booking_status !== 'Awaiting Payment') {
            return back()->with('error', 'This reservation is not awaiting payment.');
        }

        $data = $request->validate([
            'payment_option'      => 'required|in:deposit,full',    
            'payer_name'          => 'required|string|max:255',
            'payer_number'        => 'required|string|max:20',
            'receipt_screenshot'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'valid_id'            => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        if ($request->hasFile('receipt_screenshot')) {
            $fileName = time() . '_' . $request->receipt_screenshot->getClientOriginalName();
            $path = $request->receipt_screenshot->storeAs('receipt_screenshot', $fileName, 'public');
            $booking->receipt_screenshot = $path;
        }

        $validIdPath = null;
        if ($request->hasFile('valid_id')) {
            $fileName = time() . '_' . $request->valid_id->getClientOriginalName();
            $validIdPath = $request->valid_id->storeAs('valid_ids', $fileName, 'public');
        }

        $expectedAmount = $data['payment_option'] === 'deposit'
            ? $booking->security_deposit
            : $booking->total_amount;

        // Basic payment info
        $booking->payer_name     = $data['payer_name'];
        $booking->payer_number   = $data['payer_number'];
        $booking->payment_method = 'GCash';              
        $booking->payment_status = 'For Verification';   
        $booking->booking_status = 'Payment Submitted'; 

        $meta = $booking->payment_meta ?? [];

        $meta['payment_for']      = 'reservation';   
        $meta['payment_option']   = $data['payment_option']; 
        $meta['expected_amount']  = (float) $expectedAmount;
        $meta['recorded_amount']  = (float) $expectedAmount;

        if ($validIdPath) {
            $meta['valid_id_path'] = $validIdPath;
        }

        $booking->payment_meta = $meta;

        $booking->save();

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reservation payment submitted! Please wait for verification.');
    }

    /**
     * Cancel a reservation (before payment/confirmation).
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->booking_status, ['Awaiting Payment', 'Pending Approval'])) {
            return back()->with('error', 'You can only cancel reservations that are not yet paid or confirmed.');
        }

        $booking->booking_status = 'Cancelled';
        $booking->payment_status = 'Cancelled';

        $booking->save();

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    public function requestRefund(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (
            ! in_array($booking->booking_status, ['Payment Submitted','Confirmed','Ongoing']) ||
            $booking->payment_status !== 'Paid' ||
            now()->gte($booking->pickup_datetime)
        ) {
            return back()->with('error', 'This booking is not eligible for a refund.');
        }

        $booking->booking_status = 'Refund Requested';
        $booking->payment_status = 'Refund Pending';
        $booking->save();

        return back()->with('success', 'Refund request submitted. Our staff will review it shortly.');
    }
}

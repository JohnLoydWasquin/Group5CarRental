<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Create Booking (Step 1)
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'VehicleID' => 'required|exists:vehicles,VehicleID',
        'pickup_location' => 'required|string',
        'dropoff_location' => 'required|string',
        'pickup_datetime' => 'required|date|after_or_equal:today',
        'return_datetime' => 'required|date|after:pickup_datetime',
        'addons' => 'nullable|array',
    ]);

    $pickup = Carbon::parse($data['pickup_datetime']);
    $return = Carbon::parse($data['return_datetime']);

    // Prevent double booking
    $existing = Booking::where('VehicleID', $data['VehicleID'])
        ->where(function ($q) use ($pickup, $return) {
            $q->where('pickup_datetime', '<=', $return)
              ->where('return_datetime', '>=', $pickup);
        })
        ->whereIn('booking_status', ['Pending Approval', 'Payment Submitted', 'Confirmed'])
        ->exists();

    if ($existing) {
        // ✅ Always return JSON for AJAX
        return response()->json([
            'success' => false,
            'message' => 'This vehicle is already booked for the selected dates.'
        ]);
    }

    $vehicle = Vehicle::findOrFail($data['VehicleID']);
    $days = max(1, $pickup->diffInDays($return));
    $dailyRate = $vehicle->DailyPrice;

    $driver = in_array('driver', $data['addons'] ?? []) ? 500 * $days : 0;
    $childSeat = in_array('childSeat', $data['addons'] ?? []) ? 200 * $days : 0;
    $insurance = in_array('insurance', $data['addons'] ?? []) ? 300 : 0;

    $subtotal = ($dailyRate * $days) + $driver + $childSeat + $insurance;
    $securityDeposit = 3000;
    $total = $subtotal + $securityDeposit;

    $booking = Booking::create([
        'user_id' => Auth::id(),
        'VehicleID' => $vehicle->VehicleID,
        'pickup_location' => $data['pickup_location'],
        'dropoff_location' => $data['dropoff_location'],
        'pickup_datetime' => $data['pickup_datetime'],
        'return_datetime' => $data['return_datetime'],
        'rental_days' => $days,
        'addons' => $data['addons'] ?? [],
        'subtotal' => $subtotal,
        'security_deposit' => $securityDeposit,
        'total_amount' => $total,
        'payment_status' => 'Pending',
        'booking_status' => 'Pending Approval',
    ]);

    return response()->json([
        'success' => true,
        'booking_id' => $booking->booking_id,
        'total' => $total,
        'vehicleName' => $vehicle->Brand . ' ' . $vehicle->Model
    ]);
}

    /**
     * Submit Payment Proof
     */
    public function submitPayment(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,booking_id',
            'payer_name' => 'required|string|max:255',
            'payer_number' => 'required|string|max:20',
            'receipt_screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $booking = Booking::findOrFail($data['booking_id']);

        // Upload payment screenshot
        if ($request->hasFile('receipt_screenshot')) {
            $fileName = time() . '_' . $request->receipt_screenshot->getClientOriginalName();
            $path = $request->receipt_screenshot->storeAs('receipt_screenshot', $fileName, 'public');
            $booking->receipt_screenshot = $path; 
        }

        // Update status
        $booking->payer_name = $data['payer_name'];
        $booking->payer_number = $data['payer_number'];
        $booking->payment_status = 'For Verification';
        $booking->booking_status = 'Payment Submitted';
        $booking->save();

        return redirect()->back()->with('success', 'Payment submitted! Please wait for verification.');
    }
}

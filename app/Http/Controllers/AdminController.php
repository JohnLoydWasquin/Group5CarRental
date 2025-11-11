<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totalCustomers = User::where('role', 'user')->count();
        $availableVehicles = Vehicle::where('availability', 1)->count();
        $activeBookings = Booking::whereIn('booking_status', ['Pending Approval', 'Payment Submitted', 'Confirmed', 'Ongoing'])->count();

        // Only count bookings with actual revenue
        $monthlyRevenue = Booking::whereMonth('created_at', now()->month)
            ->whereIn('booking_status', ['Confirmed', 'Ongoing', 'Completed'])
            ->sum('total_amount');

        // Revenue trends per month
        $revenueTrends = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
        ->whereYear('created_at', now()->year)
        ->whereIn('booking_status', ['Confirmed', 'Ongoing', 'Completed'])
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->map(function ($item) {
            return [
                'month' => date('M', mktime(0, 0, 0, $item->month, 1)),
                'total' => (float) $item->total
            ];
        });

        // Booking status breakdown
        $bookingStatus = Booking::select('booking_status', DB::raw('COUNT(*) as count'))
            ->groupBy('booking_status')
            ->get();

        return view('layouts.authorities.dashboard', compact(
            'totalCustomers',
            'availableVehicles',
            'activeBookings',
            'monthlyRevenue',
            'revenueTrends',
            'bookingStatus'
        ));
    }
}

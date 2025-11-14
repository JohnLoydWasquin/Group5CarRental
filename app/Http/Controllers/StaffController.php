<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $totalCustomers = User::where('role', 'user')->count();
        $availableVehicles = Vehicle::where('availability', 1)->count();
        $activeBookings = Booking::whereIn('booking_status', ['Pending Approval', 'Payment Submitted', 'Confirmed', 'Ongoing'])->count();

        $monthlyRevenue = Booking::whereMonth('created_at', now()->month)
            ->whereIn('booking_status', ['Confirmed', 'Ongoing', 'Completed'])
            ->sum('total_amount');

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

        $bookingStatus = Booking::select('booking_status', DB::raw('COUNT(*) as count'))
            ->groupBy('booking_status')
            ->get();

        return view('layouts.orgStaff.dashboard', compact(
            'totalCustomers',
            'availableVehicles',
            'activeBookings',
            'monthlyRevenue',
            'revenueTrends',
            'bookingStatus'
        ));
    }
}

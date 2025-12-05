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
        // Same counts as admin
        $totalCustomers = User::where('role', 'user')->count();
        $availableVehicles = Vehicle::where('availability', 1)->count();
        $activeBookings = Booking::whereIn('booking_status', [
            'Pending Approval',
            'Payment Submitted',
            'Confirmed',
            'Ongoing'
        ])->count();

        $baseRevenue = Booking::whereYear('created_at', now()->year)
            ->whereIn('booking_status', ['Confirmed', 'Ongoing', 'Completed'])
            ->sum('paid_amount');

        $refundFees = Booking::whereYear('refund_requested_at', now()->year)
            ->where('refund_status', 'approved')
            ->sum('refund_deduction');

        $monthlyRevenue = $baseRevenue + $refundFees;

        $revenueTrends = collect(range(1, 12))->map(function ($month) {
            $base = Booking::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->whereIn('booking_status', ['Confirmed', 'Ongoing', 'Completed'])
                ->sum('paid_amount');

            $fees = Booking::whereYear('refund_requested_at', now()->year)
                ->whereMonth('refund_requested_at', $month)
                ->where('refund_status', 'approved')
                ->sum('refund_deduction');

            return [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'total' => (float) ($base + $fees),
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

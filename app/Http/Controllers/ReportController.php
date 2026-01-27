<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function buildReportData(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to',   now()->endOfMonth()->toDateString());
        $status = $request->input('status', 'all');
        $paymentStatus = $request->input('payment_status', 'all');

        $query = Booking::with(['user', 'vehicle']);

        $query->whereDate('pickup_datetime', '>=', $from)
              ->whereDate('return_datetime', '<=', $to);

        if ($status !== 'all') {
            $query->where('booking_status', $status);
        }

        if ($paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        $transactions = $query->orderBy('pickup_datetime', 'desc')->get();

        $totalBookings = $transactions->count();

        $completedBookings = $transactions->where('booking_status', 'Completed')->count();
        $cancelledBookings = $transactions->where('booking_status', 'Cancelled')->count();

        $bookingRevenue = $transactions
            ->filter(function ($b) {
                return strtolower($b->payment_status) === 'paid'
                    && $b->booking_status === 'Completed';
            })
            ->sum('total_amount');

        $refundRevenue = $transactions
            ->filter(function ($b) {
                return $b->refund_status === 'approved' && $b->refund_deduction > 0;
            })
            ->sum('refund_deduction');

        $totalRevenue = $bookingRevenue + $refundRevenue;

        $pendingRevenue = $transactions
            ->filter(function ($b) {
                $ps = strtolower($b->payment_status);
                return in_array($ps, ['pending', 'for verification']);
            })
            ->sum('total_amount');

        $rejectedAmount = $transactions
            ->filter(function ($b) {
                $bs = $b->booking_status;
                $ps = strtolower($b->payment_status);

                return $ps === 'rejected' || in_array($bs, ['Rejected', 'Cancelled']);
            })
            ->sum('total_amount');

        $mostRentedVehicles = Booking::select(
        'VehicleID',
        DB::raw('COUNT(*) as total_rentals')
            )
            ->whereDate('pickup_datetime', '>=', $from)
            ->whereDate('return_datetime', '<=', $to)
            ->where('booking_status', 'Completed')
            ->groupBy('VehicleID')
            ->orderByDesc('total_rentals')
            ->with('vehicle')
            ->limit(5)
            ->get();

        return compact(
            'transactions',
            'from',
            'to',
            'status',
            'paymentStatus',
            'totalBookings',
            'completedBookings',
            'cancelledBookings',
            'totalRevenue',
            'pendingRevenue',
            'rejectedAmount',
            'mostRentedVehicles',
        );
    }

    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        return view('layouts.reports.generateReport', $data);
    }

    public function export(Request $request)
    {
        $data = $this->buildReportData($request);

        $pdf = Pdf::loadView('layouts.reports.reportPDF', $data)
            ->setPaper('a4', 'landscape'); // nice wide table

        $filename = 'car-rental-report_' . $data['from'] . '_to_' . $data['to'] . '.pdf';

        return $pdf->download($filename);
    }
}

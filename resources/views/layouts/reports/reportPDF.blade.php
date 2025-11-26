<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>RideNow: Autopiloto Car Rental Report</title>

    <style>
        @page { 
            margin: 90px 40px 80px 40px; 
        }

        /* HEADER */
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
        }

        /* FOOTER */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 12px;
            color: #555;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .summary, .transactions {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
        }
        .summary td.label {
            background: #f9fafb;
            font-weight: bold;
        }

        table.transactions th,
        table.transactions td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            font-size: 11px;
        }

        table.transactions th {
            background: #f2f2f2;
            text-align: left;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Signature block */
        .signature-block {
            margin-top: 60px;
            width: 100%;
        }
        .signature-line {
            width: 250px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

<header>
    <div style="display:flex; align-items:center;">
        <img src="{{ public_path('images/AutoPilotoLogo.png') }}" height="50" style="margin-right: 10px;">
        <div>
            <strong style="font-size:18px;">RideNow: Autopiloto Car Rentals</strong><br>
        </div>
    </div>
</header>

<footer>
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $pdf->page_text(520, 760, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, [0,0,0]);
        }
    </script>
</footer>

<main>
    <small>
        Period: <strong>{{ $from }}</strong> to <strong>{{ $to }}</strong><br>
        Generated at: {{ now()->format('Y-m-d H:i') }}
    </small>

    <br><br>

    <table class="summary">
        <tr>
            <td class="label">Total Bookings</td>
            <td>{{ $totalBookings }}</td>
            <td class="label">Completed</td>
            <td>{{ $completedBookings }}</td>
        </tr>
        <tr>
            <td class="label">Cancelled</td>
            <td>{{ $cancelledBookings }}</td>
            <td class="label">Total Revenue (Paid)</td>
            <td>₱{{ number_format($totalRevenue, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Pending / For Verification</td>
            <td>₱{{ number_format($pendingRevenue, 2) }}</td>
            <td class="label">Rejected / Lost</td>
            <td>₱{{ number_format($rejectedAmount, 2) }}</td>
        </tr>
    </table>

    <br><br>

    <h3>Transactions</h3>

    <table class="transactions">
        <thead>
            <tr>
                <th>Booking #</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Pickup</th>
                <th>Return</th>
                <th class="text-right">Total</th>
                <th>Booking Status</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $booking)
                <tr>
                    <td>#{{ $booking->booking_id }}</td>
                    <td>{{ $booking->user->name ?? $booking->payer_name ?? 'N/A' }}</td>
                    <td>
                        {{ $booking->vehicle->model ?? $booking->vehicle->VehicleName ?? 'Vehicle #'.$booking->VehicleID }}
                    </td>
                    <td>{{ $booking->pickup_datetime?->format('M d, Y H:i') }}</td>
                    <td>{{ $booking->return_datetime?->format('M d, Y H:i') }}</td>
                    <td class="text-right">₱{{ number_format($booking->total_amount, 2) }}</td>
                    <td>{{ $booking->booking_status }}</td>
                    <td>{{ ucfirst($booking->payment_status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No transactions.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

<br><br><br>

@php
    $user = auth()->user();
    $role = ucfirst($user->role ?? 'Staff');
    $managerName = "Jasper Lalog";
@endphp

<table width="100%" style="margin-top: 40px;">
    <tr>
        <td width="50%" style="text-align:left; padding-left:20px;">
            <div style="width:220px; border-bottom:1px solid #000; margin-bottom:5px;"></div>
            <strong>{{ $user->name }}</strong><br>
            <small>Prepared By — {{ $role }}</small>
        </td>

        <td width="50%" style="text-align:right; padding-right:20px;">
            <div style="width:220px; border-bottom:1px solid #000; margin-left:auto; margin-bottom:5px;"></div>
            <strong>{{ $managerName }}</strong><br>
            <small>Approved By — Manager</small>
        </td>
    </tr>
</table>


</main>

</body>
</html>

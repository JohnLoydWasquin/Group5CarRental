@extends(auth()->user()->role === 'admin' ? 'layouts.authorities.admin' : 'layouts.orgStaff.staff')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Generate Report</h1>

    <a href="{{ route('reports.export', request()->query()) }}"
       class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600">
        Export Report
    </a>
</div>

<form method="GET" action="{{ route('reports.index') }}" class="bg-white p-4 rounded-lg shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="w-full border-gray-300 rounded-md shadow-sm">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Booking Status</label>
            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="Pending Approval">Pending Approval</option>
                <option value="Awaiting Payment">Awaiting Payment</option>
                <option value="Under Review">Under Review</option>
                <option value="Payment Submitted">Payment Submitted</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Rejected">Rejected</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
            <select name="payment_status" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>All</option>
                <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="for verification" {{ $paymentStatus === 'for verification' ? 'selected' : '' }}>For Verification</option>
                <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="rejected" {{ $paymentStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

    </div> {{-- ✅ CLOSE THE GRID HERE --}}

    <div class="mt-4 flex justify-end">
        <button type="submit"
                class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900">
            Apply Filters
        </button>
    </div>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Bookings</p>
        <h2 class="text-3xl font-bold">{{ $totalBookings }}</h2>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Completed</p>
        <h2 class="text-3xl font-bold text-green-600">{{ $completedBookings }}</h2>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Cancelled</p>
        <h2 class="text-3xl font-bold text-red-500">{{ $cancelledBookings }}</h2>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Revenue (Paid)</p>
        <h2 class="text-3xl font-bold text-emerald-600">₱{{ number_format($totalRevenue, 2) }}</h2>
        <p class="text-xs text-gray-400 mt-1">
            Pending / For Verification: ₱{{ number_format($pendingRevenue, 2) }}<br>
            Rejected / Lost: ₱{{ number_format($rejectedAmount, 2) }}
        </p>
    </div>
</div>

{{-- 🚗 CAR INVENTORY ANALYTICS --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="px-4 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">
            Car Inventory – Most Rented Vehicles
        </h2>
        <p class="text-xs text-gray-500">
            Based on completed bookings from {{ $from }} to {{ $to }}
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Rank</th>
                    <th class="px-4 py-2 text-left">Vehicle</th>
                    <th class="px-4 py-2 text-center">Total Rentals</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mostRentedVehicles as $index => $item)
                    <tr>
                        <td class="px-4 py-2 font-semibold">#{{ $index + 1 }}</td>
                        <td class="px-4 py-2">
                            {{ $item->vehicle->Brand ?? '' }}
                            {{ $item->vehicle->Model ?? 'Vehicle #'.$item->VehicleID }}
                        </td>
                        <td class="px-4 py-2 text-center font-bold text-blue-600">
                            {{ $item->total_rentals }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                            No completed rentals in this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Transactions table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">Transactions</h2>
        <p class="text-xs text-gray-500">
            Showing {{ $transactions->count() }} record(s) from
            <span class="font-semibold">{{ $from }}</span> to <span class="font-semibold">{{ $to }}</span>
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Booking #</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Customer</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Vehicle</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Pickup</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Return</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-600">Total</th>
                    <th class="px-4 py-2 text-center font-medium text-gray-600">Booking Status</th>
                    <th class="px-4 py-2 text-center font-medium text-gray-600">Payment</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($transactions as $booking)
                    <tr>
                        <td class="px-4 py-2 text-gray-700">#{{ $booking->booking_id }}</td>

                        {{-- main customer name: user name, or fallback to payer_name --}}
                        <td class="px-4 py-2 text-gray-700">
                            {{ $booking->user->name ?? $booking->payer_name ?? 'N/A' }}
                        </td>

                        {{-- show vehicle info – adjust columns to your Vehicle model --}}
                        <td class="px-4 py-2 text-gray-700">
                            {{ $booking->vehicle->model ?? $booking->vehicle->VehicleName ?? 'Vehicle #'.$booking->VehicleID }}
                        </td>

                        <td class="px-4 py-2 text-gray-700">
                            {{ $booking->pickup_datetime?->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-2 text-gray-700">
                            {{ $booking->return_datetime?->format('M d, Y H:i') }}
                        </td>

                        <td class="px-4 py-2 text-right text-gray-800 font-semibold">
                            ₱{{ number_format($booking->total_amount, 2) }}
                        </td>

                        <td class="px-4 py-2 text-center">
                            @php $bs = $booking->booking_status; @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                @class([
                                    // waiting states
                                    'bg-yellow-100 text-yellow-800' => in_array($bs, [
                                        'Pending Approval',
                                        'Awaiting Payment',
                                        'Under Review',
                                        'Payment Submitted',
                                    ]),

                                    // active / in-progress
                                    'bg-blue-100 text-blue-800' => in_array($bs, [
                                        'Confirmed',
                                        'Ongoing',
                                    ]),

                                    // finished
                                    'bg-green-100 text-green-800' => $bs === 'Completed',

                                    // failed / lost
                                    'bg-red-100 text-red-800' => in_array($bs, [
                                        'Rejected',
                                        'Cancelled',
                                    ]),
                                ])">
                                {{ $bs }}
                            </span>
                        </td>

                        <td class="px-4 py-2 text-center">
                            @php $ps = strtolower($booking->payment_status); @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                @class([
                                    'bg-yellow-100 text-yellow-800' => $ps === 'pending',
                                    'bg-blue-100 text-blue-800'     => $ps === 'for verification',
                                    'bg-green-100 text-green-800'   => $ps === 'paid',
                                    'bg-red-100 text-red-800'       => $ps === 'rejected',
                                ])">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-4 text-center text-gray-500">
                            No transactions found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

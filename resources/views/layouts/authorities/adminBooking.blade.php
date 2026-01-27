@extends('layouts.authorities.admin')
@vite('resources/js/admin/adminBooking.js')

@section('content')
<div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold text-gray-800">Bookings</h1>

    <form method="GET" action="{{ route('admin.bookings') }}" class="mt-4 flex justify-end">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search booking, customer, vehicle..."
            class="border rounded-lg px-3 py-2 w-72
                focus:outline-none focus:ring focus:ring-indigo-400"
        >
    </form>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-2">
            {{ session('error') }}
        </div>
    @endif

    <!-- Card Container -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-indigo-50 rounded-t-xl">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Booking ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Customer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Vehicle
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Pickup → Return
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Payment
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $pendingStatuses = ['Pending Approval', 'Payment Submitted', 'Awaiting Payment'];
                        $paymentColors = [
                            'Pending' => 'bg-yellow-100 text-yellow-700',
                            'For Verification' => 'bg-indigo-100 text-indigo-700',
                            'Paid' => 'bg-emerald-100 text-emerald-700',
                            'Rejected' => 'bg-red-100 text-red-700'
                        ];
                        $statusColors = [
                            'Pending Approval' => 'bg-yellow-100 text-yellow-700',
                            'Payment Submitted' => 'bg-indigo-100 text-indigo-700',
                            'Confirmed' => 'bg-emerald-100 text-emerald-700',
                            'Ongoing' => 'bg-blue-100 text-blue-700',
                            'Completed' => 'bg-gray-100 text-gray-700',
                            'Cancelled' => 'bg-red-200 text-red-800',
                            'Rejected' => 'bg-red-100 text-red-700'
                        ];
                    @endphp

                    @foreach($bookings as $booking)

                    @php
                        $meta       = $booking->payment_meta ?? [];
                        $total      = (float) ($booking->total_amount ?? 0);
                        $paidAmount = (float) ($booking->paid_amount ?? 0);

                        $isDepositMeta =
                            ($meta['payment_for']    ?? null) === 'reservation' &&
                            ($meta['payment_option'] ?? null) === 'deposit';

                        // --- IMPORTANT: fix for old data / Book Now & deposit ---
                        if ($paidAmount <= 0) {
                            if ($isDepositMeta) {
                                // Reservation deposit: assume they paid the deposit amount
                                $paidAmount = (float) ($booking->security_deposit ?? 0);
                            } elseif ($booking->payment_status === 'Paid') {
                                // Book Now: payment marked Paid but no paid_amount stored -> treat as full
                                $paidAmount = $total;
                            }
                        }

                        $balanceRemaining = max(0, $total - $paidAmount);

                        if ($total > 0 && $paidAmount >= $total - 0.01) {
                            $paymentTypeLabel = 'Full Payment';
                        } elseif ($isDepositMeta) {
                            $paymentTypeLabel = 'Deposit Only';
                        } elseif ($paidAmount > 0 && $paidAmount < $total) {
                            $paymentTypeLabel = 'Partial Payment';
                        } else {
                            $paymentTypeLabel = '—';
                        }
                    @endphp

                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $booking->booking_id }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $booking->user->name }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $booking->vehicle->Brand ?? 'Unknown' }} {{ $booking->vehicle->Model ?? '' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y H:i') }}<br>
                            <span class="text-xs text-gray-500">→</span>
                            {{ \Carbon\Carbon::parse($booking->return_datetime)->format('M d, Y H:i') }}
                        </td>

                        <td class="px-6 py-4 text-sm text-right text-gray-700">
                            ₱{{ number_format($booking->total_amount, 2) }}
                        </td>

                        {{-- Payment status pill --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$booking->payment_status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $booking->payment_status }}
                            </span>
                        </td>

                        {{-- Booking status pill --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$booking->booking_status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $booking->booking_status }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('admin.bookings.show', $booking->booking_id) }}"
                               class="inline-flex items-center justify-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-sm font-semibold shadow-sm">
                                Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--  Modals -->
@foreach($bookings as $booking)
    @if($booking->receipt_screenshot)
        @php
            $meta       = $booking->payment_meta ?? [];
            $total      = (float) ($booking->total_amount ?? 0);
            $paidAmount = (float) ($booking->paid_amount ?? 0);

            $isDepositMeta =
                ($meta['payment_for']    ?? null) === 'reservation' &&
                ($meta['payment_option'] ?? null) === 'deposit';

            if ($paidAmount <= 0) {
                if ($isDepositMeta) {
                    $paidAmount = (float) ($booking->security_deposit ?? 0);
                } elseif ($booking->payment_status === 'Paid') {
                    $paidAmount = $total;
                }
            }

            $balanceRemaining = max(0, $total - $paidAmount);

            if ($isDepositMeta) {
                $paymentTypeLabel = 'Deposit Only';
            } elseif ($total > 0 && $paidAmount >= $total - 0.01) {
                $paymentTypeLabel = 'Full Payment';
            } elseif ($paidAmount > 0 && $paidAmount < $total) {
                $paymentTypeLabel = 'Partial Payment';
            } else {
                $paymentTypeLabel = '—';
            }
        @endphp

        <!-- Proof of Payment Modal -->
        <div id="paymentModal{{ $booking->booking_id }}" class="modal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2 p-6">
                <h2 class="text-xl font-bold mb-4">Proof of Payment</h2>

                <img src="{{ asset('storage/' . $booking->receipt_screenshot) }}" 
                     alt="Proof of Payment" 
                     class="w-full max-w-md max-h-[400px] mx-auto rounded-lg mb-4 object-contain">

                <p><strong>Payer Name:</strong> {{ $booking->payer_name }}</p>
                <p><strong>Payer Number:</strong> {{ $booking->payer_number }}</p>
                <p><strong>Payment Type:</strong> {{ $paymentTypeLabel }}</p>

                <p><strong>Amount Paid:</strong>
                    ₱{{ number_format(min($paidAmount, $total), 2) }}
                </p>

                @if($balanceRemaining > 0)
                    <p><strong>Balance Remaining:</strong>
                        ₱{{ number_format($balanceRemaining, 2) }}
                    </p>
                @endif

                <p><strong>Date Uploaded:</strong> {{ $booking->updated_at }}</p>
                <div class="mt-6 text-right">
                    <button data-modal-toggle="paymentModal{{ $booking->booking_id }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl">Close</button>
                </div>
            </div>
        </div>
    @endif
@endforeach
<div class="mt-6">
    {{ $bookings->links() }}
</div>
@endsection

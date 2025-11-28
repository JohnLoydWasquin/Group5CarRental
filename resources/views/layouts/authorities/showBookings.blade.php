@extends('layouts.authorities.admin')
@vite('resources/js/admin/adminBooking.js')

@section('content')
<div class="p-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Booking Details</h1>
            <p class="text-gray-500 text-sm">
                Review booking information, payment, and status before taking action.
            </p>
        </div>
        <a href="{{ route('admin.bookings') }}"
           class="text-sm text-blue-600 hover:text-blue-800">
            ← Back to list
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @php
        $paymentColors = [
            'Pending'         => 'bg-yellow-100 text-yellow-700',
            'For Verification'=> 'bg-indigo-100 text-indigo-700',
            'Paid'            => 'bg-emerald-100 text-emerald-700',
            'Rejected'        => 'bg-red-100 text-red-700',
        ];

        $statusColors = [
            'Pending Approval'  => 'bg-yellow-100 text-yellow-700',
            'Payment Submitted' => 'bg-indigo-100 text-indigo-700',
            'Confirmed'         => 'bg-emerald-100 text-emerald-700',
            'Ongoing'           => 'bg-blue-100 text-blue-700',
            'Completed'         => 'bg-gray-100 text-gray-700',
            'Cancelled'         => 'bg-red-200 text-red-800',
            'Rejected'          => 'bg-red-100 text-red-700',
        ];

        $pendingStatuses = ['Pending Approval', 'Payment Submitted', 'Awaiting Payment'];

        $meta       = $booking->payment_meta ?? [];
        $total      = (float) ($booking->total_amount ?? 0);
        $paidAmount = (float) ($booking->paid_amount ?? 0);

        $isDepositMeta =
            ($meta['payment_for']    ?? null) === 'reservation' &&
            ($meta['payment_option'] ?? null) === 'deposit';

        // --- IMPORTANT FALLBACKS ---
        if ($paidAmount <= 0) {
            if ($isDepositMeta) {
                // Reservation with deposit only
                $paidAmount = (float) ($booking->security_deposit ?? 0);
            } elseif ($booking->payment_status === 'Paid') {
                // Book Now fully paid but no paid_amount stored
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

        $refundInfo = null;
        if ($booking->refund_status === 'pending' && $paidAmount > 0) {
            $minutesUsed    = $booking->refund_minutes_used ?? 0;
            $deduction      = $booking->refund_deduction ?? ($minutesUsed * 1); // ₱1/min
            $computedRefund = $booking->refund_amount ?? max(0, $paidAmount - $deduction);

            $refundInfo = [
                'paidForRefund'  => $paidAmount,
                'minutesUsed'    => $minutesUsed,
                'deduction'      => $deduction,
                'computedRefund' => $computedRefund,
            ];
        }
    @endphp

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: INFO CARDS --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Booking Summary --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <div class="flex justify-between items-start flex-wrap gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            Booking #{{ $booking->booking_id }}
                        </h2>
                        <p class="text-gray-500 text-sm">
                            {{ $booking->vehicle->Brand ?? 'Unknown' }}
                            {{ $booking->vehicle->Model ?? '' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $statusColors[$booking->booking_status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $booking->booking_status }}
                        </span>

                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $paymentColors[$booking->payment_status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $booking->payment_status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
                    <div>
                        <p class="font-semibold text-gray-700">Pickup → Return</p>
                        <p class="text-gray-600">
                            {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y H:i') }} →
                            {{ \Carbon\Carbon::parse($booking->return_datetime)->format('M d, Y H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700">Created At</p>
                        <p class="text-gray-600">
                            {{ $booking->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Customer Info</h3>
                <div class="space-y-1 text-sm text-gray-700">
                    <p><span class="font-semibold">Name:</span> {{ $booking->user->name }}</p>
                    <p><span class="font-semibold">Email:</span> {{ $booking->user->email }}</p>
                    <p><span class="font-semibold">Phone:</span> {{ $booking->user->phone ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Trip Details --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Trip Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <p class="font-semibold">Pickup Location</p>
                        <p>{{ $booking->pickup_location }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Drop-off Location</p>
                        <p>{{ $booking->dropoff_location }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Payment Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <p class="font-semibold">Payment Type</p>
                        <p>{{ $paymentTypeLabel }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Payment Status</p>
                        <p>{{ $booking->payment_status }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Total Amount</p>
                        <p>₱{{ number_format($total, 2) }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Paid Amount</p>
                        <p>
                            @if($paidAmount > 0)
                                ₱{{ number_format(min($paidAmount, $total), 2) }}
                            @else
                                —
                            @endif
                        </p>
                    </div>

                    @if($balanceRemaining > 0)
                        <div>
                            <p class="font-semibold text-red-600">Balance Remaining</p>
                            <p class="text-red-600">
                                ₱{{ number_format($balanceRemaining, 2) }}
                            </p>
                        </div>
                    @endif

                    @if($booking->payer_name || $booking->payer_number)
                        <div>
                            <p class="font-semibold">Payer Name</p>
                            <p>{{ $booking->payer_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="font-semibold">Payer Number</p>
                            <p>{{ $booking->payer_number ?? '—' }}</p>
                        </div>
                    @endif

                    {{-- ★ NEW: show refund status + amount if exists --}}
                    @if($booking->refund_status)
                        <div>
                            <p class="font-semibold">Refund Status</p>
                            <p class="text-gray-700">
                                {{ ucfirst($booking->refund_status) }}
                                @if($booking->refund_status === 'approved' && $booking->refund_amount !== null)
                                    – Refunded: ₱{{ number_format($booking->refund_amount, 2) }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Admin Actions --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Actions</h3>

                {{-- ★ NEW: if refund is pending, show refund calculation + Approve/Reject --}}
                @if($booking->refund_status === 'pending')
                    @if($refundInfo)
                        <div class="mb-4 text-sm text-gray-700">
                            <p><strong>Refund request pending.</strong></p>
                            <p>
                                Paid amount: ₱{{ number_format($refundInfo['paidForRefund'], 2) }}<br>
                                Minutes used: {{ $refundInfo['minutesUsed'] }}<br>
                                Deduction (₱1/min): ₱{{ number_format($refundInfo['deduction'], 2) }}<br>
                                <span class="font-semibold">
                                    Computed refund: ₱{{ number_format($refundInfo['computedRefund'], 2) }}
                                </span>
                            </p>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        <form action="{{ route('admin.bookings.refund.approve', $booking->booking_id) }}"
                              method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm">
                                Approve Refund
                            </button>
                        </form>

                        <form action="{{ route('admin.bookings.refund.reject', $booking->booking_id) }}"
                              method="POST"
                              onsubmit="return confirm('Reject this refund request?');">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm">
                                Reject Refund
                            </button>
                        </form>
                    </div>

                @else
                    {{-- ORIGINAL ACTION BUTTONS (unchanged, just wrapped in @else) --}}
                    <div class="flex flex-wrap gap-2">

                        @if(in_array($booking->booking_status, $pendingStatuses))
                            {{-- Confirm --}}
                            <form action="{{ route('admin.bookings.confirm', $booking->booking_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm">
                                    Confirm
                                </button>
                            </form>

                            {{-- Reject -> open modal --}}
                            <button type="button"
                                    data-modal-toggle="rejectModal{{ $booking->booking_id }}"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm">
                                Reject
                            </button>

                        @elseif($booking->booking_status === 'Confirmed')
                            {{-- Mark as Ongoing --}}
                            <form action="{{ route('admin.bookings.ongoing', $booking->booking_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm">
                                    Mark as Ongoing
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl text-sm">
                                    Delete
                                </button>
                            </form>

                        @elseif($booking->booking_status === 'Ongoing')
                            {{-- Mark as Completed --}}
                            <form action="{{ route('admin.bookings.completed', $booking->booking_id) }}"
                                  method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm">
                                    Mark as Completed
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl text-sm">
                                    Delete
                                </button>
                            </form>

                        @elseif(in_array($booking->booking_status, ['Completed', 'Rejected', 'Cancelled']))
                            {{-- Only Delete --}}
                            <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl text-sm">
                                    Delete
                                </button>
                            </form>

                        @else
                            <span class="text-gray-400 text-sm">No actions available for this status.</span>
                        @endif

                    </div>
                @endif
            </div>

        </div>

        <div class="space-y-4">

            {{-- Proof of Payment --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Proof of Payment</h3>

                @if($booking->receipt_screenshot)
                    <img src="{{ asset('storage/' . $booking->receipt_screenshot) }}"
                         alt="Proof of Payment"
                         class="w-full max-h-[380px] object-contain rounded-lg border">
                @else
                    <p class="text-sm text-gray-500">No proof of payment uploaded.</p>
                @endif
            </div>

            {{-- Vehicle Preview --}}
            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Vehicle</h3>
                @if($booking->vehicle && $booking->vehicle->Image)
                    <img src="{{ asset('images/' . $booking->vehicle->Image) }}"
                         alt="Vehicle"
                         class="w-full max-h-[260px] object-cover rounded-lg border mb-3">
                @endif

                <p class="text-sm text-gray-700">
                    <span class="font-semibold">Model:</span>
                    {{ $booking->vehicle->Brand ?? 'Unknown' }}
                    {{ $booking->vehicle->Model ?? '' }}
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal{{ $booking->booking_id }}"
     class="modal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-2xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2 p-6">
        <h2 class="text-xl font-bold mb-4">Reject Booking</h2>

        <form action="{{ route('admin.bookings.reject', $booking->booking_id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-2">Reason</label>
                <div class="flex flex-wrap gap-2 mb-2">
                    <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">
                        Wrong Amount
                    </button>
                    <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">
                        Blurry/Unreadable
                    </button>
                    <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">
                        Invalid Receipt
                    </button>
                    <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">
                        No Payment Found
                    </button>
                </div>
                <input type="hidden" name="reason" id="reasonInput{{ $booking->booking_id }}">
                <input type="text" name="additional_note"
                       placeholder="Additional note (optional)"
                       class="w-full border rounded-xl px-3 py-2">
            </div>
            <div class="text-right space-x-2">
                <button type="button"
                        data-modal-toggle="rejectModal{{ $booking->booking_id }}"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-xl">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl">
                    Send SMS & Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<style>
        body { background-color: #e6eaf0ff; }
</style>
<div class="container py-5 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">My Transactions</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($reservations->isEmpty())
        <div class="text-center py-5">
            <p class="mb-3">You have no reservations yet.</p>
            <a href="{{ route('vehicles') ?? url('/vehicles') }}" class="btn btn-primary">
                Reserve a vehicle
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Vehicle</th>
                        <th>Pickup → Return</th>
                        <th>Status</th>
                        <th>Time Used</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($reservations as $booking)
                    @php
                        $paidAmount = $booking->paid_amount ?? 0;
                        $balanceDue = max(0, $booking->total_amount - $paidAmount);

                        $now = \Carbon\Carbon::now();
                        $hasAnyPayment = ($booking->paid_amount ?? 0) > 0;

                        $canRequestRefund =
                            $hasAnyPayment &&
                            in_array($booking->booking_status, ['Payment Submitted','Confirmed','Ongoing']) &&
                            ! in_array($booking->refund_status, ['pending', 'approved']);

                        // NEW: decide if we show a running timer
                        $showTimer = in_array($booking->booking_status, ['Confirmed','Ongoing','Completed']);

                        // 🔹 NEW: choose start time for the timer
                        // - For Confirmed/Ongoing, start from updated_at (when admin changed status)
                        // - Otherwise fall back to pickup_datetime
                        $timerStartTs = null;
                        if ($showTimer) {
                            if (in_array($booking->booking_status, ['Confirmed', 'Ongoing'])) {
                                $timerStartTs = ($booking->updated_at ?? $booking->pickup_datetime)->timestamp;
                            } else {
                                // e.g. Completed
                                $timerStartTs = $booking->pickup_datetime->timestamp;
                            }
                        }
                    @endphp

                    <tr>
                        <td>{{ $booking->booking_id }}</td>
                        <td>{{ $booking->vehicle->Brand ?? '' }} {{ $booking->vehicle->Model ?? '' }}</td>
                        <td>
                            {{ $booking->pickup_datetime->format('M d, Y H:i') }} →
                            {{ $booking->return_datetime->format('M d, Y H:i') }}
                        </td>
                        <td>
                            @php
                                $status = $booking->booking_status;
                                $badgeClass = match($status) {
                                    'Awaiting Payment'  => 'bg-warning text-dark',
                                    'Payment Submitted' => 'bg-info text-dark',
                                    'Confirmed'         => 'bg-success',
                                    'Under Review'      => 'bg-secondary',
                                    'Ongoing'           => 'bg-primary text-light',
                                    default             => 'bg-light text-dark',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                        </td>

                        {{-- NEW: Time Used cell --}}
                        <td>
                            @if($showTimer && $timerStartTs)
                                <span
                                    class="time-used"
                                    data-start="{{ $timerStartTs }}"
                                    data-end="{{ $booking->return_datetime ? $booking->return_datetime->timestamp : '' }}"
                                    data-status="{{ $booking->booking_status }}"
                                >
                                    0s
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td>₱{{ number_format($booking->total_amount, 2) }}</td>

                        {{-- ✅ SINGLE TD FOR ACTIONS (no nested <td>) --}}
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                @if($booking->booking_status === 'Awaiting Payment')
                                    {{-- FIRST PAYMENT (deposit or full) --}}
                                    <button class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reservationPayModal{{ $booking->booking_id }}">
                                        Pay Now
                                    </button>

                                    <form action="{{ route('reservations.cancel', $booking->booking_id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Cancel this reservation?');">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Cancel
                                        </button>
                                    </form>

                                @elseif(in_array($booking->booking_status, ['Confirmed','Ongoing'])
                                        && $booking->paid_amount < $booking->total_amount)

                                    {{-- SECOND PAYMENT: PAY REMAINING BALANCE --}}
                                    <button class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#payBalanceModal{{ $booking->booking_id }}">
                                        Pay Balance
                                    </button>

                                    {{-- ⭐ NEW: allow refund request as well (optional) --}}
                                    @if($canRequestRefund)
                                        <form action="{{ route('reservations.refundRequest', $booking->booking_id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Request a refund for this booking?');">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-warning">
                                                Request Refund
                                            </button>
                                        </form>
                                    @endif

                                @else
                                    @if($canRequestRefund)
                                        {{-- Booking fully paid, before pickup → can only request refund --}}
                                        <form action="{{ route('reservations.refundRequest', $booking->booking_id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Request a refund for this booking?');">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-warning">
                                                Request Refund
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">{{ $booking->payment_status }}</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- ====== FIRST PAYMENT MODALS (DEPOSIT / FULL) ====== --}}
        @foreach($reservations as $booking)
            @if($booking->booking_status === 'Awaiting Payment')
                <div class="modal fade"
                     id="reservationPayModal{{ $booking->booking_id }}"
                     tabindex="-1"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Pay Reservation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body" style="max-height: 70vh; overflow-y:auto;">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        {{-- GCash QR --}}
                                        <div class="p-3 bg-white rounded-3 shadow-sm mb-4 text-center">
                                            <h6 class="fw-bold mb-2 text-primary">Scan to Pay</h6>

                                            <img src="{{ asset('images/PaymentGcashQR.png') }}"
                                                 alt="GCash QR"
                                                 class="rounded-3 border"
                                                 style="max-width: 180px;">

                                            <div class="mt-3 bg-light rounded-3 py-2 px-3 d-inline-block text-start"
                                                 style="min-width: 200px;">
                                                <p class="mb-1 small text-muted">GCash Number:</p>
                                                <h6 class="fw-bold text-dark mb-2">09317683228</h6>

                                                <p class="mb-1 small text-muted">Account Name:</p>
                                                <h6 class="fw-bold text-dark mb-0">John Loyd G</h6>
                                            </div>

                                            <p class="mt-3 small text-muted">
                                                Use your GCash app to scan this QR and pay.
                                            </p>
                                        </div>

                                        {{-- Reservation Summary --}}
                                        <h6 class="fw-bold">Reservation Summary</h6>
                                        <p class="mb-1">
                                            {{ $booking->vehicle->Brand ?? '' }}
                                            {{ $booking->vehicle->Model ?? '' }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Pickup:</strong>
                                            {{ $booking->pickup_datetime->format('M d, Y H:i') }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Return:</strong>
                                            {{ $booking->return_datetime->format('M d, Y H:i') }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Total:</strong>
                                            ₱{{ number_format($booking->total_amount, 2) }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Deposit:</strong>
                                            ₱{{ number_format($booking->security_deposit, 2) }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <form method="POST"
                                              action="{{ route('reservations.pay', $booking->booking_id) }}"
                                              enctype="multipart/form-data">
                                            @csrf

                                            {{-- Payment Type --}}
                                            <div class="mb-3">
                                                <label class="form-label">Payment Type</label>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           name="payment_option"
                                                           id="payDeposit{{ $booking->booking_id }}"
                                                           value="deposit"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="payDeposit{{ $booking->booking_id }}">
                                                        Pay Security Deposit Only
                                                        (₱{{ number_format($booking->security_deposit, 2) }})
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           name="payment_option"
                                                           id="payFull{{ $booking->booking_id }}"
                                                           value="full">
                                                    <label class="form-check-label"
                                                           for="payFull{{ $booking->booking_id }}">
                                                        Pay Full Amount
                                                        (₱{{ number_format($booking->total_amount, 2) }})
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Valid ID --}}
                                            <div class="mb-3">
                                                <label class="form-label">Upload Valid ID (optional)</label>
                                                <input type="file"
                                                       name="valid_id"
                                                       accept="image/*,application/pdf"
                                                       class="form-control">
                                                <small class="text-muted">
                                                    Driver’s license, passport, etc.
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Name on GCash</label>
                                                <input type="text"
                                                       name="payer_name"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">GCash Number</label>
                                                <input type="text"
                                                       name="payer_number"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Upload GCash Screenshot</label>
                                                <input type="file"
                                                       name="receipt_screenshot"
                                                       accept="image/*"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <button type="submit"
                                                    class="btn btn-primary w-100">
                                                Submit Payment
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- ====== PAY BALANCE MODALS ====== --}}
        @foreach($reservations as $booking)
            @php
                $paidAmount = $booking->paid_amount ?? 0;
                $balanceDue = max(0, $booking->total_amount - $paidAmount);
            @endphp

            @if($balanceDue > 0 && in_array($booking->booking_status, ['Confirmed', 'Ongoing']))
                <div class="modal fade"
                     id="payBalanceModal{{ $booking->booking_id }}"
                     tabindex="-1"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pay Remaining Balance</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body" style="max-height: 70vh; overflow-y:auto;">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-white rounded-3 shadow-sm mb-4 text-center">
                                            <h6 class="fw-bold mb-2 text-primary">Scan to Pay</h6>

                                            <img src="{{ asset('images/PaymentGcashQR.png') }}"
                                                 alt="GCash QR"
                                                 class="rounded-3 border"
                                                 style="max-width: 180px;">

                                            <div class="mt-3 bg-light rounded-3 py-2 px-3 d-inline-block text-start"
                                                 style="min-width: 200px;">
                                                <p class="mb-1 small text-muted">GCash Number:</p>
                                                <h6 class="fw-bold text-dark mb-2">09317683228</h6>

                                                <p class="mb-1 small text-muted">Account Name:</p>
                                                <h6 class="fw-bold text-dark mb-0">John Loyd G</h6>
                                            </div>

                                            <p class="mt-3 small text-muted">
                                                Use your GCash app to scan this QR and pay the remaining balance.
                                            </p>
                                        </div>

                                        <h6 class="fw-bold">Payment Summary</h6>
                                        <p class="mb-1"><strong>Total Amount:</strong> ₱{{ number_format($booking->total_amount, 2) }}</p>
                                        <p class="mb-1"><strong>Already Paid:</strong> ₱{{ number_format($paidAmount, 2) }}</p>
                                        <p class="mb-1 text-danger"><strong>Balance Due:</strong> ₱{{ number_format($balanceDue, 2) }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <form method="POST"
                                              action="{{ route('reservations.payBalance', $booking->booking_id) }}"
                                              enctype="multipart/form-data">
                                            @csrf

                                            <div class="mb-3">
                                                <label class="form-label">Name on GCash</label>
                                                <input type="text"
                                                       name="payer_name"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">GCash Number</label>
                                                <input type="text"
                                                       name="payer_number"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Upload GCash Screenshot</label>
                                                <input type="file"
                                                       name="receipt_screenshot"
                                                       accept="image/*"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <button type="submit"
                                                    class="btn btn-primary w-100">
                                                Submit Balance Payment
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timers = document.querySelectorAll('.time-used');

        timers.forEach(function (el) {
            const status = el.dataset.status;
            const startTs = parseInt(el.dataset.start, 10);    
            const endTs   = el.dataset.end ? parseInt(el.dataset.end, 10) : null;

            if (!startTs) {
                el.textContent = '0s';
                return;
            }

            function formatDuration(sec) {
                if (sec < 0) sec = 0;
                const h = Math.floor(sec / 3600);
                const m = Math.floor((sec % 3600) / 60);
                const s = sec % 60;

                const parts = [];
                if (h > 0) parts.push(h + 'h');
                if (m > 0 || h > 0) parts.push(m.toString().padStart(2, '0') + 'm');
                parts.push(s.toString().padStart(2, '0') + 's');
                return parts.join(' ');
            }

            function update() {
                const nowMs   = Date.now();
                const startMs = startTs * 1000;
                const endMs   = endTs ? endTs * 1000 : null;

                let effectiveNow = nowMs;

                if (endMs && nowMs > endMs) {
                    effectiveNow = endMs;
                }

                let diffSec = Math.floor((effectiveNow - startMs) / 1000);
                if (diffSec < 0) diffSec = 0; 
                el.textContent = formatDuration(diffSec);

                if (status === 'Completed' || (endMs && nowMs > endMs)) {
                    return false;
                }
                return true;
            }

            let keepGoing = update();

            if (keepGoing) {
                const intervalId = setInterval(function () {
                    const still = update();
                    if (!still) clearInterval(intervalId);
                }, 1000);
            }
        });
    });
</script>
@endsection

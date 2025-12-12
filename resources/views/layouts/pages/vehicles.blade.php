<head>
    <title>AutoPiloto | Vehicles</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.app')

@section('content')
@php
/** @var \Illuminate\Pagination\LengthAwarePaginator $vehicles */
@endphp
<style>
  body {
    background-color: #dadee1ff;
  }

  input[type="date"],
  input[type="time"]{
    cursor: pointer;
  }

  .custom-modal{
    max-width: 900px;
  }

  button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}


  .card-img-top {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .total-section {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
  }

  .total-section p {
    margin: 0;
    font-size: 15px;
  }

  .total-section .fw-bold {
    font-size: 17px;
  }

  .btn-book {
    background-color: #4d0000;
    color: white;
    border-radius: 8px;
    padding: 8px 20px;
    transition: 0.3s;
  }

  .btn-book:hover {
    background-color: #660000;
    color: #fff;
  }

  .btn-reserve {
    background-color: #ffeeee;
    color: #4d0000;
    border-radius: 8px;
    padding: 8px 20px;
    border: 1px solid #f5cccc;
    transition: 0.3s;
  }

  .btn-reserve:hover {
    background-color: #f8dcdc;
    color: #4d0000;
  }
  .payment-tabs .nav-link {
  background: #1f2733;
  border-radius: 8px;
  margin-right: 8px;
  padding: 8px 15px;
  color: #9ba5b4;
  font-weight: 500;
  }
  .payment-tabs .nav-link.active {
    background: #0d6efd;
    color: #fff;
  }
  .payment-input {
    background: #1f2733 !important;
    border: 1px solid #2f3a48 !important;
    color: #fff !important;
  }
  .payment-input::placeholder {
    color: #6c757d !important;
  }
  .summary-box {
    background: #111827;
    border-radius: 14px;
    padding: 25px;
  }
  .summary-box p {
    margin: 0;
    font-size: 14px;
  }
  .pay-btn {
    background: #0d6efd;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
  }
</style>

<div class="container py-5">
  <div class="text-center py-5 mt-5">
    <h1 class="fw-bold">Our Fleet</h1>
    <p class="text-muted">Choose from our premium selection of vehicles</p>

  <form method="GET" action="{{ route('vehicles') }}" class="mb-4">
    <div class="row g-3 align-items-end">

      <div class="col-md-4">
        <label class="form-label fw-semibold">Search</label>
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="Search brand or model..."
          value="{{ request('search') }}"
        >
      </div>

      <div class="col-md-3">
        <label class="form-label fw-semibold">Brand</label>
        <select name="brand" class="form-select">
          <option value="">All brands</option>
          @foreach($brands as $brand)
            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
              {{ $brand }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label fw-semibold">Price range (₱ / day)</label>
        <div class="d-flex gap-2">
          <input
            type="number"
            name="price_min"
            class="form-control"
            placeholder="Min"
            value="{{ request('price_min') }}"
            min="0"
          >
          <input
            type="number"
            name="price_max"
            class="form-control"
            placeholder="Max"
            value="{{ request('price_max') }}"
            min="0"
          >
        </div>
      </div>

      <div class="col-md-2">
        <label class="form-label fw-semibold">Sort by</label>
        <select name="sort" class="form-select">
          <option value="">Default</option>
          <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Price: Low to High</option>
          <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
        </select>
      </div>

      <div class="col-12 col-md-12 mt-2 d-flex gap-2 justify-content-end">
        <button type="submit" class="btn btn-primary">
          Apply
        </button>
        <a href="{{ route('vehicles') }}" class="btn btn-outline-secondary">
          Reset
        </a>
      </div>

    </div>
  </form>
  </div>

  <div class="row">
    @foreach ($vehicles as $vehicle)
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm border-0">
       <img 
              src="{{ $vehicle->Image 
                  ? (file_exists(public_path('images/' . $vehicle->Image)) 
                      ? asset('images/' . $vehicle->Image)
                      : asset('storage/' . $vehicle->Image))
                  : asset('images/default-car.png') }}"
              class="card-img-top" 
              alt="{{ $vehicle->Model }}"
          >

        <div class="card-body">
          <h5 class="card-title fw-bold">{{ $vehicle->Brand }} {{ $vehicle->Model }}</h5>

          <div class="d-flex justify-content-between text-muted small mb-3">
            <span><i class="bi bi-people"></i> {{ $vehicle->Passengers }} Passengers</span>
            <span><i class="bi bi-gear"></i> {{ $vehicle->Transmission }}</span>
            <span><i class="bi bi-fuel-pump"></i> {{ $vehicle->FuelType }}</span>
          </div>

          <p class="fs-5 fw-semibold text-danger">
            ₱{{ number_format($vehicle->DailyPrice) }}
            <small class="text-muted">/day</small>
          </p>

          <div class="d-flex justify-content-between">
              @if ($vehicle->Availability == 1)

                  @auth
                      @if (auth()->user()->kyc_status === 'Approved')
                          {{-- ✅ Verified user: normal modals --}}
                          <button
                              class="btn btn-book w-50 me-2"
                              data-bs-toggle="modal"
                              data-bs-target="#bookModal{{ $vehicle->VehicleID }}">
                              Book Now
                          </button>

                          <button
                              class="btn btn-reserve w-50"
                              data-bs-toggle="modal"
                              data-bs-target="#reserveModal{{ $vehicle->VehicleID }}">
                              Reserve
                          </button>
                      @else
                          {{-- ⚠️ Logged-in but NOT KYC verified --}}
                          <button
                              type="button"
                              class="btn btn-book w-50 me-2 btn-require-kyc"
                              data-redirect="{{ route('kyc.form') }}">
                              Book Now
                          </button>

                          <button
                              type="button"
                              class="btn btn-reserve w-50 btn-require-kyc"
                              data-redirect="{{ route('kyc.form') }}">
                              Reserve
                          </button>
                      @endif
                  @else
                      {{-- 🧍 Guest (not logged in) --}}
                      <button
                          type="button"
                          class="btn btn-book w-50 me-2 btn-require-login"
                          data-redirect="{{ route('login') }}">
                          Book Now
                      </button>

                      <button
                          type="button"
                          class="btn btn-reserve w-50 btn-require-login"
                          data-redirect="{{ route('login') }}">
                          Reserve
                      </button>
                  @endauth

              @else
                  {{-- Vehicle unavailable --}}
                  <button class="btn btn-secondary w-50 me-2" disabled title="This vehicle is currently booked.">
                      Book Now
                  </button>

                  <button class="btn btn-secondary w-50" disabled title="This vehicle is currently booked.">
                      Reserve
                  </button>
              @endif
          </div>
        </div>
      </div>
    </div>

{{-- Book Modal --}}
<div class="modal fade" id="bookModal{{ $vehicle->VehicleID }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable custom-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Book Your Ride</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="fw-bold mb-1">{{ $vehicle->Brand }} {{ $vehicle->Model }}</p>
        <p class="text-muted mb-4">₱{{ number_format($vehicle->DailyPrice) }}/day</p>

        <form class="bookingForm" data-vehicle-id="{{ $vehicle->VehicleID }}" action="{{ route('vehicles.store') }}" method="POST">
          @csrf
          <input type="hidden" name="VehicleID" value="{{ $vehicle->VehicleID }}">
          <input type="hidden" name="pickup_location" id="pickupLocationInput{{ $vehicle->VehicleID }}">
          <input type="hidden" name="dropoff_location" id="dropoffLocationInput{{ $vehicle->VehicleID }}">
          <input type="hidden" name="pickup_datetime" id="pickupDateTime{{ $vehicle->VehicleID }}">
          <input type="hidden" name="return_datetime" id="returnDateTime{{ $vehicle->VehicleID }}">
          <input type="hidden" name="addons" id="addonsInput{{ $vehicle->VehicleID }}">

          <div id="addonsContainer{{ $vehicle->VehicleID }}"></div>

          {{-- Location --}}
          <div class="mb-3">
            <select class="form-select" id="locationType{{ $vehicle->VehicleID }}">
              <option value="same">Same Drop-off Location</option>
              <option value="different">Different Drop-off Location</option>
            </select>

            <div class="border p-3 rounded mt-2">
              <div class="row g-2">
                <div class="col-12" id="pickupOnly{{ $vehicle->VehicleID }}">
                  <label class="fw-semibold text-secondary mb-2">Pick-up Location</label>
                  <input type="text"
                      class="form-control js-location-autocomplete js-pickup-location"
                      id="pickupLocation{{ $vehicle->VehicleID }}"
                      placeholder="Province, city">
                </div>
                <div class="col-md-6" id="pickupDifferent{{ $vehicle->VehicleID }}" style="display:none;">
                  <label class="fw-semibold text-secondary mb-2">Pick-up Location</label>
                  <input type="text"
                      class="form-control js-location-autocomplete js-pickup-location"
                      id="pickupLocationDiff{{ $vehicle->VehicleID }}"
                      placeholder="Province, city">
                </div>
                <div class="col-md-6" id="dropoffDifferent{{ $vehicle->VehicleID }}" style="display:none;">
                  <label class="fw-semibold text-secondary mb-2">Drop-off Location</label>
                  <input type="text"
                      class="form-control js-location-autocomplete js-dropoff-location"
                      id="dropoffLocationDiff{{ $vehicle->VehicleID }}"
                      placeholder="Province, city">
                </div>
              </div>
            </div>
          </div>

          {{-- Dates --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Pickup Date</label>
              <input type="date" class="form-control" id="pickupDate{{ $vehicle->VehicleID }}" min="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Pickup Time</label>
              <input type="time" class="form-control" id="pickupTime{{ $vehicle->VehicleID }}">
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Return Date</label>
              <input type="date" class="form-control" id="returnDate{{ $vehicle->VehicleID }}" min="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Return Time</label>
              <input type="time" class="form-control" id="returnTime{{ $vehicle->VehicleID }}">
            </div>
          </div>

          {{-- Add-ons --}}
          <div class="addons mb-3">
          <label class="form-label fw-bold">Optional Add-ons</label>
          <div class="form-check">
            <input class="form-check-input addon" type="checkbox" id="driver{{ $vehicle->VehicleID }}" value="driver">
            <label class="form-check-label">Driver Service (+₱500/day)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input addon" type="checkbox" id="childSeat{{ $vehicle->VehicleID }}" value="childSeat">
            <label class="form-check-label">Child Seat (+₱200/day)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input addon" type="checkbox" id="insurance{{ $vehicle->VehicleID }}" value="insurance">
            <label class="form-check-label">Insurance (+₱300)</label>
          </div>
        </div>

          {{-- Total --}}
          <div class="total-section mt-4">
            <p>Security Deposit (Refundable): <span class="float-end text-primary">₱3,000</span></p>
            <p>Duration Cost: <span class="float-end text-primary" id="durationCost{{ $vehicle->VehicleID }}">₱0</span></p>
            <p>Add-ons: <span class="float-end text-primary" id="addonsTotal{{ $vehicle->VehicleID }}">₱0</span></p>
            <p>Subtotal: <span class="float-end text-primary" id="subtotal{{ $vehicle->VehicleID }}">₱0</span></p>
            <hr>
            <p class="fw-bold">Grand Total: <span class="float-end text-danger" id="grandTotal{{ $vehicle->VehicleID }}">₱3,000</span></p>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary confirmBookingBtn" data-vehicle-id="{{ $vehicle->VehicleID }}">
              Confirm Booking
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

{{-- ✅ GCash Payment Modal --}}
<div class="modal fade" id="paymentModal{{ $vehicle->VehicleID }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      {{-- HEADER --}}
      <div class="d-flex justify-content-between align-items-center bg-primary text-white px-4 py-2">
        <div class="d-flex align-items-center">
          <button type="button" class="btn btn-light btn-sm me-2 backToBooking" data-vehicle-id="{{ $vehicle->VehicleID }}">
            <i class="bi bi-arrow-left"></i> Back
          </button>
          <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i> GCash Payment</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- BODY --}}
      <div class="modal-body bg-light">
        <div class="row g-4 align-items-start">

          {{-- LEFT SIDE: QR + Summary --}}
          <div class="col-md-6 text-center">
            <div class="p-3 bg-white rounded-3 shadow-sm">
              <h6 class="fw-bold mb-2 text-primary">Scan to Pay</h6>

                {{-- GCash QR --}}
                <img src="{{ asset('images/PaymentGcashQR.png') }}" 
                    alt="GCash QR" 
                    class="rounded-3 border" 
                    style="max-width:220px;">

                {{-- Static GCash Info (added here) --}}
                <div class="mt-3 bg-light rounded-3 py-2 px-3 d-inline-block text-start" style="min-width: 200px;">
                  <p class="mb-1 small text-muted">GCash Number:</p>
                  <h6 class="fw-bold text-dark mb-2">09317683228</h6>

                  <p class="mb-1 small text-muted">Account Name:</p>
                  <h6 class="fw-bold text-dark mb-0">John Loyd G</h6>
                </div>

                <p class="mt-3 small text-muted">
                  Use your GCash app to scan this QR and pay the total amount shown.
                </p>

                <hr class="my-3">

              <div class="text-start px-2">
                <p class="mb-1 fw-semibold text-secondary">Vehicle:</p>
                <p class="fw-bold" id="vehicleName{{ $vehicle->VehicleID }}">{{ $vehicle->Brand }} {{ $vehicle->Model }}</p>

                <p class="mb-1 fw-semibold text-secondary">Pickup:</p>
                <p id="pickupText{{ $vehicle->VehicleID }}">-</p>

                <p class="mb-1 fw-semibold text-secondary">Return:</p>
                <p id="returnText{{ $vehicle->VehicleID }}">-</p>

                <hr>

                {{-- Cost Breakdown --}}
                <div class="text-start px-2">
                  <div class="d-flex justify-content-between">
                    <span>Base Price:</span>
                    <span id="basePrice{{ $vehicle->VehicleID }}">₱0</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Driver:</span>
                    <span id="driverPrice{{ $vehicle->VehicleID }}">₱0</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Child Seat:</span>
                    <span id="childSeatPrice{{ $vehicle->VehicleID }}">₱0</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Insurance:</span>
                    <span id="insurancePrice{{ $vehicle->VehicleID }}">₱0</span>
                  </div>
                  <div class="d-flex justify-content-between mt-2 border-top pt-2 fw-bold">
                    <span>Security Deposit:</span>
                    <span>₱3000</span>
                  </div>
                  <div class="d-flex justify-content-between mt-1 fw-bold fs-5 text-success">
                    <span>Total Amount:</span>
                    <span id="gcashTotalText{{ $vehicle->VehicleID }}">₱0</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- RIGHT SIDE: Upload Receipt --}}
          <div class="col-md-6">
            <div class="bg-white p-4 rounded-3 shadow-sm">
              <form method="POST" action="{{ route('booking.payment') }}" enctype="multipart/form-data" id="gcashForm{{ $vehicle->VehicleID }}">
                @csrf
                <input type="hidden" name="VehicleID" value="{{ $vehicle->VehicleID }}">
                <input type="hidden" name="booking_id" id="bookingId{{ $vehicle->VehicleID }}">
                <input type="hidden" name="total_amount" id="paymentTotal{{ $vehicle->VehicleID }}">

                <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i> Upload Proof of Payment</h6>

                <div class="mb-3">
                  <label class="form-label">Name on GCash</label>
                  <input type="text" name="payer_name" class="form-control" placeholder="Ex. Juan Dela Cruz" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">GCash Mobile Number</label>
                  <input type="text" name="payer_number" class="form-control" placeholder="09XXXXXXXXX" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Upload Screenshot</label>
                  <input type="file" name="receipt_screenshot" accept="image/*" class="form-control" required>
                  <small class="text-muted">Make sure the amount and reference number are visible.</small>
                </div>

                <div class="alert alert-info py-2 small mt-2">
                  <i class="bi bi-info-circle me-2"></i>
                  After uploading, wait for our staff to confirm your payment.
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mt-3">
                  <i class="bi bi-send-check me-1"></i> Submit Payment
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>

      {{-- FOOTER --}}
      <div class="modal-footer bg-light border-0">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

        {{-- Reserve Modal --}}
    <div class="modal fade" id="reserveModal{{ $vehicle->VehicleID }}" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable custom-modal">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title fw-bold">Reserve Vehicle</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p class="fw-bold mb-1">{{ $vehicle->Brand }} {{ $vehicle->Model }}</p>
            <p class="text-muted mb-4">Reserve now and pay later.</p>

            <form class="reserveForm" data-vehicle-id="{{ $vehicle->VehicleID }}"
                  action="{{ route('booking.reserve') }}" method="POST">
              @csrf
              <input type="hidden" name="VehicleID" value="{{ $vehicle->VehicleID }}">

              <input type="hidden" name="dropoff_location" id="dropoffLocationHidden{{ $vehicle->VehicleID }}">

              <div class="mb-3">
                <select class="form-select" id="locationTypeReserve{{ $vehicle->VehicleID }}">
                  <option value="same">Same Drop-off Location</option>
                  <option value="different">Different Drop-off Location</option>
                </select>

                <div class="border p-3 rounded mt-2">
                  <div class="row g-2">
                    <div class="col-12" id="pickupOnlyReserve{{ $vehicle->VehicleID }}">
                      <label class="fw-semibold text-secondary mb-2">Pick-up Location</label>
                      <input type="text"
                        class="form-control js-location-autocomplete js-pickup-location"
                        id="pickupLocationReserve{{ $vehicle->VehicleID }}"
                        name="pickup_location" placeholder="Province, city">
                    </div>
                    <div class="col-md-6" id="pickupDifferentReserve{{ $vehicle->VehicleID }}" style="display:none;">
                      <label class="fw-semibold text-secondary mb-2">Pick-up Location</label>
                      <input type="text"
                            class="form-control js-location-autocomplete js-pickup-location"
                            id="pickupLocationDiffReserve{{ $vehicle->VehicleID }}"
                            placeholder="Enter pickup location" placeholder="Province, city">
                    </div>
                    <div class="col-md-6" id="dropoffDifferentReserve{{ $vehicle->VehicleID }}" style="display:none;">
                      <label class="fw-semibold text-secondary mb-2">Drop-off Location</label>
                      <input type="text"
                            class="form-control js-location-autocomplete js-dropoff-location"
                            id="dropoffLocationDiffReserve{{ $vehicle->VehicleID }}"
                            placeholder="Enter drop-off location" placeholder="Province, city">
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Pickup Date</label>
                  <input type="date"
                         class="form-control reserve-pickup-date"
                         id="pickupDate{{ $vehicle->VehicleID }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Pickup Time</label>
                  <input type="time"
                         class="form-control reserve-pickup-time"
                         id="pickupTime{{ $vehicle->VehicleID }}">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Return Date</label>
                  <input type="date"
                         class="form-control reserve-return-date"
                         id="returnDate{{ $vehicle->VehicleID }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Return Time</label>
                  <input type="time"
                         class="form-control reserve-return-time"
                         id="returnTime{{ $vehicle->VehicleID }}">
                </div>
              </div>

              <div class="addons mb-3">
                <label class="form-label fw-bold">Optional Add-ons</label>
                <div class="form-check">
                  <input class="form-check-input addon reserve-addon"
                         type="checkbox"
                         id="driver{{ $vehicle->VehicleID }}"
                         value="driver">
                  <label class="form-check-label">Driver Service (+₱500/day)</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input addon reserve-addon"
                         type="checkbox"
                         id="childSeat{{ $vehicle->VehicleID }}"
                         value="childSeat">
                  <label class="form-check-label">Child Seat (+₱200/day)</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input addon reserve-addon"
                         type="checkbox"
                         id="insurance{{ $vehicle->VehicleID }}"
                         value="insurance">
                  <label class="form-check-label">Insurance (+₱300)</label>
                </div>
              </div>

              <div id="addonsContainerReserve{{ $vehicle->VehicleID }}"></div>

              <div class="total-section mt-4">
                <p>Security Deposit:
                  <span class="float-end text-primary">₱3,000</span>
                </p>
                <p>Duration Cost:
                  <span class="float-end text-primary reserve-duration"
                        id="durationCost{{ $vehicle->VehicleID }}">₱0</span>
                </p>
                <p>Add-ons:
                  <span class="float-end text-primary reserve-addons"
                        id="addonsTotal{{ $vehicle->VehicleID }}">₱0</span>
                </p>
                <p>Subtotal:
                  <span class="float-end text-primary reserve-subtotal"
                        id="subtotal{{ $vehicle->VehicleID }}">₱0</span>
                </p>
                <hr>
                <p class="fw-bold">Total:
                  <span class="float-end text-danger reserve-grandtotal"
                        id="grandTotal{{ $vehicle->VehicleID }}">₱3,000</span>
                </p>
              </div>

              <input type="hidden" name="pickup_datetime" id="pickupDateTimeReserve{{ $vehicle->VehicleID }}">
              <input type="hidden" name="return_datetime" id="returnDateTimeReserve{{ $vehicle->VehicleID }}">
              <input type="hidden" name="addons[]" id="addonsReserve{{ $vehicle->VehicleID }}">

              <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary btn-confirm-reserve"
                        data-vehicle-id="{{ $vehicle->VehicleID }}"
                        type="button">
                  Confirm Reservation
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

    @endforeach
  </div>
  <div class="d-flex justify-content-center mt-4 mb-5">
    {{ $vehicles->links('pagination::bootstrap-5') }}
</div>
</div>
@if ($errors->has('reserve'))
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Warning',
        text: "{{ $errors->first('reserve') }}",
        confirmButtonColor: '#d33',
    });
</script>
@endif


<div id="vehicleData" data-vehicles='@json($vehicles->items())'></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const token = csrfMeta ? csrfMeta.content : '';

    // ✅ Always correct URL (no matter where app is hosted)
    const searchUrl = "{{ route('locations.search') }}";

    document.querySelectorAll('.js-location-autocomplete').forEach(function (input) {
        let dropdown = null;

        input.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length < 2) {
                closeDropdown();
                return;
            }

            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(res => res.json())
                .then(data => {
                    console.log('Locations result:', data); // 👀 debug in console
                    showDropdown(this, data);
                })
                .catch(err => console.error('Locations search error:', err));
        });

        input.addEventListener('blur', function () {
            setTimeout(closeDropdown, 200);
        });

        function showDropdown(inputEl, items) {
            closeDropdown();

            if (!items.length) return;

            dropdown = document.createElement('div');
            dropdown.className = 'list-group position-absolute w-100 shadow-sm bg-white';
            dropdown.style.zIndex = 9999;
            dropdown.style.maxHeight = '220px';
            dropdown.style.overflowY = 'auto';

            items.forEach(function (item) {
                const div = document.createElement('button');
                div.type = 'button';
                div.className = 'list-group-item list-group-item-action small text-start';

                let label = item.name;
                if (item.type === 'airport' && item.code) {
                    label += ` (${item.code})`;
                } else if (item.province) {
                    label += `, ${item.province}`;
                }

                div.textContent = label;

                div.addEventListener('click', function () {
                    inputEl.value = label;
                    closeDropdown();
                });

                dropdown.appendChild(div);
            });

            // parent needs to be relative to position dropdown correctly
            const parent = inputEl.parentElement;
            if (getComputedStyle(parent).position === 'static') {
                parent.style.position = 'relative';
            }
            parent.appendChild(dropdown);
        }

        function closeDropdown() {
            if (dropdown && dropdown.parentNode) {
                dropdown.parentNode.removeChild(dropdown);
                dropdown = null;
            }
        }
    });
});
</script>

<script src="{{ asset('js/vehicles.js') }}"></script>
@endsection

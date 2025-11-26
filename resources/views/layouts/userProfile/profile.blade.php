@extends('layouts.app')

@section('content')
<style>
body { background-color: #e6eaf0ff; }
.profile-container { margin-top: 120px !important; padding-bottom: 100px; }
.profile-header { background: #b3dcf0ff; border-radius: 20px; padding: 40px; }
.profile-avatar { width: 130px; height: 130px; border-radius: 50%; background: #007BFF; color: white; font-size: 45px; font-weight: bold; display: flex; justify-content: center; align-items: center; position: relative; overflow: hidden; }
.upload-btn { position: absolute; bottom: 0; right: 0; background: #fff; border-radius: 50%; width: 38px; height: 38px; display: flex; justify-content: center; align-items: center; cursor: pointer; border: 2px solid #007BFF; }
.rental-card { border-radius: 16px; padding: 20px; border: 1px solid #dbe2ea; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.stat-number { font-size: 32px; font-weight: bold; color: #0056D2; }
.profile-tabs .nav-link.active { background: #E9F8FF; border-bottom: 3px solid #007BFF; font-weight: 600; }
</style>

<div class="container profile-container">

    <div class="profile-header shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <div class="position-relative d-inline-block">
                    @if(Auth::user()->profile_image)
                        <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" class="profile-avatar border border-3 border-white" style="object-fit: cover;">
                    @else
                        @php
                            $nameParts = explode(' ', Auth::user()->name);
                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr(end($nameParts), 0, 1) : ''));
                        @endphp
                        <div class="profile-avatar">{{ $initials }}</div>
                    @endif

                    <label class="upload-btn">
                        <i class="bi bi-camera-fill text-primary"></i>
                        <form id="uploadForm" action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="profile_image" id="profileInput" hidden onchange="document.getElementById('uploadForm').submit()">
                        </form>
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="fw-bold">{{ Auth::user()->name }}</h2>
                <div class="mt-3">
                    <p class="mb-1"><i class="bi bi-envelope"></i> {{ Auth::user()->email }}</p>
                    <p class="mb-1">
                        <i class="bi bi-telephone"></i>
                        {{ Auth::user()->phone ?? 'No phone number added' }}
                    </p>
                    <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ Auth::user()->address }}</p>
                    <p class="mb-1"><i class="bi bi-calendar"></i> {{ Auth::user()->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="col-md-4 text-end">
                <button class="btn btn-primary px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#editProfileModal">
                    Edit Profile
                </button>
            </div>
        </div>

        <div class="row text-center mt-4">
        <div class="col">
            <div class="stat-number">{{ $totalBookings }}</div>
            <p>Total Rentals</p>
        </div>
        <div class="col">
            <div class="stat-number">{{ $averageRating ?? 'N/A' }}</div>
            <p>Average Rating</p>
        </div>
        <div class="col">
            <div class="stat-number">₱{{ number_format($totalSpent, 2) }}</div>
            <p>Total Spent</p>
        </div>
    </div>
    </div>

    <ul class="nav nav-tabs profile-tabs mt-4">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#rentals">Rental History</a></li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#verify">
                Verify Account
                @if(Auth::user()->kyc_status == 'Approved')
                    <span class="badge bg-success ms-1">✔</span>
                @elseif(Auth::user()->kyc_status == 'Pending')
                    <span class="badge bg-warning ms-1">⏳</span>
                @else
                    <span class="badge bg-danger ms-1">!</span>
                @endif
            </a>
        </li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">Payment Methods</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#settings">Settings</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="rentals">
            @forelse($bookings as $booking)
                <div class="rental-card bg-white mt-4">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="{{ asset('images/' . $booking->vehicle->Image) }}" class="img-fluid rounded" alt="{{ $booking->vehicle->Model }}">
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold">{{ $booking->vehicle->Brand }} {{ $booking->vehicle->Model }}</h5>
                            <p class="mb-1"><i class="bi bi-calendar"></i>
                                {{ $booking->pickup_datetime->format('M d, Y H:i') }} - 
                                {{ $booking->return_datetime->format('M d, Y H:i') }}
                            </p>
                            <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ $booking->pickup_location }}</p>
                        </div>
                        <div class="col-md-2 text-primary fw-bold fs-5">
                            ₱{{ number_format($booking->total_amount, 2) }}
                        </div>
                        <div class="col-md-2 text-end">
                            <span class="badge bg-success px-3 py-2">{{ ucfirst($booking->booking_status) }}</span>
                            <br>
                            <a href="#" class="btn btn-outline-primary btn-sm mt-2">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="mt-4">No bookings found.</p>
            @endforelse
        </div>

        <div class="tab-pane fade" id="verify">
            <div class="rental-card bg-white mt-4">

                <h4 class="fw-bold mb-3">Account Verification</h4>

                @if(Auth::user()->kyc_status == 'Approved')
                <div class="alert alert-success">
                    <strong>Your identity has been verified ✔</strong><br>
                    You can now book and reserve vehicles without restrictions.
                </div>

                @elseif(Auth::user()->kyc_status == 'Pending')
                    <div class="alert alert-warning">
                        <strong>Your identity verification is currently under review ⏳</strong><br>
                        Please wait while we process your documents.
                    </div>

                @elseif(Auth::user()->kyc_status == 'Rejected')
                    <div class="alert alert-danger">
                        <strong>Your identity verification was not approved ❌</strong><br>
                        Please review your details and submit your verification again.
                    </div>

                @else
                    <div class="alert alert-info">
                        <strong>Your identity is not verified.</strong><br>
                        Please complete the verification form below to continue.
                    </div>
                @endif

                @include('layouts.kyc._form', [
                    'kyc'  => $kyc ?? null,
                    'user' => Auth::user(),
                ])
            </div>
        </div>
        <div class="tab-pane fade" id="payment">Payment methods coming soon.</div>
        <div class="tab-pane fade" id="settings">Settings coming soon.</div>
    </div>
    @if(session('profile_success'))
    <div class="alert alert-success mt-3">
        {{ session('profile_success') }}
    </div>
@endif

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', Auth::user()->name) }}"
                   required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text"
                   name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', Auth::user()->phone) }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text"
                   name="address"
                   class="form-control @error('address') is-invalid @enderror"
                   value="{{ old('address', Auth::user()->address) }}">
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Optional: allow email change too --}}
          {{-- 
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', Auth::user()->email) }}">
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          --}}

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            Save Changes
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.location.hash === "#verify") {
        const tabTrigger = document.querySelector('a[href="#verify"]');
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }
});
</script>

@endsection

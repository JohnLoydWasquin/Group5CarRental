@php
    $locked = in_array($user->kyc_status, ['Pending', 'Approved']);
@endphp

@php
    $status = $user->kyc_status;
    $isViewOnly = in_array($status, ['Pending', 'Approved']);
    $canResubmit = $status === 'Rejected';
@endphp


<div class="card kyc-card">
    <div class="card-body">

        <style>
            .kyc-image-modal .modal-dialog {
                max-width: 650px;          
            }

            .kyc-image-modal .modal-content {
                background: transparent;
                border: 0;
                box-shadow: none;
            }

            .kyc-image-modal .modal-body {
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #kycModalImg {
                max-width: 100%;
                max-height: 80vh;           
                border-radius: 10px;
                box-shadow: 0 0 25px rgba(0,0,0,0.4);
            }
        </style>

        <form method="POST"
              action="{{ route('kyc.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- Personal info --}}
            <h5 class="fw-bold mb-3">Personal Information</h5>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input type="text" name="full_name"
                        class="form-control"
                        value="{{ old('full_name', $kyc->full_name ?? $user->name) }}"
                        {{ $locked ? 'readonly' : '' }} required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sex</label>

                    @if($locked)
                        <select class="form-select" disabled>
                            <option>{{ $kyc->sex }}</option>
                        </select>

                        <input type="hidden" name="sex" value="{{ $kyc->sex }}">
                    @else
                        <select name="sex" class="form-select" required>
                            <option value="">Select</option>
                            @foreach(['Male','Female','Prefer not to say'] as $sex)
                                <option value="{{ $sex }}"
                                    {{ old('sex') === $sex ? 'selected' : '' }}>
                                    {{ $sex }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="col-md-3">
                    <label class="form-label">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate"
                        class="form-control"
                        value="{{ old('birthdate', $kyc?->birthdate?->format('Y-m-d')) }}"
                        {{ $locked ? 'readonly' : '' }} required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Age</label>
                    <input type="text" id="age" class="form-control" readonly>
                </div>
            </div>

            {{-- Address --}}
            <h5 class="fw-bold mt-4">Address</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label>Country</label>
                    <select name="country" class="form-select" {{ $locked ? 'readonly' : '' }}>
                        <option value="Philippines" selected>Philippines</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mt-3">
                    <label>Address Line (Street / Barangay)</label>
                    <input type="text"
                        name="address_line"
                        class="form-control"
                        value="{{ old('address_line', $kyc?->address_line ?? '') }}"
                        {{ $locked ? 'readonly' : '' }}
                        required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6 position-relative">
                        <label class="form-label">City</label>
                        <input type="text"
                            name="city"
                            id="kyc_city"
                            class="form-control js-location-autocomplete"
                            value="{{ old('city', $kyc?->city ?? '') }}"
                            {{ $locked ? 'readonly' : '' }}
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Province</label>
                        <input type="text"
                            name="province"
                            id="kyc_province"
                            class="form-control js-location-autocomplete"
                            value="{{ old('province', $kyc?->province ?? '') }}"
                            {{ $locked ? 'readonly' : '' }}
                            required>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <label>Postal Code</label>
                <input type="text"
                    name="postal_code"
                    class="form-control"
                    value="{{ old('postal_code', $kyc?->postal_code ?? '') }}"
                    maxlength="4"
                    pattern="\d{4}"
                    inputmode="numeric"
                    placeholder="e.g. 1000"
                    {{ $locked ? 'readonly' : '' }}
                    required>
                <small class="text-muted">
                    Philippine postal codes are 4 digits.
                </small>
            </div>

            {{-- ID details --}}
            <h5 class="fw-bold mt-4">ID Details</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <label>ID Type</label>
                    <select id="id_type" name="id_type" class="form-select" {{ $locked ? 'readonly' : '' }} required>
                        <option value="">Select</option>
                        <option value="Passport">Passport</option>
                        <option value="Driver License">Driver License</option>
                        <option value="National ID">National ID</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>ID Number</label>
                    <input type="text" id="id_number" name="id_number"
                        class="form-control"
                        value="{{ old('id_number', $kyc->id_number ?? '') }}"
                        {{ $locked ? 'readonly' : '' }} required>
                    <div class="invalid-feedback" id="idError"></div>
                </div>
            </div>

            {{-- Uploads --}}
            <h5 class="fw-bold mb-3 mt-4">Uploads</h5>

            <div class="row g-3 mb-3">

                {{-- ID IMAGE --}}
                <div class="col-md-6">
                    <label class="form-label">Government ID (front)</label>
                    <input type="file" name="id_image" accept="image/*"
                           class="form-control @error('id_image') is-invalid @enderror">
                    @error('id_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    @if(!empty($kyc?->id_image_path))
                        <small class="d-block mt-1">
                            Current file:
                            <a href="javascript:void(0)"
                               class="kyc-image-link"
                               data-img="{{ asset('storage/' . $kyc->id_image_path) }}">
                                View
                            </a>
                        </small>
                    @endif
                </div>

                {{-- SELFIE IMAGE --}}
                <div class="col-md-6">
                    <label class="form-label">Selfie with ID</label>
                    <input type="file" name="selfie_image" accept="image/*"
                           class="form-control @error('selfie_image') is-invalid @enderror">
                    @error('selfie_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    @if(!empty($kyc?->selfie_image_path))
                        <small class="d-block mt-1">
                            Current file:
                            <a href="javascript:void(0)"
                               class="kyc-image-link"
                               data-img="{{ asset('storage/' . $kyc->selfie_image_path) }}">
                                View
                            </a>
                        </small>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                Submit Verification
            </button>
        </form>
    </div>
</div>

<div class="modal fade kyc-image-modal" id="kycImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <button type="button"
                    class="btn-close ms-auto me-3 mt-3"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>

            <div class="modal-body">
                <img id="kycModalImg" src="" alt="Preview">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const links   = document.querySelectorAll('.kyc-image-link');
    const img     = document.getElementById('kycModalImg');
    const modalEl = document.getElementById('kycImageModal');

    if (!modalEl || !img) return;

    const modal = new bootstrap.Modal(modalEl);

    links.forEach(link => {
        link.addEventListener('click', function () {
            img.src = this.dataset.img;
            modal.show();
        });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* AGE */
    const birthdate = document.getElementById('birthdate');
    if (birthdate) {
        birthdate.addEventListener('change', function () {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            document.getElementById('age').value = age >= 0 ? age : '';
        });
    }

    /* ID VALIDATION */
    const rules = {
        'Passport': 9,
        'Driver License': 11,
        'National ID': 12
    };

    const idType = document.getElementById('id_type');
    const idNum  = document.getElementById('id_number');

    if (idType && idNum) {
        idNum.addEventListener('input', function () {
            const limit = rules[idType.value];
            if (limit && this.value.length > limit) {
                this.classList.add('is-invalid');
                document.getElementById('idError').innerText =
                    `${idType.value} must be exactly ${limit} characters`;
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
});
</script>

<script src="{{ asset('js/location-autocomplete.js') }}"></script>


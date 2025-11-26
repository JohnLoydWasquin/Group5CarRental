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
                    <input type="text"
                           name="full_name"
                           class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name', $kyc->full_name ?? $user->name) }}"
                           required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Birthdate</label>
                    <input type="date"
                           name="birthdate"
                           class="form-control @error('birthdate') is-invalid @enderror"
                           value="{{ old('birthdate', optional($kyc->birthdate ?? null)->format('Y-m-d')) }}">
                    @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Address --}}
            <h5 class="fw-bold mb-3 mt-4">Address</h5>

            <div class="mb-3">
                <label class="form-label">Address line</label>
                <input type="text"
                       name="address_line"
                       class="form-control @error('address_line') is-invalid @enderror"
                       value="{{ old('address_line', $kyc->address_line ?? $user->address) }}"
                       required>
                @error('address_line')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text"
                           name="city"
                           class="form-control @error('city') is-invalid @enderror"
                           value="{{ old('city', $kyc->city ?? '') }}"
                           required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Province</label>
                    <input type="text"
                           name="province"
                           class="form-control @error('province') is-invalid @enderror"
                           value="{{ old('province', $kyc->province ?? '') }}"
                           required>
                    @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Postal code</label>
                    <input type="text"
                           name="postal_code"
                           class="form-control @error('postal_code') is-invalid @enderror"
                           value="{{ old('postal_code', $kyc->postal_code ?? '') }}">
                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ID details --}}
            <h5 class="fw-bold mb-3 mt-4">ID Details</h5>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">ID type</label>
                    <input type="text"
                           name="id_type"
                           class="form-control @error('id_type') is-invalid @enderror"
                           value="{{ old('id_type', $kyc->id_type ?? '') }}"
                           placeholder="e.g. Driver's License, Passport"
                           required>
                    @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">ID number</label>
                    <input type="text"
                           name="id_number"
                           class="form-control @error('id_number') is-invalid @enderror"
                           value="{{ old('id_number', $kyc->id_number ?? '') }}"
                           required>
                    @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

@extends('layouts.app')
@section('content')

<style>
    .rating-section {
        background: radial-gradient(circle at top left, #16324f 0, #050816 55%, #020617 100%);
        min-height: calc(100vh - 70px);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.97);
        border-radius: 1rem;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.7);
    }
    .review-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .review-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 1rem 2rem rgba(15, 23, 42, 0.25);
    }

    /* ===== FADE IN ANIMATION ===== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .fade-in-up {
        animation: fadeInUp .5s ease forwards;
    }

    /* ===== STAR RATING ANIMATIONS ===== */
    .rating-group {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: center;
    }
    .rating__input {
        position: absolute !important;
        left: -9999px;
    }
    .rating__label {
        font-size: 2rem;
        color: #dde1e7;
        cursor: pointer;
        padding: 0 .1rem;
        transition: transform .2s ease, text-shadow .2s ease, color .2s ease;
    }
    .rating__label:hover,
    .rating__label:hover ~ .rating__label {
        color: #ffc107;
        transform: translateY(-3px) scale(1.05);
        text-shadow: 0 0 8px rgba(255, 193, 7, 0.7);
    }
    .rating__input:checked ~ .rating__label {
        color: #ffc107;
    }

    @keyframes starPop {
        0%   { transform: scale(0.8); }
        60%  { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    .rating__input:checked + .rating__label {
        animation: starPop .25s ease-out;
    }

    .avg-ring {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: radial-gradient(circle, #ffc107 0%, #f97316 55%, transparent 60%);
        opacity: 0.25;
        position: absolute;
        inset: 0;
        margin: auto;
        filter: blur(4px);
    }
</style>

<section class="rating-section py-5 mt-5">
    <div class="container py-3">

        <div class="row mb-4 text-white">
            <div class="col-md-8 fade-in-up">
                <h1 class="fw-bold mb-2">Rate Your Ride Experience</h1>
                <p class="text-light mb-0">
                    Your feedback helps us improve Autopiloto Car Rentals and provide better service on your next trip.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 fade-in-up">
                <div class="card border-0 shadow-sm d-inline-block glass-card position-relative overflow-hidden">
                    <div class="card-body text-center px-4 py-3">
                        @php
                            $avg = round($averageRating, 1);
                            $filled = floor($averageRating);
                        @endphp

                        <div class="position-absolute avg-ring"></div>

                        <h6 class="text-muted mb-1 position-relative">Average Rating</h6>
                        <div class="mb-1 position-relative">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $filled)
                                    <span class="text-warning fs-5">&#9733;</span>
                                @else
                                    <span class="text-secondary fs-5">&#9733;</span>
                                @endif
                            @endfor
                        </div>
                        <div class="fw-bold fs-4 position-relative">{{ number_format($avg, 1) }}/5</div>
                        <div class="text-muted small position-relative">
                            {{ $totalReviews }} review{{ $totalReviews === 1 ? '' : 's' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5 mb-4 fade-in-up">
                <div class="card shadow-sm border-0 glass-card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            {{ $userReview ? 'Update Your Review' : 'Leave a Review' }}
                        </h5>

                        @if(session('success'))
                            <div class="alert alert-success small mb-2">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('rateus.store') }}" method="POST">
                            @csrf

                            <div class="mb-3 text-center">
                                <label class="form-label d-block mb-2">Your Rating</label>

                                @php $currentRating = $userReview->rating ?? null; @endphp

                                <div class="rating-group mb-2" id="ratingGroup">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input class="rating__input"
                                               name="rating"
                                               id="rating-{{ $i }}"
                                               value="{{ $i }}"
                                               type="radio"
                                               {{ $currentRating == $i ? 'checked' : '' }}>
                                        <label aria-label="{{ $i }} star" class="rating__label" for="rating-{{ $i }}">
                                            <span class="rating__icon rating__icon--star">&#9733;</span>
                                        </label>
                                    @endfor
                                </div>

                                <div id="ratingHint" class="small text-muted">
                                    @if($currentRating)
                                        You previously rated <strong>{{ $currentRating }}</strong> ★
                                    @else
                                        Tap a star to rate your ride.
                                    @endif
                                </div>
                            </div>

                            {{-- Comment --}}
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comments (optional)</label>
                                <textarea name="comment" id="comment" rows="4"
                                          class="form-control"
                                          placeholder="Tell us what you liked or what we can improve...">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                            </div>

                            {{-- Info --}}
                            @if($userReview)
                                <p class="text-muted small mb-2">
                                    <strong>Status:</strong> {{ ucfirst($userReview->status) }}<br>
                                    <span>Last updated {{ $userReview->updated_at->format('M d, Y H:i') }}</span>
                                </p>
                            @endif

                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                {{ $userReview ? 'Update Review' : 'Submit Review' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 fade-in-up">
                <h5 class="mb-3 text-white">What our customers say</h5>

                @if($reviews->isEmpty())
                    <div class="alert alert-light border small glass-card">
                        No reviews yet. Be the first to rate your experience!
                    </div>
                @else
                    @foreach($reviews as $review)
                        <div class="card border-0 shadow-sm mb-3 review-card glass-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <div class="text-warning small">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    &#9733;
                                                @else
                                                    <span class="text-secondary">&#9733;</span>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        {{ $review->created_at->format('M d, Y') }}
                                    </div>
                                </div>

                                @if($review->comment)
                                    <p class="mt-2 mb-0 text-muted">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3 text-white-50">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.rating__input');
        const hint = document.getElementById('ratingHint');
        if (!radios || !hint) return;

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                const val = this.value;
                hint.innerHTML = 'You selected <strong>' + val + '</strong> ★';
            });
        });
    });
</script>

@endsection

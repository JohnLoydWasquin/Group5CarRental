@extends('layouts.app')

@section('content')
<section class="hero hero-center" data-aos="fade-left">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <span class="badge bg-yellow text-navy fw-bold mb-3">2025 Top Rental Partners</span>
                <h1 class="fw-bold display-5 mb-3 text-navy">Welcome to Autopiloto Car Rentals</h1>
                <p class="lead mb-4 text-secondary">
                    Experience seamless car rental services across the beautiful landscapes of Luzon, tailored to meet your travel needs with utmost convenience.
                </p>
                <div class="d-flex gap-3" data-aos="fade-up">
                    <a href="#" class="btn btn-yellow fw-bold px-4 py-2">Browse Vehicles</a>
                    <a href="#" class="btn btn-outline-navy fw-bold px-4 py-2">Call Us</a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-5">
                    <div class="text-center">
                        <i class="bi bi-shield-lock fs-3 text-yellow"></i>
                        <p class="mb-0 fw-bold text-navy">Secure Booking</p>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-check2-circle fs-3 text-yellow"></i>
                        <p class="mb-0 fw-bold text-navy">Instant Confirmation</p>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-calendar-range fs-3 text-yellow"></i>
                        <p class="mb-0 fw-bold text-navy">Flexible Scheduling</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <img src="{{ asset('images/ford-mustang-gt.jpg') }}" class="card-img-top" alt="Ford Mustang GT">
                    <div class="card-img-overlay d-flex flex-column justify-content-end bg-gradient">
                        <h5 class="fw-bold text-white text-end">Ford Mustang GT</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="why-choose bg-black text-white py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-3" data-aos="fade-down">Your Trusted Car Rental Partner</h1>
            <p class="fw-bold text-secondary mb-0" data-aos="fade-up">Discover the reasons that set us apart.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 col-sm-6" data-aos="zoom-in">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 service-card">
                    <img src="{{ asset('images/Service1.webp') }}" class="card-img-top" alt="Insurance">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-navy">Comprehensive Insurance Coverage</h5>
                        <p class="text-secondary">
                            Enjoy peace of mind with insurance options included in every rental. Drive with confidence wherever your journey takes you.
                        </p>
                        <a href="{{ route('about') }}" class="btn btn-yellow fw-bold px-3">Read More</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="150">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 service-card">
                    <img src="{{ asset('images/Service2.webp') }}" class="card-img-top" alt="Flexible Rental Options">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-navy">Flexible Rental Options</h5>
                        <p class="text-secondary">
                            Whether you need a car for a day, a week, or even longer, our flexible rental terms cater to your specific needs. We adapt to your schedule, providing you with the freedom to explore without constraints.
                        </p>
                        <a href="{{ route('about') }}" class="btn btn-yellow fw-bold px-3">Read More</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 service-card">
                    <img src="{{ asset('images/Service3.webp') }}" class="card-img-top" alt="Convenient Booking Process">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-navy">Convenient Booking Process</h5>
                        <p class="text-secondary">
                            Our user-friendly booking system allows you to reserve your vehicle in just a few clicks. With transparent pricing and no hidden fees, we ensure a smooth and straightforward experience from start to finish.
                        </p>
                        <a href="{{ route('about') }}" class="btn btn-yellow fw-bold px-3">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="vehicles" class="bg-dark text-center py-5 mb-0">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold display-5 text-white">Our Fleet</h2>
      <p class="text-secondary fs-5">
        Choose from our wide selection of well-maintained vehicles for every occasion.
      </p>
    </div>

    <div class="row g-4">
      @php
        $vehicles = [
          [
            'name' => 'Ford Mustang GT',
            'category' => 'Sports Car',
            'image' => 'images/Ford Mustang GT.jpg',
            'price' => '₱5,500',
            'passengers' => 4,
            'transmission' => 'Automatic',
            'fuel' => 'Gasoline',
            'available' => true,
          ],
          [
            'name' => 'Toyota Fortuner',
            'category' => 'SUV',
            'image' => 'images/Toyota Fortuner.jpg',
            'price' => '₱3,800',
            'passengers' => 7,
            'transmission' => 'Automatic',
            'fuel' => 'Diesel',
            'available' => true,
          ],
          [
            'name' => 'Honda Civic',
            'category' => 'Sedan',
            'image' => 'images/Ford Mustang GT.jpg',
            'price' => '₱2,500',
            'passengers' => 5,
            'transmission' => 'Automatic',
            'fuel' => 'Gasoline',
            'available' => true,
          ],
          [
            'name' => 'Mitsubishi Xpander',
            'category' => 'MPV',
            'image' => 'images/Mitsubishi Xpander.jpg',
            'price' => '₱2,800',
            'passengers' => 7,
            'transmission' => 'Automatic',
            'fuel' => 'Gasoline',
            'available' => false,
          ],
        ];
      @endphp

      @foreach ($vehicles as $vehicle)
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 shadow-sm border-0 bg-black text-light">
            <div class="position-relative">
              <img src="{{ asset($vehicle['image']) }}" class="card-img-top" alt="{{ $vehicle['name'] }}">
              @if ($vehicle['available'])
                <span class="badge bg-success position-absolute top-0 end-0 m-2">Available</span>
              @else
                <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Booked</span>
              @endif
            </div>

            <div class="card-body text-light">
              <p class="text-muted small mb-1">{{ $vehicle['category'] }}</p>
              <h5 class="fw-bold mb-3">{{ $vehicle['name'] }}</h5>

              <div class="d-flex justify-content-between text-muted small mb-3">
                <div><i class="bi bi-people"></i> {{ $vehicle['passengers'] }}</div>
                <div><i class="bi bi-gear"></i> {{ $vehicle['transmission'] }}</div>
                <div><i class="bi bi-fuel-pump"></i> {{ $vehicle['fuel'] }}</div>
              </div>

              <div class="d-flex align-items-baseline gap-1">
                <h4 class="text-warning fw-bold mb-0">{{ $vehicle['price'] }}</h4>
                <span class="text-muted">/day</span>
              </div>
            </div>

            <div class="card-footer bg-transparent border-0">
              @if ($vehicle['available'])
                <a href="{{ route('vehicles', ['vehicle' => urlencode($vehicle['name'])]) }}" class="btn btn-primary w-100 fw-bold">
                  Book Now
                </a>
              @else
                <button class="btn btn-secondary w-100 fw-bold" disabled>Currently Unavailable</button>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="text-center mt-5">
      <a href="{{ url('/vehicles') }}" class="btn btn-view-all">View All Vehicles</a>
    </div>
  </div>
</section>
@endsection

@extends('layouts.app')

@section('content')
<style>
#vehicles {
  background: radial-gradient(circle at top left, #1f2933 0, #020617 55%, #020617 100%);
}

.service-card {
  position: relative;
  border-radius: 1.5rem;
  padding: 2rem 2rem 2.25rem;
  color: #fff;
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.06);
  backdrop-filter: blur(10px);
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.service-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
  border-color: rgba(255, 255, 255, 0.18);
}

.service-icon-badge {
  width: 52px;
  height: 52px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(6px);
}

.service-icon-badge i {
  font-size: 1.4rem;
  color: #ffffff;
}

.service-card--blue {
  background: linear-gradient(135deg, #22d3ee, #1d4ed8);
}

.service-card--pink {
  background: linear-gradient(135deg, #ec4899, #7c3aed);
}

.service-card--green {
  background: linear-gradient(135deg, #22c55e, #0f766e);
}

.service-card--purple {
  background: linear-gradient(135deg, #6366f1, #0f172a);
}
</style>
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
                    <a href="{{ route('vehicles') }}" class="btn btn-yellow fw-bold px-4 py-2">Browse Vehicles</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-navy fw-bold px-4 py-2">Call Us</a>
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
<section id="vehicles" class="bg-dark py-5 mb-0">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold display-5 text-white">Our Services</h2>
      <p class="text-secondary fs-5 mb-0">
        Choose from our wide range of professional transportation solutions tailored to your needs.
      </p>
    </div>

    @php
      $services = [
        [
          'title'       => 'Self-Drive Car Rental',
          'tag'         => 'Self-Drive',
          'icon'        => 'bi-car-front',
          'description' => 'Rent a car and drive on your own. Ideal for personal trips, errands, and business travel.',
          'class'       => 'service-card--blue',
        ],
        [
          'title'       => 'Driver Service',
          'tag'         => 'With Driver',
          'icon'        => 'bi-person-badge',
          'description' => 'Enjoy a professional, courteous driver for a safer and more comfortable ride.',
          'class'       => 'service-card--pink',
        ],
        [
          'title'       => 'Airport Pick-up & Drop-off',
          'tag'         => 'Airport Service',
          'icon'        => 'bi-geo-alt',
          'description' => 'On-time airport transfers so you never miss a flight and always arrive stress-free.',
          'class'       => 'service-card--green',
        ],
        [
          'title'       => 'Long-Term Leasing',
          'tag'         => 'Leasing',
          'icon'        => 'bi-calendar-event',
          'description' => 'Flexible weekly or monthly options for individuals and companies needing vehicles longer term.',
          'class'       => 'service-card--purple',
        ],
      ];
    @endphp

    <div class="row g-4">
      @foreach ($services as $service)
        <div class="col-md-6">
          <div class="service-card {{ $service['class'] }}">
            <div class="d-flex justify-content-between align-items-start mb-4">
              <span class="badge rounded-pill bg-light bg-opacity-10 text-uppercase small fw-semibold px-3 py-2">
                {{ $service['tag'] }}
              </span>
              <div class="service-icon-badge">
                <i class="bi {{ $service['icon'] }}"></i>
              </div>
            </div>

            <h3 class="h4 fw-bold mb-2 text-white">{{ $service['title'] }}</h3>
            <p class="mb-4 text-white-50">
              {{ $service['description'] }}
            </p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection

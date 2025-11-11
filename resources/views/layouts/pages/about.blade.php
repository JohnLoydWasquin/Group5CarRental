<head>
    <title>AutoPiloto | About Us</title>
    <link href="{{ asset('css/about.css') }}" rel="stylesheet">
</head>

@extends('layouts.app')

@section('content')
<section class="hero-section text-white d-flex align-items-center justify-content-center position-relative" data-aos="fade">
    <div class="overlay position-absolute w-100 h-100"></div>
    <div class="container text-center position-relative">
        <h1 class="display-4 fw-bold" data-aos="fade-up" data-aos-delay="100">Your Journey, Our Passion</h1>
        <p class="lead mt-3" data-aos="fade-up" data-aos-delay="200">
            Discover the freedom of the open road with Autopiloto Car Rentals.
            Wherever you go in Luzon, we make every trip smooth, easy, and memorable.
        </p>
        <a href="https://www.facebook.com/AutoPILOTOCarRentals" target="_blank" class="btn btn-yellow fw-bold px-4 py-2" data-aos="fade-up" data-aos-delay="300">
            Contact Us
        </a>
    </div>
</section>

<section class="py-5 bg-light">
  <div class="container text-center mb-5" data-aos="fade-up">
    <h2 class="fw-bold">Our Commitment to Excellence</h2>
    <p class="text-muted fs-5">Driving your journey with integrity and professionalism</p>
  </div>

  <div class="container">
    <div class="row gy-4">
      <div class="col-md-12" data-aos="fade-right">
        <div class="d-md-flex align-items-center bg-white shadow-sm rounded-3 p-3 service-box">
          <div class="me-md-4 mb-3 mb-md-0">
            <img src="{{ asset('images/AutoPilotoCustomer-approach.webp') }}" class="img-fluid rounded-3" style="width: 600px; height: 150px; object-fit: cover;" alt="Customer-Centric Approach">
          </div>
          <div>
            <h5 class="fw-bold">Customer-Centric Approach</h5>
            <p class="text-muted mb-3">
              At Autopiloto Car Rentals, our customers are at the heart of everything we do. We strive to understand their unique needs and preferences to provide tailored solutions. Our dedicated team is always ready to assist, ensuring that your car rental experience is smooth and enjoyable from start to finish.
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-12" data-aos="fade-left">
        <div class="d-md-flex align-items-center bg-white shadow-sm rounded-3 p-3 service-box">
          <div class="me-md-4 mb-3 mb-md-0">
            <img src="{{ asset('images/AutoPilotoQualityAndRellability.webp') }}" class="img-fluid rounded-3" style="width: 600px; height: 150px; object-fit: cover;" alt="Quality and Reliability">
          </div>
          <div>
            <h5 class="fw-bold">Quality and Reliability</h5>
            <p class="text-muted mb-3">
              We pride ourselves on offering a fleet of well-maintained, high-quality vehicles that cater to various travel requirements. Each car undergoes rigorous inspections to guarantee safety and reliability. Our commitment to quality ensures that you can focus on your journey with peace of mind.
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-12" data-aos="fade-right">
        <div class="d-md-flex align-items-center bg-white shadow-sm rounded-3 p-3 service-box">
          <div class="me-md-4 mb-3 mb-md-0">
            <img src="{{ asset('images/AutoPilotoPractices.webp') }}" class="img-fluid rounded-3" style="width: 700px; height: 150px; object-fit: cover;" alt="Sustainability Practices">
          </div>
          <div>
            <h5 class="fw-bold">Sustainability Practices</h5>
            <p class="text-muted mb-3">
              We are dedicated to promoting sustainable travel options. Autopiloto Car Rentals actively seeks to reduce our environmental footprint by incorporating fuel-efficient vehicles into our fleet and implementing eco-friendly practices in our operations. We believe in responsible tourism that benefits both our customers and the planet.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5" style="background-color: grey;">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5">
      <h2 class="fw-bold display-6 text-dark">Our Journey So Far</h2>
      <p class="text-muted fs-5">A timeline of growth and dedication</p>
    </div>

    <div class="timeline">
      <div class="timeline-item left" data-aos="fade-right">
        <div class="timeline-content bg-white shadow-sm rounded-4 p-4">
          <h5 class="fw-bold text-dark">Foundation</h5>
          <p class="text-muted mb-0">
            Autopilot Car Rentals was established in 2010, with a vision to provide exceptional car rental services across
            the beautiful islands of the Philippines. Our founders recognized a gap in the market for reliable and
            customer-focused rental options — and thus, the journey began.
          </p>
        </div>
      </div>

      <div class="timeline-item right" data-aos="fade-left">
        <div class="timeline-content bg-white shadow-sm rounded-4 p-4">
          <h5 class="fw-bold text-dark">Expansion</h5>
          <p class="text-muted mb-0">
            In 2015, we expanded our fleet and service locations, allowing us to cater to a broader audience.
            This growth enabled us to introduce a wide variety of vehicles, from compact cars to luxury sedans, ensuring
            we meet the diverse needs of our clients.
          </p>
        </div>
      </div>

      <div class="timeline-item left" data-aos="fade-right">
        <div class="timeline-content bg-white shadow-sm rounded-4 p-4">
          <h5 class="fw-bold text-dark">Recognition</h5>
          <p class="text-muted mb-0">
            By 2020, Autopilot Car Rentals gained recognition as one of the leading car rental services in the Philippines.
            Our commitment to quality service and customer satisfaction earned us numerous accolades and positive reviews,
            solidifying our reputation in the industry.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #f1f5f9;">
  <div class="container" data-aos="fade-up">
    <div class="text-center mb-5">
      <h2 class="fw-bold display-6 text-white">Meet Our Dedicated Team</h2>
      <p class="text-secondary fs-5">Passionate professionals committed to your journey</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-right">
        <div class="card border-0 shadow-lg bg-dark text-center text-light h-100 rounded-4 overflow-hidden">
          <img src="{{ asset('images/AutoPilotoCEO.webp') }}" class="card-img-top" alt="John Doe" style="height: 300px; object-fit: cover; filter: brightness(0.9);">
          <div class="card-body">
            <h5 class="fw-bold text-warning">John Doe - CEO</h5>
            <p class="text-light opacity-75">
              With over 15 years in the automotive industry, John leads with a focus on innovation and customer satisfaction, 
              making car rentals accessible and enjoyable for everyone.
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up">
        <div class="card border-0 shadow-lg bg-dark text-center text-light h-100 rounded-4 overflow-hidden">
          <img src="{{ asset('images/AutoPilotoOperationsManager.webp') }}" class="card-img-top" alt="Jane Smith" style="height: 300px; object-fit: cover; filter: brightness(0.9);">
          <div class="card-body">
            <h5 class="fw-bold text-warning">Jane Smith - Operations Manager</h5>
            <p class="text-light opacity-75">
              Jane ensures smooth operations with precision and care. Her dedication to excellence maintains our top-tier standards of service.
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-left">
        <div class="card border-0 shadow-lg bg-dark text-center text-light h-100 rounded-4 overflow-hidden">
          <img src="{{ asset('images/AutoPilotoCSL.webp') }}" class="card-img-top" alt="Mark Johnson" style="height: 300px; object-fit: cover; filter: brightness(0.9);">
          <div class="card-body">
            <h5 class="fw-bold text-warning">Mark Johnson - Customer Service Lead</h5>
            <p class="text-light opacity-75">
              Mark is the friendly face behind our service team, ensuring every client gets full support from booking to vehicle return.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

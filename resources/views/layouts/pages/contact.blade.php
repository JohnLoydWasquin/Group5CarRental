<head>
    <title>AutoPiloto | Contact Us</title>
</head>

@extends('layouts.app')

@section('content')

<section class="contact-hero">
  <div class="overlay"></div>

  <div class="contact-container-wrapper" data-aos="fade-up">
  <div class="contact-container-wrapper" >
    <h1 class="display-4 fw-bold mb-3">Get in Touch</h1>
    <p class="lead mb-4">
      Have questions about our rental services? Our friendly team is always ready to assist you with your travel needs. Reach out today, and we'll get back to you as soon as possible to help you hit the road with confidence.
    </p>
  </div>
</section>
<section class="contact-section">
    <div class="contact-container-wrapper">
        <div class="contact-container">
            <div class="contact-form">
                <h2>Send us a Message</h2>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form action="{{ route('contact_send') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" placeholder="Juan Dela Cruz">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="juan@example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="09XXXXXXXXX">
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject">
                            <option value="">Select a subject</option>
                            <option value="rental">Rental Inquiry</option>
                            <option value="support">Customer Support</option>
                            <option value="feedback">Feedback</option>
                            <option value="partnership">Partnership</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea rows="5" name="message" placeholder="Tell us how we can help..."></textarea>
                    </div>

                    <button type="submit">Send Message</button>
                </form>
            </div>
            <div class="contact-info">
                <h2>Contact Information</h2>

                <div class="contact-card">
                <div class="icon">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <h3>Phone</h3>
                    <p>09941206710</p>
                    <p>0917 120 2***</p>
                </div>
            </div>

            <div class="contact-card">
                <div class="icon">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div>
                    <h3>Email</h3>
                    <p>johnloydwasquin27@gmail.com</p>
                    <p>autopilotocarrentals@gmail.com</p>
                </div>
            </div>

            <div class="contact-card">
                <div class="icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                    <h3>Address</h3>
                    <p>Lot 5 Commercial Area Bloomfields Subdivision Tambo, Lipa City, Batangas</p>
                </div>
            </div>

            <div class="contact-card">
                <div class="icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <h3>Business Hours</h3>
                    <p>Mon - Sat: 8:00 AM - 11:00 PM</p>
                    <p>Sun: 8:00 AM - 5:00 PM</p>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>
<section class="map-section">
  <div class="container">
    <h2 class="text-center mb-4">Visit Our Location</h2>
    <p class="text-center mb-4">Find us on the map and plan your visit</p>
    <div class="map-container">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15489.28277731098!2d121.14001919999998!3d13.939500949999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd6d1db7701c51%3A0xc6eb4a17f4006f43!2sAuto%20Piloto%20Car%20Rentals!5e0!3m2!1sen!2sph!4v1761245562954!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</section>
@endsection

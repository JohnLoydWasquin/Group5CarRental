<nav class="navbar navbar-expand-lg navbar-dark bg-navy py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/AutopilotoLogo.png') }}" alt="Logo" height="40" class="me-2">
            <div class="d-flex flex-column lh-1">
                <span class="fw-bold text-yellow">RideNow</span>
                <small class="text-white-50">Autopiloto Car Rentals</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Browse Vehicles</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                <li class="nav-item"><a class="nav-link text-white-50" href="#">Login</a></li>
                <li class="nav-item ms-2">
                    <a href="#" class="btn btn-yellow fw-bold px-3">Book Now</a>
                </li>
            </ul>
        </div>
    </div>  
</nav>
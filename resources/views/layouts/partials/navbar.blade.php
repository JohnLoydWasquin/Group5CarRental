<nav class="navbar navbar-expand-lg navbar-dark bg-navy py-3 shadow-sm fixed-top">
    <div class="container">
        {{-- Autopiloto Logo --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/AutopilotoLogo.png') }}" alt="Logo" height="40" class="me-2">
            <div class="d-flex flex-column lh-1">
                <span class="fw-bold text-yellow">RideNow</span>
                <small class="text-white-50">Autopiloto Car Rentals</small>
            </div>
        </a>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navbar Links --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('vehicles' ? 'active' : '') }}" href="{{ route('vehicles') }}">Vehicles</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : ''}}" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            {{-- User Profile --}}
            <ul class="navbar-nav align-items-center">
                @guest
                    {{-- If the user is are not Login --}}
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="#" class="btn btn-yellow fw-bold px-3">Rent Now</a>
                    </li>
                @endguest

                @auth
                    {{-- If the user already Login --}}
                    <li class="nav-item ms-2">
                        <a href="#" class="btn btn-yellow fw-bold px-3">Rent Now</a>
                    </li>

                    {{-- Then if the user are already login the circle profile will appear --}}
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center p-0"
                           href="#" id="profileDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(Auth::user()->profile_image)
                                {{-- User profile image --}}
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}"
                                     alt="Profile"
                                     class="rounded-circle border border-warning"
                                     width="40" height="40"
                                     style="object-fit: cover;">
                            @else
                                @php
                                    $nameParts = explode(' ', Auth::user()->name);
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr(end($nameParts), 0, 1) : ''));
                                @endphp
                                <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center border border-warning"
                                     style="width: 40px; height: 40px;">
                                    {{ $initials }}
                                </div>
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2" aria-labelledby="profileDropdown">
                            <li class="px-3 py-2">
                                <p class="fw-semibold mb-0">{{ Auth::user()->name }}</p>
                                <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}">View Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('userBooking') }}">My Bookings</a></li>
                            <li><a class="dropdown-item" href="{{ route('settings') }}">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locations-search-url" content="{{ route('locations.search') }}">
    <title>RideNow | AutoPiloto Car Rentals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/favicon.jpg') }}">
</head>
<body>

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
    AOS.init({
        duration: 1000,
        once: true,
        easing: 'ease-in-out'
    });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const _fetch = window.fetch;
        window.fetch = function (url, options = {}) {
            options.headers = Object.assign({
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            }, options.headers || {});
            return _fetch(url, options);
        };
    });
    </script>

    <script src="{{ asset('js/vehicles.js') }}"></script>
</body>
</html>

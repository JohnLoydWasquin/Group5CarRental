<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AutoPilot Car Rentals</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@100..900&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <div class="flex min-h-screen">

        <aside class="w-64 bg-[#020617] text-white flex flex-col fixed h-screen">
            <div class="flex items-center gap-3 p-6 border-b border-gray-700">
                <img src="{{ asset('images/AutopilotoLogo.png') }}" alt="Logo" class="w-10 h-10 rounded-md bg-blue-600 p-1">
                <div>
                    <h1 class="text-xl font-bold tracking-wide">AUTO PILOTO</h1>
                    <p class="text-sm text-gray-400">Car Rentals</p>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2 text-gray-200">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-800">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.customers') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-800">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Customers</span>
                </a>
                <a href="{{ route('admin.vehicles.index') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-800">
                    <i data-lucide="car" class="w-5 h-5"></i>
                    <span>Car Inventory</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-800">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span>Bookings</span>
                </a>
                <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-800">
                    <i data-lucide="user-cog" class="w-5 h-5"></i>
                    <span>Staff Management</span>
                </a>
                <a href="{{ route('chat.index', ['userId' => auth()->user()->id]) }}" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-gray-800">
                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                    <span>Chat</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded flex items-center justify-center gap-2">
                        <i data-lucide="log-out" class="w-5 h-5"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col ml-64">
            <header class="flex justify-between items-center bg-white p-4 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                <div class="flex items-center gap-6">
                    <button class="relative">
                        <i data-lucide="bell" class="w-6 h-6 text-gray-600 hover:text-gray-800"></i>
                        <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                    </button>

                    <button>
                        <i data-lucide="settings" class="w-6 h-6 text-gray-600 hover:text-gray-800"></i>
                    </button>

                    <div class="h-6 border-l border-gray-300"></div>

                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 flex items-center justify-center bg-blue-600 text-white font-semibold rounded-full">
                            AD
                        </div>
                        <span class="text-gray-800 font-medium">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

@extends('layouts.orgStaff.staff')
@vite('resources/js/admin/dashboard.js')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500">Total Customers</p>
        <h2 class="text-3xl font-bold">{{ $totalCustomers }}</h2>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500">Available Cars</p>
        <h2 class="text-3xl font-bold">{{ $availableVehicles }}</h2>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500">Active Bookings</p>
        <h2 class="text-3xl font-bold">{{ $activeBookings }}</h2>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-gray-500">Monthly Revenue</p>
        <h2 class="text-3xl font-bold">₱{{ number_format($monthlyRevenue, 2) }}</h2>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
    <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-2">Revenue Trends</h2>
        <canvas id="revenueChart" width="400" height="200"></canvas>
    </div>

    <div class="bg-white p-4 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-2">Booking Status</h2>
        <canvas id="statusChart"></canvas>
    </div>
</div>
<div 
    id="dashboardData"
    data-revenue='@json($revenueTrends)'
    data-status='@json($bookingStatus)'>
</div>
@endsection

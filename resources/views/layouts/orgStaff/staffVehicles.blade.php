@php
/** @var \Illuminate\Pagination\LengthAwarePaginator $vehicles */
@endphp

@extends('layouts.orgStaff.staff')
@vite('resources/js/admin/adminVehicle.js')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Car Inventory</h1>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input 
            type="text" 
            id="searchVehicle" 
            placeholder="Search vehicles..." 
            class="border rounded-lg px-3 py-2 w-full md:w-64 focus:outline-none focus:ring focus:ring-yellow-400 transition"
        >
    </div>

    <!-- Vehicle Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Plate No</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Brand & Model</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Availability</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Price/Day</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Condition</th>
                </tr>
            </thead>

            <tbody id="vehicleTableBody" class="divide-y divide-gray-100">
                @foreach($vehicles as $vehicle)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $vehicle->PlateNo }}</td>

                    <td class="px-4 py-3">
                        <span>{{ $vehicle->Brand }}</span>
                        <span>{{ $vehicle->Model }}</span>
                    </td>

                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-sm font-medium 
                            {{ $vehicle->Availability ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $vehicle->Availability ? 'Available' : 'Rented' }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        ₱{{ number_format($vehicle->DailyPrice, 2) }}
                    </td>

                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-sm font-medium 
                            {{ $vehicle->Condition === 'Good' ? 'bg-green-100 text-green-800' : 
                               ($vehicle->Condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800') }}">
                            {{ $vehicle->Condition ?? 'Unknown' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection

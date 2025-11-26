@php
/** @var \Illuminate\Pagination\LengthAwarePaginator $vehicles */
@endphp

@extends('layouts.authorities.admin')
@vite('resources/js/admin/adminVehicle.js')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Car Inventory</h1>

        <!-- Add Vehicle Button -->
        <button id="addVehicleBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fa-solid fa-plus"></i> Add Vehicle
        </button>
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
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Passengers</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Price/Day</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Condition</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody id="vehicleTableBody" class="divide-y divide-gray-100">
                @foreach($vehicles as $vehicle)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $vehicle->PlateNo }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <span>{{ $vehicle->Brand }}</span>
                        <span>{{ $vehicle->Model }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-sm font-medium
                            @if($vehicle->Condition === 'Maintenance')
                                bg-gray-300 text-gray-800
                            @elseif(!$vehicle->Availability)
                                bg-red-100 text-red-800
                            @else
                                bg-green-100 text-green-800
                            @endif
                        ">
                            @if($vehicle->Condition === 'Maintenance')
                                Unavailable
                            @elseif(!$vehicle->Availability)
                                Rented
                            @else
                                Available
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        {{ $vehicle->Passengers ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3">₱{{ number_format($vehicle->DailyPrice, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-sm font-medium 
                            {{ $vehicle->Condition === 'Good' ? 'bg-green-100 text-green-800' : 
                               ($vehicle->Condition === 'Fair' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-red-100 text-red-800') }}">
                            {{ $vehicle->Condition ?? 'Unknown' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                        <button 
                            type="button" 
                            class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110 editBtn cursor-pointer"
                            data-id="{{ $vehicle->VehicleID }}"
                            data-plate="{{ $vehicle->PlateNo }}"
                            data-brand="{{ $vehicle->Brand }}"
                            data-model="{{ $vehicle->Model }}"
                            data-price="{{ $vehicle->DailyPrice }}"
                            data-availability="{{ $vehicle->Availability }}"
                            data-condition="{{ $vehicle->Condition }}"
                            title="Edit Vehicle"
                        >
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>

                        <form action="{{ route('admin.vehicles.destroy', $vehicle->VehicleID) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 transition transform hover:scale-110 cursor-pointer" title="Delete Vehicle">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
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

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl w-96 p-6 relative shadow-lg animate-slide-down">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        <h2 class="text-2xl font-bold mb-5 text-gray-900">Edit Vehicle</h2>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="text" name="PlateNo" id="editPlateNo" placeholder="Plate No" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
            <input type="text" name="Brand" id="editBrand" placeholder="Brand" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
            <input type="text" name="Model" id="editModel" placeholder="Model" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
            <input type="number" name="DailyPrice" id="editDailyPrice" placeholder="Price/Day" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
            
            <select name="Availability" id="editAvailability" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
                <option value="1">Available</option>
                <option value="0">Rented</option>
            </select>

            <input type="number" name="Passengers" min="1" placeholder="Passengers" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400" value="{{ old('Passengers', 4) }}">

            <select name="Condition" id="editCondition" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
                <option value="Excellent">Excellent</option>
                <option value="Fair">Fair</option>
                <option value="Maintenance">Maintenance</option>
            </select>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Update Vehicle
            </button>
        </form>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div id="addVehicleModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex justify-center items-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl w-96 p-6 relative shadow-lg animate-slide-down">
        <button id="closeAddModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        <h2 class="text-2xl font-bold mb-5 text-gray-900">Add New Vehicle</h2>

        <form id="addVehicleForm" method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Validation Errors --}}
            @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-2">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <input type="text" name="PlateNo" placeholder="Plate No" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400" value="{{ old('PlateNo') }}">
            <input type="text" name="Brand" placeholder="Brand" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400" value="{{ old('Brand') }}">
            <input type="text" name="Model" placeholder="Model" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400" value="{{ old('Model') }}">
            <input type="number" name="DailyPrice" placeholder="Price/Day" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400" value="{{ old('DailyPrice') }}">

            <input type="number" name="Passengers" min="1" placeholder="Passengers" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400" value="{{ old('Passengers', 4) }}">

            <select name="Condition" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-yellow-400">
                <option value="Excellent" {{ old('Condition') == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                <option value="Fair" {{ old('Condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                <option value="Poor" {{ old('Condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
            </select>

            <!-- Vehicle Image Upload -->
            <div>
                <label class="block text-sm font-medium mb-1">Vehicle Image</label>
                <input type="file" name="Image" id="addImage" accept="image/*" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-yellow-400">
                <img id="addImagePreview" src="{{ $vehicle->Image ? asset('storage/' . $vehicle->Image) : 'default-car.png' }}" alt="Image Preview" class="hidden mt-2 w-32 h-20 object-cover rounded">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Add Vehicle
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const addImageInput = document.getElementById('addImage');
    const addImagePreview = document.getElementById('addImagePreview');

    addImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            addImagePreview.src = URL.createObjectURL(file);
            addImagePreview.classList.remove('hidden');
        } else {
            addImagePreview.src = '#';
            addImagePreview.classList.add('hidden');
        }
    });
</script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const conditionSelect    = document.getElementById('editCondition');
    const availabilitySelect = document.getElementById('editAvailability');

    if (!conditionSelect || !availabilitySelect) return;

    function syncAvailabilityWithCondition() {
        if (conditionSelect.value === 'Maintenance') {
            availabilitySelect.value = '0';
            availabilitySelect.disabled = true;
        } else {
            availabilitySelect.disabled = false;

            if (conditionSelect.value === 'Excellent') {
                availabilitySelect.value = '1';
            }
        }
    }

    conditionSelect.addEventListener('change', syncAvailabilityWithCondition);

    // Optional: if the modal opens with Maintenance already selected and you want it disabled immediately:
    // syncAvailabilityWithCondition();

    window.syncAvailabilityWithCondition = syncAvailabilityWithCondition;
});
</script>
@endpush
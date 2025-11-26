@extends('layouts.orgStaff.staff')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('staff.customers') }}" class="p-2 hover:bg-gray-200 rounded-lg transition">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Customer Profile</h1>
            <p class="text-gray-600 mt-1">View and manage customer information</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden flex items-center justify-center">
                            @if($customer->profile_image)
                                <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="{{ $customer->name }}" class="w-full h-full object-cover">
                            @else
                                @php
                                    $nameParts = explode(' ', $customer->name);
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr(end($nameParts), 0, 1) : ''));
                                @endphp
                                <div class="w-full h-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">{{ $initials }}</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-white">{{ $customer->name ?? 'N/A' }}</h2>
                            <p class="text-blue-100 text-sm">Customer ID: {{ $customer->id ?? 'N/A' }}</p>
                        </div>
                        <span class="px-4 py-2 rounded-full font-medium text-sm {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($customer->status ?? 'inactive') }}
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Email Address</p>
                                <p class="text-gray-900 font-semibold mt-1">{{ $customer->email ?? 'N/A' }}</p>
                                @if($customer->email)
                                    <a href="mailto:{{ $customer->email }}" class="text-blue-600 text-xs hover:underline mt-1">Send Email</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Phone Number</p>
                                <p class="text-gray-900 font-semibold mt-1">{{ $customer->phone ?? 'N/A' }}</p>
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}" class="text-green-600 text-xs hover:underline mt-1">Call Customer</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Member Since</p>
                                <p class="text-gray-900 font-semibold mt-1">{{ $customer->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                                <p class="text-gray-500 text-xs mt-1">{{ $customer->created_at?->diffForHumans() ?? '' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Total Bookings</p>
                                <p class="text-gray-900 font-semibold mt-1">{{ $customer->bookings?->count() ?? 0 }}</p>
                                <p class="text-gray-500 text-xs mt-1">Active rentals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($customer->bookings?->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Booking History</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $customer->bookings->count() }} total bookings</p>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @foreach($customer->bookings as $booking)
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $booking->car->model ?? 'Car' }} {{ $booking->car->brand ?? '' }}</h4>
                                <p class="text-sm text-gray-600">Booking ID: #{{ $booking->id ?? 'N/A' }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ 
                                $booking->status === 'approved' ? 'bg-green-100 text-green-800' :
                                ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-red-100 text-red-800')
                            }}">
                                {{ ucfirst($booking->status ?? 'N/A') }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Check-in</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $booking->start_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Check-out</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $booking->end_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Amount</p>
                                <p class="text-sm font-semibold text-gray-900">₱{{ number_format($booking->total_amount ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-600">No booking history yet</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">

            <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($customer->id)
                    <a href="#" class="block w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center font-medium">
                        Send Message
                    </a>
                    @endif
                    <button class="block w-full px-4 py-3 border border-gray-300 text-gray-900 rounded-lg hover:bg-gray-50 transition font-medium">
                        Edit Profile
                    </button>
                    <button class="block w-full px-4 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition font-medium">
                        Deactivate Account
                    </button>
                </div>
            </div> -->

            <!-- Account Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Account Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Active Bookings</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $customer->bookings?->where('status', 'approved')->count() ?? 0 }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                        <span class="text-gray-600">Pending Bookings</span>
                        <span class="text-2xl font-bold text-yellow-600">{{ $customer->bookings?->where('status', 'pending')->count() ?? 0 }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                        <span class="text-gray-600">Total Spent</span>
                        <span class="text-2xl font-bold text-green-600">₱{{ number_format($customer->bookings?->sum('total_amount') ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Account Information</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p class="text-gray-900 font-semibold capitalize">{{ $customer->status ?? 'inactive' }}</p>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-gray-600">Created</p>
                        <p class="text-gray-900 font-semibold">{{ $customer->created_at?->format('F j, Y \a\t g:i A') ?? 'N/A' }}</p>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-gray-600">Last Updated</p>
                        <p class="text-gray-900 font-semibold">{{ $customer->updated_at?->format('F j, Y \a\t g:i A') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

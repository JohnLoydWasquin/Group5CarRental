@extends('layouts.authorities.admin')
@vite('resources/js/admin/adminBooking.js')

@section('content')
<div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold text-gray-800">Bookings</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Container -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-indigo-50 rounded-t-xl">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Pickup → Return</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Pickup Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Drop-off Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Proof of Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Booking Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $pendingStatuses = ['Pending Approval', 'Payment Submitted'];
                        $paymentColors = [
                            'Pending' => 'bg-yellow-100 text-yellow-700',
                            'For Verification' => 'bg-indigo-100 text-indigo-700',
                            'Paid' => 'bg-emerald-100 text-emerald-700',
                            'Rejected' => 'bg-red-100 text-red-700'
                        ];
                        $statusColors = [
                            'Pending Approval' => 'bg-yellow-100 text-yellow-700',
                            'Payment Submitted' => 'bg-indigo-100 text-indigo-700',
                            'Confirmed' => 'bg-emerald-100 text-emerald-700',
                            'Ongoing' => 'bg-blue-100 text-blue-700',
                            'Completed' => 'bg-gray-100 text-gray-700',
                            'Cancelled' => 'bg-red-200 text-red-800',
                            'Rejected' => 'bg-red-100 text-red-700'
                        ];
                    @endphp

                    @foreach($bookings as $booking)
                    <tr class="hover:bg-indigo-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->booking_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->vehicle->Brand ?? 'Unknown' }} {{ $booking->vehicle->Model ?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y H:i') }} → 
                            {{ \Carbon\Carbon::parse($booking->return_datetime)->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->pickup_location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->dropoff_location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->receipt_screenshot)
                                <a href="#" data-modal-toggle="paymentModal{{ $booking->booking_id }}" class="text-indigo-600 hover:underline">📎 View</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$booking->payment_status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $booking->payment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$booking->booking_status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $booking->booking_status }}
                            </span>
                        </td>

                        <!-- ✅ Actions -->
                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                            @if(in_array($booking->booking_status, $pendingStatuses))
                                <!-- Confirm -->
                                <form action="{{ route('admin.bookings.confirm', $booking->booking_id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm">Confirm</button>
                                </form>

                                <!-- Reject -->
                                <button type="button" data-modal-toggle="rejectModal{{ $booking->booking_id }}" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm">Reject</button>

                            @elseif($booking->booking_status === 'Confirmed')
                                <!-- Ongoing -->
                                <form action="{{ route('admin.bookings.ongoing', $booking->booking_id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm">Ongoing</button>
                                </form>

                                <!-- Delete -->
                                <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm">Delete</button>
                                </form>

                            @elseif($booking->booking_status === 'Ongoing')
                                <!-- Complete -->
                                <form action="{{ route('admin.bookings.completed', $booking->booking_id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm">Complete</button>
                                </form>

                                <!-- Delete -->
                                <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm">Delete</button>
                                </form>

                            @elseif($booking->booking_status === 'Completed' || $booking->booking_status === 'Rejected')
                                <!-- Delete -->
                                <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm">Delete</button>
                                </form>

                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ✅ Modals -->
@foreach($bookings as $booking)
    @if($booking->receipt_screenshot)
        <!-- Proof of Payment Modal -->
        <div id="paymentModal{{ $booking->booking_id }}" class="modal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2 p-6">
                <h2 class="text-xl font-bold mb-4">Proof of Payment</h2>
                <img src="{{ asset('storage/' . $booking->receipt_screenshot) }}" 
                     alt="Proof of Payment" 
                     class="w-full max-w-md max-h-[400px] mx-auto rounded-lg mb-4 object-contain">
                <p><strong>Payer Name:</strong> {{ $booking->payer_name }}</p>
                <p><strong>Payer Number:</strong> {{ $booking->payer_number }}</p>
                <p><strong>Amount Sent:</strong> ₱{{ number_format($booking->total_amount, 2) }}</p>
                <p><strong>Date Uploaded:</strong> {{ $booking->updated_at }}</p>
                <div class="mt-6 text-right">
                    <button data-modal-toggle="paymentModal{{ $booking->booking_id }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl">Close</button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div id="rejectModal{{ $booking->booking_id }}" class="modal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="modal-content bg-white rounded-2xl shadow-lg w-11/12 md:w-2/3 lg:w-1/2 p-6">
                <h2 class="text-xl font-bold mb-4">Reject Booking</h2>

                <form action="{{ route('admin.bookings.reject', $booking->booking_id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-medium mb-2">Reason</label>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">Wrong Amount</button>
                            <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">Blurry/Unreadable</button>
                            <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">Invalid Receipt</button>
                            <button type="button" class="reason-btn px-3 py-1 bg-red-100 text-red-700 rounded-xl text-sm">No Payment Found</button>
                        </div>
                        <input type="hidden" name="reason" id="reasonInput{{ $booking->booking_id }}">
                        <input type="text" name="additional_note" placeholder="Additional note (optional)" class="w-full border rounded-xl px-3 py-2">
                    </div>
                    <div class="text-right space-x-2">
                        <button type="button" data-modal-toggle="rejectModal{{ $booking->booking_id }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl">Send SMS & Reject</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection

@extends('layouts.authorities.admin')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="mb-4">
        <h1 class="text-3xl font-bold">Manage Customers</h1>
        <p class="text-gray-600">View and manage all customer accounts</p>
    </div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Customers</h2>
        <input 
            type="text" 
            id="searchCustomer" 
            placeholder="Search customers..." 
            class="border rounded-lg px-3 py-2 w-64 focus:outline-none focus:ring focus:ring-yellow-400"
        >
    </div>

        <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left text-gray-600 text-sm md:text-base">
                    <th class="p-3 border-b">Name</th>
                    <th class="p-3 border-b">Email</th>
                    <th class="p-3 border-b">Phone</th>
                    <th class="p-3 border-b">Bookings</th>
                    <th class="p-3 border-b">Status</th>
                    <th class="p-3 border-b">Verification</th>
                    <th class="p-3 border-b">Join Date</th>
                    <th class="p-3 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="customerTableBody" class="text-sm md:text-base">
                @foreach ($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border-b whitespace-nowrap">{{ $customer->name }}</td>
                        <td class="p-3 border-b">{{ $customer->email }}</td>
                        <td class="p-3 border-b whitespace-nowrap">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="p-3 border-b text-center">
                            {{ $customer->bookings()->count() }}
                        </td>
                        <td class="p-3 border-b">
                            <span class="px-2 py-1 rounded text-xs md:text-sm
                                {{ $customer->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($customer->status ?? 'inactive') }}
                            </span>
                        </td>
                        <td class="p-3 border-b">
                            @php
                                $k = $customer->kyc_status ?? 'None';
                                $color = match($k) {
                                    'Approved' => 'bg-green-100 text-green-700',
                                    'Pending'  => 'bg-yellow-100 text-yellow-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    default    => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs md:text-sm {{ $color }}">
                                {{ $k === 'None' ? 'Not submitted' : $k }}
                            </span>

                            @if($customer->kycSubmission)
                                <a href="{{ route('admin.kyc.show', $customer->kycSubmission->id) }}"
                                class="ml-2 text-[11px] md:text-xs text-blue-600 hover:text-blue-800">
                                    Review
                                </a>
                            @endif
                        </td>
                        <td class="p-3 border-b whitespace-nowrap">
                            {{ $customer->created_at->format('M d, Y') }}
                        </td>

                        <td class="p-3 border-b text-center">
                            <div class="flex flex-col md:flex-row justify-center gap-2 md:gap-4">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                class="text-blue-600 hover:text-blue-800 transition transform hover:scale-105 flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i>
                                    <span class="text-sm">View</span>
                                </a>

                                <form action="{{ route('admin.customers.destroy', $customer->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 transition transform hover:scale-105 flex items-center gap-1">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="text-sm">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

@vite('resources/js/admin/customers.js')
@endsection

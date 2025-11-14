@extends('layouts.orgStaff.staff')
@vite('resources/js/staff/customers.js')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="mb-4">
        <h1 class="text-3xl font-bold">Manage Customers</h1>
        <p class="text-gray-600">View all customer accounts</p>
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
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left text-gray-600">
                    <th class="p-3 border-b">Name</th>
                    <th class="p-3 border-b">Email</th>
                    <th class="p-3 border-b">Phone</th>
                    <th class="p-3 border-b text-center">Bookings</th>
                    <th class="p-3 border-b">Status</th>
                    <th class="p-3 border-b">Join Date</th>
                    <th class="p-3 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="customerTableBody">
                @foreach ($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 border-b">{{ $customer->name }}</td>
                        <td class="p-3 border-b">{{ $customer->email }}</td>
                        <td class="p-3 border-b">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="p-3 border-b text-center">{{ $customer->bookings()->count() }}</td>
                        <td class="p-3 border-b">
                            <span class="px-2 py-1 rounded text-sm 
                                {{ $customer->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($customer->status ?? 'inactive') }}
                            </span>
                        </td>
                        <td class="p-3 border-b">{{ $customer->created_at->format('M d, Y') }}</td>
                        <td class="p-3 border-b text-center">
                            <a href="{{ route('staff.customers.show', $customer->id) }}" 
                                class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110 flex items-center gap-1">
                                <i class="fa-solid fa-eye"></i>
                                View
                            </a>
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
@endsection

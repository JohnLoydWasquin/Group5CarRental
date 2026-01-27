@extends('layouts.authorities.admin')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-xl shadow-md">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Account Verifications
            </h1>
            <p class="text-gray-600 text-sm sm:text-base">
                Review and approve customer identity submissions.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.kyc.index') }}">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name or email..."
                class="border rounded-lg px-3 py-2 w-64
                       focus:outline-none focus:ring focus:ring-yellow-400"
            >
        </form>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-700 text-sm">
                <tr>
                    <th class="px-4 py-3 border-b text-left">Customer</th>
                    <th class="px-4 py-3 border-b text-left">Email</th>
                    <th class="px-4 py-3 border-b text-left">Status</th>
                    <th class="px-4 py-3 border-b text-left">Submitted At</th>
                    <th class="px-4 py-3 border-b text-center">Action</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">
                @forelse ($submissions as $submission)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 border-b">
                            {{ $submission->user->name }}
                        </td>

                        <td class="px-4 py-3 border-b">
                            {{ $submission->user->email }}
                        </td>

                        <td class="px-4 py-3 border-b">
                            @php
                                $color = match($submission->status) {
                                    'Approved' => 'bg-green-100 text-green-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    default    => 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                {{ $submission->status }}
                            </span>
                        </td>

                        <td class="px-4 py-3 border-b">
                            {{ $submission->created_at->format('M d, Y H:i') }}
                        </td>

                        <td class="px-4 py-3 border-b text-center">
                            <a href="{{ route('admin.kyc.show', $submission->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5
                                      bg-blue-600 hover:bg-blue-700
                                      text-white text-xs font-medium rounded">
                                <i class="fa-solid fa-eye text-xs"></i>
                                Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="py-6 text-center text-gray-500 text-sm">
                            No verification submissions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $submissions->links() }}
    </div>

</div>
@endsection

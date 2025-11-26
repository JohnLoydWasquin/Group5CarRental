@extends('layouts.authorities.admin')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-4 sm:p-6 lg:p-8">
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">
                Verification Details
            </h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">
                Review customer information and documents before approving.
            </p>
        </div>

        <a href="{{ route('admin.kyc.index') }}"
           class="inline-flex items-center justify-center text-xs sm:text-sm font-medium text-blue-600 hover:text-blue-800">
            <span class="mr-1">&larr;</span> Back to list
        </a>
    </div>

    {{-- Success alert --}}
    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
        {{-- LEFT COLUMN --}}
        <div class="space-y-4 sm:space-y-5">
            {{-- Customer Info --}}
            <section class="border border-gray-200 rounded-lg p-4 sm:p-5 bg-gray-50/60">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Customer Info</h2>

                <div class="space-y-1.5 text-sm sm:text-base">
                    <p><span class="font-semibold text-gray-700">Name:</span> {{ $submission->user->name }}</p>
                    <p><span class="font-semibold text-gray-700">Email:</span> {{ $submission->user->email }}</p>
                    <p><span class="font-semibold text-gray-700">Phone:</span> {{ $submission->user->phone ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-gray-700">Joined:</span> {{ $submission->user->created_at->format('M d, Y') }}</p>
                </div>

                <p class="mt-3 text-sm sm:text-base">
                    <span class="font-semibold text-gray-700">Account verification status:</span>
                    <span class="inline-flex items-center ml-2 px-2.5 py-1 rounded-full text-xs font-semibold
                        @if($submission->user->kyc_status === 'Approved') bg-green-100 text-green-700
                        @elseif($submission->user->kyc_status === 'Rejected') bg-red-100 text-red-700
                        @elseif($submission->user->kyc_status === 'Pending') bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $submission->user->kyc_status }}
                    </span>
                </p>
            </section>

            {{-- Submitted Details --}}
            <section class="border border-gray-200 rounded-lg p-4 sm:p-5 bg-gray-50/60">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Submitted Details</h2>

                <div class="space-y-1.5 text-sm sm:text-base">
                    <p><span class="font-semibold text-gray-700">Full name:</span> {{ $submission->full_name }}</p>
                    <p>
                        <span class="font-semibold text-gray-700">Birthdate:</span>
                        {{ optional($submission->birthdate)->format('M d, Y') ?? 'N/A' }}
                    </p>
                    <p><span class="font-semibold text-gray-700">Address:</span> {{ $submission->address_line }}</p>
                    <p><span class="font-semibold text-gray-700">City:</span> {{ $submission->city }}</p>
                    <p><span class="font-semibold text-gray-700">Province:</span> {{ $submission->province }}</p>
                    <p><span class="font-semibold text-gray-700">Postal code:</span> {{ $submission->postal_code }}</p>
                    <p class="mt-2">
                        <span class="font-semibold text-gray-700">ID type:</span> {{ $submission->id_type }}<br>
                        <span class="font-semibold text-gray-700">ID number:</span> {{ $submission->id_number }}
                    </p>
                </div>
            </section>

            {{-- Admin Notes + Actions --}}
            <section class="border border-gray-200 rounded-lg p-4 sm:p-5 bg-white">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Admin Notes & Actions</h2>

                @if($submission->admin_notes)
                    <div class="mb-4 rounded-md bg-gray-50 border border-gray-200 p-3">
                        <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-1">Last notes</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line">
                            {{ $submission->admin_notes }}
                        </p>
                    </div>
                @endif

                <div class="mt-3 flex flex-col gap-4">
                    {{-- APPROVE --}}
                    <form method="POST"
                          action="{{ route('admin.kyc.approve', $submission->id) }}"
                          class="space-y-2">
                        @csrf
                        <label class="text-xs sm:text-sm font-semibold text-gray-700">
                            Optional note to save with approval
                        </label>
                        <textarea name="admin_notes" rows="2"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                         focus:outline-none focus:ring-2 focus:ring-green-500/70 focus:border-green-500"
                                  placeholder="Optional comment..."></textarea>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-1.5 rounded-md
                                       bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                                       hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1">
                            Approve Verification
                        </button>
                    </form>

                    {{-- REJECT --}}
                    <form method="POST"
                          action="{{ route('admin.kyc.reject', $submission->id) }}"
                          class="space-y-2">
                        @csrf
                        <label class="text-xs sm:text-sm font-semibold text-gray-700">
                            Reason for rejection <span class="text-red-500">*</span>
                        </label>
                        <textarea name="admin_notes" rows="2" required
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                         focus:outline-none focus:ring-2 focus:ring-red-500/70 focus:border-red-500"
                                  placeholder="Explain why the documents are not acceptable..."></textarea>
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-1.5 rounded-md
                                       bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                                       hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                            Reject Verification
                        </button>
                    </form>
                </div>
            </section>
        </div>

        {{-- RIGHT COLUMN (Images) --}}
        <div class="space-y-4 sm:space-y-5">
            {{-- Government ID --}}
            <section class="border border-gray-200 rounded-lg p-4 sm:p-5 bg-gray-50">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Government ID</h2>

                @if($submission->id_image_path)
                    <div class="rounded-md bg-white border border-dashed border-gray-200 p-2 sm:p-3">
                        <div class="max-h-[420px] overflow-auto flex items-center justify-center bg-gray-50 rounded">
                            <img src="{{ asset('storage/'.$submission->id_image_path) }}"
                                 alt="Government ID"
                                 class="max-h-[400px] w-auto object-contain rounded shadow-sm">
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No ID image uploaded.</p>
                @endif
            </section>

            {{-- Selfie with ID --}}
            <section class="border border-gray-200 rounded-lg p-4 sm:p-5 bg-gray-50">
                <h2 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Selfie with ID</h2>

                @if($submission->selfie_image_path)
                    <div class="rounded-md bg-white border border-dashed border-gray-200 p-2 sm:p-3">
                        <div class="max-h-[420px] overflow-auto flex items-center justify-center bg-gray-50 rounded">
                            <img src="{{ asset('storage/'.$submission->selfie_image_path) }}"
                                 alt="Selfie with ID"
                                 class="max-h-[400px] w-auto object-contain rounded shadow-sm">
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No selfie image uploaded.</p>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

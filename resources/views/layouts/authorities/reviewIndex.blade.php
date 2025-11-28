@extends('layouts.authorities.admin')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="star" class="w-6 h-6 text-yellow-400"></i>
                Customer Reviews
            </h1>
            <p class="text-sm text-gray-500">
                Monitor customer feedback and manage which reviews appear on the public site.
            </p>
        </div>

        {{-- Filter + Search --}}
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <form method="GET" class="flex items-center gap-2">
                <select name="status"
                        class="border border-gray-300 text-sm rounded-lg px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="hidden"   {{ request('status') === 'hidden'   ? 'selected' : '' }}>Hidden</option>
                </select>

                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="border border-gray-300 text-sm rounded-lg pl-9 pr-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Search by name or comment">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5"></i>
                </div>

                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Apply
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Average rating --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white shadow-lg">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-400/20 rounded-full blur-3xl"></div>
            <div class="p-5 flex items-center justify-between relative">
                <div>
                    <p class="text-sm text-slate-300">Average Rating</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight">
                        {{ number_format($averageRating ?? 0, 1) }}/5
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Based on {{ $totalReviews ?? 0 }} review{{ ($totalReviews ?? 0) === 1 ? '' : 's' }}
                    </p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="flex">
                        @php $filled = floor($averageRating ?? 0); @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $filled)
                                <i data-lucide="star" class="w-5 h-5 text-yellow-400"></i>
                            @else
                                <i data-lucide="star" class="w-5 h-5 text-slate-500"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="mt-1 text-[11px] text-slate-300 uppercase tracking-wide">
                        Insight
                    </span>
                </div>
            </div>
        </div>

        {{-- Total approved --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Approved reviews</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">
                    {{ $approvedCount ?? 0 }}
                </p>
                <p class="mt-1 text-xs text-emerald-600 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Visible on website
                </p>
            </div>
            <div class="flex items-center justify-center bg-emerald-50 text-emerald-600 w-11 h-11 rounded-full">
                <i data-lucide="eye" class="w-5 h-5"></i>
            </div>
        </div>

        {{-- Pending --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pending moderation</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">
                    {{ $pendingCount ?? 0 }}
                </p>
                <p class="mt-1 text-xs text-amber-600 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Waiting for review
                </p>
            </div>
            <div class="flex items-center justify-center bg-amber-50 text-amber-600 w-11 h-11 rounded-full">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    {{-- REVIEWS TABLE --}}
    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Recent Reviews</h2>
            <span class="text-xs text-gray-400">
                Showing {{ $reviews->count() }} of {{ $reviews->total() }} records
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Customer</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Rating</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Comment</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Date</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            {{-- Customer --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-600/10 flex items-center justify-center text-xs font-semibold text-blue-700">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $review->user->name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $review->user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i data-lucide="star" class="w-4 h-4 text-yellow-400"></i>
                                        @else
                                            <i data-lucide="star" class="w-4 h-4 text-gray-300"></i>
                                        @endif
                                    @endfor
                                    <span class="text-xs text-gray-500 ml-1">
                                        {{ $review->rating }}/5
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-3 max-w-md">
                                @if($review->comment)
                                    <p class="text-gray-700 line-clamp-2">
                                        {{ $review->comment }}
                                    </p>
                                @else
                                    <span class="text-xs text-gray-400 italic">No comment</span>
                                @endif
                            </td>

                            <td class="px-5 py-3 text-gray-500">
                                <div class="text-xs">
                                    {{ $review->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    {{ $review->created_at->format('H:i') }}
                                </div>
                            </td>

                            <td class="px-5 py-3">
                                @php
                                    $status = $review->status;
                                    $badgeClasses = match($status) {
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'hidden'   => 'bg-gray-100 text-gray-600 border-gray-300',
                                        default    => 'bg-gray-100 text-gray-600 border-gray-300',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full border {{ $badgeClasses }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        @if($status === 'approved') bg-emerald-500
                                        @elseif($status === 'pending') bg-amber-500
                                        @else bg-gray-400 @endif"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td class="px-5 py-3 text-right">
                            <div class="inline-flex items-center gap-2">

                                @if($review->status !== 'approved')
                                    <form action="{{ route('admin.reviews.approve', $review->id) }}"
                                        method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs rounded-lg
                                                    bg-emerald-50 text-emerald-700 hover:bg-emerald-100
                                                    border border-emerald-200 transition">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if($review->status !== 'hidden')
                                    <form action="{{ route('reviews.reject', $review->id) }}"
                                        method="POST"
                                        class="inline-block">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs rounded-lg
                                                    bg-gray-50 text-gray-700 hover:bg-gray-100
                                                    border border-gray-200 transition">
                                            <i data-lucide="eye-off" class="w-3.5 h-3.5"></i>
                                            Hide
                                        </button>
                                    </form>
                                @endif
                            </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-sm text-gray-500">
                                No reviews found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between text-xs text-gray-500">
            <div>
                Showing
                <span class="font-semibold text-gray-700">{{ $reviews->firstItem() ?? 0 }}</span>
                -
                <span class="font-semibold text-gray-700">{{ $reviews->lastItem() ?? 0 }}</span>
                of
                <span class="font-semibold text-gray-700">{{ $reviews->total() }}</span>
            </div>
            <div class="text-right">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

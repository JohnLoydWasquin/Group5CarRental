<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .fade { transition: opacity 0.8s ease-in-out; }
    .password-hint {
      display: none;
      background: #f9f9f9;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-top: 0.5rem;
      border: 1px solid #ddd;
    }
  </style>
</head>

@extends('layouts.authorities.admin')

@section('content')
<div class="p-6 space-y-8">

    <nav class="text-sm text-gray-500 mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Create New Staff Account</h1>
    </nav>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Staff Form -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <p class="text-gray-500 mb-6">Fill out the form to add a new staff or admin account.</p>

        <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div class="relative">
                    <label class="block text-gray-700 mb-1 font-semibold">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500 pr-10"
                        onfocus="showPasswordHint()" onblur="hidePasswordHint()" oninput="validatePassword(this.value)">
                    <button type="button" onclick="togglePassword('password', this)"
                        class="absolute right-3 top-12 text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

                    <div id="passwordHint" class="password-hint shadow">
                        <p class="mb-2 font-semibold">Password must include:</p>
                        <ul class="text-sm space-y-1">
                        <li id="lenRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i>8-20 <strong>characters</strong></li>
                        <li id="upperRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i>At least one <strong>capital letter</strong></li>
                        <li id="numRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i>At least one <strong>number</strong></li>
                        <li id="spaceRule"><i class="fa-solid fa-xmark text-red-500 mr-2"></i><strong>No spaces</strong></li>
                        </ul>
                    </div>
                    </div>

                    <div class="relative">
                    <label class="block text-gray-700 mb-1 font-semibold">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full border-gray-300 rounded-lg p-3 border focus:ring-green-500 focus:border-green-500 pr-10">
                    <button type="button" onclick="togglePassword('password_confirmation', this)"
                        class="absolute right-3 top-12 text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.staff.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition shadow-sm">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-sm">Create Account</button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-lg rounded-2xl p-6 mt-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Search Staff/Admin</h2>
        <form id="searchForm" action="{{ route('admin.staff.index') }}#staffList" method="GET" class="flex flex-col md:flex-row md:items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition w-full md:w-64">
            <select name="filter_role" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white">
                <option value="">All Roles</option>
                <option value="admin" {{ request('filter_role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ request('filter_role') == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">Search</button>
        </form>
    </div>

    <div id="staffList" class="bg-white shadow-lg rounded-2xl p-6 mt-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Staff & Admin List</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase">
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Phone</th>
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse ($staff as $member)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-6 py-3">{{ $member->name }}</td>
                            <td class="px-6 py-3">{{ $member->email }}</td>
                            <td class="px-6 py-3">{{ $member->phone }}</td>
                            <td class="px-6 py-3 capitalize">{{ $member->role }}</td>
                            <td class="px-6 py-3 flex justify-center gap-3">
                                <a href="{{ route('admin.staff.update', $member->id) }}" class="px-3 py-1.5 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200">Edit</a>
                                <form action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff/admin?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">No staff/admin found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($staff, 'links'))
            <div class="mt-4">
                {{ $staff->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', function() {
    setTimeout(() => {
        document.querySelector('#staffList').scrollIntoView({ behavior: 'smooth' });
    }, 100);
});
</script>
<script src="{{ asset('js/auth.js') }}"></script>
@endsection

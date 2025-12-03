<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminStaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->whereIn('role', ['admin', 'staff']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_role')) {
            $query->where('role', $request->input('filter_role'));
        }

        $staff = $query->paginate(10)->withQueryString();

        return view('layouts.authorities.adminStaff', compact('staff'));
    }


    public function create(Request $request)
    {
        return $this->index($request);
    }

    public function edit($id)
    {
        $staff = User::whereIn('role', ['admin', 'staff'])->findOrFail($id);

        return view('layouts.authorities.editStaff', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'address' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
            'role' => 'required|string|in:staff,admin',
        ], [
            'phone.unique' => 'This phone number is already registered.',
            'password.regex' => 'Password must include uppercase, lowercase, number, and special character.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff account created successfully.');
    }

    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $staff->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $staff->id,
            'address' => 'required|string|max:255',
            'role' => 'required|in:admin,staff',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.staff.create')->with('success', 'Staff information updated successfully.');
    }

    // Delete staff
    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.staff.create')->with('success', 'Staff account deleted successfully.');
    }
}

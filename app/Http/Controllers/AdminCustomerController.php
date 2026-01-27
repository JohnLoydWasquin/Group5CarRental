<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'user')
            ->latest()
            ->paginate(3);

        return view('layouts.authorities.customers', compact('customers'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $customers = User::where('role', 'user')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->withCount('bookings')
            ->take(10)
            ->get();

        return response()->json($customers);
    }

    public function show($id)
    {
        $customer = User::with('bookings')->findOrFail($id);
        return view('layouts.authorities.customers_show', compact('customer'));
    }

    public function destroy($id)
    {
        $customer = User::findOrFail($id);

        $customer->delete();
        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }
}

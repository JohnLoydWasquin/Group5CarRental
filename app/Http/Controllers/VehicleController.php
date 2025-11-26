<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        $search   = $request->input('search');
        $brand    = $request->input('brand');
        $sort     = $request->input('sort');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');

        $query = Vehicle::query()
            ->with(['bookings' => function ($q) use ($now) {
                $q->whereIn('booking_status', [
                    'Pending Approval',
                    'Awaiting Payment',
                    'Under Review',
                    'Payment Submitted',
                    'Confirmed',
                    'Ongoing',
                ])
                ->where('pickup_datetime', '<=', $now)
                ->where('return_datetime', '>=', $now);
            }]);

        // SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('Brand', 'LIKE', "%{$search}%")
                  ->orWhere('Model', 'LIKE', "%{$search}%");
            });
        }

        // FILTER BRAND
        if (!empty($brand)) {
            $query->where('Brand', $brand);
        }

        // ⭐ FILTER PRICE RANGE
        if ($priceMin !== null && $priceMin !== '') {
            $query->where('DailyPrice', '>=', (float) $priceMin);
        }

        if ($priceMax !== null && $priceMax !== '') {
            $query->where('DailyPrice', '<=', (float) $priceMax);
        }

        // SORTING
        if ($sort === 'price_asc') {
            $query->orderBy('DailyPrice', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('DailyPrice', 'desc');
        }

        // PAGINATION
        $vehicles = $query->paginate(6)->withQueryString();

        // Set helper property for JS / UI
        foreach ($vehicles as $vehicle) {
            $current = $vehicle->bookings->first();
            $vehicle->current_booking_until = $current ? $current->return_datetime : null;
        }

        $brands = Vehicle::select('Brand')->distinct()->pluck('Brand');

        return view('layouts.pages.vehicles', compact(
            'vehicles',
            'brands',
            'search',
            'brand',
            'sort'
        ));
    }
}

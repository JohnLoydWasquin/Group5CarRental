<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q');

        if (!$q) {
            return response()->json([]);
        }

        $results = Location::where('name', 'LIKE', "%{$q}%")
            ->orderBy('type') 
            ->limit(20)
            ->get(['id', 'name', 'type', 'province', 'region', 'code']);

        return response()->json($results);
    }
}

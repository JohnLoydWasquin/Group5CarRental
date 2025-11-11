<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        // Fetch all vehicles from the database
        $vehicles = Vehicle::all();

        // Return the correct Blade file path
        return view('layouts.pages.vehicles', compact('vehicles'));
    }
}

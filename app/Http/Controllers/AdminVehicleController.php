<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class AdminVehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('created_at', 'desc')->paginate(10);
        return view('layouts.authorities.adminVehicles', compact('vehicles'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('layouts.authorities.editVehicle', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'PlateNo' => 'required|string|max:255',
            'Brand' => 'required|string|max:255',
            'Model' => 'required|string|max:255',
            'DailyPrice' => 'required|numeric',
            'Availability' => 'required|boolean',
            'Condition' => 'required|string|max:50',
        ]);

        $vehicle->update([
            'PlateNo' => $request->PlateNo,
            'Brand' => $request->Brand,
            'Model' => $request->Model,
            'DailyPrice' => $request->DailyPrice,
            'Availability' => (bool)$request->Availability,
            'Condition' => $request->Condition,
        ]);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index');
    }
}

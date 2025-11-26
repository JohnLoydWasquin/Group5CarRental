<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;


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
            'Passengers' => 'required|integer|min:1',
            'Condition' => 'required|string|max:50',
        ]);

        $availability = $request->Condition === 'Maintenance'
            ? 0
            : (bool) $request->Availability;

        $vehicle->update([
            'PlateNo'     => $request->PlateNo,
            'Brand'       => $request->Brand,
            'Model'       => $request->Model,
            'DailyPrice'  => $request->DailyPrice,
            'Availability'=> $availability,
            'Passengers'  => $request->Passengers,
            'Condition'   => $request->Condition,
        ]);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'PlateNo' => 'required|string|unique:vehicles,PlateNo',
        'Brand' => 'required|string',
        'Model' => 'required|string',
        'DailyPrice' => 'required|numeric',
        'Condition' => 'required|string|max:50',
        'Image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('Image')) {
        $validated['Image'] = $request->file('Image')->store('vehicles', 'public');
    } else {
        $validated['Image'] = null;
    }

    $validated['Passengers'] = $request->Passengers ?? 0;
    $validated['FuelType'] = $request->FuelType ?? 'Unleaded';
    $validated['Transmission'] = $request->Transmission ?? 'Automatic';
    $validated['Passengers'] = $request->Passengers ?? 0;
    $validated['Availability'] = $request->Condition === 'Maintenance' ? 0 : 1;
    $validated['EmpID'] = 1;

    Vehicle::create($validated);

    return redirect()->route('admin.vehicles.index')
                     ->with('success', 'Vehicle added successfully!');
}

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index');
    }
}

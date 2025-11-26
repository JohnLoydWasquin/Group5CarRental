<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $primaryKey = 'VehicleID';

    protected $table = 'vehicles';

    protected $fillable = [
    'PlateNo',
    'Brand',
    'Model',
    'DailyPrice',
    'Condition',
    'Availability',
    'Passengers',
    'FuelType',
    'Transmission',
    'Image',
    'EmpID'
];

public function bookings()
{
    return $this->hasMany(\App\Models\Booking::class, 'VehicleID', 'VehicleID');
}



}

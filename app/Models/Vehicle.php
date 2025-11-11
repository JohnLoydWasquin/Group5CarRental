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
        'VehicleName',
        'Type',
        'PricePerDay',
        'Image',
        'PlateNo',      
        'Brand',
        'Model',
        'DailyPrice', 
        'Availability',
        'Condition',
    ];
}

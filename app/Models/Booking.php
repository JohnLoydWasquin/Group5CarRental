<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'VehicleID',
        'pickup_location',
        'dropoff_location',
        'pickup_datetime',
        'return_datetime',
        'rental_days',
        'addons',
        'subtotal',
        'security_deposit',
        'total_amount',
        'payment_method',
        'payment_status',
        'paypal_transaction_id',
        'payment_meta',
        'booking_status',
        'payer_name',
        'payer_number',
        'receipt_screenshot',
    ];

    protected $casts = [
        'addons' => 'array',
        'pickup_datetime' => 'datetime',
        'return_datetime' => 'datetime',
        'payment_meta' => 'array',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'VehicleID', 'VehicleID');
    }
}

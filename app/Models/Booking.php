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
        // DO NOT add paid_amount here, it's computed only
    ];

    protected $casts = [
        'addons'           => 'array',
        'pickup_datetime'  => 'datetime',
        'return_datetime'  => 'datetime',
        'payment_meta'     => 'array',
    ];

    public const ACTIVE_STATUSES = [
        'Pending Approval',
        'Awaiting Payment',
        'Under Review',
        'Payment Submitted',
        'Confirmed',
        'Ongoing',
    ];

    public function scopeActiveForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
                     ->whereIn('booking_status', self::ACTIVE_STATUSES);
    }

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


    /**
     * Human label for what this payment is for (booking vs reservation).
     */
    public function getPaymentForLabelAttribute(): string
    {
        $meta = $this->payment_meta ?? [];

        if (($meta['payment_for'] ?? null) === 'reservation') {
            return 'Reserve';
        }

        return 'Booking';
    }

    /**
     * Human label for payment option (deposit / full).
     */
    public function getPaymentOptionLabelAttribute(): string
    {
        $meta = $this->payment_meta ?? [];
        $option = $meta['payment_option'] ?? null;

        if ($option === 'deposit') return 'Deposit Only';
        if ($option === 'full') return 'Full Payment';

        // fallback for older bookings
        if (in_array($this->payment_status, ['For Verification', 'Paid'])) {
            return 'Full Payment';
        }

        return '—';
    }

    /**
     * Computed total paid amount (deposit/full/balance).
     *
     * This value is calculated from payment_meta + paid_amount field (if exists).
     */
    public function getPaidAmountAttribute(): float
    {
        if (array_key_exists('paid_amount', $this->attributes)) {
            return (float) $this->attributes['paid_amount'];
        }

        // Otherwise fallback to payment_meta
        $meta = $this->payment_meta ?? [];
        $option = $meta['payment_option'] ?? null;

        if ($option === 'deposit') {
            return (float) $this->security_deposit;
        }

        if ($option === 'full') {
            return (float) $this->total_amount;
        }

        if (in_array($this->payment_status, ['For Verification', 'Paid'])) {
            return (float) $this->total_amount;
        }

        return 0.0;
    }
}

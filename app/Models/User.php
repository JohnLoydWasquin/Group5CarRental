<?php

namespace App\Models;

use App\Models\Booking;
use App\Models\KycSubmission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property \App\Models\KycSubmission|null $kycSubmission
 */

class User extends Authenticatable
{
    use HasFactory, Notifiable; 
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'profile_image',
        'kyc_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function bookings(){
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }

    public function kycSubmission()
    {
        return $this->hasOne(KycSubmission::class);
    }
}



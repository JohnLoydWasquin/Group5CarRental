<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'phone', 
        'otp', 
        'expires_at'
    ];

    public $timestamps = true;
}

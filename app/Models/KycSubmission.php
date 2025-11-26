<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KycSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'birthdate',
        'address_line',
        'city',
        'province',
        'postal_code',
        'id_type',
        'id_number',
        'id_image_path',
        'selfie_image_path',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

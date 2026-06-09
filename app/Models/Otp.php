<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    // Pinapayagan nito si Laravel na mag-save ng data sa mga columns na ito
    protected $fillable = ['email', 'otp', 'expires_at'];
}
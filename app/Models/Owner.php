<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Owner extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'permit_number',
        'property_type',
        'residential_permit',
        'business_permit',
        'permit_numbers',
        'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * Relationship with permit applications
     */
    public function permitApplications()
    {
        return $this->hasMany(PermitApplication::class, 'user_id');
    }
    
    /**
     * Get residential permit
     */
    public function getResidentialPermitAttribute($value)
    {
        return $value;
    }
    
    /**
     * Get business permit
     */
    public function getBusinessPermitAttribute($value)
    {
        return $value;
    }
    
    /**
     * Get the user's profile photo URL (for notifications display)
     */
    public function getProfilePhotoUrlAttribute()
    {
        // Return default avatar if no photo is set
        return 'https://ui-avatars.com/api/?background=00A2FF&color=fff&name=' . urlencode($this->name ?? 'Owner');
    }
    
    /**
     * Route notifications for the Vonage channel (if using SMS)
     */
    public function routeNotificationForVonage()
    {
        return $this->phone;
    }
    
    /**
     * Route notifications for mail channel
     */
    public function routeNotificationForMail()
    {
        return [$this->email => $this->name];
    }
}
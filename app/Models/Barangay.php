<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Barangay extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 
        'is_active',
        'slug',
        'logo',
        'available_count',
        'total_count',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'available_count' => 'integer',
        'total_count' => 'integer',
    ];
    
    // Relationships
    
    /**
     * Get the apartments in this barangay.
     */
    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class);
    }
    
    /**
     * Get the business spaces in this barangay.
     */
    public function businessSpaces(): HasMany
    {
        return $this->hasMany(Business::class, 'barangay_id');
    }
    
    /**
     * Get the users (tenants) in this barangay.
     */
    public function users(): HasMany
    {
        // Kung ang User model ang nagsisilbing Tenant
        return $this->hasMany(User::class);
    }
    
    /**
     * Scope for active barangays only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for barangays with apartments.
     */
    public function scopeHasApartments(Builder $query): Builder
    {
        return $query->has('apartments');
    }
    
    /**
     * Get the total apartment count for this barangay.
     */
    public function getTotalApartmentsCountAttribute(): int
    {
        return $this->apartments()->count();
    }
    
    /**
     * Get the available apartments count for this barangay.
     */
    public function getAvailableApartmentsCountAttribute(): int
    {
        return $this->apartments()
            ->where('status', 'Vacant')
            ->where('verification_status', 'approved')
            ->count();
    }
    
    /**
     * Get the total business spaces count for this barangay.
     */
    public function getTotalBusinessSpacesCountAttribute(): int
    {
        return $this->businessSpaces()->count();
    }
    
    /**
     * Get the available business spaces count for this barangay.
     */
    public function getAvailableBusinessSpacesCountAttribute(): int
    {
        return $this->businessSpaces()
            ->where('status', 'Available')
            ->count();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'apartment_id', 'first_name', 'last_name', 'email', 'phone',
        'alternate_phone', 'address', 'barangay_id', 'move_in_date', 
        'lease_end_date', 'security_deposit', 'monthly_rent', 'status', 
        'emergency_contact', 'notes'
    ];
    
    protected $casts = [
        'emergency_contact' => 'array',
        'move_in_date' => 'date',
        'lease_end_date' => 'date',
        'security_deposit' => 'decimal:2',
        'monthly_rent' => 'decimal:2'
    ];
    
    protected $appends = ['full_name', 'status_badge'];
    
    // Relationships
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
    
    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'entity');
    }
    
    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Active' => 'badge-success',
            'Inactive' => 'badge-secondary',
            'Pending' => 'badge-warning',
            'Evicted' => 'badge-danger',
            default => 'badge-info'
        };
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    
    public function scopeLeaseExpiring($query, $days = 30)
    {
        return $query->where('status', 'Active')
                    ->whereNotNull('lease_end_date')
                    ->whereDate('lease_end_date', '<=', now()->addDays($days))
                    ->whereDate('lease_end_date', '>=', now());
    }
    
    // Boot Method
    protected static function booted()
    {
        static::created(function ($tenant) {
            if ($tenant->apartment) {
                $tenant->apartment->updateStatus();
            }
            
            ActivityLog::create([
                'action' => 'created',
                'entity_type' => 'tenant',
                'entity_id' => $tenant->id,
                'description' => "Added new tenant: {$tenant->full_name}",
                'new_data' => $tenant->toArray()
            ]);
        });
        
        static::updated(function ($tenant) {
            if ($tenant->wasChanged('apartment_id')) {
                // Update old apartment status
                if ($tenant->getOriginal('apartment_id')) {
                    Apartment::find($tenant->getOriginal('apartment_id'))?->updateStatus();
                }
                // Update new apartment status
                if ($tenant->apartment_id) {
                    $tenant->apartment->updateStatus();
                }
            }
        });
        
        static::deleted(function ($tenant) {
            if ($tenant->apartment) {
                $tenant->apartment->updateStatus();
            }
            
            ActivityLog::create([
                'action' => 'deleted',
                'entity_type' => 'tenant',
                'entity_id' => $tenant->id,
                'description' => "Removed tenant: {$tenant->full_name}",
                'old_data' => $tenant->toArray()
            ]);
        });
    }
}
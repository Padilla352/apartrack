<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BusinessSpace extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business_spaces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'barangay_id',
        'barangay_name',
        'business_name',
        'unit_number',
        'type',
        'description',
        'address',
        'monthly_rent',
        'floor_area_sqm',
        'images',
        'amenities',
        'business_features',
        'permit_number',
        'status',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images'            => 'array',
        'amenities'         => 'array',
        'business_features' => 'array',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'verified_at'       => 'datetime',
        'monthly_rent'      => 'decimal:2',
        'floor_area_sqm'    => 'decimal:2',
    ];

    /**
     * Get the owner of this business space.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the barangay where this business space is located.
     *
     * @return BelongsTo
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

    /**
     * Get all conversations related to this business space.
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get all messages related to this business space through conversations.
     */
    public function messages()
    {
        return $this->hasManyThrough(Message::class, Conversation::class);
    }

    /**
     * Get active conversations (with recent messages).
     */
    public function activeConversations()
    {
        return $this->conversations()
            ->whereHas('messages')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * Get unread messages count for owner.
     */
    public function getUnreadMessagesCountAttribute()
    {
        return Message::whereHas('conversation', function($q) {
                $q->where('business_space_id', $this->id);
            })
            ->where('sender_type', 'App\Models\User')
            ->where('is_read', false)
            ->count();
    }

    // Accessor for images - auto-decode JSON
    public function getImagesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $cleaned = stripslashes($value);
            $decoded = json_decode($cleaned, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            
            return [$value];
        }
        
        return [];
    }

    // Mutator for images - encode array to JSON
    public function setImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['images'] = json_encode($value);
        } else {
            $this->attributes['images'] = $value;
        }
    }

    // Helper: get full URLs of all images
    public function getImageUrlsAttribute()
    {
        $images = $this->getImagesAttribute($this->attributes['images'] ?? null);
        $urls = [];
        
        foreach ($images as $image) {
            $urls[] = asset('storage/' . ltrim($image, '/'));
        }
        
        return $urls;
    }

    // Helper: get first image URL
    public function getFirstImageUrlAttribute()
    {
        $images = $this->getImagesAttribute($this->attributes['images'] ?? null);
        
        if (empty($images)) {
            return null;
        }
        
        return asset('storage/' . ltrim($images[0], '/'));
    }

    /**
     * Check if business space is verified.
     */
    public function isVerified()
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if business space is pending verification.
     */
    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if business space is rejected.
     */
    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Check if business space is available.
     */
    public function isAvailable()
    {
        return $this->status === 'Available';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Available' => 'badge-success',
            'Reserved' => 'badge-warning',
            'Occupied' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get verification badge class.
     */
    public function getVerificationBadgeClassAttribute()
    {
        return match($this->verification_status) {
            'approved' => 'badge-success',
            'pending' => 'badge-warning',
            'rejected' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get formatted monthly rent.
     */
    public function getFormattedRentAttribute()
    {
        return '₱' . number_format($this->monthly_rent, 2);
    }

    /**
     * Get floor area with unit.
     */
    public function getFormattedFloorAreaAttribute()
    {
        return $this->floor_area_sqm ? $this->floor_area_sqm . ' sqm' : 'Not specified';
    }

    /**
     * Get amenities as comma-separated string.
     */
    public function getAmenitiesListAttribute()
    {
        if (empty($this->amenities)) {
            return 'No amenities listed';
        }
        return implode(', ', $this->amenities);
    }

    /**
     * Get business features as comma-separated string.
     */
    public function getBusinessFeaturesListAttribute()
    {
        if (empty($this->business_features)) {
            return 'No features listed';
        }
        return implode(', ', $this->business_features);
    }

    /**
     * Get short description (truncated).
     */
    public function getShortDescriptionAttribute($length = 100)
    {
        if (empty($this->description)) {
            return 'No description available';
        }
        return strlen($this->description) > $length 
            ? substr($this->description, 0, $length) . '...' 
            : $this->description;
    }

    /**
     * Get business type label.
     */
    public function getTypeLabelAttribute()
    {
        $types = [
            'Office' => 'Office Space',
            'Retail' => 'Retail Store',
            'Restaurant' => 'Restaurant / Cafe',
            'Warehouse' => 'Warehouse',
            'Co-working' => 'Co-working Space',
            'Studio' => 'Studio',
            'Other' => 'Other'
        ];
        
        return $types[$this->type] ?? $this->type ?? 'Not specified';
    }

    /**
     * Scope for verified business spaces.
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /**
     * Scope for pending verification.
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    /**
     * Scope for available business spaces.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    /**
     * Scope for business spaces by barangay.
     */
    public function scopeInBarangay($query, $barangay)
    {
        return $query->where('barangay_name', $barangay);
    }

    /**
     * Scope for business spaces by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for business spaces within price range.
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('monthly_rent', [$min, $max]);
    }

    /**
     * Boot method to register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // When a business space is deleted, remove its images from storage
        static::deleting(function ($businessSpace) {
            $images = $businessSpace->images;
            if (is_array($images)) {
                foreach ($images as $imagePath) {
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }
        });
    }
}
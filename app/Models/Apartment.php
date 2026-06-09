<?php

namespace App\Models;

use App\Notifications\ApartmentAddedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'unit_number',
        'type',
        'bedrooms',
        'bathrooms',
        'monthly_rent',
        'floor_area_sqm',
        'description',
        'address',
        'barangay_name',
        'permit_number',
        'status',
        'images',
        'amenities',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'amenities' => 'array',
        'verified_at' => 'datetime',
    ];

    // Accessor for images - auto-decode JSON
    public function getImagesAttribute($value)
    {
        Log::info('getImagesAttribute called with value: ' . $value);
        
        if (empty($value)) {
            Log::info('Images is empty, returning []');
            return [];
        }
        
        if (is_array($value)) {
            Log::info('Images is already an array: ' . json_encode($value));
            return $value;
        }
        
        if (is_string($value)) {
            Log::info('Images is string, trying to decode: ' . $value);
            $cleaned = stripslashes($value);
            Log::info('After stripslashes: ' . $cleaned);
            
            $decoded = json_decode($cleaned, true);
            if (is_array($decoded)) {
                Log::info('Decoded successfully (with stripslashes): ' . json_encode($decoded));
                return $decoded;
            }
            
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                Log::info('Decoded successfully (direct): ' . json_encode($decoded));
                return $decoded;
            }
            
            Log::info('Treating as single image path');
            return [$value];
        }
        
        Log::info('Returning empty array as fallback');
        return [];
    }

    // Mutator for images - encode array to JSON
    public function setImagesAttribute($value)
    {
        Log::info('setImagesAttribute called with: ' . json_encode($value));
        
        if (is_array($value)) {
            $this->attributes['images'] = json_encode($value);
            Log::info('Saved as JSON: ' . $this->attributes['images']);
        } else {
            $this->attributes['images'] = $value;
            Log::info('Saved as is: ' . $value);
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

    // Relationship with Owner
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get all conversations related to this apartment.
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get all messages related to this apartment through conversations.
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
                $q->where('apartment_id', $this->id);
            })
            ->where('sender_type', 'App\Models\User')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Check if apartment is verified.
     */
    public function isVerified()
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if apartment is pending verification.
     */
    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if apartment is rejected.
     */
    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Check if apartment is available for rent.
     */
    public function isAvailable()
    {
        return $this->status === 'Vacant';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Vacant' => 'badge-success',
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
     * Scope for verified apartments.
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
     * Scope for available apartments.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Vacant');
    }

    /**
     * Scope for apartments by barangay.
     */
    public function scopeInBarangay($query, $barangay)
    {
        return $query->where('barangay_name', $barangay);
    }

    /**
     * Scope for apartments within price range.
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('monthly_rent', [$min, $max]);
    }

    /**
     * Scope for apartments with minimum bedrooms.
     */
    public function scopeMinBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', '>=', $bedrooms);
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
     * Boot method to register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // When an apartment is created, send notification to all users
        static::created(function ($apartment) {
            // Only send notification if the apartment is approved
            // You can adjust condition based on your business logic
            if ($apartment->verification_status === 'approved') {
                // Get all users (assuming 'role' column exists or all users are tenants)
                $users = User::where('role', 'user')->get();
                
                // Send notification using the dedicated Notification class
                Notification::send($users, new ApartmentAddedNotification($apartment));
            }
        });

        // When an apartment is updated to approved, send notification
        static::updated(function ($apartment) {
            // Check if verification status changed to approved
            if ($apartment->isDirty('verification_status') && 
                $apartment->verification_status === 'approved' &&
                $apartment->getOriginal('verification_status') !== 'approved') {
                
                $users = User::where('role', 'user')->get();
                Notification::send($users, new ApartmentAddedNotification($apartment));
            }
        });

        // When an apartment is deleted, remove its images from storage
        static::deleting(function ($apartment) {
            $images = $apartment->images;
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
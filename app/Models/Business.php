<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'business_spaces';
    
    protected $fillable = [
        'owner_id',
        'business_name',
        'unit_number',
        'type',
        'price',
        'monthly_rent',
        'status',
        'barangay_id',
        'barangay_name',
        'address',
        'floor_area_sqm',
        'image',
        'images',
        'amenities',
        'business_features',
        'description',
        'permit_number',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'latitude',
        'longitude',
    ];
    
    protected $casts = [
        'amenities' => 'array',
        'business_features' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'monthly_rent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
    
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    
    public function getImagesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return [$value];
        }
        return [];
    }
    
    public function setImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['images'] = json_encode($value);
        } else {
            $this->attributes['images'] = $value;
        }
    }
    
    public function getAmenitiesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
    
    public function getBusinessFeaturesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($business) {
            $images = $business->images;
            if (is_array($images)) {
                foreach ($images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            if ($business->image && Storage::disk('public')->exists($business->image)) {
                Storage::disk('public')->delete($business->image);
            }
        });
    }
}
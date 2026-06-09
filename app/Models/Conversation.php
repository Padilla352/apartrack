<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 
        'owner_id', 
        'apartment_id', 
        'business_space_id', 
        'subject', 
        'user_id',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function businessSpace()
    {
        return $this->belongsTo(BusinessSpace::class);
    }

    // Accessors
    public function getListingAttribute()
    {
        if ($this->apartment_id) {
            return $this->apartment;
        }
        if ($this->business_space_id) {
            return $this->businessSpace;
        }
        return null;
    }

    public function getListingNameAttribute()
    {
        if ($this->apartment) {
            return $this->apartment->name;
        }
        if ($this->businessSpace) {
            return $this->businessSpace->business_name;
        }
        return 'General Inquiry';
    }

    public function getUnreadCountAttribute()
    {
        if (Auth::check()) {
            // User (tenant) is logged in - count messages from owners
            return $this->messages()
                ->where('sender_type', 'App\Models\Owner')
                ->where('is_read', false)
                ->count();
        }
        
        if (Auth::guard('owner')->check()) {
            // Owner is logged in - count messages from users
            return $this->messages()
                ->where('sender_type', 'App\Models\User')
                ->where('is_read', false)
                ->count();
        }
        
        return 0;
    }

    public function unreadCountFor($userType, $userId)
    {
        if ($userType === 'tenant') {
            return $this->messages()
                ->where('sender_type', 'App\Models\Owner')
                ->where('is_read', false)
                ->where('conversation_id', $this->id)
                ->count();
        }
        
        if ($userType === 'owner') {
            return $this->messages()
                ->where('sender_type', 'App\Models\User')
                ->where('is_read', false)
                ->where('conversation_id', $this->id)
                ->count();
        }
        
        return 0;
    }

    // Scopes
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeForApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
    }

    public function scopeForBusinessSpace($query, $businessSpaceId)
    {
        return $query->where('business_space_id', $businessSpaceId);
    }

    public function scopeWithUnreadForOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId)
            ->whereHas('messages', function($q) {
                $q->where('sender_type', 'App\Models\User')
                  ->where('is_read', false);
            });
    }

    public function scopeWithUnreadForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId)
            ->whereHas('messages', function($q) {
                $q->where('sender_type', 'App\Models\Owner')
                  ->where('is_read', false);
            });
    }

    public function scopeWithLatestMessage($query)
    {
        return $query->with('lastMessage');
    }

    public function scopeOrderedByLatest($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    // Helper methods
    public function markMessagesAsRead($senderType)
    {
        return $this->messages()
            ->where('sender_type', $senderType)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getOtherPartyAttribute()
    {
        if (Auth::guard('owner')->check()) {
            return $this->tenant;
        }
        if (Auth::check()) {
            return $this->owner;
        }
        return null;
    }

    public function getOtherPartyNameAttribute()
    {
        $otherParty = $this->getOtherPartyAttribute();
        return $otherParty ? $otherParty->name : 'Unknown';
    }

    public function getLastMessagePreviewAttribute($length = 50)
    {
        $lastMsg = $this->lastMessage;
        if (!$lastMsg) {
            return 'No messages yet';
        }
        return strlen($lastMsg->message) > $length 
            ? substr($lastMsg->message, 0, $length) . '...' 
            : $lastMsg->message;
    }

    public function getLastMessageTimeAttribute()
    {
        $lastMsg = $this->lastMessage;
        if (!$lastMsg) {
            return '';
        }
        return $lastMsg->created_at->diffForHumans();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($conversation) {
            if ($conversation->isDirty('last_message_at')) {
                // Additional logic if needed
            }
        });
    }
}
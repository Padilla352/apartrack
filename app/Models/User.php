<?php

namespace App\Models;

use App\Models\Conversation;
use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'avatar',
        'email_verified_at',
        'maintenance_notifications',
        'announcement_notifications',
        'email_notifications',
        'push_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'maintenance_notifications' => 'boolean',
        'announcement_notifications' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------------
    */

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant' || $this->role === 'user';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MESSAGING SYSTEM (CHAT) - FOR TENANT/USER
    |--------------------------------------------------------------------------
    */

    /**
     * Get conversations where this user is the tenant.
     * Uses tenant_id column in conversations table.
     */
    public function conversationsAsTenant()
    {
        return $this->hasMany(Conversation::class, 'tenant_id');
    }

    /**
     * Get conversations where this user is the owner (if user has owner role).
     * Uses owner_id column in conversations table.
     */
    public function conversationsAsOwner()
    {
        return $this->hasMany(Conversation::class, 'owner_id');
    }

    /**
     * Get all conversations for this user (both as tenant and owner).
     */
    public function allConversations()
    {
        return Conversation::where('tenant_id', $this->id)
            ->orWhere('owner_id', $this->id)
            ->orderBy('updated_at', 'desc');
    }

    /**
     * Get conversations with latest message for this user.
     */
    public function getRecentConversations($limit = 10)
    {
        return $this->allConversations()
            ->with(['lastMessage', 'tenant', 'owner'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread messages count for this user (as tenant).
     */
    public function getUnreadMessagesCountAttribute()
    {
        return Message::whereHas('conversation', function($q) {
                $q->where('tenant_id', $this->id);
            })
            ->where('sender_type', 'App\Models\Owner')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get total unread messages count (both as tenant and owner).
     */
    public function getTotalUnreadCountAttribute()
    {
        $asTenant = Message::whereHas('conversation', function($q) {
                $q->where('tenant_id', $this->id);
            })
            ->where('sender_type', 'App\Models\Owner')
            ->where('is_read', false)
            ->count();

        $asOwner = Message::whereHas('conversation', function($q) {
                $q->where('owner_id', $this->id);
            })
            ->where('sender_type', 'App\Models\User')
            ->where('is_read', false)
            ->count();

        return $asTenant + $asOwner;
    }

    /**
     * Send a message to an owner.
     */
    public function sendMessageToOwner($ownerId, $message, $apartmentId = null, $businessSpaceId = null)
    {
        $conversation = Conversation::firstOrCreate(
            [
                'tenant_id' => $this->id,
                'owner_id' => $ownerId,
            ],
            [
                'apartment_id' => $apartmentId,
                'business_space_id' => $businessSpaceId,
                'subject' => 'New inquiry',
            ]
        );

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->id,
            'sender_type' => 'App\Models\User',
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BUSINESS RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function businessSpaces()
    {
        return $this->hasMany(BusinessSpace::class, 'owner_id');
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        return asset('images/default-avatar.png');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?? 'User';
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word) && strlen($initials) < 2) {
                $initials .= strtoupper($word[0]);
            }
        }
        return $initials ?: 'U';
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
}
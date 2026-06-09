<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender (polymorphic: User for tenants, Owner for owners).
     */
    public function sender()
    {
        return $this->morphTo('sender', 'sender_type', 'sender_id');
    }

    /**
     * Check if the message was sent by an owner.
     */
    public function isFromOwner()
    {
        return $this->sender_type === 'App\Models\Owner';
    }

    /**
     * Check if the message was sent by a user/tenant.
     */
    public function isFromUser()
    {
        return $this->sender_type === 'App\Models\User';
    }

    /**
     * Get the sender name.
     */
    public function getSenderNameAttribute()
    {
        if ($this->isFromOwner()) {
            $owner = Owner::find($this->sender_id);
            return $owner ? $owner->name : 'Owner';
        }
        $user = User::find($this->sender_id);
        return $user ? $user->name : 'User';
    }

    /**
     * Get the sender avatar.
     */
    public function getSenderAvatarAttribute()
    {
        if ($this->isFromOwner()) {
            $owner = Owner::find($this->sender_id);
            if ($owner && $owner->profile_photo_url) {
                return asset($owner->profile_photo_url);
            }
            return 'https://ui-avatars.com/api/?background=00A2FF&color=fff&name=' . urlencode($this->getSenderNameAttribute());
        }
        
        $user = User::find($this->sender_id);
        if ($user && $user->avatar) {
            return asset('storage/' . $user->avatar);
        }
        return 'https://ui-avatars.com/api/?background=6c757d&color=fff&name=' . urlencode($this->getSenderNameAttribute());
    }

    /**
     * Get the sender role label.
     */
    public function getSenderRoleAttribute()
    {
        return $this->isFromOwner() ? 'Owner' : 'Tenant';
    }

    /**
     * Check if the message is the latest in its conversation.
     */
    public function getIsLatestAttribute()
    {
        $lastMessage = $this->conversation->lastMessage;
        return $lastMessage && $lastMessage->id === $this->id;
    }

    /**
     * Mark this message as read.
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            return $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        return false;
    }

    /**
     * Mark this message as unread.
     */
    public function markAsUnread()
    {
        if ($this->is_read) {
            return $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        }
        return false;
    }

    /**
     * Get formatted created_at time (e.g., "2:30 PM").
     */
    public function getTimeAttribute()
    {
        return $this->created_at ? $this->created_at->format('h:i A') : '';
    }

    /**
     * Get formatted date (e.g., "Jan 15, 2024").
     */
    public function getDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : '';
    }

    /**
     * Get formatted datetime (e.g., "Jan 15, 2024 at 2:30 PM").
     */
    public function getDateTimeAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y \a\t h:i A') : '';
    }

    /**
     * Get time ago (e.g., "5 minutes ago").
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * Get short message preview.
     */
    public function getPreviewAttribute($length = 50)
    {
        if (empty($this->message)) {
            return '';
        }
        return strlen($this->message) > $length 
            ? substr($this->message, 0, $length) . '...' 
            : $this->message;
    }

    // ==================== SCOPES ====================

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read messages.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get messages from owners.
     */
    public function scopeFromOwner($query)
    {
        return $query->where('sender_type', 'App\Models\Owner');
    }

    /**
     * Scope to get messages from users/tenants.
     */
    public function scopeFromUser($query)
    {
        return $query->where('sender_type', 'App\Models\User');
    }

    /**
     * Scope to get messages for a specific conversation.
     */
    public function scopeInConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope to get latest messages first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get oldest messages first (for chat display).
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope to get messages for a specific owner.
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->whereHas('conversation', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });
    }

    /**
     * Scope to get messages for a specific tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->whereHas('conversation', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        });
    }

    /**
     * Scope to get unread messages for owner.
     */
    public function scopeUnreadForOwner($query, $ownerId)
    {
        return $query->forOwner($ownerId)
            ->fromUser()
            ->unread();
    }

    /**
     * Scope to get unread messages for tenant.
     */
    public function scopeUnreadForTenant($query, $tenantId)
    {
        return $query->forTenant($tenantId)
            ->fromOwner()
            ->unread();
    }

    // ==================== STATIC METHODS ====================

    /**
     * Get unread count for the authenticated user.
     */
    public static function getUnreadCountForAuthUser()
    {
        if (Auth::guard('owner')->check()) {
            $ownerId = Auth::guard('owner')->id();
            return static::unreadForOwner($ownerId)->count();
        }
        
        if (Auth::check()) {
            $userId = Auth::id();
            return static::unreadForTenant($userId)->count();
        }
        
        return 0;
    }

    /**
     * Get all unread messages for a user.
     */
    public static function getUnreadMessagesForUser($userId, $userType = 'tenant')
    {
        if ($userType === 'owner') {
            return static::forOwner($userId)->fromUser()->unread()->get();
        }
        return static::forTenant($userId)->fromOwner()->unread()->get();
    }

    /**
     * Mark all messages as read for a conversation and user type.
     */
    public static function markAllAsRead($conversationId, $userType)
    {
        $query = static::where('conversation_id', $conversationId)->where('is_read', false);
        
        if ($userType === 'owner') {
            $query->where('sender_type', 'App\Models\User');
        } else {
            $query->where('sender_type', 'App\Models\Owner');
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    // ==================== BOOT METHOD ====================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update conversation's updated_at when a new message is created
        static::created(function ($message) {
            if ($message->conversation) {
                $message->conversation->touch();
                $message->conversation->update([
                    'last_message_at' => $message->created_at
                ]);
            }
        });

        // Also update when message is updated (like marking as read)
        static::updated(function ($message) {
            if ($message->conversation) {
                $message->conversation->touch();
            }
        });
    }
}
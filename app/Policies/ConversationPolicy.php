<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Owner;

class ConversationPolicy
{
    public function view($user, Conversation $conversation)
    {
        // If $user is a tenant (User model)
        if ($user instanceof User) {
            return $conversation->tenant_id === $user->id;
        }
        
        // If $user is an owner (Owner model)
        if ($user instanceof Owner) {
            return $conversation->owner_id === $user->id;
        }
        
        return false;
    }
}
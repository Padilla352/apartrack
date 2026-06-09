<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // $user here is the authenticated user from 'web' guard (tenants)
    // We need to also handle owner guard separately because Broadcast::channel only works with one guard by default.
    // For simplicity, we'll check both guards inside.
    
    $conversation = Conversation::find($conversationId);
    if (!$conversation) return false;

    // Check if the authenticated user is a tenant (web guard)
    if ($user && $conversation->tenant_id == $user->id) {
        return true;
    }

    // Check if the authenticated user is an owner (via owner guard)
    if (auth()->guard('owner')->check() && $conversation->owner_id == auth()->guard('owner')->id()) {
        return true;
    }

    return false;
});
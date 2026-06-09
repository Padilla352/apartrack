<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends \App\Http\Controllers\Controller
{
    /**
     * Get messages between authenticated user and owner (JSON API).
     * Used by Flutter app.
     */
    public function getMessages(int $ownerId, int $lastId = 0): \Illuminate\Http\JsonResponse
    {
        $messages = Chat::where(function ($q) use ($ownerId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $ownerId);
        })->orWhere(function ($q) use ($ownerId) {
            $q->where('sender_id', $ownerId)->where('receiver_id', Auth::id());
        })
        ->where('id', '>', $lastId)
        ->orderBy('id')
        ->get();

        // Mark received messages as read
        Chat::where('receiver_id', Auth::id())
            ->where('sender_id', $ownerId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages->values()->toArray());
    }

    /**
     * Send a message to an owner (JSON API).
     * Used by Flutter app.
     */
    public function sendMessage(Request $request, int $ownerId): \Illuminate\Http\JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $ownerId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message_id' => $chat->id,
            'message' => $chat->message,
            'created_at' => $chat->created_at,
        ], 201);
    }

    /**
     * Legacy view method for web (kept for compatibility).
     */
    public function show($ownerId)
    {
        $owner = User::find($ownerId);

        if (!$owner) {
            $owner = new User();
            $owner->id = $ownerId;
            $demoNames = [
                101 => "Juan Dela Cruz",
                102 => "Maria Santos",
                103 => "Antonio Reyes",
                104 => "Luzviminda Fernandez",
                105 => "Ramon Garcia",
            ];
            $owner->name = $demoNames[$ownerId] ?? "Apartment Owner";
            $owner->email = "owner@example.com";
        }

        $messages = Chat::where(function ($q) use ($ownerId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $ownerId);
        })->orWhere(function ($q) use ($ownerId) {
            $q->where('sender_id', $ownerId)->where('receiver_id', Auth::id());
        })->orderBy('id')->get();

        $lastMessageId = $messages->last()->id ?? 0;

        Chat::where('receiver_id', Auth::id())->where('sender_id', $ownerId)->update(['is_read' => true]);

        return view('chat.owner', compact('owner', 'messages', 'lastMessageId'));
    }

    public function send(Request $request, $ownerId)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $ownerId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message_id' => $chat->id
        ]);
    }

    public function poll($ownerId, Request $request)
    {
        $lastId = $request->get('last_id', 0);
        $messages = Chat::where(function ($q) use ($ownerId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $ownerId);
        })->orWhere(function ($q) use ($ownerId) {
            $q->where('sender_id', $ownerId)->where('receiver_id', Auth::id());
        })->where('id', '>', $lastId)->orderBy('id')->get();

        foreach ($messages as $msg) {
            if ($msg->receiver_id == Auth::id()) {
                $msg->update(['is_read' => true]);
            }
            $msg->formatted_time = $msg->created_at->format('g:i A');
        }

        return response()->json(['messages' => $messages]);
    }
}
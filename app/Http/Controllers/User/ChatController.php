<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Owner;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\TypingEvent;

class ChatController extends Controller
{
    // Start or get conversation (tenant side)
    public function start($ownerId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $tenantId = auth()->id();
        Owner::findOrFail($ownerId); // ensure owner exists

        $conversation = Conversation::firstOrCreate([
            'tenant_id' => $tenantId,
            'owner_id'  => $ownerId,
        ]);

        return redirect()->route('chat.show', $conversation->id);
    }

    // Show chat view (works for both tenant and owner)
    public function show(Conversation $conversation)
    {
        $conversations = collect();

        if (auth()->check()) {
            if ($conversation->tenant_id != auth()->id()) {
                abort(403);
            }
            $myType = 'tenant'; // for view only
            $myId = auth()->id();
            $otherUser = $conversation->owner;
            $isOwner = false;

            $conversations = Conversation::where('tenant_id', $myId)
                ->with(['owner', 'lastMessage'])
                ->orderBy('updated_at', 'desc')
                ->get();
        } elseif (auth()->guard('owner')->check()) {
            if ($conversation->owner_id != auth()->guard('owner')->id()) {
                abort(403);
            }
            $myType = 'owner'; // for view only
            $myId = auth()->guard('owner')->id();
            $otherUser = $conversation->tenant;
            $isOwner = true;

            $conversations = Conversation::where('owner_id', $myId)
                ->with(['tenant', 'lastMessage'])
                ->orderBy('updated_at', 'desc')
                ->get();
        } else {
            abort(403);
        }

        return view('user.chat.show', compact('conversation', 'otherUser', 'myType', 'myId', 'isOwner', 'conversations'));
    }

    // Send message (returns JSON always)
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'message' => 'required|string|max:5000',
            ]);

            $conversation = Conversation::findOrFail($request->conversation_id);

            if (auth()->check()) {
                if ($conversation->tenant_id != auth()->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                $senderId = auth()->id();
                $senderType = 'App\Models\User'; // full namespace for tenant
            } elseif (auth()->guard('owner')->check()) {
                if ($conversation->owner_id != auth()->guard('owner')->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                $senderId = auth()->guard('owner')->id();
                $senderType = 'App\Models\Owner'; // full namespace for owner
            } else {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $message->load('sender');
            broadcast(new MessageSent($message))->toOthers();

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Fetch messages (returns JSON always)
    public function fetchMessages(Conversation $conversation)
    {
        try {
            $this->authorizeParticipant($conversation);
            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->paginate(50);
            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    // Mark as read
    public function markAsRead(Conversation $conversation)
    {
        try {
            $this->authorizeParticipant($conversation);

            $isOwner = auth()->guard('owner')->check();
            // Use full namespace for comparison
            $mySenderType = $isOwner ? 'App\Models\Owner' : 'App\Models\User';

            $updated = Message::where('conversation_id', $conversation->id)
                ->where('is_read', false)
                ->where('sender_type', '!=', $mySenderType)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            if ($updated) {
                $myId = $isOwner ? auth()->guard('owner')->id() : auth()->id();
                $myTypeString = $isOwner ? 'owner' : 'tenant'; // for event
                broadcast(new MessageRead($conversation, $myId, $myTypeString))->toOthers();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    // Typing indicator
    public function typing(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'is_typing' => 'required|boolean',
            ]);

            $conversation = Conversation::findOrFail($request->conversation_id);
            $this->authorizeParticipant($conversation);

            $isOwner = auth()->guard('owner')->check();
            $myId = $isOwner ? auth()->guard('owner')->id() : auth()->id();
            $myType = $isOwner ? 'owner' : 'tenant'; // for event

            broadcast(new TypingEvent($conversation, $myId, $myType, $request->is_typing))->toOthers();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    // Helper: authorize participant
    private function authorizeParticipant(Conversation $conversation)
    {
        if (auth()->check()) {
            if ($conversation->tenant_id != auth()->id()) abort(403);
        } elseif (auth()->guard('owner')->check()) {
            if ($conversation->owner_id != auth()->guard('owner')->id()) abort(403);
        } else {
            abort(403);
        }
    }

    // Conversation list for owner
    public function indexForOwner()
    {
        $ownerId = auth()->guard('owner')->id();
        $conversations = Conversation::where('owner_id', $ownerId)
            ->with(['tenant', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('owner.messages.index', compact('conversations'));
    }

    // Conversation list for tenant
    public function indexForTenant()
    {
        $tenantId = auth()->id();
        $conversations = Conversation::where('tenant_id', $tenantId)
            ->with(['owner', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('tenant.chat.index', compact('conversations'));
    }
}
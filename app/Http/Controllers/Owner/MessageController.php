<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Apartment;
use App\Models\BusinessSpace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Display list of conversations for the owner.
     */
    public function index()
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            // Get all conversations where this owner is the owner
            $conversations = Conversation::where('owner_id', $ownerId)
                ->with(['tenant', 'apartment', 'businessSpace', 'lastMessage'])
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $unreadCount = $this->getUnreadCountValue();
            
            return view('owner.messages.index', compact('conversations', 'unreadCount'));
        } catch (\Exception $e) {
            Log::error('MessageController index error: ' . $e->getMessage());
            // Return empty conversations if error occurs
            $conversations = collect();
            $unreadCount = 0;
            return view('owner.messages.index', compact('conversations', 'unreadCount'));
        }
    }
    
    /**
     * Show a specific conversation.
     */
    public function show(Conversation $conversation)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            // Ensure the owner owns this conversation
            if ($conversation->owner_id !== $ownerId) {
                abort(403);
            }
            
            // Mark messages from tenant as read
            $conversation->messages()
                ->where('sender_type', 'App\Models\User')
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            
            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get();
            
            $otherUser = $conversation->tenant;
            $listing = $conversation->apartment ?? $conversation->businessSpace;
            
            // Get all conversations for sidebar
            $conversations = Conversation::where('owner_id', $ownerId)
                ->with(['tenant', 'apartment', 'businessSpace', 'lastMessage'])
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $unreadCount = $this->getUnreadCountValue();
            
            return view('owner.messages.show', compact('conversation', 'messages', 'otherUser', 'listing', 'conversations', 'unreadCount'));
        } catch (\Exception $e) {
            Log::error('MessageController show error: ' . $e->getMessage());
            return redirect()->route('owner.messages.index')->with('error', 'Unable to load conversation.');
        }
    }
    
    /**
     * Show form to compose a new message.
     */
    public function create()
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            // Get all tenants (users)
            $tenants = User::where(function($q) {
                    $q->where('role', 'tenant')
                      ->orWhere('role', 'user')
                      ->orWhereNull('role');
                })
                ->orderBy('name')
                ->get();
            
            if ($tenants->isEmpty()) {
                $tenants = User::all();
            }
            
            // Get owner's listings for reference
            $apartments = Apartment::where('owner_id', $ownerId)->get();
            $businessSpaces = BusinessSpace::where('owner_id', $ownerId)->get();
            
            return view('owner.messages.create', compact('tenants', 'apartments', 'businessSpaces'));
        } catch (\Exception $e) {
            Log::error('MessageController create error: ' . $e->getMessage());
            return redirect()->route('owner.messages.index')->with('error', 'Unable to load message form.');
        }
    }
    
    /**
     * Send a new message to a tenant (start a conversation).
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tenant_id' => 'required|exists:users,id',
                'message' => 'required|string|max:5000',
                'apartment_id' => 'nullable|exists:apartments,id',
                'business_space_id' => 'nullable|exists:business_spaces,id',
            ]);
            
            $ownerId = Auth::guard('owner')->id();
            $tenantId = $request->tenant_id;
            
            // Find or create conversation
            $conversation = Conversation::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'owner_id' => $ownerId,
                ],
                [
                    'apartment_id' => $request->apartment_id,
                    'business_space_id' => $request->business_space_id,
                    'subject' => $this->generateSubject($request->apartment_id, $request->business_space_id),
                ]
            );
            
            // Update apartment/business space if provided
            if ($request->apartment_id && !$conversation->apartment_id) {
                $conversation->update(['apartment_id' => $request->apartment_id]);
            }
            if ($request->business_space_id && !$conversation->business_space_id) {
                $conversation->update(['business_space_id' => $request->business_space_id]);
            }
            
            // Create the message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $ownerId,
                'sender_type' => 'App\Models\Owner',
                'message' => $request->message,
                'is_read' => false,
            ]);
            
            $conversation->touch();
            
            return redirect()->route('owner.messages.show', $conversation)
                ->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            Log::error('MessageController store error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }
    
    /**
     * Start a conversation from a listing (apartment or business space).
     */
    public function startConversation(Request $request)
    {
        try {
            $request->validate([
                'listing_id' => 'required|integer',
                'listing_type' => 'required|in:apartment,business',
                'message' => 'required|string|max:5000',
                'tenant_id' => 'nullable|exists:users,id',
            ]);
            
            $ownerId = Auth::guard('owner')->id();
            $tenantId = $request->tenant_id ?? 1;
            
            // Get the listing and verify ownership
            if ($request->listing_type === 'apartment') {
                $listing = Apartment::findOrFail($request->listing_id);
                if ($listing->owner_id !== $ownerId) {
                    abort(403, 'You do not own this property');
                }
                
                $conversation = Conversation::firstOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'owner_id' => $ownerId,
                    ],
                    [
                        'apartment_id' => $request->listing_id,
                        'subject' => 'Inquiry about: ' . $listing->name,
                    ]
                );
                
                if (!$conversation->apartment_id) {
                    $conversation->update(['apartment_id' => $request->listing_id]);
                }
            } else {
                $listing = BusinessSpace::findOrFail($request->listing_id);
                if ($listing->owner_id !== $ownerId) {
                    abort(403, 'You do not own this business space');
                }
                
                $conversation = Conversation::firstOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'owner_id' => $ownerId,
                    ],
                    [
                        'business_space_id' => $request->listing_id,
                        'subject' => 'Inquiry about: ' . $listing->business_name,
                    ]
                );
                
                if (!$conversation->business_space_id) {
                    $conversation->update(['business_space_id' => $request->listing_id]);
                }
            }
            
            // Create the message
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $tenantId,
                'sender_type' => 'App\Models\User',
                'message' => $request->message,
                'is_read' => false,
            ]);
            
            $conversation->touch();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('owner.messages.show', $conversation->id),
                    'message' => 'Conversation started successfully!'
                ]);
            }
            
            return redirect()->route('owner.messages.show', $conversation->id)
                ->with('success', 'Message sent to tenant.');
        } catch (\Exception $e) {
            Log::error('MessageController startConversation error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to start conversation: ' . $e->getMessage());
        }
    }
    
    /**
     * Reply to a conversation.
     */
    public function reply(Request $request, Conversation $conversation)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:5000',
            ]);
            
            $ownerId = Auth::guard('owner')->id();
            
            if ($conversation->owner_id !== $ownerId) {
                abort(403);
            }
            
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $ownerId,
                'sender_type' => 'App\Models\Owner',
                'message' => $request->message,
                'is_read' => false,
            ]);
            
            $conversation->touch();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'message' => $message->message,
                        'created_at' => $message->created_at->format('M d, h:i A'),
                        'is_sent' => true,
                    ]
                ]);
            }
            
            return redirect()->back()->with('success', 'Reply sent.');
        } catch (\Exception $e) {
            Log::error('MessageController reply error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to send reply.');
        }
    }
    
    /**
     * Fetch messages for a conversation (for AJAX real-time chat).
     */
    public function fetchMessages(Conversation $conversation)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            // Verify ownership
            if ($conversation->owner_id !== $ownerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Mark messages from user as read
            $conversation->messages()
                ->where('sender_type', 'App\Models\User')
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            
            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'is_from_owner' => $message->isFromOwner(),
                        'sender_name' => $message->getSenderNameAttribute(),
                        'sender_avatar' => $message->getSenderAvatarAttribute(),
                        'time' => $message->getTimeAttribute(),
                        'time_ago' => $message->getTimeAgoAttribute(),
                        'created_at' => $message->created_at->toISOString(),
                    ];
                });
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'conversation_id' => $conversation->id,
            ]);
        } catch (\Exception $e) {
            Log::error('fetchMessages error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Send a message via AJAX (for real-time chat).
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:5000',
            ]);
            
            $ownerId = Auth::guard('owner')->id();
            
            // Verify ownership
            if ($conversation->owner_id !== $ownerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $ownerId,
                'sender_type' => 'App\Models\Owner',
                'message' => $request->message,
                'is_read' => false,
            ]);
            
            // Update conversation timestamp
            $conversation->touch();
            
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_from_owner' => true,
                    'sender_name' => $message->getSenderNameAttribute(),
                    'sender_avatar' => $message->getSenderAvatarAttribute(),
                    'time' => $message->getTimeAttribute(),
                    'time_ago' => $message->getTimeAgoAttribute(),
                    'created_at' => $message->created_at->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('sendMessage error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get unread count for AJAX polling.
     */
    public function getUnreadCount()
    {
        try {
            return response()->json(['count' => $this->getUnreadCountValue()]);
        } catch (\Exception $e) {
            Log::error('MessageController getUnreadCount error: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }
    
    /**
     * Helper to compute unread messages count for the owner.
     */
    private function getUnreadCountValue()
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            return Message::whereHas('conversation', function($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })
                ->where('sender_type', 'App\Models\User')
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            Log::error('getUnreadCountValue error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent conversations for dashboard widget.
     */
    public function getRecentMessages()
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            $conversations = Conversation::where('owner_id', $ownerId)
                ->with(['tenant', 'apartment', 'businessSpace', 'lastMessage'])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($conv) {
                    $lastMsg = $conv->lastMessage;
                    $listingName = $conv->apartment ? $conv->apartment->name : ($conv->businessSpace ? $conv->businessSpace->business_name : 'Listing');
                    
                    return [
                        'id' => $conv->id,
                        'tenant_name' => $conv->tenant->name ?? 'Guest',
                        'listing_name' => $listingName,
                        'preview' => $lastMsg ? substr($lastMsg->message, 0, 50) : 'No messages',
                        'time_ago' => $conv->updated_at->diffForHumans(),
                        'is_unread' => $lastMsg && $lastMsg->sender_type === 'App\Models\User' && !$lastMsg->is_read,
                    ];
                });
            
            return response()->json($conversations);
        } catch (\Exception $e) {
            Log::error('getRecentMessages error: ' . $e->getMessage());
            return response()->json([]);
        }
    }
    
    /**
     * Delete a conversation.
     */
    public function destroy(Conversation $conversation)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            if ($conversation->owner_id !== $ownerId) {
                abort(403);
            }
            
            $conversation->messages()->delete();
            $conversation->delete();
            
            return redirect()->route('owner.messages.index')
                ->with('success', 'Conversation deleted.');
        } catch (\Exception $e) {
            Log::error('MessageController destroy error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete conversation.');
        }
    }
    
    /**
     * Generate a subject for the conversation.
     */
    private function generateSubject($apartmentId, $businessSpaceId)
    {
        if ($apartmentId) {
            $apartment = Apartment::find($apartmentId);
            return 'Inquiry about: ' . ($apartment ? $apartment->name : 'Property');
        }
        
        if ($businessSpaceId) {
            $business = BusinessSpace::find($businessSpaceId);
            return 'Inquiry about: ' . ($business ? $business->business_name : 'Business Space');
        }
        
        return 'General Inquiry';
    }
}
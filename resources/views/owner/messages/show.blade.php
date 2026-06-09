@extends('owner.layouts.app')

@section('title', 'Conversation')
@section('page-title', 'conversation')

@section('content')
<div class="messages-wrapper">
    <div class="messages-container">
        <!-- Left Sidebar - Conversations List -->
        <div class="conversations-sidebar">
            <div class="sidebar-header">
                <div class="header-title">
                    <i class="fas fa-comment-dots"></i>
                    <h3>Conversations</h3>
                </div>
                <a href="{{ route('owner.messages.create') }}" class="new-chat-btn" title="New Message">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="search-conversations">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search conversations..." id="searchConversations">
            </div>
            <div class="conversations-list">
                @forelse($conversations as $conv)
                    @php
                        $tenant = $conv->tenant;
                        $personName = $tenant->name ?? 'Tenant';
                        $initials = strtoupper(substr($personName, 0, 2));
                        $lastMessage = $conv->lastMessage;
                        $isUnread = $conv->getUnreadCountAttribute() > 0;
                    @endphp
                    <a href="{{ route('owner.messages.show', $conv->id) }}" 
                       class="conversation-item {{ $conv->id == $conversation->id ? 'active' : '' }} {{ $isUnread ? 'unread' : '' }}">
                        <div class="conversation-avatar">
                            <div class="avatar-placeholder" style="background: {{ $loop->iteration % 2 == 0 ? '#4F46E5' : '#10B981' }}">
                                {{ $initials }}
                            </div>
                            @if($isUnread)
                                <span class="unread-badge"></span>
                            @endif
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">{{ $personName }}</div>
                            <div class="conversation-preview">{{ Str::limit($lastMessage->message ?? '', 45) }}</div>
                            <div class="conversation-time">{{ $conv->updated_at->diffForHumans() }}</div>
                        </div>
                        @if($isUnread)
                            <div class="unread-indicator"><span class="unread-count">●</span></div>
                        @endif
                    </a>
                @empty
                    <div class="empty-conversations">No conversations yet.</div>
                @endforelse
            </div>
        </div>
        
        <!-- Right Side - Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                <div class="chat-user-info">
                    <div class="chat-avatar">
                        <div class="avatar-placeholder large" style="background: #4F46E5">
                            {{ strtoupper(substr($otherUser->name ?? 'T', 0, 2)) }}
                        </div>
                    </div>
                    <div class="chat-user-details">
                        <h3>{{ $otherUser->name ?? 'Tenant' }}</h3>
                        <div class="user-status offline">Tenant</div>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="chat-action-btn" onclick="window.print()"><i class="fas fa-print"></i></button>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                @php $currentDate = ''; @endphp
                @foreach($messages as $msg)
                    @php
                        $msgDate = $msg->created_at->format('Y-m-d');
                        if ($msgDate != $currentDate) {
                            $currentDate = $msgDate;
                            $displayDate = $msg->created_at->isToday() ? 'Today' : $msg->created_at->format('F d, Y');
                            echo '<div class="message-date"><span>' . $displayDate . '</span></div>';
                        }
                    @endphp
                    <div class="message {{ $msg->sender_type == 'App\Models\Owner' ? 'sent' : 'received' }}">
                        <div class="message-bubble">
                            <div class="message-text">{{ nl2br(e($msg->message)) }}</div>
                            <div class="message-time">{{ $msg->created_at->format('h:i A') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="chat-input">
                <div class="input-wrapper">
                    <textarea id="messageInput" class="message-input" placeholder="Type your reply..." rows="1"></textarea>
                    <button type="button" id="sendButton" class="send-button"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .messages-wrapper { position: relative; min-height: calc(100vh - 200px); background: url('{{ asset("images/BINALONAN TOWNHALL.jpg") }}') no-repeat center center fixed; background-size: cover; border-radius: 24px; padding: 20px; }
    .messages-wrapper::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 3, 51, 0.6); border-radius: 24px; z-index: 0; }
    .messages-container { position: relative; z-index: 1; display: flex; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.2); overflow: hidden; min-height: 600px; }
    .conversations-sidebar { width: 360px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-right: 1px solid rgba(255, 255, 255, 0.2); display: flex; flex-direction: column; }
    .sidebar-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 20px 16px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.15); }
    .header-title { display: flex; align-items: center; gap: 10px; }
    .header-title i { font-size: 22px; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .header-title h3 { font-size: 18px; font-weight: 700; color: white; margin: 0; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .new-chat-btn { width: 36px; height: 36px; background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
    .new-chat-btn:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(79,70,229,0.5); color: white; }
    .search-conversations { padding: 16px 20px; position: relative; border-bottom: 1px solid rgba(255,255,255,0.15); }
    .search-conversations i { position: absolute; left: 32px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7); font-size: 14px; }
    .search-conversations input { width: 100%; padding: 10px 16px 10px 40px; border: 1px solid rgba(255,255,255,0.2); border-radius: 30px; font-size: 13px; background: rgba(255,255,255,0.15); color: white; }
    .search-conversations input::placeholder { color: rgba(255,255,255,0.6); }
    .search-conversations input:focus { outline: none; border-color: #4F46E5; background: rgba(255,255,255,0.25); box-shadow: 0 0 0 3px rgba(79,70,229,0.2); }
    .conversations-list { flex: 1; overflow-y: auto; padding: 8px 0; }
    .conversation-item { display: flex; align-items: center; gap: 14px; padding: 14px 20px; text-decoration: none; transition: all 0.2s; position: relative; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .conversation-item:hover { background: rgba(255,255,255,0.15); }
    .conversation-item.active { background: rgba(79,70,229,0.3); border-left: 3px solid #4F46E5; }
    .conversation-item.unread { background: rgba(255,255,255,0.2); }
    .conversation-avatar { position: relative; }
    .avatar-placeholder { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .avatar-placeholder.large { width: 56px; height: 56px; font-size: 20px; }
    .unread-badge { position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #EF4444; border-radius: 50%; border: 2px solid white; }
    .conversation-info { flex: 1; }
    .conversation-name { font-weight: 600; font-size: 14px; color: white; margin-bottom: 4px; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .conversation-item.unread .conversation-name { color: #A78BFA; font-weight: 700; }
    .conversation-preview { font-size: 12px; color: rgba(255,255,255,0.8); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .conversation-time { font-size: 10px; color: rgba(255,255,255,0.6); margin-top: 4px; }
    .unread-indicator { margin-left: 8px; }
    .unread-count { color: #F87171; font-size: 12px; }
    .empty-conversations { text-align: center; padding: 48px 20px; color: white; }
    .chat-area { flex: 1; display: flex; flex-direction: column; background: rgba(255,255,255,0.08); }
    .chat-header { padding: 16px 24px; background: rgba(255,255,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.15); display: flex; justify-content: space-between; align-items: center; }
    .chat-user-info { display: flex; align-items: center; gap: 14px; }
    .chat-user-details h3 { font-size: 16px; font-weight: 700; color: white; margin: 0 0 4px 0; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .user-status { font-size: 11px; display: flex; align-items: center; gap: 6px; }
    .user-status.offline { color: rgba(255,255,255,0.6); }
    .chat-actions { display: flex; gap: 8px; }
    .chat-action-btn { width: 36px; height: 36px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 50%; cursor: pointer; color: white; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .chat-action-btn:hover { background: #4F46E5; transform: scale(1.05); border-color: transparent; }
    .chat-messages { flex: 1; padding: 20px 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; max-height: calc(70vh - 150px); }
    .message-date { text-align: center; margin: 8px 0; }
    .message-date span { background: rgba(0,0,0,0.4); backdrop-filter: blur(5px); padding: 4px 12px; border-radius: 20px; font-size: 11px; color: rgba(255,255,255,0.9); }
    .message { display: flex; max-width: 75%; }
    .message.received { justify-content: flex-start; align-self: flex-start; }
    .message.sent { justify-content: flex-end; align-self: flex-end; margin-left: auto; }
    .message-text { padding: 10px 16px; border-radius: 18px; font-size: 14px; line-height: 1.5; word-wrap: break-word; }
    .message.received .message-text { background: rgba(255,255,255,0.9); color: #1f2937; border-bottom-left-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .message.sent .message-text { background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%); color: white; border-bottom-right-radius: 4px; box-shadow: 0 2px 8px rgba(79,70,229,0.3); }
    .message-time { font-size: 10px; color: rgba(255,255,255,0.7); margin-top: 4px; margin-left: 8px; margin-right: 8px; }
    .message.sent .message-time { text-align: right; }
    .chat-input { padding: 16px 24px 20px; background: rgba(255,255,255,0.08); border-top: 1px solid rgba(255,255,255,0.15); }
    .input-wrapper { display: flex; gap: 12px; align-items: flex-end; background: rgba(255,255,255,0.15); border-radius: 30px; padding: 6px 6px 6px 20px; border: 1px solid rgba(255,255,255,0.2); transition: all 0.2s; }
    .input-wrapper:focus-within { border-color: #4F46E5; background: rgba(255,255,255,0.25); box-shadow: 0 0 0 2px rgba(79,70,229,0.2); }
    .message-input { flex: 1; background: transparent; border: none; padding: 10px 0; font-size: 14px; font-family: inherit; resize: none; max-height: 100px; color: white; }
    .message-input:focus { outline: none; }
    .message-input::placeholder { color: rgba(255,255,255,0.6); }
    .send-button { width: 40px; height: 40px; background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%); border: none; border-radius: 50%; color: white; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .send-button:hover { transform: scale(1.03); box-shadow: 0 2px 8px rgba(79,70,229,0.4); }
    .send-button:disabled { opacity: 0.5; cursor: not-allowed; }
    @media (max-width: 768px) { .conversations-sidebar { width: 300px; } .message { max-width: 85%; } }
    @media (max-width: 640px) { .messages-wrapper { padding: 12px; } .messages-container { flex-direction: column; } .conversations-sidebar { width: 100%; max-height: 320px; } .conversation-item { padding: 12px 16px; } .avatar-placeholder { width: 40px; height: 40px; font-size: 14px; } .message { max-width: 90%; } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const conversationId = {{ $conversation->id }};
        const csrfToken = '{{ csrf_token() }}';
        const messagesContainer = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        
        // Scroll to bottom of messages
        function scrollToBottom() {
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
        
        // Auto-resize textarea
        if (messageInput) {
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });
        }
        
        // Escape HTML helper
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Format time
        function formatTime(date) {
            return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        
        // Fetch messages via AJAX
        async function fetchMessages() {
            try {
                const response = await fetch(`/owner/messages/${conversationId}/fetch`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.messages) {
                    // Check if we need to update (compare last message)
                    const currentLastMsg = messagesContainer.querySelector('.message:last-child .message-text')?.textContent;
                    const newLastMsg = data.messages[data.messages.length - 1]?.message;
                    
                    if (currentLastMsg !== newLastMsg) {
                        // Reload the page to refresh all messages
                        location.reload();
                    }
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }
        
        // Send message via AJAX
        async function sendMessage() {
            const message = messageInput.value.trim();
            
            if (!message) {
                return;
            }
            
            // Disable button and show loading
            sendButton.disabled = true;
            const originalIcon = sendButton.innerHTML;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            try {
                const response = await fetch(`/owner/messages/${conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Clear input
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    
                    // Add message to chat immediately
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message sent';
                    messageDiv.innerHTML = `
                        <div class="message-bubble">
                            <div class="message-text">${escapeHtml(message)}</div>
                            <div class="message-time">Just now</div>
                        </div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                    scrollToBottom();
                    
                    // Reload after short delay to sync with server
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Network error. Please check your connection and try again.');
            } finally {
                sendButton.disabled = false;
                sendButton.innerHTML = originalIcon;
            }
        }
        
        // Search conversations
        const searchInput = document.getElementById('searchConversations');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.conversation-item').forEach(item => {
                    const name = item.querySelector('.conversation-name')?.textContent.toLowerCase() || '';
                    const preview = item.querySelector('.conversation-preview')?.textContent.toLowerCase() || '';
                    item.style.display = (name.includes(term) || preview.includes(term)) ? 'flex' : 'none';
                });
            });
        }
        
        // Event listeners
        if (sendButton) {
            sendButton.addEventListener('click', sendMessage);
        }
        
        if (messageInput) {
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }
        
        // Initial scroll to bottom
        scrollToBottom();
        
        // Poll for new messages every 5 seconds
        let interval = setInterval(fetchMessages, 5000);
        
        // Cleanup interval on page unload
        window.addEventListener('beforeunload', () => {
            if (interval) clearInterval(interval);
        });
    });
</script>
@endsection
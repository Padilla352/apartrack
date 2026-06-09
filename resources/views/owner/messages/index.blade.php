@extends('owner.layouts.app')

@section('title', 'Messages')
@section('page-title', 'messages')

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
            
            <div class="conversations-list" id="conversationsList">
                @forelse($conversations as $conv)
                    @php
                        $tenant = $conv->tenant;
                        $personName = $tenant->name ?? 'Tenant';
                        $initials = strtoupper(substr($personName, 0, 2));
                        $lastMessage = $conv->lastMessage;
                        $isUnread = $conv->getUnreadCountAttribute() > 0;
                    @endphp
                    <a href="{{ route('owner.messages.show', $conv->id) }}" 
                       class="conversation-item {{ (isset($currentConversation) && $currentConversation->id == $conv->id) ? 'active' : '' }}
                              {{ $isUnread ? 'unread' : '' }}">
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
                            <div class="conversation-preview">{{ Str::limit($lastMessage->message ?? 'Start a conversation', 45) }}</div>
                            <div class="conversation-time">{{ $conv->updated_at->diffForHumans() }}</div>
                        </div>
                        @if($isUnread)
                            <div class="unread-indicator">
                                <span class="unread-count">●</span>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="empty-conversations">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h4>No messages yet</h4>
                        <p>When tenants contact you, conversations will appear here</p>
                        <a href="{{ route('owner.messages.create') }}" class="btn-empty-action">
                            <i class="fas fa-plus"></i> Send New Message
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Right Side - Chat Area -->
        <div class="chat-area">
            @if(isset($currentConversation))
                @php
                    $tenant = $currentConversation->tenant;
                    $personName = $tenant->name ?? 'Tenant';
                    $initials = strtoupper(substr($personName, 0, 2));
                @endphp
                
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-user-info">
                        <div class="chat-avatar">
                            <div class="avatar-placeholder large" style="background: #4F46E5">
                                {{ $initials }}
                            </div>
                        </div>
                        <div class="chat-user-details">
                            <h3>{{ $personName }}</h3>
                            <div class="user-status offline">
                                <span class="status-dot"></span> Tenant
                            </div>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button class="chat-action-btn" onclick="window.print()" title="Print">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Chat Messages -->
                <div class="chat-messages" id="chatMessages">
                    @php
                        $messages = $currentConversation->messages()->orderBy('created_at', 'asc')->get();
                        $currentDate = '';
                    @endphp
                    
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
                
                <!-- Message Input -->
                <div class="chat-input">
                    <form action="{{ route('owner.messages.reply', $currentConversation->id) }}" method="POST" id="replyForm" class="input-form">
                        @csrf
                        <div class="input-wrapper">
                            <textarea name="message" class="message-input" placeholder="Type your message here..." rows="1" required></textarea>
                            <button type="submit" class="send-button">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- No Conversation Selected -->
                <div class="no-conversation">
                    <div class="no-conversation-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <h3>Welcome to Messages</h3>
                        <p>Select a conversation from the sidebar to start messaging</p>
                        <a href="{{ route('owner.messages.create') }}" class="btn-new-conversation">
                            <i class="fas fa-plus"></i> New Conversation
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* ========== GLASSMORPHISM STYLES ========== */
    .messages-wrapper {
        position: relative;
        min-height: calc(100vh - 200px);
        background: url('{{ asset("images/BINALONAN TOWNHALL.jpg") }}') no-repeat center center fixed;
        background-size: cover;
        border-radius: 24px;
        padding: 20px;
    }
    .messages-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 3, 51, 0.6);
        border-radius: 24px;
        z-index: 0;
    }
    .messages-container {
        position: relative;
        z-index: 1;
        display: flex;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        min-height: 600px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .conversations-sidebar {
        width: 360px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
    }
    .sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 20px 16px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }
    .header-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .header-title i {
        font-size: 22px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .header-title h3 {
        font-size: 18px;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .new-chat-btn {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    .new-chat-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.5);
        color: white;
    }
    .search-conversations {
        padding: 16px 20px;
        position: relative;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }
    .search-conversations i {
        position: absolute;
        left: 32px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
    }
    .search-conversations input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 30px;
        font-size: 13px;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        transition: all 0.2s;
    }
    .search-conversations input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    .search-conversations input:focus {
        outline: none;
        border-color: #4F46E5;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 0;
    }
    .conversation-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
        cursor: pointer;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .conversation-item:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    .conversation-item.active {
        background: rgba(79, 70, 229, 0.3);
        border-left: 3px solid #4F46E5;
    }
    .conversation-item.unread {
        background: rgba(255, 255, 255, 0.2);
    }
    .conversation-avatar {
        position: relative;
    }
    .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .avatar-placeholder.large {
        width: 56px;
        height: 56px;
        font-size: 20px;
    }
    .unread-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background: #EF4444;
        border-radius: 50%;
        border: 2px solid white;
    }
    .conversation-info {
        flex: 1;
    }
    .conversation-name {
        font-weight: 600;
        font-size: 14px;
        color: white;
        margin-bottom: 4px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .conversation-item.unread .conversation-name {
        color: #A78BFA;
        font-weight: 700;
    }
    .conversation-preview {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .conversation-time {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 4px;
    }
    .unread-indicator {
        margin-left: 8px;
    }
    .unread-count {
        color: #F87171;
        font-size: 12px;
    }
    .empty-conversations {
        text-align: center;
        padding: 48px 20px;
    }
    .empty-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    .empty-icon i {
        font-size: 36px;
        color: rgba(255, 255, 255, 0.7);
    }
    .empty-conversations h4 {
        font-size: 16px;
        color: white;
        margin-bottom: 8px;
    }
    .empty-conversations p {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 20px;
    }
    .btn-empty-action {
        background: #4F46E5;
        color: white;
        padding: 8px 18px;
        border-radius: 30px;
        text-decoration: none;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-empty-action:hover {
        background: #6366F1;
        transform: translateY(-2px);
        color: white;
    }
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.08);
    }
    .chat-header {
        padding: 16px 24px;
        background: rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-user-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .chat-user-details h3 {
        font-size: 16px;
        font-weight: 700;
        color: white;
        margin: 0 0 4px 0;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .user-status {
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .user-status.online {
        color: #34D399;
    }
    .user-status.offline {
        color: rgba(255, 255, 255, 0.6);
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .user-status.online .status-dot {
        background: #34D399;
        box-shadow: 0 0 0 2px rgba(52, 211, 153, 0.3);
    }
    .user-status.offline .status-dot {
        background: rgba(255, 255, 255, 0.5);
    }
    .chat-actions {
        display: flex;
        gap: 8px;
    }
    .chat-action-btn {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        cursor: pointer;
        color: white;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chat-action-btn:hover {
        background: #4F46E5;
        transform: scale(1.05);
        border-color: transparent;
    }
    .chat-messages {
        flex: 1;
        padding: 20px 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .message-date {
        text-align: center;
        margin: 8px 0;
    }
    .message-date span {
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(5px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.9);
    }
    .message {
        display: flex;
        max-width: 75%;
    }
    .message.received {
        justify-content: flex-start;
        align-self: flex-start;
    }
    .message.sent {
        justify-content: flex-end;
        align-self: flex-end;
        margin-left: auto;
    }
    .message-bubble {
        max-width: 100%;
    }
    .message-text {
        padding: 10px 16px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.5;
        word-wrap: break-word;
    }
    .message.received .message-text {
        background: rgba(255, 255, 255, 0.9);
        color: #1f2937;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .message.sent .message-text {
        background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
        color: white;
        border-bottom-right-radius: 4px;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
    }
    .message-time {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 4px;
        margin-left: 8px;
        margin-right: 8px;
    }
    .message.sent .message-time {
        text-align: right;
    }
    .chat-input {
        padding: 16px 24px 20px;
        background: rgba(255, 255, 255, 0.08);
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }
    .input-form {
        width: 100%;
    }
    .input-wrapper {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        padding: 6px 6px 6px 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s;
    }
    .input-wrapper:focus-within {
        border-color: #4F46E5;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    .message-input {
        flex: 1;
        background: transparent;
        border: none;
        padding: 10px 0;
        font-size: 14px;
        font-family: inherit;
        resize: none;
        max-height: 100px;
        color: white;
    }
    .message-input:focus {
        outline: none;
    }
    .message-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    .send-button {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .send-button:hover {
        transform: scale(1.03);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4);
    }
    .no-conversation {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .no-conversation-content {
        text-align: center;
        max-width: 300px;
    }
    .icon-wrapper {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .icon-wrapper i {
        font-size: 36px;
        color: rgba(255, 255, 255, 0.8);
    }
    .no-conversation-content h3 {
        font-size: 18px;
        font-weight: 600;
        color: white;
        margin-bottom: 8px;
    }
    .no-conversation-content p {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 24px;
    }
    .btn-new-conversation {
        background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-new-conversation:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        color: white;
    }
    .conversations-list::-webkit-scrollbar, .chat-messages::-webkit-scrollbar {
        width: 4px;
    }
    .conversations-list::-webkit-scrollbar-track, .chat-messages::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .conversations-list::-webkit-scrollbar-thumb, .chat-messages::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }
    @media (max-width: 768px) {
        .conversations-sidebar { width: 300px; }
        .message { max-width: 85%; }
        .chat-header { padding: 12px 16px; }
        .chat-messages { padding: 16px; }
        .chat-input { padding: 12px 16px; }
        .input-wrapper { padding: 4px 4px 4px 16px; }
    }
    @media (max-width: 640px) {
        .messages-wrapper { padding: 12px; }
        .messages-container { flex-direction: column; }
        .conversations-sidebar { width: 100%; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.2); max-height: 320px; }
        .conversation-item { padding: 12px 16px; }
        .avatar-placeholder { width: 40px; height: 40px; font-size: 14px; }
        .message { max-width: 90%; }
        .message-text { padding: 8px 14px; font-size: 13px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chatMessages');
        if (container) container.scrollTop = container.scrollHeight;
        
        const textarea = document.querySelector('.message-input');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });
        }
        
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
        
        const replyForm = document.getElementById('replyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const input = this.querySelector('.message-input');
                const message = input.value.trim();
                if (!message) return;
                const btn = this.querySelector('.send-button');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                try {
                    const formData = new FormData(this);
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        const newMsg = document.createElement('div');
                        newMsg.className = 'message sent';
                        newMsg.innerHTML = `<div class="message-bubble"><div class="message-text">${escapeHtml(message)}</div><div class="message-time">Just now</div></div>`;
                        document.getElementById('chatMessages').appendChild(newMsg);
                        input.value = '';
                        input.style.height = 'auto';
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                } catch (err) {
                    alert('Network error');
                } finally {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    });
</script>
@endsection
@extends('layouts.app')

@section('title', 'Chat - Municipality of Binalonan')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: #eef2ff;
        height: 100vh;
        overflow: hidden;
    }

    /* Fixed na container - hindi na mag-aadjust */
    .chat-container {
        max-width: 1400px;
        margin: 20px auto;
        height: calc(100vh - 40px);
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        overflow: hidden;
    }

    /* Sidebar - fixed width */
    .chat-sidebar {
        width: 320px;
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
        background: white;
        flex-shrink: 0;
    }

    .sidebar-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1e3a8a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-header p {
        font-size: 0.7rem;
        color: #3b82f6;
        margin-top: 5px;
    }

    .conv-list {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }

    .conv-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.2s;
        margin-bottom: 4px;
    }

    .conv-item:hover {
        background: #eef2ff;
    }

    .conv-item.active {
        background: #dbeafe;
    }

    .conv-avatar {
        width: 48px;
        height: 48px;
        background: #3b82f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .conv-details {
        flex: 1;
        min-width: 0;
    }

    .conv-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #0f172a;
    }

    .conv-preview {
        font-size: 0.75rem;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-time {
        font-size: 0.65rem;
        color: #64748b;
    }

    .unread-dot {
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
    }

    /* MAIN CHAT - Dito importante para laging kita ang send button */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        overflow: hidden;
        height: 100%;
    }

    .chat-header {
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: white;
        flex-shrink: 0;
    }

    .chat-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .online-badge {
        background: #dcfce7;
        color: #15803d;
        font-size: 0.65rem;
        padding: 2px 10px;
        border-radius: 30px;
        font-weight: 500;
    }

    /* Messages area - scrollable dito lang */
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #fefefe;
        min-height: 0; /* mahalaga para hindi mag-expand */
    }

    /* Date separator */
    .date-sep {
        text-align: center;
        margin: 12px 0 8px;
    }

    .date-sep span {
        background: #eef2ff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        color: #1e40af;
        font-weight: 500;
    }

    /* Message bubbles */
    .message {
        max-width: 70%;
        display: flex;
        flex-direction: column;
        animation: fadeIn 0.15s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message.sent {
        align-self: flex-start;
    }

    .message.received {
        align-self: flex-end;
    }

    .bubble {
        padding: 8px 14px;
        border-radius: 18px;
        font-size: 0.85rem;
        line-height: 1.4;
        word-break: break-word;
        cursor: pointer;
    }

    .message.sent .bubble {
        background: white;
        border: 1px solid #3b82f6;
        color: #1f2a44;
        border-bottom-left-radius: 4px;
    }

    .message.received .bubble {
        background: #3b82f6;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .timestamp {
        font-size: 0.6rem;
        margin-top: 4px;
        opacity: 0;
        transition: opacity 0.1s;
        height: 0;
        overflow: hidden;
    }

    .timestamp.visible {
        opacity: 1;
        height: auto;
        margin-top: 4px;
    }

    .message.sent .timestamp {
        text-align: left;
        color: #64748b;
    }

    .message.received .timestamp {
        text-align: right;
        color: #94a3b8;
    }

    /* Input area - fixed sa baba, laging kita */
    .input-area {
        padding: 16px 24px 20px;
        border-top: 1px solid #e2e8f0;
        background: white;
        flex-shrink: 0;
    }

    .chat-form {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .chat-form textarea {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 24px;
        padding: 10px 16px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        resize: none;
        max-height: 100px;
        background: white;
    }

    .chat-form textarea:focus {
        outline: none;
        border-color: #3b82f6;
    }

    .chat-form button {
        background: #3b82f6;
        border: none;
        border-radius: 30px;
        padding: 10px 20px;
        color: white;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .chat-form button:hover {
        background: #2563eb;
    }

    .empty-state, .error-state {
        text-align: center;
        padding: 40px;
        color: #64748b;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 5px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chat-sidebar {
            width: 260px;
        }
        .message {
            max-width: 85%;
        }
        .input-area {
            padding: 12px 16px;
        }
    }

    @media (max-width: 640px) {
        .chat-container {
            flex-direction: column;
            margin: 0;
            height: 100vh;
            border-radius: 0;
        }
        .chat-sidebar {
            width: 100%;
            max-height: 250px;
        }
        .input-area {
            padding: 12px 16px;
        }
    }
</style>
@endsection

@section('content')
<div class="chat-container">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-landmark"></i> Binalonan Chat</h2>
            <p>Opisyal na komunikasyon</p>
        </div>
        <div class="conv-list">
            @forelse($conversations as $conv)
                @php
                    $other = $conv->owner;
                    $name = $other->name ?? 'User';
                    $lastMsg = $conv->lastMessage;
                    $isUnread = ($lastMsg && $lastMsg->sender_type != $myType && !$lastMsg->is_read);
                @endphp
                <a href="{{ route('chat.show', $conv->id) }}" 
                   class="conv-item {{ $conv->id == $conversation->id ? 'active' : '' }}">
                    <div class="conv-avatar">{{ strtoupper(substr($name, 0, 1)) }}</div>
                    <div class="conv-details">
                        <div class="conv-name">{{ $name }}</div>
                        <div class="conv-preview">{{ Str::limit($lastMsg->message ?? 'Magandang araw!', 35) }}</div>
                        <div class="conv-time">{{ $conv->updated_at->diffForHumans() }}</div>
                    </div>
                    @if($isUnread)
                        <div class="unread-dot"></div>
                    @endif
                </a>
            @empty
                <div class="empty-state">Walang laman na usapan.</div>
            @endforelse
        </div>
    </div>

    <!-- Chat area -->
    <div class="chat-main">
        <div class="chat-header">
            <h3>
                <i class="fas fa-user-circle"></i>
                {{ $otherUser->name ?? 'Resident / Official' }}
                <span class="online-badge"><i class="fas fa-circle" style="font-size: 0.35rem;"></i> Online</span>
            </h3>
        </div>

        <div class="messages-area" id="messagesArea">
            <div class="empty-state">Loading messages...</div>
        </div>

        <div class="input-area">
            <form id="messageForm" class="chat-form">
                @csrf
                <textarea id="msgInput" rows="1" placeholder="Mag-type ng mensahe..."></textarea>
                <button type="submit"><i class="fas fa-paper-plane"></i> Send</button>
            </form>
            <small style="display: block; margin-top: 8px; font-size: 0.6rem; color: #94a3b8;">
                <i class="fas fa-info-circle"></i> I-click ang mensahe para makita ang oras
            </small>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const conversationId = {{ $conversation->id }};
    const myType = '{{ $myType }}';
    let polling = null;
    let autoScroll = true;
    const messagesContainer = document.getElementById('messagesArea');

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(dateStr) {
        return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function getDateKey(dateStr) {
        return new Date(dateStr).toISOString().split('T')[0];
    }

    function formatDateDivider(dateKey) {
        const today = new Date().toISOString().split('T')[0];
        const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0];
        if (dateKey === today) return 'Ngayong araw';
        if (dateKey === yesterday) return 'Kahapon';
        return new Date(dateKey).toLocaleDateString('fil-PH', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function isScrolledToBottom(el) {
        return el.scrollHeight - el.scrollTop - el.clientHeight < 30;
    }

    function scrollToBottom() {
        messagesContainer.scrollTo({ top: messagesContainer.scrollHeight, behavior: 'smooth' });
    }

    function hideAllTimestamps() {
        document.querySelectorAll('.timestamp').forEach(el => el.classList.remove('visible'));
    }

    function attachClickEvents() {
        document.querySelectorAll('.bubble').forEach(bubble => {
            bubble.removeEventListener('click', handleBubbleClick);
            bubble.addEventListener('click', handleBubbleClick);
        });
    }

    function handleBubbleClick(e) {
        e.stopPropagation();
        const msgDiv = e.currentTarget.closest('.message');
        if (msgDiv) {
            const ts = msgDiv.querySelector('.timestamp');
            if (ts) {
                hideAllTimestamps();
                ts.classList.toggle('visible');
            }
        }
    }

    function renderMessages(messages) {
        if (!messages.length) return '<div class="empty-state">Walang mensahe. Magsimula ng usapan.</div>';
        let html = '';
        let lastDate = null;
        messages.forEach(msg => {
            const dateKey = getDateKey(msg.created_at);
            if (dateKey !== lastDate) {
                html += `<div class="date-sep"><span>${formatDateDivider(dateKey)}</span></div>`;
                lastDate = dateKey;
            }
            const isSent = msg.sender_type === myType;
            const messageClass = isSent ? 'sent' : 'received';
            html += `
                <div class="message ${messageClass}">
                    <div class="bubble">${escapeHtml(msg.message)}</div>
                    <div class="timestamp">${formatTime(msg.created_at)}</div>
                </div>
            `;
        });
        return html;
    }

    function loadMessages() {
        fetch(`/api/chat/messages/${conversationId}`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to load');
                return res.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);
                const wasBottom = autoScroll && isScrolledToBottom(messagesContainer);
                const html = renderMessages(data.data || []);
                messagesContainer.innerHTML = html;
                attachClickEvents();
                if (wasBottom) scrollToBottom();
            })
            .catch(err => {
                messagesContainer.innerHTML = `<div class="error-state">Error: ${err.message}</div>`;
            });
    }

    function sendMessage(message) {
        if (!message.trim()) return;
        const btn = document.querySelector('#messageForm button');
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Send';
        fetch('/api/chat/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                message: message
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to send');
            return res.json();
        })
        .then(() => {
            document.getElementById('msgInput').value = '';
            autoScroll = true;
            loadMessages();
            setTimeout(scrollToBottom, 100);
        })
        .catch(err => alert('Error: ' + err.message))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = original;
            document.getElementById('msgInput').focus();
        });
    }

    // Event listeners
    document.getElementById('messageForm').addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage(document.getElementById('msgInput').value);
    });

    const textarea = document.getElementById('msgInput');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    messagesContainer.addEventListener('scroll', () => {
        autoScroll = isScrolledToBottom(messagesContainer);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.bubble')) hideAllTimestamps();
    });

    loadMessages();
    polling = setInterval(loadMessages, 3000);
    textarea.focus();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) clearInterval(polling);
        else polling = setInterval(loadMessages, 3000);
    });
</script>
@endsection
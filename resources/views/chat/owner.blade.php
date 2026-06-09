@extends('layouts.app')

@section('title', 'Chat with Owner')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Poppins', sans-serif; }
    .chat-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    .chat-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 160px);
        min-height: 500px;
    }
    .chat-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #f8fafc;
    }
    .owner-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
    }
    .owner-info h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
    }
    .owner-info p {
        font-size: 0.8rem;
        color: #10b981;
        margin: 0;
    }
    .back-btn {
        margin-left: auto;
        background: #e2e8f0;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: 0.2s;
    }
    .back-btn:hover { background: #cbd5e1; }
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: #ffffff;
    }
    .message {
        display: flex;
        max-width: 70%;
    }
    .message.received { align-self: flex-start; }
    .message.sent { align-self: flex-end; }
    .bubble {
        padding: 0.75rem 1rem;
        border-radius: 18px;
        position: relative;
        word-wrap: break-word;
    }
    .message.received .bubble {
        background: #f1f5f9;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }
    .message.sent .bubble {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .message-time {
        font-size: 0.65rem;
        margin-top: 0.25rem;
        display: block;
        color: #94a3b8;
    }
    .message.sent .message-time { text-align: right; color: #cbd5e1; }
    .input-area {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: white;
    }
    .input-wrapper {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }
    .chat-input {
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 0.75rem 1rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        resize: none;
        outline: none;
    }
    .chat-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59,130,246,0.1);
    }
    .send-btn {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        color: white;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .send-btn:hover { transform: scale(1.05); }
    .requirements-card {
        background: #fef9e3;
        border-left: 4px solid #f59e0b;
        margin: 1rem 1.5rem;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        font-size: 0.85rem;
    }
    @media (max-width: 640px) {
        .message { max-width: 85%; }
        .chat-card { height: calc(100vh - 120px); }
        .chat-container { margin: 1rem auto; }
    }
</style>
@endsection

@section('content')
<div class="chat-container">
    <div class="chat-card">
        <div class="chat-header">
            <div class="owner-avatar">{{ strtoupper(substr($owner->name ?? 'O', 0, 1)) }}</div>
            <div class="owner-info">
                <h3>{{ $owner->name ?? 'Juan Dela Cruz' }}</h3>
                <p>● Online • Usually replies in minutes</p>
            </div>
            <button class="back-btn" onclick="window.history.back()">← Back</button>
        </div>

       
        <div class="messages-area" id="messagesArea">
            @forelse($messages as $msg)
                <div class="message {{ $msg->sender_id == auth()->id() ? 'sent' : 'received' }}">
                    <div class="bubble">
                        {{ $msg->message }}
                        <span class="message-time">{{ $msg->created_at->format('g:i A') }}</span>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:#94a3b8; padding:2rem;">
                    <i class="fa fa-comment-dots" style="font-size:2rem;"></i>
                    <p>Start your conversation with the owner.</p>
                </div>
            @endforelse
        </div>

        <div class="input-area">
            <div class="input-wrapper">
                <textarea id="messageInput" class="chat-input" rows="1" placeholder="Type your message..." onkeydown="if(event.key === 'Enter' && !event.shiftKey){ event.preventDefault(); sendMessage(); }"></textarea>
                <button class="send-btn" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const messagesArea = document.getElementById('messagesArea');
    const messageInput = document.getElementById('messageInput');
    let lastMessageId = {{ $lastMessageId ?? 0 }};

    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function appendMessage(text, type, time = null) {
        const div = document.createElement('div');
        div.className = `message ${type}`;
        const timeString = time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        div.innerHTML = `
            <div class="bubble">
                ${escapeHtml(text)}
                <span class="message-time">${timeString}</span>
            </div>
        `;
        messagesArea.appendChild(div);
        scrollToBottom();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function sendMessage() {
        const msg = messageInput.value.trim();
        if (!msg) return;
        const btn = document.querySelector('.send-btn');
        btn.disabled = true;

        fetch('{{ route("chat.send", $owner->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appendMessage(msg, 'sent');
                messageInput.value = '';
                messageInput.style.height = 'auto';
                lastMessageId = data.message_id;
            } else {
                alert('Failed to send message.');
            }
        })
        .catch(err => console.error(err))
        .finally(() => btn.disabled = false);
    }

    function pollMessages() {
        fetch('{{ route("chat.poll", $owner->id) }}?last_id=' + lastMessageId)
        .then(res => res.json())
        .then(data => {
            if (data.messages && data.messages.length) {
                data.messages.forEach(msg => {
                    const type = msg.sender_id == {{ auth()->id() }} ? 'sent' : 'received';
                    appendMessage(msg.message, type, msg.formatted_time);
                    lastMessageId = msg.id;
                });
            }
        })
        .catch(e => console.log('Poll error', e));
    }

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    setInterval(pollMessages, 3000);
    scrollToBottom();
</script>
@endsection
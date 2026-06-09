@extends('layouts.app')

@section('title', 'Notifications')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    .notifications-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .btn-mark-all {
        background: white;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-mark-all:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59,130,246,0.2);
    }

    .btn-clear {
        background: white;
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-clear:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(239,68,68,0.2);
    }

    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .notification-item {
        background: white;
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .notification-item.unread {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-left: 4px solid #3b82f6;
    }

    .notification-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        border-color: #3b82f6;
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .unread-badge {
        background: #3b82f6;
        color: white;
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .notification-message {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 0.5rem;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .notification-actions {
        display: flex;
        gap: 0.5rem;
        margin-left: auto;
        align-items: center;
    }

    .btn-mark-read {
        background: none;
        border: none;
        color: #3b82f6;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .btn-mark-read:hover {
        background: #eff6ff;
    }

    .btn-delete {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #64748b;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .notifications-container {
            padding: 0 1rem;
            margin: 1rem auto;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .notification-item {
            flex-wrap: wrap;
        }
        .notification-actions {
            margin-left: 0;
            width: 100%;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }
    }

    .toast-notification {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: white;
        border-left: 4px solid #3b82f6;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 2001;
        animation: slideInRight 0.3s ease;
        font-family: 'Poppins', sans-serif;
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="notifications-container">
    <div class="page-header">
        <h1>
            <span>🔔</span> Notifications
            @if($unreadCount > 0)
                <span class="unread-badge" style="margin-left: 0.5rem;">{{ $unreadCount }} unread</span>
            @endif
        </h1>
        <div class="header-actions">
            <button class="btn-mark-all" id="markAllReadBtn">✓ Mark all as read</button>
            <button class="btn-clear" id="clearAllBtn">🗑️ Clear all</button>
        </div>
    </div>

    <div class="notifications-list" id="notificationsList">
        @forelse($notifications as $notification)
            @php
                $data = is_string($notification->data) ? json_decode($notification->data, true) : (array) $notification->data;
                $title = $data['title'] ?? 'Notification';
                $message = $data['message'] ?? 'You have a new notification';
                $actionUrl = $data['action_url'] ?? null;
                $icon = $data['icon'] ?? '🔔';
                $isRead = !is_null($notification->read_at);
                $timeAgo = $notification->created_at->diffForHumans();
            @endphp
            <div class="notification-item {{ !$isRead ? 'unread' : '' }}" data-link="{{ $actionUrl }}">
                <div class="notification-icon">{{ $icon }}</div>
                <div class="notification-content">
                    <div class="notification-title">
                        {{ $title }}
                        @if(!$isRead)
                            <span class="unread-badge">New</span>
                        @endif
                    </div>
                    <div class="notification-message">{{ $message }}</div>
                    <div class="notification-time">
                        <span>🕒</span> {{ $timeAgo }}
                    </div>
                </div>
                <div class="notification-actions">
                    @if(!$isRead)
                        <button class="btn-mark-read" data-id="{{ $notification->id }}" data-action="mark-read">✓ Mark read</button>
                    @endif
                    <button class="btn-delete" data-id="{{ $notification->id }}" data-action="delete" title="Delete notification">✕</button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">🔕</div>
                <h3>No notifications yet</h3>
                <p>When you receive notifications, they'll appear here.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        let icon = '✓';
        let color = '#10b981';
        if (type === 'info') {
            icon = 'ℹ';
            color = '#3b82f6';
        } else if (type === 'error') {
            icon = '✗';
            color = '#ef4444';
        }
        toast.innerHTML = `<i style="color: ${color};">${icon}</i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Handle click on notification item (redirect if link exists)
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            const link = item.dataset.link;
            if (link && link !== '#') {
                window.location.href = link;
            }
        });
    });

    // Mark as read (AJAX - works only with real database, but we'll simulate)
    document.querySelectorAll('[data-action="mark-read"]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            const id = btn.dataset.id;
            // For dummy notifications, just update UI
            // In real app, send AJAX to route('notifications.mark-read', id)
            const notificationDiv = btn.closest('.notification-item');
            notificationDiv.classList.remove('unread');
            btn.remove();
            showToast('Marked as read (demo)', 'info');
        });
    });

    // Delete notification
    document.querySelectorAll('[data-action="delete"]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.stopPropagation();
            const id = btn.dataset.id;
            // Remove from DOM
            const notificationDiv = btn.closest('.notification-item');
            notificationDiv.remove();
            showToast('Notification removed', 'info');
            // If no notifications left, show empty state
            if (document.querySelectorAll('.notification-item').length === 0) {
                location.reload(); // Or dynamically show empty state
            }
        });
    });

    // Mark all as read (just UI demo)
    document.getElementById('markAllReadBtn').addEventListener('click', () => {
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.classList.remove('unread');
            const markBtn = item.querySelector('[data-action="mark-read"]');
            if (markBtn) markBtn.remove();
        });
        showToast('All marked as read (demo)', 'success');
    });

    // Clear all (demo)
    document.getElementById('clearAllBtn').addEventListener('click', () => {
        if (confirm('Clear all notifications? (demo)')) {
            document.querySelectorAll('.notification-item').forEach(item => item.remove());
            showToast('All cleared (demo)', 'success');
            // Optionally reload to show empty state
            if (document.querySelectorAll('.notification-item').length === 0) {
                location.reload();
            }
        }
    });
</script>
@endsection
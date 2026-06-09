// Notification System
class NotificationManager {
    constructor() {
        this.notifications = [
            {
                id: 1,
                title: 'New Apartment Available',
                message: 'A new 2BR apartment is now available in Barangay Central',
                time: '5 minutes ago',
                read: false,
                type: 'success',
                icon: 'fa-home'
            },
            {
                id: 2,
                title: 'Booking Confirmed',
                message: 'Your booking for Unit A in Barangay Riverside has been confirmed',
                time: '1 hour ago',
                read: false,
                type: 'info',
                icon: 'fa-check-circle'
            },
            {
                id: 3,
                title: 'Price Drop Alert',
                message: 'Apartment prices in Barangay Uptown have been reduced by 10%',
                time: '3 hours ago',
                read: true,
                type: 'warning',
                icon: 'fa-tag'
            },
            {
                id: 4,
                title: 'Maintenance Schedule',
                message: 'Scheduled maintenance on April 15, 2024',
                time: '1 day ago',
                read: true,
                type: 'info',
                icon: 'fa-tools'
            }
        ];
        
        this.init();
    }
    
    init() {
        this.cacheDom();
        this.bindEvents();
        this.render();
        this.updateBadge();
        
        // Add welcome notification after 2 seconds
        setTimeout(() => {
            this.addNotification('Welcome to APARTrack!', 'Start exploring apartments near you', 'success');
        }, 2000);
    }
    
    cacheDom() {
        this.notifWrapper = document.getElementById('notifWrapper');
        this.notifPopup = document.getElementById('notifPopup');
        this.notifBadge = document.getElementById('notifBadge');
        this.notifList = document.getElementById('notifList');
        this.markAllReadBtn = document.getElementById('markAllReadBtn');
    }
    
    bindEvents() {
        // Toggle popup on wrapper click
        if (this.notifWrapper) {
            this.notifWrapper.addEventListener('click', (e) => {
                e.stopPropagation();
                this.togglePopup();
            });
        }
        
        // Mark all as read
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.markAllAsRead();
            });
        }
        
        // Close popup when clicking outside
        document.addEventListener('click', (event) => {
            if (this.notifPopup && this.notifWrapper && 
                !this.notifWrapper.contains(event.target)) {
                this.closePopup();
            }
        });
        
        // Prevent popup from closing when clicking inside
        if (this.notifPopup) {
            this.notifPopup.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }
    }
    
    togglePopup() {
        if (this.notifPopup.classList.contains('show')) {
            this.closePopup();
        } else {
            this.openPopup();
        }
    }
    
    openPopup() {
        this.notifPopup.classList.add('show');
        this.render();
    }
    
    closePopup() {
        this.notifPopup.classList.remove('show');
    }
    
    render() {
        if (!this.notifList) return;
        
        if (this.notifications.length === 0) {
            this.notifList.innerHTML = `
                <div class="empty-notifications">
                    <i class="fa fa-bell-slash"></i>
                    <p>No notifications yet</p>
                </div>
            `;
            return;
        }
        
        this.notifList.innerHTML = this.notifications.map(notification => `
            <div class="notification-item ${!notification.read ? 'unread' : ''}" data-id="${notification.id}">
                <div class="notification-icon" style="background: ${this.getIconBackground(notification.type)}">
                    <i class="fa ${notification.icon}" style="color: ${this.getIconColor(notification.type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                    <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                    <div class="notification-time">${notification.time}</div>
                </div>
            </div>
        `).join('');
        
        // Add click events to notifications
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = parseInt(item.dataset.id);
                this.markAsRead(id);
            });
        });
    }
    
    getIconBackground(type) {
        switch(type) {
            case 'success': return 'rgba(16, 185, 129, 0.1)';
            case 'error': return 'rgba(239, 68, 68, 0.1)';
            case 'warning': return 'rgba(245, 158, 11, 0.1)';
            default: return 'rgba(59, 130, 246, 0.1)';
        }
    }
    
    getIconColor(type) {
        switch(type) {
            case 'success': return '#10b981';
            case 'error': return '#ef4444';
            case 'warning': return '#f59e0b';
            default: return '#3b82f6';
        }
    }
    
    getIconForType(type) {
        switch(type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            default: return 'fa-bell';
        }
    }
    
    markAsRead(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification && !notification.read) {
            notification.read = true;
            this.render();
            this.updateBadge();
            this.showToast('Notification marked as read', 'info');
            this.closePopup();
        }
    }
    
    markAllAsRead() {
        let hasUnread = false;
        this.notifications.forEach(n => {
            if (!n.read) {
                n.read = true;
                hasUnread = true;
            }
        });
        
        if (hasUnread) {
            this.render();
            this.updateBadge();
            this.showToast('All notifications marked as read', 'success');
            this.closePopup();
        }
    }
    
    addNotification(title, message, type = 'info') {
        const newNotification = {
            id: Date.now(),
            title: title,
            message: message,
            time: 'Just now',
            read: false,
            type: type,
            icon: this.getIconForType(type)
        };
        
        this.notifications.unshift(newNotification);
        this.render();
        this.updateBadge();
        this.showToast(title, type);
        
        // Optional: Play sound
        // this.playNotificationSound();
    }
    
    updateBadge() {
        if (!this.notifBadge) return;
        
        const unreadCount = this.notifications.filter(n => !n.read).length;
        this.notifBadge.textContent = unreadCount;
        this.notifBadge.style.display = unreadCount > 0 ? 'block' : 'none';
    }
    
    playNotificationSound() {
        // Optional: Add notification sound
        // const audio = new Audio('/sounds/notification.mp3');
        // audio.play().catch(e => console.log('Audio play failed:', e));
    }
    
    showToast(message, type = 'success') {
        // Remove existing toast
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        
        let icon = '✓';
        if (type === 'error') icon = '✗';
        if (type === 'info') icon = 'ℹ';
        if (type === 'warning') icon = '⚠';
        
        toast.innerHTML = `
            <i style="color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'}">${icon}</i>
            <span style="color: #1e293b;">${this.escapeHtml(message)}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s reverse';
            setTimeout(() => {
                if (toast.parentNode) toast.remove();
            }, 300);
        }, 3000);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize notification system when DOM is ready
let notificationManager;

document.addEventListener('DOMContentLoaded', function() {
    notificationManager = new NotificationManager();
});

// Global function to add notification from anywhere
window.addNotification = function(title, message, type = 'info') {
    if (notificationManager) {
        notificationManager.addNotification(title, message, type);
    }
};

// Global function to get unread count
window.getUnreadCount = function() {
    if (notificationManager) {
        return notificationManager.notifications.filter(n => !n.read).length;
    }
    return 0;
};

// Toggle dropdown function
function toggleDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('dropdownMenu');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}
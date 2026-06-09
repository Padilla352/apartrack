// Profile Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const editProfileForm = document.getElementById('editProfileForm');
    
    // Statistics elements
    const totalBookingsEl = document.getElementById('totalBookings');
    const activeBookingsEl = document.getElementById('activeBookings');
    const completedBookingsEl = document.getElementById('completedBookings');
    const savedPropertiesEl = document.getElementById('savedProperties');
    
    // User data (you can fetch this from your backend)
    let userData = {
        name: document.getElementById('profileName')?.innerText || '',
        email: document.getElementById('profileEmail')?.innerText || '',
        phone: '',
        totalBookings: 0,
        activeBookings: 0,
        completedBookings: 0,
        savedProperties: 0
    };
    
    // Initialize the page
    function init() {
        loadUserData();
        attachEventListeners();
        animateStats();
    }
    
    // Load user data (you can replace this with an API call)
    function loadUserData() {
        // Simulate loading data from server
        setTimeout(() => {
            // Update statistics (you would get these from your backend)
            if (totalBookingsEl) totalBookingsEl.textContent = userData.totalBookings;
            if (activeBookingsEl) activeBookingsEl.textContent = userData.activeBookings;
            if (completedBookingsEl) completedBookingsEl.textContent = userData.completedBookings;
            if (savedPropertiesEl) savedPropertiesEl.textContent = userData.savedProperties;
        }, 500);
    }
    
    // Animate statistics counters
    function animateStats() {
        const statValues = document.querySelectorAll('.stat-value');
        
        statValues.forEach(stat => {
            const target = parseInt(stat.textContent);
            if (target > 0) {
                animateValue(stat, 0, target, 1000);
            }
        });
    }
    
    // Animation helper
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = Math.floor(progress * (end - start) + start);
            element.textContent = currentValue;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    // Attach all event listeners
    function attachEventListeners() {
        // Edit profile button click
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', openEditModal);
        }
        
        // Close modal buttons
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }
        
        // Form submission
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', handleFormSubmit);
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (editProfileModal && e.target === editProfileModal) {
                closeModal();
            }
        });
        
        // Avatar hover effect
        const avatar = document.querySelector('.profile-avatar');
        if (avatar) {
            avatar.addEventListener('click', () => {
                showToast('Profile picture upload coming soon!', 'info');
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Press 'E' to edit profile
            if (e.key === 'e' || e.key === 'E') {
                if (!editProfileModal || editProfileModal.style.display !== 'flex') {
                    openEditModal();
                }
            }
            
            // Press 'ESC' to close modal
            if (e.key === 'Escape' && editProfileModal && editProfileModal.style.display === 'flex') {
                closeModal();
            }
        });
    }
    
    // Open edit modal
    function openEditModal() {
        if (!editProfileModal) return;
        
        // Pre-fill form with current user data
        const editName = document.getElementById('editName');
        const editEmail = document.getElementById('editEmail');
        const editPhone = document.getElementById('editPhone');
        
        if (editName) editName.value = userData.name;
        if (editEmail) editEmail.value = userData.email;
        if (editPhone) editPhone.value = userData.phone || '';
        
        // Show modal with animation
        editProfileModal.style.display = 'flex';
        editProfileModal.classList.add('show');
        
        // Focus on first input
        setTimeout(() => {
            if (editName) editName.focus();
        }, 100);
    }
    
    // Close modal
    function closeModal() {
        if (!editProfileModal) return;
        
        editProfileModal.classList.remove('show');
        setTimeout(() => {
            editProfileModal.style.display = 'none';
        }, 300);
    }
    
    // Handle form submission
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        // Get form values
        const newName = document.getElementById('editName')?.value.trim();
        const newEmail = document.getElementById('editEmail')?.value.trim();
        const newPhone = document.getElementById('editPhone')?.value.trim();
        
        // Validate
        if (!newName || !newEmail) {
            showToast('Please fill in all required fields', 'error');
            return;
        }
        
        if (!isValidEmail(newEmail)) {
            showToast('Please enter a valid email address', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('.btn-save');
        const originalText = submitBtn?.innerHTML;
        if (submitBtn) {
            submitBtn.innerHTML = '<span class="loading"></span> Saving...';
            submitBtn.disabled = true;
        }
        
        // Simulate API call (replace with actual AJAX request)
        setTimeout(() => {
            // Update user data
            userData.name = newName;
            userData.email = newEmail;
            userData.phone = newPhone;
            
            // Update UI
            const profileName = document.getElementById('profileName');
            const profileEmail = document.getElementById('profileEmail');
            const fullName = document.getElementById('fullName');
            const emailAddress = document.getElementById('emailAddress');
            const profileAvatar = document.getElementById('profileAvatar');
            
            if (profileName) profileName.textContent = newName;
            if (profileEmail) profileEmail.textContent = newEmail;
            if (fullName) fullName.textContent = newName;
            if (emailAddress) emailAddress.textContent = newEmail;
            if (profileAvatar) profileAvatar.textContent = newName.charAt(0).toUpperCase();
            
            // Close modal and show success message
            closeModal();
            showToast('Profile updated successfully!', 'success');
            
            // Reset button
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }, 1000);
    }
    
    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Show toast notification
    function showToast(message, type = 'success') {
        // Remove existing toast if any
        const existingToast = document.querySelector('.toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Icon based on type
        let icon = '✓';
        if (type === 'error') icon = '✗';
        if (type === 'info') icon = 'ℹ';
        
        toast.innerHTML = `
            <i style="font-size: 1.2rem; color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'}">${icon}</i>
            <span style="color: #1e293b;">${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s reverse';
            setTimeout(() => {
                if (toast.parentNode) toast.remove();
            }, 300);
        }, 3000);
    }
    
    // Load statistics from server (example function)
    async function loadStatistics() {
        try {
            // Replace with your actual API endpoint
            // const response = await fetch('/api/user/statistics');
            // const data = await response.json();
            
            // Simulated data
            const data = {
                totalBookings: 0,
                activeBookings: 0,
                completedBookings: 0,
                savedProperties: 0
            };
            
            // Update user data
            userData.totalBookings = data.totalBookings;
            userData.activeBookings = data.activeBookings;
            userData.completedBookings = data.completedBookings;
            userData.savedProperties = data.savedProperties;
            
            // Update UI
            if (totalBookingsEl) totalBookingsEl.textContent = data.totalBookings;
            if (activeBookingsEl) activeBookingsEl.textContent = data.activeBookings;
            if (completedBookingsEl) completedBookingsEl.textContent = data.completedBookings;
            if (savedPropertiesEl) savedPropertiesEl.textContent = data.savedProperties;
            
            // Animate the new values
            animateStats();
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }
    
    // Call loadStatistics if needed
    // loadStatistics();
    
    // Start the application
    init();
});
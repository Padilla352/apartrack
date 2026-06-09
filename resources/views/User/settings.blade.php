{{-- resources/views/user/settings.blade.php --}}
@extends('layouts.app')

@section('title', 'Settings - APARTrack')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* ---------- HIDE THE NAVBAR (from partials/navbar.blade.php) ---------- */
    .main-header {
        display: none !important;
    }

    /* Remove any top spacing left by the hidden navbar */
    body {
        padding-top: 0 !important;
        margin-top: 0 !important;
        background: #F5F7FA;
    }

    /* Back Button Styling */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: white;
        border: 1.5px solid #1E88E5;
        color: #1E88E5;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        font-family: 'Poppins', sans-serif;
    }
    .back-button i {
        font-size: 0.9rem;
    }
    .back-button:hover {
        background: #1E88E5;
        color: white;
        transform: translateX(-3px);
        box-shadow: 0 4px 10px rgba(30,136,229,0.2);
    }

    /* Your original CSS – keep as is */
    * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #F5F7FA; color: #222222; }
    .settings-container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }
    .settings-header { margin-bottom: 2rem; }
    .settings-title { font-size: 2rem; font-weight: 700; color: #1E88E5; margin-bottom: 0.25rem; }
    .settings-subtitle { color: #5f6c80; font-weight: 400; }
    .settings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 1.8rem; }
    .settings-card { background: #FFFFFF; border-radius: 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s; overflow: hidden; }
    .settings-card:hover { transform: translateY(-3px); box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.1); }
    .card-header { display: flex; align-items: center; gap: 10px; padding: 1.2rem 1.5rem; background: #FFFFFF; border-bottom: 1px solid #EFF3F8; }
    .card-header i { font-size: 1.5rem; color: #1E88E5; }
    .card-header h2 { font-size: 1.3rem; font-weight: 600; margin: 0; color: #1a2c3e; }
    .card-body { padding: 1.5rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #3a4a62; margin-bottom: 0.4rem; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; transition: all 0.2s; }
    .form-control:focus { outline: none; border-color: #1E88E5; box-shadow: 0 0 0 3px rgba(30,136,229,0.1); }
    textarea.form-control { resize: vertical; }
    .btn-primary, .btn-outline { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0.6rem 1.5rem; border-radius: 40px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; border: none; }
    .btn-primary { background: #1E88E5; color: white; box-shadow: 0 2px 6px rgba(30,136,229,0.2); }
    .btn-primary:hover { background: #1976d2; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(30,136,229,0.3); }
    .btn-outline { background: white; border: 1.5px solid #1E88E5; color: #1E88E5; }
    .btn-outline:hover { background: #eef6fe; transform: translateY(-1px); }
    .profile-avatar { display: flex; justify-content: center; margin-bottom: 1.5rem; }
    .avatar-wrapper { position: relative; width: 100px; height: 100px; }
    .avatar-wrapper img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .avatar-edit-btn { position: absolute; bottom: 5px; right: 5px; background: #1E88E5; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; transition: all 0.2s; }
    .avatar-edit-btn:hover { background: #0b5e9e; transform: scale(1.05); }
    .toggle-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #eff3f8; }
    .toggle-item:last-child { border-bottom: none; }
    .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: 0.3s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%; }
    input:checked + .slider { background-color: #1E88E5; }
    input:checked + .slider:before { transform: translateX(24px); }
    .support-buttons { display: flex; flex-direction: column; gap: 1rem; }
    .help-link { text-align: center; display: inline-block; margin-top: 0.5rem; color: #1E88E5; text-decoration: none; font-size: 0.85rem; }
    .divider { height: 1px; background: #eef2f6; margin: 1.5rem 0; }
    body.dark-mode { background: #121212; }
    body.dark-mode .settings-card { background: #1e1e2f; box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
    body.dark-mode .card-header { background: #1e1e2f; border-bottom-color: #2d2d3a; }
    body.dark-mode .card-header h2, body.dark-mode .toggle-item span { color: #e0e0e0; }
    body.dark-mode .form-control { background: #2d2d3a; border-color: #3a3a4a; color: white; }
    body.dark-mode .btn-outline { background: #2d2d3a; border-color: #1E88E5; color: #1E88E5; }
    @media (max-width: 768px) {
        .settings-container { padding: 0 1rem; margin: 1rem auto; }
        .settings-grid { grid-template-columns: 1fr; gap: 1.2rem; }
        .card-body { padding: 1.2rem; }
        .back-button { width: 100%; justify-content: center; }
    }
    .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
    .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 0.6s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .message-success { color: #10b981; font-size: 0.8rem; margin-top: 0.5rem; }
    .message-error { color: #ef4444; font-size: 0.8rem; margin-top: 0.5rem; }
</style>
@endsection

@section('content')
<div class="settings-container">
    <!-- Back Button to Dashboard (change route('dashboard') to route('profile') if you prefer) -->
    <a href="{{ route('dashboard') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="settings-header">
        <h1 class="settings-title">Settings</h1>
        <p class="settings-subtitle">Manage your account and preferences</p>
    </div>

    <div class="settings-grid">
        <!-- Edit Profile Card -->
        <div class="settings-card">
            <div class="card-header">
                <i class="fas fa-user-edit"></i>
                <h2>Edit Profile</h2>
            </div>
            <div class="card-body">
                <div class="profile-avatar">
                    <div class="avatar-wrapper">
                        <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : asset('images/default-avatar.png') }}" 
                             alt="Profile" id="avatarPreview">
                        <button type="button" class="avatar-edit-btn" id="avatarEditBtn">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <input type="file" id="avatarInput" accept="image/*" style="display:none;">
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="fullName" 
                           value="{{ old('name', Auth::user()->name) }}" 
                           class="form-control"
                           pattern="[A-Za-z\s\-']+"
                           title="Only letters, spaces, hyphens, and apostrophes allowed"
                           oninput="this.value = this.value.replace(/[^A-Za-z\s\-']/g, '')"
                           required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" id="phoneNumber" 
                           value="{{ old('phone', Auth::user()->phone ?? '') }}" 
                           class="form-control" 
                           placeholder="+63 XXX XXX XXXX"
                           pattern="[\+]?[0-9\s\-\(\)]+"
                           title="Only numbers, spaces, dashes, parentheses, and optional + allowed"
                           oninput="this.value = this.value.replace(/[^0-9\+\s\-\(\)]/g, '')">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2" 
                              placeholder="Your address">{{ old('address', Auth::user()->address ?? '') }}</textarea>
                </div>

                <button type="button" class="btn-primary" id="saveProfileBtn">Save Changes</button>
                <div id="profileMessage" class="message-success"></div>
            </div>
        </div>

        <!-- Security Card -->
        <div class="settings-card">
            <div class="card-header">
                <i class="fas fa-lock"></i>
                <h2>Security</h2>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    @csrf
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn-primary">Change Password</button>
                    <div id="passwordMessage" class="message-success"></div>
                </form>

                <div class="divider"></div>

                <div class="action-buttons" style="display: flex; flex-wrap: wrap; gap: 1rem;">
                    <button type="button" class="btn-outline" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                    <button type="button" class="btn-outline" id="logoutAllBtn">
                        <i class="fas fa-laptop-code"></i> Logout from all devices
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications Card -->
        <div class="settings-card">
            <div class="card-header">
                <i class="fas fa-bell"></i>
                <h2>Notifications</h2>
            </div>
            <div class="card-body">
                <div class="toggle-item">
                    <span><i class="fas fa-tools"></i> Maintenance notifications</span>
                    <label class="switch">
                        <input type="checkbox" id="maintenance_notifications" {{ Auth::user()->maintenance_notifications ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <span><i class="fas fa-bullhorn"></i> Apartment announcements</span>
                    <label class="switch">
                        <input type="checkbox" id="announcement_notifications" {{ Auth::user()->announcement_notifications ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <span><i class="fas fa-envelope"></i> Email notifications</span>
                    <label class="switch">
                        <input type="checkbox" id="email_notifications" {{ Auth::user()->email_notifications ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="toggle-item">
                    <span><i class="fas fa-mobile-alt"></i> Push notifications</span>
                    <label class="switch">
                        <input type="checkbox" id="push_notifications" {{ Auth::user()->push_notifications ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Preferences Card -->
        <div class="settings-card">
            <div class="card-header">
                <i class="fas fa-sliders-h"></i>
                <h2>Preferences</h2>
            </div>
            <div class="card-body">
                <div class="toggle-item">
                    <span><i class="fas fa-moon"></i> Dark mode</span>
                    <label class="switch">
                        <input type="checkbox" id="darkModeToggle">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-language"></i> Language</label>
                    <select id="languageSelect" class="form-control">
                        <option value="en">English</option>
                        <option value="es">Español</option>
                        <option value="fr">Français</option>
                    </select>
                </div>
                <div class="toggle-item">
                    <span><i class="fas fa-universal-access"></i> Accessibility mode</span>
                    <label class="switch">
                        <input type="checkbox" id="accessibilityToggle">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Support Card -->
        <div class="settings-card">
            <div class="card-header">
                <i class="fas fa-headset"></i>
                <h2>Support</h2>
            </div>
            <div class="card-body support-buttons">
                <button type="button" class="btn-primary" id="contactAdminBtn">
                    <i class="fas fa-envelope"></i> Contact admin
                </button>
                <button type="button" class="btn-outline" id="reportIssueBtn">
                    <i class="fas fa-exclamation-triangle"></i> Report issue
                </button>
                <a href="#" class="help-link"><i class="fas fa-question-circle"></i> Help / FAQ</a>
            </div>
        </div>
    </div>
</div>

<!-- Hidden forms for logout actions -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
<form id="logoutAllForm" action="{{ route('logout.all') }}" method="POST" style="display:none;">@csrf</form>
@endsection

@section('scripts')
<script>
    // CSRF token setup
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // ---------- AVATAR UPLOAD ----------
    const avatarEditBtn = document.getElementById('avatarEditBtn');
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    avatarEditBtn.addEventListener('click', () => avatarInput.click());
    avatarInput.addEventListener('change', function() {
        if (!avatarInput.files.length) return;
        const file = avatarInput.files[0];
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', token);

        // Preview locally
        const reader = new FileReader();
        reader.onload = (e) => { avatarPreview.src = e.target.result; };
        reader.readAsDataURL(file);

        fetch('{{ route("settings.avatar") }}', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                avatarPreview.src = data.avatar_url + '?t=' + Date.now();
                showMessage('profileMessage', 'Avatar updated', 'success');
            } else {
                showMessage('profileMessage', data.message || 'Avatar upload failed', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showMessage('profileMessage', 'Network error uploading avatar', 'error');
        });
    });

    // ---------- SAVE PROFILE (Name, Phone, Address) ----------
    const saveBtn = document.getElementById('saveProfileBtn');
    saveBtn.addEventListener('click', function() {
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner"></span> Saving...';

        const data = {
            name: document.getElementById('fullName').value,
            phone: document.getElementById('phoneNumber').value,
            address: document.getElementById('address').value,
            _token: token
        };

        fetch('{{ route("settings.profile") }}', {
            method: 'PUT',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            const text = await response.text();
            if (!response.ok) {
                let msg = text || `HTTP ${response.status}`;
                try {
                    const json = JSON.parse(text);
                    msg = json.message || JSON.stringify(json);
                } catch (e) {}
                throw new Error(msg);
            }
            try { return JSON.parse(text); } catch(e) { return {} }
        })
        .then(data => {
            if (data.success) {
                showMessage('profileMessage', data.message || 'Profile updated successfully!', 'success');
                setTimeout(() => { window.location.href = '{{ route("profile") }}'; }, 1200);
            } else {
                showMessage('profileMessage', data.message || 'Validation error', 'error');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Save profile error:', error);
            showMessage('profileMessage', 'Error: ' + error.message, 'error');
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        });
    });

    function showMessage(elementId, message, type) {
        const el = document.getElementById(elementId);
        if (!el) return;
        el.innerHTML = (type === 'success' ? '✓ ' : '⚠️ ') + message;
        el.className = type === 'success' ? 'message-success' : 'message-error';
        setTimeout(() => {
            if (el) el.innerHTML = '';
        }, 4000);
    }

    // ---------- CHANGE PASSWORD ----------
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Updating...';

        const formData = new FormData(form);
        formData.append('_token', token);
        fetch('{{ route("settings.password") }}', {
            method: 'PUT',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage('passwordMessage', data.message, 'success');
                form.reset();
            } else {
                showMessage('passwordMessage', data.message || 'Error changing password', 'error');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        })
        .catch(() => {
            showMessage('passwordMessage', 'Network error', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // ---------- LOGOUT ----------
    document.getElementById('logoutBtn').addEventListener('click', () => {
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('logoutForm').submit();
        }
    });
    document.getElementById('logoutAllBtn').addEventListener('click', () => {
        if (confirm('This will logout all your devices. Are you sure?')) {
            document.getElementById('logoutAllForm').submit();
        }
    });

    // ---------- NOTIFICATION TOGGLES ----------
    const saveNotification = (key, value) => {
        fetch('{{ route("settings.notifications") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ [key]: value })
        }).catch(err => console.log(err));
    };
    document.querySelectorAll('.toggle-item input').forEach(toggle => {
        toggle.addEventListener('change', function() {
            saveNotification(this.id, this.checked);
        });
    });

    // ---------- DARK MODE ----------
    const darkToggle = document.getElementById('darkModeToggle');
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkToggle.checked = true;
    }
    darkToggle.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
        }
    });

    // ---------- SUPPORT BUTTONS (placeholders) ----------
    document.getElementById('contactAdminBtn').addEventListener('click', () => alert('Contact admin form would open here.'));
    document.getElementById('reportIssueBtn').addEventListener('click', () => alert('Report issue form would open here.'));
    document.getElementById('languageSelect').addEventListener('change', (e) => alert('Language changed to ' + e.target.value));
    document.getElementById('accessibilityToggle').addEventListener('change', (e) => alert('Accessibility mode: ' + (e.target.checked ? 'ON' : 'OFF')));
</script>
@endsection
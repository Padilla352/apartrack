@extends('layouts.admin')

@section('content')
<div class="settings-page">
    <div class="settings-container">
        {{-- Header Section --}}
        <div class="settings-header">
            <div>
                <h1 class="settings-title">Settings</h1>
                <p class="settings-subtitle">Manage account security and personal information</p>
            </div>
            
            @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="settings-grid">
            {{-- Sidebar Navigation --}}
            <div class="settings-sidebar">
                <button type="button" onclick="switchTab('profile')" id="btn-profile" class="tab-btn tab-btn-active">
                    <span><i class="fas fa-user-circle"></i> Profile</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <button type="button" onclick="switchTab('security')" id="btn-security" class="tab-btn tab-btn-inactive">
                    <span><i class="fas fa-shield-alt"></i> Security</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            {{-- Main Content Area --}}
            <div class="settings-content">
                
                {{-- Profile Tab --}}
                <div id="tab-profile" class="tab-content tab-content-active">
                    <div class="settings-card">
                        <!-- FIXED: Changed from 'settings.update' to 'admin.settings.update' -->
                        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                            @csrf
                            <div class="profile-photo-section">
                                <div class="profile-photo-wrapper">
                                    <div class="profile-photo-frame">
                                        <div class="profile-photo-inner">
                                            @php
                                                $profileImagePath = isset($admin->profile_image) ? $admin->profile_image : null;
                                                $hasImage = !empty($profileImagePath) && Storage::disk('public')->exists($profileImagePath);
                                            @endphp
                                            
                                            <img id="preview" 
                                                 src="{{ $hasImage ? asset('storage/' . $profileImagePath) : '#' }}" 
                                                 class="profile-photo-img {{ !$hasImage ? 'hidden' : '' }}">
                                            
                                            <i id="default-icon" class="fas fa-user profile-photo-icon {{ $hasImage ? 'hidden' : '' }}"></i>
                                        </div>
                                    </div>
                                    <label for="profile_image" class="profile-photo-upload-btn">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                    <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*" onchange="previewImage(event)">
                                </div>
                                <div>
                                    <h3 class="profile-photo-title">Profile Photo</h3>
                                    <p class="profile-photo-hint">Recommended: Square JPG or PNG, Max 2MB</p>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="error-box">
                                    <ul class="error-list">
                                        @foreach ($errors->all() as $error)
                                            <li>⚠️ {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required class="form-input">
                            </div>

                            <button type="submit" class="btn-save">
                                Save Profile
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Security Tab --}}
                <div id="tab-security" class="tab-content tab-content-hidden">
                    <div class="settings-card">
                        <!-- FIXED: Changed from 'settings.password' to 'admin.settings.password' -->
                        <form action="{{ route('admin.settings.password') }}" method="POST" class="settings-form">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" required class="form-input">
                                @error('current_password')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" required class="form-input">
                                @error('new_password')
                                    <p class="error-text">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" required class="form-input">
                            </div>

                            <button type="submit" class="btn-update-password">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('tab-content-active');
            tab.classList.add('tab-content-hidden');
        });
        
        const selectedTab = document.getElementById('tab-' + tabName);
        if (selectedTab) {
            selectedTab.classList.remove('tab-content-hidden');
            selectedTab.classList.add('tab-content-active');
        }

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('tab-btn-active');
            btn.classList.add('tab-btn-inactive');
        });
        
        const activeBtn = document.getElementById('btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('tab-btn-inactive');
            activeBtn.classList.add('tab-btn-active');
        }
    }

    function previewImage(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('preview');
        const defaultIcon = document.getElementById('default-icon');

        if (file) {
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                event.target.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert('Image size must be less than 2MB.');
                event.target.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewImg && defaultIcon) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    defaultIcon.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->has('current_password') || $errors->has('new_password'))
            switchTab('security');
        @endif
        
        console.log('%c⚙️ APARTrack Settings | Matching Dashboard Theme', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
</script>

<style>
/* ========== SETTINGS PAGE - MATCHING DASHBOARD THEME ========== */
/* NO TAILWIND */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
}

/* Main Container - FIXED: Matching dashboard background */
.settings-page {
    min-height: 100vh;
    background: #0a0c10 !important;
    background-attachment: fixed;
    padding: 1.5rem;
}

/* Match the exact same body background as dashboard */
html, body, #app {
    background-color: #0a0c10;
}

@media (min-width: 768px) {
    .settings-page {
        padding: 2rem;
    }
}

.settings-container {
    max-width: 896px;
    margin: 0 auto;
}

/* Header Section */
.settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.settings-title {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: -0.3px;
}

@media (min-width: 768px) {
    .settings-title {
        font-size: 1.75rem;
    }
}

.settings-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    margin-top: 0.25rem;
}

/* Alert Messages */
.alert-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    padding: 0.5rem 1.25rem;
    border-radius: 60px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    animation: fadeIn 0.3s ease-out forwards;
}

.alert-error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #ffffff;
    padding: 0.5rem 1.25rem;
    border-radius: 60px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Settings Grid */
.settings-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr 2fr;
        gap: 2rem;
    }
}

/* Sidebar Navigation */
.settings-sidebar {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.tab-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-radius: 20px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.tab-btn i:first-child {
    margin-right: 0.75rem;
}

.tab-btn i:last-child {
    font-size: 0.7rem;
}

.tab-btn-active {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #ffffff;
    box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.2);
}

.tab-btn-inactive {
    background: rgba(15, 17, 21, 0.9);
    color: #9ca3af;
    border: 1px solid rgba(245, 184, 27, 0.15);
}

.tab-btn-inactive:hover {
    background: rgba(30, 33, 57, 0.9);
    color: #e2e8f0;
}

/* Content Area */
.settings-content {
    width: 100%;
}

.tab-content {
    transition: all 0.3s ease;
}

.tab-content-active {
    display: block;
    animation: fadeInUp 0.3s ease-out forwards;
}

.tab-content-hidden {
    display: none;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Settings Card */
.settings-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    padding: 1.75rem;
    border: 1px solid rgba(245, 184, 27, 0.15);
    transition: all 0.3s ease;
}

.settings-card:hover {
    border-color: rgba(245, 184, 27, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

/* Profile Photo Section */
.profile-photo-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.profile-photo-wrapper {
    position: relative;
}

.profile-photo-frame {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f5b81b, #ffcc44);
    padding: 3px;
    box-shadow: 0 0 20px rgba(245, 184, 27, 0.3);
}

.profile-photo-inner {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #0f1115;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.profile-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-photo-icon {
    font-size: 2rem;
    color: #cbd5e1;
}

.profile-photo-upload-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 32px;
    height: 32px;
    background: #f5b81b;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s ease;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    border: 2px solid #0f1115;
}

.profile-photo-upload-btn:hover {
    transform: scale(1.1);
}

.profile-photo-upload-btn i {
    font-size: 0.875rem;
    color: #0a0c10;
}

.profile-photo-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #ffffff;
}

.profile-photo-hint {
    font-size: 0.625rem;
    color: #64748b;
    margin-top: 0.25rem;
}

/* Form Styles */
.settings-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.7;
    margin-left: 0.5rem;
}

.form-input {
    width: 100%;
    background: rgba(15, 17, 21, 0.8);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 60px;
    padding: 0.875rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #e2e8f0;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.form-input:focus {
    outline: none;
    border-color: #f5b81b;
    box-shadow: 0 0 0 3px rgba(245, 184, 27, 0.1);
}

/* Error Box */
.error-box {
    background: rgba(239, 68, 68, 0.1);
    border-radius: 20px;
    padding: 1rem;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.error-list {
    list-style: none;
    color: #f87171;
    font-size: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.error-text {
    color: #f87171;
    font-size: 0.625rem;
    margin-top: 0.25rem;
    margin-left: 0.5rem;
}

/* Buttons */
.btn-save {
    width: 100%;
    padding: 0.875rem 1.25rem;
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
    border: 1px solid rgba(245, 184, 27, 0.3);
    border-radius: 60px;
    font-weight: 800;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
}

@media (min-width: 768px) {
    .btn-save {
        width: auto;
        padding: 0.875rem 2rem;
    }
}

.btn-save:hover {
    background: rgba(245, 184, 27, 0.22);
    border-color: #f5b81b;
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(245, 184, 27, 0.2);
}

.btn-update-password {
    width: 100%;
    padding: 0.875rem 1.25rem;
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 60px;
    font-weight: 800;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
}

@media (min-width: 768px) {
    .btn-update-password {
        width: auto;
        padding: 0.875rem 2rem;
    }
}

.btn-update-password:hover {
    background: rgba(239, 68, 68, 0.22);
    border-color: #ef4444;
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);
}

/* Utility Classes */
.hidden {
    display: none;
}

/* Custom Scrollbar - Matching dashboard */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #0f1115;
}

::-webkit-scrollbar-thumb {
    background: #f5b81b;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #ffcc44;
}

/* Text Selection - Matching dashboard */
::selection {
    background: #f5b81b;
    color: #0a0c10;
}

/* Responsive */
@media (max-width: 640px) {
    .settings-card {
        padding: 1.5rem;
    }
    
    .profile-photo-section {
        flex-direction: column;
        text-align: center;
    }
    
    .settings-title {
        font-size: 1.25rem;
    }
    
    .alert-success, .alert-error {
        width: 100%;
        justify-content: center;
    }
}

/* Print Styles */
@media print {
    .settings-page {
        background: white !important;
    }
    
    .tab-btn,
    .profile-photo-upload-btn,
    .btn-save,
    .btn-update-password {
        display: none !important;
    }
    
    .settings-card {
        border: 1px solid #ddd;
        background: white !important;
    }
}
</style>
@endsection
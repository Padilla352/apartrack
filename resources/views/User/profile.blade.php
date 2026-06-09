{{-- resources/views/user/profile.blade.php --}}
@extends('layouts.app')

@section('title', 'My Profile - APARTrack')

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
        background: #f5f9ff;
    }

    * {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        background: #f5f9ff;
    }
    .profile-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        animation: fadeIn 0.4s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Back Button Styling */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: white;
        border: 1.5px solid #3b82f6;
        color: #3b82f6;
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .back-button i {
        font-size: 0.9rem;
    }
    .back-button:hover {
        background: #3b82f6;
        color: white;
        transform: translateX(-3px);
        box-shadow: 0 4px 10px rgba(59,130,246,0.2);
    }

    .profile-header {
        background: white;
        border-radius: 32px;
        padding: 2rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
        box-shadow: 0 12px 30px rgba(0,0,0,0.04);
        border: 1px solid #eef2ff;
    }
    .avatar-frame {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #f1f5f9;
        padding: 4px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .avatar-frame img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    .profile-info h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
    .profile-info .user-email {
        color: #3b82f6;
        background: #eff6ff;
        display: inline-block;
        padding: 0.2rem 1rem;
        border-radius: 40px;
        font-size: 0.9rem;
    }
    .member-since {
        font-size: 0.85rem;
        color: #5b6e8c;
        margin-top: 0.5rem;
    }
    .edit-link {
        margin-top: 0.8rem;
    }
    .edit-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .edit-link a:hover {
        text-decoration: underline;
    }
    .info-card {
        background: white;
        border-radius: 28px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.02);
        border: 1px solid #eef2ff;
    }
    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e3a8a;
        border-left: 4px solid #3b82f6;
        padding-left: 0.8rem;
        margin-bottom: 1.2rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.7rem 0;
        border-bottom: 1px dashed #eef2f6;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 500;
        color: #5b6e8c;
    }
    .info-value {
        font-weight: 500;
        color: #1e293b;
    }
    .btn-outline-sm {
        background: transparent;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        padding: 0.5rem 1.5rem;
        border-radius: 40px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-outline-sm:hover {
        background: #3b82f6;
        color: white;
    }
    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
        }
        .info-row {
            flex-direction: column;
            gap: 0.3rem;
        }
        .back-button {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <!-- Back Button to Dashboard -->
    <a href="{{ route('dashboard') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>

    <div class="profile-header">
        <div class="avatar-frame">
            {{-- ✅ FIXED AVATAR: supports both Facebook URL and local storage path --}}
            @php
                $avatar = Auth::user()->avatar;
                $avatarSrc = asset('images/default-avatar.png'); // fallback default
                
                if ($avatar) {
                    if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                        // It's a full URL (e.g., from Facebook)
                        $avatarSrc = $avatar;
                    } else {
                        // It's a local storage path
                        $avatarSrc = Storage::url($avatar);
                    }
                }
            @endphp
            <img src="{{ $avatarSrc }}" alt="Avatar">
        </div>
        <div class="profile-info">
            <h1>{{ Auth::user()->name }}</h1>
            <div class="user-email"><i class="fas fa-envelope"></i> {{ Auth::user()->email }}</div>
            <div class="member-since">
                <i class="far fa-calendar-alt"></i> Member since {{ Auth::user()->created_at->format('F Y') }}
            </div>
            <div class="edit-link">
                <a href="{{ route('settings') }}"><i class="fas fa-sliders-h"></i> Edit profile & settings →</a>
            </div>
        </div>
    </div>

    <div class="info-card">
        <div class="card-title"><i class="fas fa-user"></i> Personal Information</div>
        <div class="info-row">
            <span class="info-label">Full Name</span>
            <span class="info-value">{{ Auth::user()->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email Address</span>
            <span class="info-value">{{ Auth::user()->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone Number</span>
            <span class="info-value">{{ Auth::user()->phone ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Address</span>
            <span class="info-value">{{ Auth::user()->address ?? '—' }}</span>
        </div>
    </div>

    <div class="info-card" style="text-align: center;">
        <a href="{{ route('settings') }}" class="btn-outline-sm">
            <i class="fas fa-cog"></i> Go to Settings
        </a>
    </div>
</div>
@endsection
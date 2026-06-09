<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'APARTrack - Explore')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    @yield('styles')
</head>
<body>

<!-- ========== GUEST NAVBAR (no search, no profile) ========== -->
<nav class="navbar guest-navbar">
    <div class="nav-container">
        <div class="nav-left">
            <a href="{{ route('explore') }}" class="logo">
                <img src="{{ asset('images/apartrack_logo/logo.png') }}" alt="APARTrack Logo" class="logo-img">
                <span class="logo-text">APARTrack</span>
            </a>
        </div>
        <div class="nav-right">
            <a href="{{ route('explore') }}" class="nav-link {{ request()->routeIs('explore') ? 'active' : '' }}">
                <i class="fa fa-home"></i>
                <span class="nav-label">Home</span>
            </a>
            <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                <i class="fa fa-info-circle"></i>
                <span class="nav-label">About</span>
            </a>
            <div class="guest-actions">
                <a href="{{ route('login') }}" class="btn-guest-login">
                    <i class="fa fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="btn-guest-register">
                    <i class="fa fa-user-plus"></i>
                    <span>Register</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Main content -->
<main class="main-content">
    @yield('content')
</main>

<!-- Styles for guest navbar (reuse dashboard styles + adjustments) -->
<style>
    /* Reuse dashboard navbar styles (assuming they exist in dashboard.css) */
    /* If not, include the minimal guest navbar CSS below */
    .guest-navbar {
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 0.75rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid #e2e8f0;
    }
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }
    .nav-left {
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    .logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    .logo-img {
        height: 32px;
        width: auto;
    }
    .logo-text {
        font-size: 1.3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #3b82f6, #1e3a8a);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .nav-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 0;
        position: relative;
    }
    .nav-link i {
        font-size: 1.1rem;
    }
    .nav-link:hover {
        color: #3b82f6;
    }
    .nav-link.active {
        color: #3b82f6;
    }
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #3b82f6;
        border-radius: 2px;
    }
    .guest-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .btn-guest-login, .btn-guest-register {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    .btn-guest-login {
        background: transparent;
        color: #3b82f6;
        border: 1px solid #3b82f6;
    }
    .btn-guest-login:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
    }
    .btn-guest-register {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
    }
    .btn-guest-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    @media (max-width: 768px) {
        .nav-container {
            flex-direction: column;
            text-align: center;
        }
        .nav-left, .nav-right {
            justify-content: center;
        }
        .nav-label {
            display: none;
        }
    }
    .main-content {
        min-height: calc(100vh - 70px);
    }
    
</style>

@yield('scripts')
</body>
</html>
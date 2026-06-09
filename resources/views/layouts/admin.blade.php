<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartrack - Property Management System | Black Edition</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* -------------------- RESET & GLOBAL STYLES -------------------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #000000;
            color: #e5e7eb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
        }

        button {
            background: none;
            border: none;
            cursor: pointer;
        }

        ul {
            list-style: none;
        }

        /* -------------------- LAYOUT CONTAINERS -------------------- */
        .app-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-layout {
            display: flex;
            flex: 1;
            padding: 0.5rem;
            gap: 1rem;
            min-height: 0;
        }

        @media (min-width: 768px) {
            .main-layout {
                padding: 1rem;
                gap: 1rem;
            }
        }

        @media (min-width: 1024px) {
            .main-layout {
                gap: 1.5rem;
            }
        }

        /* -------------------- NAVBAR STYLES -------------------- */
        .navbar {
            background-color: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #1f1f1f;
            padding: 0.75rem 1rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            align-items: center;
            border-radius: 0 0 1.5rem 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            margin: 0.5rem 0.5rem 0 0.5rem;
            position: sticky;
            top: 0;
            z-index: 60;
        }

        @media (min-width: 768px) {
            .navbar {
                padding: 1rem 2rem;
                margin: 0.5rem 1rem 0 1rem;
            }
        }

        @media (min-width: 1024px) {
            .navbar {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Navbar Left Section */
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-toggle-btn {
            padding: 0.5rem;
            color: #9ca3af;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            background: transparent;
        }

        .sidebar-toggle-btn:hover {
            color: #ffffff;
            background-color: #1a1a1a;
        }

        @media (min-width: 1024px) {
            .sidebar-toggle-btn {
                display: none;
            }
        }

        .logo-wrapper {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 9999px;
            background-color: #000000;
            border: 1px solid #2a2a2a;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 0.25rem;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 9999px;
        }

        .brand-text {
            color: #ffffff;
            letter-spacing: 0.1em;
            font-size: 1rem;
            font-weight: 700;
            background: linear-gradient(to right, #ffffff, #9ca3af);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        /* Navbar Center */
        .navbar-center {
            display: none;
            justify-content: center;
        }

        @media (min-width: 1024px) {
            .navbar-center {
                display: flex;
            }
        }

        .admin-badge {
            color: #d4af37;
            font-weight: 900;
            letter-spacing: 0.3em;
            font-size: 1.25rem;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Navbar Right */
        .navbar-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
        }

        /* Notification Button & Menu */
        .notification-wrapper {
            position: relative;
        }

        .notification-btn {
            padding: 0.625rem;
            border-radius: 0.75rem;
            background-color: #0a0a0a;
            border: 1px solid #252525;
            transition: all 0.2s ease;
            position: relative;
        }

        .notification-btn:hover {
            border-color: rgba(212, 175, 55, 0.5);
            background-color: #141414;
        }

        .notification-btn i {
            color: #d1d5db;
            transition: color 0.2s ease;
        }

        .notification-btn:hover i {
            color: #d4af37;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            font-size: 0.65rem;
            padding: 0.125rem 0.375rem;
            font-weight: bold;
            min-width: 1.25rem;
            text-align: center;
        }

        .notification-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.75rem;
            width: 20rem;
            background-color: #0a0a0a;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid #222222;
            z-index: 100;
            backdrop-filter: blur(4px);
            overflow: hidden;
        }

        .notification-menu.show {
            display: block;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid #1f1f1f;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h3 {
            font-weight: 700;
            color: #ffffff;
        }

        .notification-list {
            max-height: 350px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            padding: 1rem;
            border-bottom: 1px solid #151515;
            transition: background 0.2s ease;
            text-decoration: none;
        }

        .notification-item:hover {
            background-color: #111111;
        }

        .notification-icon {
            width: 2.5rem;
            height: 2.5rem;
            background-color: #1a1a1a;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon.gold {
            color: #d4af37;
        }

        .notification-icon.red {
            color: #ef4444;
        }

        .notification-content {
            margin-left: 0.75rem;
        }

        .notification-title {
            font-size: 0.875rem;
            color: #e5e7eb;
        }

        .notification-title strong {
            color: #ffffff;
            font-weight: 700;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .empty-notifications {
            padding: 2rem;
            text-align: center;
            color: #6b7280;
        }

        .empty-notifications i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .profile-avatar {
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(to bottom right, #1f1f1f, #0a0a0a);
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(212, 175, 55, 0.3);
            transition: all 0.2s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-btn:hover .profile-avatar {
            border-color: #d4af37;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar i {
            color: #d4af37;
            font-size: 0.875rem;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.75rem;
            width: 14rem;
            background-color: #0a0a0a;
            border-radius: 1rem;
            border: 1px solid #222222;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            z-index: 100;
            padding: 0.5rem;
        }

        .profile-dropdown.active .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem;
            border-radius: 0.75rem;
            transition: background 0.2s;
            color: #d1d5db;
            text-decoration: none;
        }

        .dropdown-item:hover {
            background-color: #141414;
        }

        .dropdown-item i {
            margin-right: 0.75rem;
            color: #d4af37;
        }

        .dropdown-divider {
            border-top: 1px solid #1f1f1f;
            margin: 0.25rem 0;
        }

        .logout-btn {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            color: #f87171;
            padding: 0.75rem;
            border-radius: 0.75rem;
            transition: background 0.2s;
            cursor: pointer;
            font-size: 1rem;
        }

        .logout-btn:hover {
            background-color: rgba(69, 10, 10, 0.4);
        }

        .logout-btn i {
            margin-right: 0.75rem;
        }

        /* -------------------- SIDEBAR STYLES -------------------- */
        .sidebar {
            background-color: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(4px);
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            border: 1px solid #1a1a1a;
            width: 18rem;
            flex-shrink: 0;
            transition: left 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        @media (max-width: 1023px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -300px;
                height: 100vh;
                z-index: 200;
                border-radius: 0 1.5rem 1.5rem 0;
                margin: 0;
                padding-top: 5rem;
                background-color: #000000;
                border-right: 1px solid #1f1f1f;
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background-color: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(3px);
                z-index: 150;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 0.75rem;
            color: #d1d5db;
            transition: all 0.2s;
            text-decoration: none;
        }

        .sidebar-link:hover {
            background-color: #0f0f0f;
            color: #ffffff;
        }

        .sidebar-link.active {
            background-color: #111111;
            color: #ffffff;
            border-left: 2px solid #d4af37;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.1);
        }

        .sidebar-link i {
            width: 1.25rem;
            font-size: 1.125rem;
        }

        .sidebar-link span {
            font-weight: 500;
        }

        /* -------------------- MAIN CONTENT -------------------- */
        .main-content {
            flex: 1;
            background-color: #030303;
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #181818;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            min-width: 0;
            overflow: hidden;
        }

        .main-content img {
            max-width: 100%;
            height: auto;
        }

        /* -------------------- UTILITY CLASSES -------------------- */
        .hidden {
            display: none;
        }

        .text-center {
            text-align: center;
        }

        @media (min-width: 1024px) {
            .lg\:space-x-4 {
                gap: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>

<body>
<div id="sidebar-overlay-bg" class="sidebar-overlay"></div>

<div class="app-container">
    <nav class="navbar">
        <div class="navbar-left">
            <button id="sidebar-toggle" class="sidebar-toggle-btn">
                <i class="fas fa-bars fa-xl"></i>
            </button>
            <div class="logo-wrapper">
                <img src="{{ asset('admin/apartrack_logo.png') }}" class="logo-img" alt="Apartrack Logo">
            </div>
            <span class="brand-text">APARTrack</span>
        </div>

        <div class="navbar-center">
            <span class="admin-badge">ADMIN</span>
        </div>

        <div class="navbar-right">
            <div class="notification-wrapper">
                <button id="notification-btn" class="notification-btn">
                    <i class="fas fa-bell"></i>
                </button>
                <div id="notification-menu" class="notification-menu">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                    </div>
                    <div class="notification-list">
                        <div class="empty-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <p>No notifications yet</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-dropdown" id="profile-dropdown">
                <div class="profile-btn" id="profile-btn">
                    <div class="profile-avatar">
                        @php
                            $admin = session('admin_data') ?? Auth::user();
                            $profileImagePath = isset($admin->profile_image) ? $admin->profile_image : null;
                            $hasImage = !empty($profileImagePath) && Storage::disk('public')->exists($profileImagePath);
                        @endphp
                        @if($hasImage)
                            <img src="{{ asset('storage/' . $profileImagePath) }}" alt="Profile">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                </div>
                <div class="dropdown-menu">
                    <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}" id="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-layout">
        <aside id="sidebar" class="sidebar">
           @php
$menu = [
    // Dashboard
    ['route' => 'admin.dashboard', 'icon' => 'fa-chart-line', 'label' => 'Dashboard'],
    
    // Users Management
    ['route' => 'users-management.index', 'icon' => 'fa-users', 'label' => 'Users Management'],
    
    // Apartments
    ['route' => 'admin.apartments.index', 'icon' => 'fa-building', 'label' => 'Apartments'],
    
    // Apartment Approval
    ['route' => 'admin.apartments.pending', 'icon' => 'fa-check-double', 'label' => 'Apartment Approval'],
    
    // Business Approval (NEW)
    ['route' => 'admin.business.pending', 'icon' => 'fa-store', 'label' => 'Commercial Spaces Approval'],
    
    // Business Listings
    ['route' => 'admin.business.index', 'icon' => 'fa-list', 'label' => 'Commercial Spaces Listings'],
    
    // Permit Numbers
    ['route' => 'permit-numbers.index', 'icon' => 'fa-clipboard-list', 'label' => 'Permit Numbers'],
    
    // Owner Verification
    ['route' => 'admin.permit-verification.index', 'icon' => 'fa-check-circle', 'label' => 'Owner Verification'],
    
    // Analytics
    ['route' => 'reports.analytics', 'icon' => 'fa-chart-pie', 'label' => 'Analytics'],
    
    // Complaints
    ['route' => 'complaints.index', 'icon' => 'fa-exclamation-triangle', 'label' => 'Complaints'],
];
@endphp
            <ul class="sidebar-menu">
                @foreach($menu as $item)
                    <li>
                        <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                           class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                            <i class="fas {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // SIDEBAR TOGGLE (Mobile)
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay-bg');
    const sidebarToggle = document.getElementById('sidebar-toggle');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // NOTIFICATION DROPDOWN
    const notificationBtn = document.getElementById('notification-btn');
    const notificationMenu = document.getElementById('notification-menu');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (notificationBtn && notificationMenu) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationMenu.classList.toggle('show');
            if (profileDropdown) profileDropdown.classList.remove('active');
        });
    }

    // PROFILE DROPDOWN
    const profileBtn = document.getElementById('profile-btn');
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
            if (notificationMenu) notificationMenu.classList.remove('show');
        });
    }

    // GLOBAL CLICK TO CLOSE DROPDOWNS
    document.addEventListener('click', function(e) {
        if (notificationMenu && !notificationMenu.contains(e.target) && notificationBtn && !notificationBtn.contains(e.target)) {
            notificationMenu.classList.remove('show');
        }
        if (profileDropdown && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('active');
        }
    });
});
</script>

@stack('scripts')
</body>
</html>
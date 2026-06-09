{{-- resources/views/partials/navbar.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/js/app.js'])
</head>
<body>
@php
    $currentRoute = Route::currentRouteName();
    $hideSearchAndAbout = in_array($currentRoute, ['login', 'register']);
@endphp

<header class="main-header">
    <div class="header-container">
        <div class="navbar-main">
            <!-- LEFT: Logo -->
            <div class="logo-section">
                <a href="{{ auth()->check() ? route('dashboard') : route('explore') }}" class="logo-link">
                    <div class="logo-icon">
                        <img src="{{ asset('images/apartrack_logo/APARTrack-logo.png') }}" alt="APARTrack Logo">
                        <div class="logo-glow"></div>
                    </div>
                    <div class="logo-text">
                        <span class="logo-name">APARTrack</span>
                        <span class="logo-tagline">Find Your Ideal Apartment</span>
                    </div>
                </a>    
            </div>

            <!-- CENTER: Search Bar with Filter Dropdown - hidden on login/register -->
            @unless($hideSearchAndAbout)
            <div class="search-section">
                <form method="GET" action="{{ auth()->check() ? route('home') : route('explore') }}" class="search-form" id="searchForm">
                    <div class="search-bar-wrapper">
                        <div class="search-input-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="searchInput"
                                placeholder="{{ auth()->check() ? 'Search apartments...' : 'Search boarding houses...' }}" 
                                value="{{ request('search') }}"
                                class="search-input" autocomplete="off">
                            @if(request('search'))
                                <button type="button" class="search-clear-right" id="clearSearchBtn">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            @endif
                            <div class="filter-wrapper">
                                <button type="button" class="filter-icon-btn" id="filterIconBtn">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                <div class="filter-dropdown" id="filterDropdown">
                                    <div class="filter-dropdown-header">
                                        <h4><i class="fas fa-sort-amount-down"></i> Sort Listings</h4>
                                    </div>
                                    <div class="filter-options">
                                        <label class="filter-option"><input type="radio" name="filter" value="" {{ request('filter') == '' ? 'checked' : '' }}><span>Default</span></label>
                                        <label class="filter-option"><input type="radio" name="filter" value="price_low" {{ request('filter') == 'price_low' ? 'checked' : '' }}><span>Price: Low to High</span></label>
                                        <label class="filter-option"><input type="radio" name="filter" value="price_high" {{ request('filter') == 'price_high' ? 'checked' : '' }}><span>Price: High to Low</span></label>
                                        <label class="filter-option"><input type="radio" name="filter" value="name_asc" {{ request('filter') == 'name_asc' ? 'checked' : '' }}><span>Name: A to Z</span></label>
                                        <label class="filter-option"><input type="radio" name="filter" value="name_desc" {{ request('filter') == 'name_desc' ? 'checked' : '' }}><span>Name: Z to A</span></label>
                                    </div>
                                </div>
                            </div>
                            <div id="autocompleteDropdown" class="autocomplete-dropdown"></div>
                        </div>
                    </div>
                </form>
            </div>
            @endunless

            <!-- RIGHT: Navigation Actions -->
            <div class="nav-actions">
                @unless($hideSearchAndAbout)
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                    <i class="fas fa-info-circle"></i><span>About</span><div class="nav-indicator"></div>
                </a>
                @endunless

                @auth
                <!-- Notifications Dropdown -->
                <div class="notification-wrapper" id="notificationWrapper">
                    <button class="nav-link notification-trigger" id="notificationTrigger">
                        <i class="fas fa-bell"></i><span>Notification</span>
                        <span class="notification-badge" id="notificationBadge">0</span><div class="nav-indicator"></div>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="dropdown-header">
                            <h4>Notifications</h4>
                            <button class="mark-all-read" id="markAllReadBtn">Mark all read</button>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-item text-center text-muted">Loading...</div>
                        </div>
                        <div class="dropdown-footer">
                            <a href="{{ route('notifications.index') }}">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="user-dropdown-wrapper">
                    <button class="user-dropdown-trigger" id="userDropdownBtn">
                        <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) . '?t=' . time() : asset('images/default-avatar.png') }}" 
                             alt="Avatar" class="user-avatar-img">
                        <span class="user-name">{{ Auth::user()->name ?? 'Account' }}</span>
                        <i class="fas fa-chevron-down user-arrow"></i>
                    </button>
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <div class="user-info-header">
                            <div class="user-info-avatar">
                                <img src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) . '?t=' . time() : asset('images/default-avatar.png') }}" alt="Avatar">
                            </div>
                            <div class="user-info-details">
                                <div class="user-info-name">{{ Auth::user()->name }}</div>
                                <div class="user-info-email">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profile') }}" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                        <a href="{{ route('settings') }}" class="dropdown-item"><i class="fas fa-cog"></i> Settings</a>
                         <a href="{{ route('settings') }}" class="dropdown-item"> <i class="fas fa-headset"></i>Support</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">@csrf</form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                @else
                <!-- Guest Dropdown -->
                <div class="guest-dropdown-wrapper">
                    <button type="button" class="guest-dropdown-trigger" id="guestDropdownBtn">
                        <i class="fa-solid fa-user"></i>
                        <span class="guest-label">Guest</span>
                        <i class="fa-solid fa-chevron-down guest-arrow"></i>
                    </button>
                    <div class="guest-dropdown-menu" id="guestDropdownMenu">
                        <a href="{{ route('help') }}" class="dropdown-item"><i class="fa-solid fa-circle-question"></i> Help Center</a>
                        <a href="{{ route('owner.login') }}" class="dropdown-item host-item">
                            <i class="fa-solid fa-house-chimney-user"></i>
                            <div class="host-content"><strong>Become a host</strong><span>It's easy to start hosting and earn extra income.</span></div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('login') }}" class="dropdown-item login-signup-item"><i class="fa-solid fa-right-to-bracket"></i> Log In</a>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<style>
    /* ========== FIX STICKY & REMOVE STRAY CHARACTERS ========== */
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100%;
        overflow-x: hidden;
    }
    body {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    /* Hide any pseudo-element that could cause a stray slash */
    body::before, body::after {
        display: none !important;
        content: none !important;
    }
    /* Force navbar to stick to top without movement */
    .main-header {
        position: sticky !important;
        top: 0 !important;
        left: 0;
        right: 0;
        width: 100%;
        background: #ffffff;
        z-index: 1000;
        margin-top: 0 !important;
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.05);
        border-bottom: 1px solid #eef2f8;
        will-change: transform;
        transform: translateZ(0);
    }
    /* Remove any top margin from the first element after navbar */
    .main-header + * {
        margin-top: 0 !important;
    }

    /* ========== GLOBAL POPPINS ========== */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    .header-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; }
    .navbar-main { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; }
    /* ========== LOGO ========== */
    .logo-section { flex-shrink: 0; }
    .logo-link { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; transition: transform 0.2s ease; }
    .logo-link:hover { transform: scale(1.02); }
    .logo-icon { position: relative; width: 56px; height: 56px; }
    .logo-icon img { width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(59,130,246,0.15)); }
    .logo-glow { position: absolute; inset: -6px; background: radial-gradient(circle, rgba(59,130,246,0.2), transparent); border-radius: 50%; opacity: 0; transition: opacity 0.3s; pointer-events: none; }
    .logo-link:hover .logo-glow { opacity: 0.8; }
    .logo-text { display: flex; flex-direction: column; line-height: 1.2; }
    .logo-name { font-size: 1.7rem; font-weight: 800; background: linear-gradient(135deg, #1e40af, #3b82f6); -webkit-background-clip: text; background-clip: text; color: transparent; letter-spacing: -0.5px; }
    .logo-tagline { font-size: 0.7rem; color: #5b6e8c; font-weight: 500; letter-spacing: 0.3px; }
    /* ========== SEARCH ========== */
    .search-section { flex: 1; max-width: 600px; margin: 0 auto; position: relative; }
    .search-bar-wrapper { display: flex; align-items: center; gap: 0.5rem; }
    .search-input-container { flex: 1; position: relative; background: #f8fafc; border-radius: 48px; border: 1px solid #e2e8f0; transition: all 0.25s; display: flex; align-items: center; }
    .search-input-container:focus-within { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); background: #ffffff; }
    .search-icon { position: absolute; left: 1.2rem; color: #94a3b8; font-size: 1rem; pointer-events: none; transition: color 0.2s; z-index: 1; }
    .search-input-container:focus-within .search-icon { color: #3b82f6; }
    .search-input { width: 100%; background: transparent; border: none; padding: 0.8rem 1rem 0.8rem 3rem; color: #0f172a; font-size: 0.95rem; font-weight: 500; outline: none; }
    .search-clear-right { background: none; border: none; color: #94a3b8; font-size: 1rem; cursor: pointer; padding: 0 0.25rem; transition: color 0.2s; margin-left: auto; }
    .search-clear-right:hover { color: #ef4444; }
    .filter-wrapper { position: relative; display: flex; align-items: center; margin-right: 0.5rem; }
    .filter-icon-btn { background: transparent; border: none; padding: 0.5rem 0.6rem; cursor: pointer; color: #64748b; transition: all 0.2s; border-radius: 50%; }
    .filter-icon-btn:hover { color: #3b82f6; background: #eef2ff; }
    .filter-icon-btn.active { color: #3b82f6; }
    .filter-dropdown { position: absolute; top: calc(100% + 12px); right: 0; width: 260px; background: #fff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 12px 28px rgba(0,0,0,0.08); padding: 0.5rem 0; z-index: 1050; opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s; }
    .filter-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); }
    .filter-dropdown-header { padding: 0.75rem 1rem 0.5rem; border-bottom: 1px solid #eef2f6; margin-bottom: 0.5rem; }
    .filter-dropdown-header h4 { color: #0f172a; font-size: 0.9rem; font-weight: 700; margin: 0; }
    .filter-dropdown-header i { color: #3b82f6; margin-right: 0.4rem; }
    .filter-options { display: flex; flex-direction: column; gap: 0.2rem; }
    .filter-option { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1rem; cursor: pointer; color: #334155; font-weight: 500; transition: all 0.15s; }
    .filter-option:hover { background: #f8fafc; }
    .filter-option input { accent-color: #3b82f6; width: 16px; height: 16px; }
    .autocomplete-dropdown { position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: #fff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 12px 28px rgba(0,0,0,0.08); max-height: 320px; overflow-y: auto; z-index: 1100; display: none; }
    .autocomplete-dropdown.show { display: block; animation: fadeInDown 0.2s ease; }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    .autocomplete-item { padding: 0.75rem 1.2rem; cursor: pointer; color: #1e293b; font-weight: 500; border-bottom: 1px solid #f1f5f9; }
    .autocomplete-item:hover, .autocomplete-item.selected { background: #eff6ff; color: #1e40af; }
    .highlight { font-weight: 700; color: #3b82f6; background: #dbeafe; border-radius: 4px; padding: 0 2px; }
    /* ========== RIGHT NAV ACTIONS ========== */
    .nav-actions { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }
    .nav-link { display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem 1rem; border-radius: 40px; color: #475569; text-decoration: none; font-weight: 600; transition: all 0.2s; position: relative; background: transparent; border: none; cursor: pointer; }
    .nav-link i { font-size: 1.1rem; }
    .nav-link:hover { background: #f1f5f9; color: #0f172a; transform: translateY(-1px); }
    .nav-link.active { background: #eff6ff; color: #3b82f6; }
    .nav-indicator { position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%); width: 0; height: 3px; background: #3b82f6; border-radius: 3px; transition: width 0.2s; }
    .nav-link.active .nav-indicator { width: 60%; }
    /* Notification Dropdown */
    .notification-wrapper { position: relative; }
    .notification-trigger { position: relative; }
    .notification-badge { position: absolute; top: -2px; right: -4px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: 700; padding: 2px 5px; border-radius: 30px; line-height: 1; border: 1px solid #ffffff; display: inline-block; min-width: 18px; text-align: center; }
    .notification-dropdown { position: absolute; top: calc(100% + 12px); right: 0; width: 340px; background: #fff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 12px 28px rgba(0,0,0,0.08); opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s; z-index: 1050; }
    .notification-wrapper.active .notification-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-header { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid #eef2f6; }
    .dropdown-header h4 { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0; }
    .mark-all-read { background: none; border: none; color: #3b82f6; font-size: 0.7rem; font-weight: 600; cursor: pointer; }
    .notification-list { max-height: 320px; overflow-y: auto; }
    .notification-item { display: flex; gap: 0.75rem; padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; cursor: pointer; }
    .notification-item.unread { background: #fefce8; }
    .notification-item:hover { background: #f8fafc; }
    .notif-icon { width: 36px; height: 36px; background: #eef2ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3b82f6; flex-shrink: 0; }
    .notif-content { flex: 1; }
    .notif-content p { margin: 0; font-size: 0.8rem; font-weight: 500; color: #1e293b; }
    .notif-content span { font-size: 0.65rem; color: #64748b; }
    .dropdown-footer { padding: 0.6rem; text-align: center; border-top: 1px solid #eef2f6; }
    .dropdown-footer a { color: #3b82f6; text-decoration: none; font-size: 0.75rem; font-weight: 600; }
    /* User & Guest Dropdowns */
    .user-dropdown-wrapper, .guest-dropdown-wrapper { position: relative; margin-left: 0.25rem; }
    .user-dropdown-trigger, .guest-dropdown-trigger { display: flex; align-items: center; gap: 0.6rem; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0.5rem 1rem 0.5rem 0.8rem; border-radius: 40px; color: #1e293b; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .user-dropdown-trigger:hover, .guest-dropdown-trigger:hover { background: #fff; border-color: #cbd5e1; transform: translateY(-1px); }
    .user-avatar-img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; display: block; }
    .user-name, .guest-label { font-weight: 600; font-size: 0.9rem; }
    .user-arrow, .guest-arrow { font-size: 0.7rem; transition: transform 0.2s; color: #64748b; }
    .user-dropdown-wrapper.active .user-arrow, .guest-dropdown-wrapper.active .guest-arrow { transform: rotate(180deg); }
    .user-dropdown-menu, .guest-dropdown-menu { position: absolute; top: calc(100% + 12px); right: 0; min-width: 280px; background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 12px 28px rgba(0,0,0,0.08); opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s; z-index: 1100; overflow: hidden; }
    .guest-dropdown-menu { width: 320px; }
    .user-dropdown-wrapper.active .user-dropdown-menu, .guest-dropdown-wrapper.active .guest-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
    .user-info-header { display: flex; align-items: center; gap: 0.8rem; padding: 0.8rem 1rem; background: #fafcff; border-bottom: 1px solid #eff3f8; }
    .user-info-avatar img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; background: #eef2ff; display: block; }
    .user-info-details { line-height: 1.3; }
    .user-info-name { font-weight: 700; color: #0f172a; font-size: 0.9rem; }
    .user-info-email { font-size: 0.7rem; color: #5b6e8c; word-break: break-all; }
    .dropdown-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: #334155; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.15s; border: none; background: none; width: 100%; text-align: left; cursor: pointer; }
    .dropdown-item i { width: 1.25rem; color: #5b6e8c; }
    .dropdown-item:hover { background: #f8fafc; color: #0f172a; }
    .dropdown-item:hover i { color: #3b82f6; }
    .dropdown-divider { height: 1px; background: #eef2f6; margin: 0.25rem 0; }
    .logout-item { color: #dc2626; }
    .logout-item i { color: #dc2626; }
    .logout-item:hover { background: #fef2f2; color: #b91c1c; }
    .host-item { display: flex !important; align-items: flex-start !important; gap: 0.75rem; }
    .host-content { display: flex; flex-direction: column; gap: 0.2rem; }
    .host-content strong { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
    .host-content span { font-size: 0.7rem; color: #64748b; line-height: 1.3; }
    .login-signup-item { color: #3b82f6; font-weight: 700; }
    .login-signup-item i { color: #3b82f6; }
    /* Responsive */
    @media (max-width: 900px) {
        .header-container { padding: 0 1rem; }
        .logo-name { font-size: 1.4rem; }
        .logo-icon { width: 48px; height: 48px; }
        .logo-tagline { font-size: 0.6rem; }
        .search-section { max-width: 380px; }
        .nav-link span, .user-name, .guest-label { display: none; }
        .nav-link { padding: 0.6rem; }
        .user-dropdown-trigger, .guest-dropdown-trigger { padding: 0.5rem 0.8rem; }
        .guest-dropdown-menu { width: 280px; right: -20px; }
        .user-dropdown-menu { min-width: 260px; }
    }
    @media (max-width: 700px) {
        .navbar-main { gap: 0.8rem; }
        .search-section { max-width: 100%; }
        .search-bar-wrapper { width: 100%; }
    }
    @media (max-width: 550px) {
        .logo-text { display: none; }
        .logo-icon { width: 42px; height: 42px; }
    }
    .text-center { text-align: center; }
    .text-muted { color: #94a3b8; }
</style>

<script>
    // ========== REMOVE STRAY BACKSLASH BEFORE NAVBAR ==========
    (function() {
        // Remove any text node that is only a backslash (possibly with whitespace)
        const body = document.body;
        if (body) {
            const nodes = body.childNodes;
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];
                if (node.nodeType === Node.TEXT_NODE) {
                    const trimmed = node.textContent.trim();
                    if (trimmed === '\\' || trimmed === '/' || trimmed === '') {
                        // Remove this text node
                        node.remove();
                        i--; // adjust index after removal
                    }
                }
            }
        }
    })();

    (function() {
        // ========== AUTOCOMPLETE ==========
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            const barangaysList = [
                "Balangobong", "Bued", "Bugayong", "Camangaan", "Canarvacanan", "Capas", "Cili", "Dumayat",
                "Linmansangan", "Mangcasuy", "Moreno", "Pasileng Norte", "Pasileng Sur", "Poblacion",
                "San Felipe Central", "San Felipe Sur", "San Pablo", "Santiago", "Santo Niño", "Santa Catalina",
                "Santa Maria Norte", "Sumabnit", "Tabuyoc", "Vacante"
            ];
            const dropdown = document.getElementById('autocompleteDropdown');
            let currentFocus = -1;

            function showSuggestions(inputText) {
                if (!inputText || inputText.trim() === '') { dropdown.classList.remove('show'); return; }
                const lowerInput = inputText.toLowerCase();
                const matches = barangaysList.filter(b => b.toLowerCase().startsWith(lowerInput)).slice(0, 8);
                if (!matches.length) { dropdown.classList.remove('show'); return; }
                let html = '';
                matches.forEach(barangay => {
                    const matchStart = barangay.toLowerCase().indexOf(lowerInput);
                    const matchEnd = matchStart + lowerInput.length;
                    const highlighted = barangay.substring(0, matchStart) + '<span class="highlight">' + barangay.substring(matchStart, matchEnd) + '</span>' + barangay.substring(matchEnd);
                    html += `<div class="autocomplete-item" data-value="${barangay}">${highlighted}</div>`;
                });
                dropdown.innerHTML = html;
                dropdown.classList.add('show');
                document.querySelectorAll('.autocomplete-item').forEach(item => {
                    item.addEventListener('click', function() {
                        searchInput.value = this.getAttribute('data-value');
                        dropdown.classList.remove('show');
                        document.getElementById('searchForm').submit();
                    });
                });
            }
            searchInput.addEventListener('input', function(e) { showSuggestions(this.value); currentFocus = -1; });
            searchInput.addEventListener('keydown', function(e) {
                const items = document.querySelectorAll('.autocomplete-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); currentFocus++; if (currentFocus >= items.length) currentFocus = 0; setActiveItem(items); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); currentFocus--; if (currentFocus < 0) currentFocus = items.length - 1; setActiveItem(items); }
                else if (e.key === 'Enter') { if (currentFocus >= 0 && items[currentFocus]) { e.preventDefault(); searchInput.value = items[currentFocus].getAttribute('data-value'); dropdown.classList.remove('show'); document.getElementById('searchForm').submit(); } }
                else if (e.key === 'Escape') { dropdown.classList.remove('show'); }
            });
            function setActiveItem(items) { items.forEach((item, idx) => idx === currentFocus ? item.classList.add('selected') : item.classList.remove('selected')); if (currentFocus >= 0 && items[currentFocus]) items[currentFocus].scrollIntoView({ block: 'nearest' }); }
            document.addEventListener('click', function(e) { if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('show'); });
        }

        // ========== FILTER DROPDOWN ==========
        const filterIcon = document.getElementById('filterIconBtn');
        const filterDropdown = document.getElementById('filterDropdown');
        if (filterIcon && filterDropdown) {
            filterIcon.addEventListener('click', (e) => { e.stopPropagation(); filterDropdown.classList.toggle('show'); filterIcon.classList.toggle('active'); });
            document.addEventListener('click', (e) => { if (!filterIcon.contains(e.target) && !filterDropdown.contains(e.target)) { filterDropdown.classList.remove('show'); filterIcon.classList.remove('active'); } });
            const filterRadios = document.querySelectorAll('input[name="filter"]');
            filterRadios.forEach(radio => { radio.addEventListener('change', function() { if (this.checked) document.getElementById('searchForm').submit(); }); });
            filterDropdown.addEventListener('click', (e) => e.stopPropagation());
        }

        // ========== USER DROPDOWN ==========
        const userWrapper = document.querySelector('.user-dropdown-wrapper');
        const userBtn = document.getElementById('userDropdownBtn');
        if (userBtn && userWrapper) {
            userBtn.addEventListener('click', (e) => { e.stopPropagation(); userWrapper.classList.toggle('active'); });
            document.addEventListener('click', (e) => { if (!userWrapper.contains(e.target)) userWrapper.classList.remove('active'); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') userWrapper.classList.remove('active'); });
            const userMenu = document.getElementById('userDropdownMenu');
            if (userMenu) userMenu.addEventListener('click', (e) => e.stopPropagation());
        }

        // ========== GUEST DROPDOWN ==========
        const guestWrapper = document.querySelector('.guest-dropdown-wrapper');
        const guestBtn = document.getElementById('guestDropdownBtn');
        if (guestBtn && guestWrapper) {
            guestBtn.addEventListener('click', (e) => { e.stopPropagation(); guestWrapper.classList.toggle('active'); });
            document.addEventListener('click', (e) => { if (!guestWrapper.contains(e.target)) guestWrapper.classList.remove('active'); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') guestWrapper.classList.remove('active'); });
            const guestMenu = document.getElementById('guestDropdownMenu');
            if (guestMenu) guestMenu.addEventListener('click', (e) => e.stopPropagation());
        }

        // ========== CLEAR SEARCH BUTTON ==========
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function(e) { e.preventDefault(); const searchField = document.getElementById('searchInput'); if (searchField) { searchField.value = ''; document.getElementById('searchForm').submit(); } });
        }

        // ========== REAL-TIME NOTIFICATIONS ==========
        @auth
        const notificationWrapper = document.getElementById('notificationWrapper');
        const notificationTrigger = document.getElementById('notificationTrigger');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        const markAllReadBtn = document.getElementById('markAllReadBtn');

        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + ' year ago';
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + ' month ago';
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + ' day ago';
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + ' hour ago';
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + ' minutes ago';
            return 'Just now';
        }

        async function fetchNotifications() {
            try {
                const res = await fetch('/notifications');
                const data = await res.json();
                renderNotifications(data.notifications);
                updateBadge(data.unread_count);
            } catch (err) { console.error('Failed to fetch notifications', err); }
        }

        function renderNotifications(notifications) {
            if (!notificationList) return;
            if (!notifications || notifications.length === 0) {
                notificationList.innerHTML = '<div class="notification-item text-center text-muted">No notifications yet</div>';
                return;
            }
            let html = '';
            notifications.forEach(notif => {
                let data = notif.data;
                if (typeof data === 'string') data = JSON.parse(data);
                const title = data.title || 'Notification';
                const message = data.message || '';
                const icon = data.icon || '🔔';
                const unreadClass = !notif.read_at ? 'unread' : '';
                const time = timeAgo(notif.created_at);
                html += `
                    <div class="notification-item ${unreadClass}" data-id="${notif.id}">
                        <div class="notif-icon"><i class="fas fa-${icon === '💬' ? 'comment' : icon === '🏠' ? 'home' : 'bell'}"></i></div>
                        <div class="notif-content">
                            <p><strong>${escapeHtml(title)}</strong><br>${escapeHtml(message)}</p>
                            <span>${time}</span>
                        </div>
                    </div>
                `;
            });
            notificationList.innerHTML = html;
            document.querySelectorAll('.notification-item.unread').forEach(el => {
                el.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = el.dataset.id;
                    if (id) {
                        await fetch(`/notifications/${id}/mark-read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } });
                        el.classList.remove('unread');
                        updateBadge(parseInt(notificationBadge.innerText) - 1);
                    }
                });
            });
        }

        function updateBadge(count) {
            if (notificationBadge) {
                notificationBadge.innerText = count;
                notificationBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        }

        function escapeHtml(str) { return str.replace(/[&<>]/g, function(m) { if (m === '&') return '&amp;'; if (m === '<') return '&lt;'; if (m === '>') return '&gt;'; return m; }); }

        if (notificationTrigger && notificationWrapper) {
            notificationTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationWrapper.classList.toggle('active');
                if (notificationWrapper.classList.contains('active')) fetchNotifications();
            });
            document.addEventListener('click', (e) => {
                if (!notificationWrapper.contains(e.target)) notificationWrapper.classList.remove('active');
            });
        }

        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                await fetch('/notifications/mark-all-read', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } });
                updateBadge(0);
                document.querySelectorAll('.notification-item.unread').forEach(el => el.classList.remove('unread'));
            });
        }

        if (window.Echo) {
            window.Echo.private('user.{{ Auth::id() }}')
                .listen('.notification.sent', (data) => {
                    const newItem = document.createElement('div');
                    newItem.className = 'notification-item unread';
                    newItem.dataset.id = data.id;
                    newItem.innerHTML = `
                        <div class="notif-icon"><i class="fas fa-${data.icon === '💬' ? 'comment' : data.icon === '🏠' ? 'home' : 'bell'}"></i></div>
                        <div class="notif-content"><p><strong>${escapeHtml(data.title)}</strong><br>${escapeHtml(data.message)}</p><span>Just now</span></div>
                    `;
                    if (notificationList) {
                        if (notificationList.innerHTML.includes('No notifications')) notificationList.innerHTML = '';
                        notificationList.insertBefore(newItem, notificationList.firstChild);
                    }
                    let current = parseInt(notificationBadge.innerText) || 0;
                    updateBadge(current + 1);
                });
        }

        fetchNotifications();
        @endauth
    })();
</script>
</body>
</html>
@extends('layouts.app')

@section('title', 'Explore Apartments - APARTrack')

@section('styles')
{{-- keep your existing styles --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* ========== SAME STYLES AS BEFORE (keep your existing CSS) ========== */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(145deg, #f4f9ff 0%, #e9f2fa 100%); min-height: 100vh; }
    .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 2rem 1.8rem; animation: fadeInUp 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .guest-sections { display: flex; flex-direction: column; gap: 2.5rem; }
    .guest-section { display: flex; flex-direction: column; gap: 1.2rem; }
    .section-heading { display: inline-flex; align-items: center; gap: 0.6rem; font-size: 1.3rem; font-weight: 700; color: #1e3a8a; margin: 0; letter-spacing: -0.3px; border-left: 4px solid #3b82f6; padding-left: 0.9rem; text-decoration: none; }
    .section-heading-button { background: none; border: none; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
    .section-heading-button:hover { transform: translateX(6px); color: #2563eb; }
    .section-heading-button i { font-size: 0.9rem; color: #3b82f6; transition: transform 0.2s; }
    .section-heading-button:hover i { transform: translateX(4px); }
    .listing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
    .barangay-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.2rem; }
    .section-divider { border-top: 2px solid rgba(59, 130, 246, 0.2); margin: 1rem 0 0.5rem 0; }
    .listing-card { background: #ffffff; border-radius: 28px; overflow: hidden; cursor: pointer; transition: all 0.3s cubic-bezier(0.2, 0, 0, 1); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02), 0 2px 6px rgba(0, 0, 0, 0.05); border: 1px solid rgba(59, 130, 246, 0.15); }
    .listing-card:hover { transform: translateY(-6px); box-shadow: 0 20px 30px -12px rgba(59, 130, 246, 0.25); border-color: #3b82f640; }
    .listing-thumb { width: 100%; aspect-ratio: 1 / 0.9; object-fit: cover; display: block; transition: transform 0.4s ease; }
    .listing-card:hover .listing-thumb { transform: scale(1.02); }
    .listing-name { font-size: 0.95rem; font-weight: 700; color: #0c4a6e; margin: 0.9rem 1rem 0.3rem; line-height: 1.3; }
    .listing-price { font-size: 0.9rem; font-weight: 700; color: #3b82f6; margin: 0.3rem 1rem 0.5rem; display: flex; align-items: center; gap: 0.3rem; }
    .listing-price i { font-size: 0.8rem; color: #3b82f6; }
    .listing-meta { display: flex; justify-content: space-between; align-items: center; margin: 0 1rem 1rem 1rem; }
    .listing-link { font-size: 0.7rem; font-weight: 600; color: #3b82f6; text-decoration: none; letter-spacing: 0.3px; transition: all 0.2s; }
    .listing-link:hover { text-decoration: underline; color: #1e40af; }
    .listing-rating { font-size: 0.7rem; color: #fbbf24; letter-spacing: 1px; }
    .compact-barangay-card { background: white; border-radius: 24px; padding: 1.2rem 0.8rem; display: flex; flex-direction: column; align-items: center; text-align: center; cursor: pointer; transition: all 0.25s ease; border: 1px solid rgba(59, 130, 246, 0.12); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02); }
    .compact-barangay-card:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 16px 24px -10px rgba(59, 130, 246, 0.2); background: #fafcff; }
    .compact-barangay-card .brgy-logo { width: 85px; height: 85px; object-fit: contain; border-radius: 50%; background: #f0f7ff; padding: 6px; margin-bottom: 0.8rem; transition: transform 0.2s; }
    .compact-barangay-card:hover .brgy-logo { transform: scale(1.02); }
    .compact-barangay-card .brgy-name { font-size: 0.85rem; font-weight: 700; color: #1e3a8a; margin: 0; letter-spacing: -0.2px; }
    .skeleton { background: linear-gradient(90deg, #eef2f8 25%, #f8fafc 50%, #eef2f8 75%); background-size: 200% 100%; animation: shimmer 1.2s infinite; border-radius: 20px; }
    @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    .skeleton-card { background: #ffffffcc; border-radius: 28px; padding: 1rem; border: 1px solid rgba(59,130,246,0.1); }
    .skeleton-thumb { width: 100%; aspect-ratio: 1 / 0.9; border-radius: 24px; margin-bottom: 0.8rem; }
    .skeleton-title { height: 14px; width: 70%; border-radius: 30px; margin-bottom: 0.5rem; }
    .skeleton-meta { height: 12px; width: 50%; border-radius: 30px; }
    .skeleton-compact { background: #ffffffcc; border-radius: 24px; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.7rem; border: 1px solid rgba(59,130,246,0.1); }
    .skeleton-logo { width: 75px; height: 75px; border-radius: 50%; }
    .skeleton-compact-title { width: 70px; height: 12px; border-radius: 20px; }
    .toast-notification { position: fixed; bottom: 2rem; right: 2rem; background: white; border-left: 4px solid #3b82f6; padding: 0.9rem 1.5rem; border-radius: 60px; box-shadow: 0 12px 24px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 0.8rem; z-index: 1100; font-weight: 500; font-size: 0.85rem; color: #0c4a6e; animation: slideInRight 0.3s ease; backdrop-filter: blur(8px); background: rgba(255,255,255,0.96); }
    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .notification-popup { background: linear-gradient(105deg, #3b82f6, #1e40af); border-radius: 20px; padding: 0.9rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; color: white; box-shadow: 0 8px 18px rgba(59,130,246,0.2); }
    .notification-content { display: flex; align-items: center; gap: 1rem; }
    .notification-icon { font-size: 1.3rem; background: rgba(255,255,255,0.2); width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .notification-text { font-weight: 500; font-size: 0.9rem; }
    .notification-close { background: none; border: none; color: white; font-size: 1.6rem; cursor: pointer; opacity: 0.8; transition: all 0.2s; line-height: 1; }
    .notification-close:hover { opacity: 1; transform: scale(1.05); }
    .notification-popup.hidden { display: none; }
    .modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(32, 40, 80, 0.5); align-items: center; justify-content: center; padding: 1rem; }
    .modal.show { display: flex; }
    .modal-content { width: min(800px, 96vw); border-radius: 28px; overflow: hidden; background: transparent; }
    .modal-body { padding: 0; }
    .apartment-detail-card { background: white; border-radius: 28px; padding: 1.8rem; box-shadow: 0 25px 45px -12px rgba(0,0,0,0.2); border: 1px solid rgba(59,130,246,0.2); }
    .apartment-title { margin: 0 0 0.5rem; color: #1e3a8a; font-weight: 800; font-size: 1.6rem; letter-spacing: -0.3px; }
    .rating-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem; }
    .view-ratings-link { font-size: 0.75rem; color: #3b82f6; text-decoration: underline; cursor: pointer; }
    .stars { color: #fbbf24; font-size: 0.9rem; letter-spacing: 2px; }
    .rating-count { color: #475569; font-size: 0.75rem; margin-left: 0.4rem; }
    .apartment-gallery { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.8rem; margin-bottom: 1.5rem; }
    .gallery-thumb { width: 100%; aspect-ratio: 1 / 0.8; border-radius: 16px; object-fit: cover; transition: 0.2s; }
    .details-features-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .detail-section h3 { color: #3b82f6; font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px; margin: 0 0 0.6rem; text-transform: uppercase; }
    .detail-list { list-style: none; padding: 0; margin: 0; }
    .detail-list li { color: #1e293b; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.3rem; display: flex; gap: 0.5rem; }
    .detail-list li strong { font-weight: 700; color: #0f172a; min-width: 70px; }
    .feature-text { color: #1e293b; font-size: 0.85rem; font-weight: 500; line-height: 1.5; }
    .action-buttons { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 0.5rem; }
    .btn-chat, .btn-map { border: none; border-radius: 40px; padding: 0.5rem 1.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-chat { background: #3b82f6; color: white; box-shadow: 0 2px 6px rgba(59,130,246,0.3); }
    .btn-chat:hover { background: #1e40af; transform: translateY(-2px); }
    .btn-map { background: #eef2ff; color: #1e40af; border: 1px solid #3b82f640; }
    .btn-map:hover { background: #e0e7ff; transform: translateY(-2px); }
    .login-prompt { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(32, 40, 80, 0.5); z-index: 3000; padding: 1rem; }
    .login-prompt-card { width: min(420px, 96vw); background: white; border-radius: 28px; padding: 1.8rem; box-shadow: 0 25px 45px -12px rgba(0,0,0,0.2); border: 1px solid #e2e8f0; text-align: center; }
    .login-prompt-card h3 { margin: 0 0 0.5rem; font-size: 1.3rem; font-weight: 700; color: #1e3a8a; }
    .login-prompt-card p { color: #475569; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .login-prompt-buttons { display: flex; justify-content: flex-end; gap: 1rem; }
    .btn-login-redirect, .btn-cancel { border: none; background: none; font-weight: 600; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 40px; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .btn-login-redirect { background: #3b82f6; color: white; }
    .btn-login-redirect:hover { background: #1e40af; }
    .btn-cancel { background: #f1f5f9; color: #334155; }
    .btn-cancel:hover { background: #e2e8f0; }
    @media (max-width: 1100px) { .barangay-grid { grid-template-columns: repeat(4, 1fr); } .listing-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 800px) { .dashboard-container { padding: 1.2rem; } .barangay-grid { grid-template-columns: repeat(3, 1fr); gap: 1rem; } .listing-grid { grid-template-columns: repeat(2, 1fr); } .section-heading { font-size: 1.1rem; } .apartment-gallery { grid-template-columns: repeat(2, 1fr); } .details-features-row { grid-template-columns: 1fr; gap: 1rem; } }
    @media (max-width: 550px) { .barangay-grid { grid-template-columns: repeat(2, 1fr); } .listing-grid { grid-template-columns: 1fr; } .compact-barangay-card .brgy-logo { width: 65px; height: 65px; } .action-buttons { justify-content: stretch; } .btn-chat, .btn-map { flex: 1; text-align: center; } }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div id="guestNotification" class="notification-popup">
        <div class="notification-content">
            <div class="notification-icon"><i class="fas fa-info-circle"></i></div>
            <div class="notification-text">✨ Discover rooms & commercial spaces near you. Click any barangay to explore available apartments.</div>
        </div>
        <button class="notification-close" id="closeNotificationBtn">&times;</button>
    </div>

    <div id="guestExploreView">
        <div class="guest-sections">
            <!-- Boarding section -->
            <section class="guest-section">
                <a href="{{ route('boarding.nearby') }}" class="section-heading section-heading-button">
                    <span>Popular boarding near UEP & WCC</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <div class="listing-grid" id="featuredBoardingGrid"></div>
            </section>

            <!-- Commercial section -->
            <section class="guest-section">
                <a href="{{ route('commercial.nearby') }}" class="section-heading section-heading-button">
                    <span>Commercial spaces at Poblacion & Market</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <div class="listing-grid" id="featuredCommercialGrid"></div>
            </section>

            <div class="section-divider"></div>

            <!-- Barangay grid section - added an ID for easy hiding -->
            <div id="barangaySection" class="guest-section">
                <h2 class="section-heading">Apartment / Commercial per Barangay</h2>
                <div class="barangay-grid" id="compactBarangayGrid"></div>
            </div>
        </div>
    </div>
</div>

<div id="apartmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<div id="loginPromptOverlay" class="login-prompt">
    <div class="login-prompt-card">
        <h3>Continue to explore?</h3>
        <p>Hi! Please log in first to continue exploring the page.</p>
        <div class="login-prompt-buttons">
            <button class="btn-cancel" id="cancelLoginPrompt">CANCEL</button>
            <a href="{{ route('login', ['role' => 'tenant']) }}" class="btn-login-redirect">LOG IN</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ========== MASTER DATA (unchanged) ==========
    const masterBarangays = [
        { name: "Balangobong", file: "balangobong.png", available: 12, total: 24 },
        { name: "Bued", file: "bued.png", available: 8, total: 18 },
        { name: "Bugayong", file: "bugayong.png", available: 15, total: 30 },
        { name: "Camangaan", file: "camangaan.png", available: 6, total: 20 },
        { name: "Canarvacanan", file: "canarvacanan.png", available: 10, total: 22 },
        { name: "Capas", file: "capas.png", available: 14, total: 28 },
        { name: "Cili", file: "cili.png", available: 9, total: 26 },
        { name: "Dumayat", file: "dumayat.png", available: 11, total: 24 },
        { name: "Linmansangan", file: "linmansangan.png", available: 13, total: 25 },
        { name: "Mangcasuy", file: "mangcasuy.png", available: 7, total: 19 },
        { name: "Moreno", file: "moreno.png", available: 16, total: 32 },
        { name: "Pasileng Norte", file: "pasileng_norte.png", available: 5, total: 15 },
        { name: "Pasileng Sur", file: "pasileng_sur.png", available: 10, total: 21 },
        { name: "Poblacion", file: "poblacion.png", available: 18, total: 35 },
        { name: "San Felipe Central", file: "sanfelipe_central.png", available: 8, total: 17 },
        { name: "San Felipe Sur", file: "sanfelipe_sur.png", available: 12, total: 23 },
        { name: "San Pablo", file: "sanpablo.png", available: 9, total: 20 },
        { name: "Santiago", file: "santiago.png", available: 14, total: 27 },
        { name: "Santonino", file: "santonino.png", available: 11, total: 22 },
        { name: "Sta. Catalina", file: "stacatalina.png", available: 7, total: 18 },
        { name: "Sta. Maria Norte", file: "stamaria_norte.png", available: 13, total: 26 },
        { name: "Sumabnit", file: "sumabnit.png", available: 10, total: 24 },
        { name: "Tabuyoc", file: "tabuyoc.png", available: 15, total: 29 },
        { name: "Vacante", file: "vacante.png", available: 4, total: 12 }
    ];

    const masterBoardingData = [
        { id: 1, title: 'Jesus and Elnora Boarding House Rental', area: 'Poblacion', type: 'boarding', rating: 4, image: 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=600&h=500&fit=crop', price: '₱2,500 / month', features: 'Studio type, bathroom, cabinet, aircon, shared kitchen, CCTV', address: 'Near UEP & WCC, Binalonan' },
        { id: 2, title: 'NVE Boarding House', area: 'UEP side', type: 'boarding', rating: 3, image: 'https://images.unsplash.com/photo-1554995207-c18c203602cb?w=600&h=500&fit=crop', price: '₱2,800 / month', features: '1BR, bathroom, kitchen, parking, CCTV', address: 'UEP Campus vicinity, Binalonan' },
        { id: 3, title: 'Comiso Dorm', area: 'WCC side', type: 'boarding', rating: 5, image: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&h=500&fit=crop', price: '₱3,000 / month', features: 'Dorm type, shared bathroom, study area, WiFi, water heater', address: 'Near WCC, Binalonan' },
        { id: 4, title: 'Marina Comiso Boarding House', area: 'Town proper', type: 'boarding', rating: 4, image: 'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?w=600&h=500&fit=crop', price: '₱3,500 / month', features: 'Studio, modern interior, aircon, own kitchen, balcony', address: 'Town proper, Binalonan' }
    ];

    const masterCommercialData = [
        { id: 5, title: 'Marietta & Leon Boarding House', area: 'Market road', type: 'commercial', rating: 4, image: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=600&h=500&fit=crop', price: '₱5,000 / month', features: 'Commercial stall, 20 sqm, high foot traffic', address: 'Market road, Poblacion' },
        { id: 6, title: 'Rosewil Boarding House', area: 'Centro', type: 'commercial', rating: 3, image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600&h=500&fit=crop', price: '₱4,500 / month', features: 'Retail space, glass front, near church', address: 'Centro, Binalonan' },
        { id: 7, title: 'RN Boarding House', area: 'Highway', type: 'commercial', rating: 5, image: 'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?w=600&h=500&fit=crop', price: '₱6,000 / month', features: 'Commercial unit, 25 sqm, parking included', address: 'National Highway, Binalonan' },
        { id: 8, title: 'Celedonias Boarding House', area: 'Barangay hall', type: 'commercial', rating: 4, image: 'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?w=600&h=500&fit=crop', price: '₱3,800 / month', features: 'Small shop space, secure area', address: 'Near Barangay Hall, Binalonan' }
    ];

    // Helper functions (unchanged)
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
        }
        return stars;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function generatePlaceholder(name) {
        const initial = name.charAt(0).toUpperCase();
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ec489a', '#14b8a6'];
        const colorIndex = name.length % colors.length;
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="${colors[colorIndex]}"/><text x="50" y="67" font-size="42" text-anchor="middle" fill="white" font-family='Poppins, sans-serif' font-weight="600">${initial}</text></svg>`;
        return 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svg)));
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `<i style="color: #3b82f6;">✓</i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    window.showLoginPrompt = function() {
        document.getElementById('loginPromptOverlay').style.display = 'flex';
    };

    function hideLoginPrompt() {
        document.getElementById('loginPromptOverlay').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('apartmentModal')?.classList.remove('show');
    }

    function goToBarangayDetails(brgyName, available, total, logoUrl) {
        const params = new URLSearchParams({ name: brgyName, available, total, logo: logoUrl });
        window.location.href = `/barangay-details?${params.toString()}`;
    }

    function renderListingGrid(containerId, items) {
        const container = document.getElementById(containerId);
        if (!container) return;
        if (!items.length) {
            container.innerHTML = '';
            return;
        }
        container.innerHTML = items.map(item => `
            <article class="listing-card" data-item='${JSON.stringify(item).replace(/"/g, '&quot;')}'>
                <img src="${item.image}" class="listing-thumb" onerror="this.src=generatePlaceholder('${item.title}')" alt="${item.title}">
                <h3 class="listing-name">${escapeHtml(item.title)}</h3>
                <div class="listing-price"><i class="fas fa-tag"></i> ${item.price}</div>
                <div class="listing-meta">
                    <span class="listing-link">view details →</span>
                    <span class="listing-rating">${generateStars(item.rating)}</span>
                </div>
            </article>
        `).join('');
        container.querySelectorAll('.listing-card').forEach(card => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                showLoginPrompt();
            });
        });
    }

    function renderBarangayGrid(barangays) {
        const container = document.getElementById('compactBarangayGrid');
        if (!container) return;
        if (!barangays.length) {
            container.innerHTML = '<div class="no-results" style="grid-column:1/-1; text-align:center; padding:2rem;">No barangays match.</div>';
            return;
        }
        container.innerHTML = barangays.map(brgy => {
            const imgPath = `{{ asset('images/brgy_logo/') }}/${brgy.file}`;
            return `
                <div class="compact-barangay-card" data-brgy='${JSON.stringify(brgy)}'>
                    <img src="${imgPath}" class="brgy-logo" onerror="this.src=generatePlaceholder('${brgy.name}')" alt="${brgy.name}">
                    <h3 class="brgy-name">${escapeHtml(brgy.name)}</h3>
                </div>
            `;
        }).join('');
        container.querySelectorAll('.compact-barangay-card').forEach(card => {
            const brgy = JSON.parse(card.getAttribute('data-brgy'));
            const imgPath = `{{ asset('images/brgy_logo/') }}/${brgy.file}`;
            card.addEventListener('click', () => goToBarangayDetails(brgy.name, brgy.available, brgy.total, imgPath));
        });
    }

    function filterListingsBySearch(items, searchTerm) {
        if (!searchTerm?.trim()) return items;
        const term = searchTerm.toLowerCase().trim();
        return items.filter(i => i.title.toLowerCase().includes(term) || (i.area && i.area.toLowerCase().includes(term)));
    }

    function filterBarangaysBySearch(barangays, searchTerm) {
        if (!searchTerm?.trim()) return barangays;
        const term = searchTerm.toLowerCase().trim();
        return barangays.filter(b => b.name.toLowerCase().includes(term));
    }

    function parsePrice(priceStr) {
        if (!priceStr) return 0;
        const numeric = priceStr.replace(/[^0-9.-]/g, '');
        return parseFloat(numeric) || 0;
    }

    function showDefaultSkeletons() {
        const barangayGrid = document.getElementById('compactBarangayGrid');
        if (barangayGrid) {
            barangayGrid.innerHTML = Array(24).fill(0).map(() => `
                <div class="skeleton-compact">
                    <div class="skeleton skeleton-logo"></div>
                    <div class="skeleton skeleton-compact-title"></div>
                </div>
            `).join('');
        }
        const boardingGrid = document.getElementById('featuredBoardingGrid');
        if (boardingGrid) {
            boardingGrid.innerHTML = Array(4).fill(0).map(() => `
                <div class="skeleton-card">
                    <div class="skeleton skeleton-thumb"></div>
                    <div class="skeleton skeleton-title"></div>
                    <div class="skeleton skeleton-meta"></div>
                </div>
            `).join('');
        }
        const commercialGrid = document.getElementById('featuredCommercialGrid');
        if (commercialGrid) {
            commercialGrid.innerHTML = Array(4).fill(0).map(() => `
                <div class="skeleton-card">
                    <div class="skeleton skeleton-thumb"></div>
                    <div class="skeleton skeleton-title"></div>
                    <div class="skeleton skeleton-meta"></div>
                </div>
            `).join('');
        }
    }

    // ========== UPDATED: now hides barangay section for price filters ==========
    function updateDashboard(searchTerm, filterType) {
        showDefaultSkeletons();

        setTimeout(() => {
            try {
                let boardingFiltered = filterListingsBySearch(masterBoardingData, searchTerm);
                let commercialFiltered = filterListingsBySearch(masterCommercialData, searchTerm);
                let filteredBarangays = filterBarangaysBySearch(masterBarangays, searchTerm);

                // Check if current filter is a price filter
                const isPriceFilter = filterType === 'price_low' || filterType === 'price_high';
                const barangaySection = document.getElementById('barangaySection');

                if (isPriceFilter) {
                    // HIDE the whole barangay section
                    if (barangaySection) barangaySection.style.display = 'none';
                } else {
                    // SHOW the barangay section (if it was hidden)
                    if (barangaySection) barangaySection.style.display = '';
                }

                // Apply sorting based on filter type
                if (filterType && filterType !== '') {
                    if (filterType === 'price_low') {
                        boardingFiltered.sort((a, b) => parsePrice(a.price) - parsePrice(b.price));
                        commercialFiltered.sort((a, b) => parsePrice(a.price) - parsePrice(b.price));
                        // No barangay sorting (and they are hidden anyway)
                    } 
                    else if (filterType === 'price_high') {
                        boardingFiltered.sort((a, b) => parsePrice(b.price) - parsePrice(a.price));
                        commercialFiltered.sort((a, b) => parsePrice(b.price) - parsePrice(a.price));
                    } 
                    else if (filterType === 'name_asc') {
                        boardingFiltered.sort((a, b) => a.title.localeCompare(b.title));
                        commercialFiltered.sort((a, b) => a.title.localeCompare(b.title));
                        filteredBarangays.sort((a, b) => a.name.localeCompare(b.name));
                    } 
                    else if (filterType === 'name_desc') {
                        boardingFiltered.sort((a, b) => b.title.localeCompare(a.title));
                        commercialFiltered.sort((a, b) => b.title.localeCompare(a.title));
                        filteredBarangays.sort((a, b) => b.name.localeCompare(a.name));
                    }
                }

                renderListingGrid('featuredBoardingGrid', boardingFiltered);
                renderListingGrid('featuredCommercialGrid', commercialFiltered);

                // Only render barangay grid if NOT a price filter
                if (!isPriceFilter) {
                    renderBarangayGrid(filteredBarangays);
                } else {
                    // Optionally clear the grid to save resources
                    const barangayContainer = document.getElementById('compactBarangayGrid');
                    if (barangayContainer) barangayContainer.innerHTML = '';
                }

                if (searchTerm && searchTerm.trim() !== '') {
                    const totalMatches = boardingFiltered.length + commercialFiltered.length + filteredBarangays.length;
                    if (totalMatches === 0) showToast(`No results found for "${searchTerm}"`);
                    else showToast(`Found ${totalMatches} result(s) for "${searchTerm}"`);
                }

                const url = new URL(window.location.href);
                if (searchTerm) url.searchParams.set('search', searchTerm);
                else url.searchParams.delete('search');
                if (filterType) url.searchParams.set('filter', filterType);
                else url.searchParams.delete('filter');
                window.history.pushState({}, '', url);
            } catch (error) {
                console.error('Dashboard update error:', error);
                try {
                    const barangays = filterBarangaysBySearch(masterBarangays, searchTerm);
                    renderBarangayGrid(barangays);
                } catch (e) { console.error('Barangay fallback failed:', e); }
            }
        }, 60);
    }

    // Override navbar's filter radios and search form to use updateDashboard (no page reload)
    function setupSearchAndFilter() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        if (!searchForm) return;

        const filterRadios = document.querySelectorAll('input[name="filter"]');

        filterRadios.forEach(radio => {
            const oldHandler = radio._customHandler;
            if (oldHandler) radio.removeEventListener('change', oldHandler);
            
            const handler = (e) => {
                e.preventDefault();
                e.stopPropagation();
                const term = searchInput?.value || '';
                const activeFilter = document.querySelector('input[name="filter"]:checked')?.value || '';
                updateDashboard(term, activeFilter);
                const dropdown = document.getElementById('filterDropdown');
                if (dropdown) dropdown.classList.remove('show');
                const filterIcon = document.getElementById('filterIconBtn');
                if (filterIcon) filterIcon.classList.remove('active');
            };
            radio.addEventListener('change', handler);
            radio._customHandler = handler;
        });

        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const term = searchInput?.value || '';
            const activeFilter = document.querySelector('input[name="filter"]:checked')?.value || '';
            updateDashboard(term, activeFilter);
        });

        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') searchForm.dispatchEvent(new Event('submit'));
            });
        }
    }

    function initNotification() {
        const notif = document.getElementById('guestNotification');
        const closeBtn = document.getElementById('closeNotificationBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                notif?.classList.add('hidden');
                localStorage.setItem('guestNotifDismissed', 'true');
            });
        }
        if (localStorage.getItem('guestNotifDismissed') === 'true') notif?.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const initialSearch = urlParams.get('search') || '';
        const initialFilter = urlParams.get('filter') || '';

        showDefaultSkeletons();

        setTimeout(() => {
            updateDashboard(initialSearch, initialFilter);
        }, 60);

        setupSearchAndFilter();
        initNotification();

        const modal = document.getElementById('apartmentModal');
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.getElementById('cancelLoginPrompt').addEventListener('click', hideLoginPrompt);
        window.addEventListener('click', (e) => {
            if (e.target === document.getElementById('loginPromptOverlay')) hideLoginPrompt();
        });

        setTimeout(() => {
            if (!initialSearch && !initialFilter) showToast('🏡 Welcome to APARTrack — find your next home or commercial space');
        }, 800);
    });
</script>
@endsection
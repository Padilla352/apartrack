@extends('layouts.app')

@section('title', ($barangayName ?? 'Barangay') . ' - Properties')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #eff6ff;
        --primary-soft: #dbeafe;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-900: #111827;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        min-height: 100vh;
    }

    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 10px;
    }

    .detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 2rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background: var(--white);
        color: var(--primary-dark);
        padding: 0.7rem 1.4rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        border: 1px solid var(--gray-200);
        transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(4px);
    }
    .back-btn i {
        transition: transform 0.2s;
    }
    .back-btn:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        transform: translateX(-5px);
        box-shadow: var(--shadow-md);
    }
    .back-btn:hover i {
        transform: translateX(-3px);
    }

    .barangay-hero-card {
        background: var(--white);
        border-radius: 2.5rem;
        padding: 2.5rem;
        margin-bottom: 2.5rem;
        text-align: center;
        border: 1px solid rgba(37, 99, 235, 0.1);
        box-shadow: var(--shadow-lg);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        overflow: hidden;
    }
    .barangay-hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary), #60a5fa, var(--primary));
    }
    .barangay-hero-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-xl);
    }

    .barangay-detail-logo {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--white);
        box-shadow: 0 15px 30px -8px rgba(0, 0, 0, 0.15);
        margin-bottom: 1.2rem;
        transition: transform 0.3s;
    }
    .barangay-hero-card:hover .barangay-detail-logo {
        transform: scale(1.02);
    }

    .barangay-detail-name {
        font-size: 2.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .location-text {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--primary-light);
        padding: 0.5rem 1.3rem;
        border-radius: 60px;
        color: var(--primary-dark);
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 0.8rem;
    }

    .stats-row {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    .stat-item {
        background: var(--gray-50);
        padding: 0.6rem 1.2rem;
        border-radius: 60px;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
    }
    .stat-item i {
        color: var(--primary);
        margin-right: 0.4rem;
    }
    .stat-number {
        font-weight: 700;
        color: var(--primary-dark);
        font-size: 1rem;
    }

    .filter-bar {
        background: var(--white);
        border-radius: 1.5rem;
        padding: 0.8rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    .filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .filter-btn {
        background: transparent;
        border: 1px solid var(--gray-200);
        padding: 0.5rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .filter-btn i {
        margin-right: 0.4rem;
        font-size: 0.75rem;
    }
    .filter-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        box-shadow: var(--shadow-sm);
    }
    .filter-btn:hover:not(.active) {
        background: var(--primary-light);
        border-color: var(--primary-soft);
        color: var(--primary-dark);
    }
    .sort-select {
        padding: 0.5rem 1rem;
        border-radius: 40px;
        border: 1px solid var(--gray-200);
        background: var(--white);
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--gray-700);
        cursor: pointer;
        outline: none;
        transition: all 0.2s;
    }
    .sort-select:focus {
        border-color: var(--primary);
    }

    .section-heading {
        font-size: 1.7rem;
        font-weight: 700;
        margin-bottom: 1.8rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-left: 5px solid var(--primary);
        padding-left: 1rem;
        color: var(--gray-900);
    }

    .properties-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 2rem;
        transition: all 0.3s;
    }

    .property-card {
        background: var(--white);
        border-radius: 1.8rem;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.2, 0, 0, 1);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        cursor: pointer;
    }
    .property-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary);
        box-shadow: var(--shadow-xl);
    }

    .image-collage {
        position: relative;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 0.6rem;
        padding: 1rem 1rem 0 1rem;
    }
    .main-image {
        overflow: hidden;
        border-radius: 1.2rem;
        background: var(--gray-100);
        cursor: pointer;
    }
    .main-image img {
        width: 100%;
        height: 210px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .property-card:hover .main-image img {
        transform: scale(1.05);
    }
    .side-images {
        display: grid;
        grid-template-rows: 1fr 1fr;
        gap: 0.6rem;
    }
    .side-images img {
        width: 100%;
        height: 97px;
        object-fit: cover;
        border-radius: 1rem;
        transition: transform 0.4s ease;
    }
    .property-card:hover .side-images img {
        transform: scale(1.03);
    }

    .property-info {
        padding: 1.2rem 1.2rem 1.5rem;
    }
    .property-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .type-badge {
        background: var(--primary-light);
        color: var(--primary-dark);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.8rem;
        border-radius: 30px;
    }
    .price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0.6rem 0;
        display: flex;
        align-items: baseline;
        gap: 0.2rem;
    }
    .price small {
        font-size: 0.8rem;
        font-weight: 400;
        color: var(--gray-500);
    }
    .details-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 0.8rem 0;
        font-size: 0.8rem;
        color: var(--gray-600);
    }
    .details-row span i {
        width: 1.2rem;
        color: var(--primary);
        margin-right: 0.3rem;
    }
    .status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #e6f7ec;
        color: #15803d;
        font-weight: 600;
        font-size: 0.7rem;
        padding: 0.25rem 1rem;
        border-radius: 30px;
        margin: 0.5rem 0;
    }
    .dot {
        width: 7px;
        height: 7px;
        background: #15803d;
        border-radius: 50%;
        display: inline-block;
    }

    .view-details-btn {
        background: linear-gradient(100deg, var(--primary), var(--primary-dark));
        color: white;
        padding: 0.7rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        width: 100%;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        border: none;
        cursor: pointer;
        transition: all 0.25s;
        box-shadow: var(--shadow-sm);
        text-decoration: none;
        text-align: center;
    }
    .view-details-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        filter: brightness(1.05);
        color: white;
    }

    .empty-state {
        background: var(--white);
        border-radius: 2rem;
        padding: 3rem;
        text-align: center;
        border: 1px solid var(--gray-200);
    }
    .empty-state i {
        font-size: 3rem;
        color: var(--gray-400);
        margin-bottom: 1rem;
    }

    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.92);
        backdrop-filter: blur(10px);
        z-index: 2000;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .lightbox.active {
        display: flex;
    }
    .lightbox-content {
        max-width: 85vw;
        max-height: 80vh;
        position: relative;
    }
    .lightbox-img {
        width: auto;
        max-width: 100%;
        max-height: 80vh;
        border-radius: 1rem;
        box-shadow: 0 25px 40px rgba(0,0,0,0.3);
        animation: fadeScale 0.25s ease;
    }
    @keyframes fadeScale {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .lb-prev, .lb-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
        border: none;
        color: white;
        font-size: 2rem;
        padding: 0.8rem;
        cursor: pointer;
        border-radius: 60px;
        transition: 0.2s;
    }
    .lb-prev { left: -70px; }
    .lb-next { right: -70px; }
    .lb-prev:hover, .lb-next:hover {
        background: var(--primary);
        transform: translateY(-50%) scale(1.05);
    }
    .lb-close {
        position: absolute;
        top: -50px;
        right: 0;
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }
    .lb-counter {
        margin-top: 1rem;
        color: white;
        font-size: 0.9rem;
    }
    @media (max-width: 768px) {
        .lb-prev { left: 10px; }
        .lb-next { right: 10px; }
        .lightbox-content { max-width: 95vw; }
        .properties-grid { grid-template-columns: 1fr; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .detail-container { padding: 1rem; }
        .barangay-detail-name { font-size: 1.8rem; }
    }

    .login-prompt {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(6px);
        z-index: 3000;
    }
    .login-prompt-card {
        max-width: 400px;
        background: var(--white);
        border-radius: 2rem;
        padding: 2rem;
        text-align: center;
        animation: slideUp 0.2s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-cancel, .btn-login-redirect {
        padding: 0.6rem 1.3rem;
        border-radius: 40px;
        font-weight: 600;
        margin: 0 0.3rem;
        border: none;
        cursor: pointer;
    }
    .btn-cancel {
        background: var(--gray-100);
        color: var(--gray-700);
    }
    .btn-login-redirect {
        background: var(--primary);
        color: white;
        text-decoration: none;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="detail-container">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('explore') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Explore
        </a>
    </div>

    <div id="barangayDetailContent">
        {{-- Hero + Stats --}}
        <div class="barangay-hero-card">
            <img src="{{ $barangayLogo ?? 'https://placehold.co/200x200/2563eb/white?text=' . substr($barangayName ?? 'B', 0, 1) }}" 
                 alt="{{ $barangayName ?? 'Barangay' }}" class="barangay-detail-logo">
            <h1 class="barangay-detail-name">{{ $barangayName ?? 'Barangay' }}</h1>
            <div class="location-text">
                <i class="fas fa-map-marker-alt"></i> {{ $barangayName ?? 'Barangay' }}, Binalonan, Pangasinan
            </div>
            <div class="stats-row">
                <div class="stat-item"><i class="fas fa-building"></i> <span class="stat-number" id="totalPropertiesCount">0</span> Total Properties</div>
                <div class="stat-item"><i class="fas fa-door-open"></i> <span class="stat-number" id="apartmentCount">0</span> Apartments</div>
                <div class="stat-item"><i class="fas fa-store"></i> <span class="stat-number" id="businessCount">0</span> Business Spaces</div>
            </div>
        </div>

        {{-- Interactive Filter Bar --}}
        <div class="filter-bar">
            <div class="filter-group">
                <button class="filter-btn active" data-filter="all"><i class="fas fa-th-large"></i> All</button>
                <button class="filter-btn" data-filter="apartment"><i class="fas fa-door-open"></i> Apartments</button>
                <button class="filter-btn" data-filter="business"><i class="fas fa-store"></i> Commercial Spaces</button>
            </div>
            <div>
                <select id="sortSelect" class="sort-select">
                    <option value="default">Sort by: Default</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>

        {{-- Combined Properties Grid --}}
        <div id="propertiesContainer">
            <div class="properties-grid" id="propertiesGrid">
                {{-- Dynamic content injected by JS --}}
            </div>
        </div>
    </div>
</div>

{{-- Lightbox Modal --}}
<div id="lightboxModal" class="lightbox">
    <button class="lb-close" id="lbClose"><i class="fas fa-times"></i></button>
    <div class="lightbox-content">
        <button class="lb-prev" id="lbPrev"><i class="fas fa-chevron-left"></i></button>
        <img id="lightboxImg" class="lightbox-img" src="" alt="Gallery">
        <button class="lb-next" id="lbNext"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="lb-counter" id="lbCounter"></div>
</div>

{{-- Login Prompt Overlay --}}
<div id="loginPromptOverlay" class="login-prompt">
    <div class="login-prompt-card">
        <i class="fas fa-lock" style="font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem;"></i>
        <h3 style="font-weight:700;">Login Required</h3>
        <p>Please log in to view full property details and contact info.</p>
        <div>
            <button class="btn-cancel" id="cancelLoginPrompt">Cancel</button>
            <a href="{{ route('login', ['role' => 'tenant']) }}" class="btn-login-redirect">Log In</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Data passed from backend (Blade → JS)
    const apartmentsData = @json(isset($apartments) ? $apartments : []);
    const businessData = @json(isset($businessSpaces) ? $businessSpaces : []);
    const barangayName = @json($barangayName ?? 'Barangay');
    const barangaySlug = barangayName.toLowerCase().replace(/\s+/g, '-');
    
    // ✅ TAMANG BUSINESS DETAILS URL (public route)
    const BUSINESS_BASE_URL = '/businesses';

    // Helper: decode images from JSON string or array
    function getImageList(item, type) {
        let images = [];
        if (item.images) {
            if (Array.isArray(item.images)) images = item.images;
            else if (typeof item.images === 'string') {
                try {
                    let parsed = JSON.parse(item.images);
                    if (Array.isArray(parsed)) images = parsed;
                    else if (parsed && typeof parsed === 'string') images = [parsed];
                    else images = [item.images];
                } catch(e) { images = [item.images]; }
            }
        }
        images = images.filter(img => img && img.trim() !== '');
        if (images.length === 0) {
            let placeholder = type === 'apartment' 
                ? 'https://placehold.co/600x400/2563eb/white?text=Apartment'
                : 'https://placehold.co/600x400/2563eb/white?text=Commercial+Space';
            images = [placeholder];
        }
        // Convert storage paths to full URLs
        return images.map(img => {
            if (img.startsWith('http')) return img;
            if (img.startsWith('/')) return img;
            return '/storage/' + img.replace(/^\/?storage\//, '');
        });
    }

    function buildPropertyCard(item, type) {
        const images = getImageList(item, type);
        const mainImg = images[0];
        const side1 = images[1] || mainImg;
        const side2 = images[2] || mainImg;
        const name = type === 'apartment' ? item.name : item.business_name;
        const typeLabel = type === 'apartment' ? (item.type ?? 'Apartment') : (item.type ?? 'Commercial');
        const unitBadge = type === 'apartment' ? (item.unit_number ? `<span class="type-badge">${item.unit_number}</span>` : `<span class="type-badge">${typeLabel}</span>`) : `<span class="type-badge">${typeLabel}</span>`;
        const price = item.monthly_rent ? parseFloat(item.monthly_rent).toLocaleString('en-PH') : '0';
        const bedrooms = type === 'apartment' ? (item.bedrooms ?? 0) : null;
        const bathrooms = type === 'apartment' ? (item.bathrooms ?? 0) : null;
        const area = item.floor_area_sqm ?? '?';
        
        const detailsHtml = `
            <div class="details-row">
                ${bedrooms !== null ? `<span><i class="fas fa-bed"></i> ${bedrooms} BR</span>` : ''}
                ${bathrooms !== null ? `<span><i class="fas fa-bath"></i> ${bathrooms} T&B</span>` : ''}
                <span><i class="fas fa-ruler-combined"></i> ${area} sqm</span>
                <span><i class="fas fa-building"></i> ${typeLabel}</span>
            </div>
            <div class="details-row">
                <span><i class="fas fa-map-marker-alt"></i> ${item.barangay_name || barangayName}</span>
            </div>
        `;
        const statusHtml = `<div class="status"><span class="dot"></span> Available</div>`;
        
        // ✅ TAMANG URL PARA SA APARTMENT AT BUSINESS
        let finalUrl = '#';
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        
        if (type === 'apartment') {
            // Apartment details route: /{barangaySlug}/apartment/{id}
            finalUrl = `/${barangaySlug}/apartment/${item.id}`;
        } else {
            // Business details route: /businesses/{id} (public, pero may login check pa rin)
            finalUrl = `${BUSINESS_BASE_URL}/${item.id}`;
            // Optional: log para sa debugging (tignan sa browser console)
            console.log('Business URL:', finalUrl, 'Business ID:', item.id);
        }
        
        const buttonHtml = isAuthenticated 
            ? `<a href="${finalUrl}" class="view-details-btn"><i class="fas fa-eye"></i> View Full Details</a>`
            : `<button onclick="showLoginPrompt()" class="view-details-btn"><i class="fas fa-eye"></i> View Full Details</button>`;

        return `
            <div class="property-card" data-type="${type}" data-price="${item.monthly_rent || 0}" data-name="${name}">
                <div class="image-collage">
                    <div class="main-image" data-images='${JSON.stringify(images)}'>
                        <img src="${mainImg}" alt="${name}" onerror="this.src='https://placehold.co/600x400/2563eb/white?text=Image+Not+Found'">
                    </div>
                    <div class="side-images">
                        <img src="${side1}" alt="view" onerror="this.src='https://placehold.co/300x200/2563eb/white?text=No+Image'">
                        <img src="${side2}" alt="view" onerror="this.src='https://placehold.co/300x200/2563eb/white?text=No+Image'">
                    </div>
                </div>
                <div class="property-info">
                    <div class="property-name">
                        ${name}
                        ${unitBadge}
                    </div>
                    <div class="price">
                        ₱${price}<small>/month</small>
                    </div>
                    ${detailsHtml}
                    ${statusHtml}
                    ${buttonHtml}
                </div>
            </div>
        `;
    }

    let allItems = [];

    function updateStats() {
        const aptCount = allItems.filter(i => i.__type === 'apartment').length;
        const bizCount = allItems.filter(i => i.__type === 'business').length;
        document.getElementById('totalPropertiesCount').innerText = allItems.length;
        document.getElementById('apartmentCount').innerText = aptCount;
        document.getElementById('businessCount').innerText = bizCount;
    }

    function renderProperties(filterType, sortBy) {
        let filtered = [...allItems];
        if (filterType === 'apartment') filtered = filtered.filter(i => i.__type === 'apartment');
        else if (filterType === 'business') filtered = filtered.filter(i => i.__type === 'business');
        
        if (sortBy === 'price_asc') filtered.sort((a,b) => (a.monthly_rent||0) - (b.monthly_rent||0));
        else if (sortBy === 'price_desc') filtered.sort((a,b) => (b.monthly_rent||0) - (a.monthly_rent||0));
        
        const grid = document.getElementById('propertiesGrid');
        if (!filtered.length) {
            grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-box-open"></i><h3>No properties found</h3><p>Try adjusting filters</p></div>`;
            return;
        }
        grid.innerHTML = filtered.map(item => buildPropertyCard(item, item.__type)).join('');
        attachLightboxEvents();
    }

    // Lightbox gallery
    let currentGalleryImages = [];
    let currentImageIndex = 0;

    function attachLightboxEvents() {
        document.querySelectorAll('.main-image').forEach(container => {
            container.addEventListener('click', (e) => {
                e.stopPropagation();
                const imagesAttr = container.getAttribute('data-images');
                if (imagesAttr) {
                    try {
                        currentGalleryImages = JSON.parse(imagesAttr);
                        if (!currentGalleryImages.length) currentGalleryImages = [container.querySelector('img')?.src];
                        currentImageIndex = 0;
                        openLightbox();
                    } catch(e) { console.warn(e); }
                }
            });
        });
    }

    function openLightbox() {
        const modal = document.getElementById('lightboxModal');
        const img = document.getElementById('lightboxImg');
        const counter = document.getElementById('lbCounter');
        if (!currentGalleryImages.length) return;
        img.src = currentGalleryImages[currentImageIndex];
        counter.innerText = `${currentImageIndex+1} / ${currentGalleryImages.length}`;
        modal.classList.add('active');
    }
    function closeLightbox() { document.getElementById('lightboxModal').classList.remove('active'); }
    function nextImage() {
        if (currentGalleryImages.length) {
            currentImageIndex = (currentImageIndex + 1) % currentGalleryImages.length;
            document.getElementById('lightboxImg').src = currentGalleryImages[currentImageIndex];
            document.getElementById('lbCounter').innerText = `${currentImageIndex+1} / ${currentGalleryImages.length}`;
        }
    }
    function prevImage() {
        if (currentGalleryImages.length) {
            currentImageIndex = (currentImageIndex - 1 + currentGalleryImages.length) % currentGalleryImages.length;
            document.getElementById('lightboxImg').src = currentGalleryImages[currentImageIndex];
            document.getElementById('lbCounter').innerText = `${currentImageIndex+1} / ${currentGalleryImages.length}`;
        }
    }

    function initData() {
        const apartments = (Array.isArray(apartmentsData) ? apartmentsData : []).map(item => ({ ...item, __type: 'apartment' }));
        const businesses = (Array.isArray(businessData) ? businessData : []).map(item => ({ ...item, __type: 'business' }));
        allItems = [...apartments, ...businesses];
        updateStats();
        renderProperties('all', 'default');
    }

    // Event listeners and initialization
    document.addEventListener('DOMContentLoaded', () => {
        initData();
        
        const filterBtns = document.querySelectorAll('.filter-btn');
        const sortSelect = document.getElementById('sortSelect');
        let activeFilter = 'all';
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeFilter = btn.getAttribute('data-filter');
                renderProperties(activeFilter, sortSelect.value);
            });
        });
        
        sortSelect.addEventListener('change', () => {
            renderProperties(activeFilter, sortSelect.value);
        });
        
        // Lightbox controls
        document.getElementById('lbClose').addEventListener('click', closeLightbox);
        document.getElementById('lbNext').addEventListener('click', nextImage);
        document.getElementById('lbPrev').addEventListener('click', prevImage);
        document.getElementById('lightboxModal').addEventListener('click', (e) => { 
            if(e.target === document.getElementById('lightboxModal')) closeLightbox(); 
        });
        
        // Login prompt functions
        window.showLoginPrompt = () => document.getElementById('loginPromptOverlay').style.display = 'flex';
        document.getElementById('cancelLoginPrompt').addEventListener('click', () => document.getElementById('loginPromptOverlay').style.display = 'none');
        document.getElementById('loginPromptOverlay').addEventListener('click', (e) => { 
            if(e.target === document.getElementById('loginPromptOverlay')) 
                document.getElementById('loginPromptOverlay').style.display = 'none'; 
        });
    });
</script>
@endsection
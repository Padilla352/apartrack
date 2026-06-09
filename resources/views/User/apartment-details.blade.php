@extends('layouts.app')

@section('title', $apartment['name'] ?? 'Apartment Details')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* ========== ALL CSS (unchanged from original) ========== */
    * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }
    body {
        background: #f1f5f9;
        margin: 0;
        padding: 0;
    }
    .breadcrumb-bar {
        background: white;
        border-radius: 50px;
        padding: 8px 20px;
        margin: 1rem auto;
        max-width: 1200px;
        width: calc(100% - 2rem);
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }
    .breadcrumb-links {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #4b5563;
        flex-wrap: wrap;
    }
    .breadcrumb-links a {
        text-decoration: none;
        color: #3b82f6;
    }
    .back-button {
        background: #f1f5f9;
        border: none;
        padding: 6px 16px;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s;
        color: #1e293b;
    }
    .back-button:hover {
        background: #e2e8f0;
        transform: translateX(2px);
    }
    .apartment-detail-container {
        max-width: 1100px;
        margin: 0 auto 2rem;
        padding: 0 1rem;
    }
    .apartment-card-detail {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 25px -8px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .gallery-main {
        background: #0f172a;
        cursor: pointer;
    }
    .main-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }
    .thumbnail-strip {
        display: flex;
        gap: 8px;
        padding: 10px;
        background: #f8fafc;
        overflow-x: auto;
    }
    .thumbnail {
        width: 70px;
        height: 55px;
        object-fit: cover;
        border-radius: 8px;
        opacity: 0.7;
        border: 2px solid transparent;
        cursor: pointer;
    }
    .thumbnail.active {
        opacity: 1;
        border-color: #3b82f6;
    }
    .detail-content {
        padding: 1.2rem 1.5rem;
    }
    .title-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .apartment-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0;
        color: #0f2b4d;
    }
    .rating-badge {
        background: #fef9e3;
        padding: 4px 12px;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .stars {
        color: #f4b117;
    }
    .two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin: 1rem 0;
    }
    .detail-section h3 {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        border-left: 4px solid #3b82f6;
        padding-left: 0.7rem;
        color: #0f2b4d;
    }
    .info-list {
        list-style: none;
        padding: 0;
        background: #f9fafc;
        border-radius: 18px;
        padding: 0.5rem 1rem;
        margin: 0;
    }
    .info-list li {
        display: flex;
        gap: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9edf2;
        font-size: 0.9rem;
    }
    .info-list li:last-child {
        border-bottom: none;
    }
    .info-list li strong {
        min-width: 110px;
        color: #334155;
    }
    .info-list li span {
        color: #475569;
    }
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: white;
        margin-top: auto;
    }
    .btn-chat, .btn-map {
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-chat {
        background: #3b82f6;
        color: white;
    }
    .btn-map {
        background: #10b981;
        color: white;
    }
    .btn-chat:hover, .btn-map:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    #lightboxModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.95);
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }
    .map-modal, .ratings-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        z-index: 3000;
        justify-content: center;
        align-items: center;
    }
    .map-modal.active, .ratings-modal.active {
        display: flex;
    }
    .map-modal-content {
        background: white;
        border-radius: 24px;
        width: 90%;
        max-width: 900px;
        height: 60vh;
        padding: 0;
        overflow: hidden;
        position: relative;
    }
    .ratings-modal-content {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        width: 380px;
        text-align: center;
        position: relative;
    }
    #map-container {
        height: 100%;
        width: 100%;
    }
    .close-modal {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 28px;
        cursor: pointer;
        color: #666;
        z-index: 10;
    }
    .close-modal:hover {
        color: #000;
    }
    .no-data-message {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 24px;
        color: #64748b;
    }
    .no-data-message i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    @media (max-width: 700px) {
        .two-columns {
            grid-template-columns: 1fr;
        }
        .main-image {
            height: 200px;
        }
        .action-buttons {
            justify-content: center;
        }
        .breadcrumb-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .back-button {
            justify-content: center;
        }
        .breadcrumb-links {
            justify-content: center;
        }
        .info-list li {
            flex-direction: column;
            gap: 0.25rem;
        }
        .info-list li strong {
            min-width: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    @if(empty($apartment) || !isset($apartment['id']))
        <div class="no-data-message">
            <i class="fas fa-building"></i>
            <h3>Apartment Not Found</h3>
            <p>The apartment you're looking for does not exist or has been removed.</p>
            <a href="{{ route('explore') }}" class="back-button" style="display: inline-block; margin-top: 1rem;">
                <i class="fas fa-arrow-left"></i> Back to Explore
            </a>
        </div>
    @else
    <div class="breadcrumb-bar">
        <div class="breadcrumb-links">
            <a href="{{ route('explore') }}">Explore</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
            <a href="{{ url()->previous() }}">Back</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 9px;"></i>
            <span>{{ $apartment['name'] ?? '' }}</span>
        </div>
    </div>

    <div class="apartment-detail-container">
        <div class="apartment-card-detail">
            @php
                $images = [];
                if (isset($apartment['images'])) {
                    if (is_array($apartment['images'])) {
                        $images = $apartment['images'];
                    } elseif (is_string($apartment['images'])) {
                        $images = json_decode($apartment['images'], true) ?: [];
                    }
                }
                $images = array_values(array_filter($images));
                $hasImages = !empty($images);
                $imageUrls = array_map(function($img) {
                    if (empty($img)) return null;
                    if (str_starts_with($img, 'http')) return $img;
                    return Storage::url(ltrim($img, '/'));
                }, $images);
                $imageUrls = array_filter($imageUrls);
            @endphp
            
            @if($hasImages)
            <div class="gallery-main" id="galleryMain">
                <img id="mainGalleryImg" class="main-image" src="{{ $imageUrls[0] }}" alt="{{ $apartment['name'] ?? '' }}">
                <div class="thumbnail-strip" id="thumbnailStrip">
                    @foreach($imageUrls as $index => $imgUrl)
                        <img src="{{ $imgUrl }}" class="thumbnail {{ $loop->first ? 'active' : '' }}" data-index="{{ $index }}" alt="Thumbnail {{ $index + 1 }}">
                    @endforeach
                </div>
            </div>
            @else
            <div class="gallery-main" style="background: #f1f5f9; min-height: 280px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                <i class="fas fa-image" style="font-size: 48px; color: #94a3b8; margin-bottom: 1rem;"></i>
                <p style="color: #64748b; font-size: 0.9rem;">No images available for this apartment</p>
            </div>
            @endif

            <div class="detail-content">
                <div class="title-section">
                    <h1 class="apartment-title">{{ $apartment['name'] ?? '' }}</h1>
                    @if(isset($apartment['rating']) && $apartment['rating'] > 0)
                    <div class="rating-badge">
                        <span class="stars" id="starsDisplay"></span>
                        <span>{{ number_format($apartment['rating'], 1) }}</span>
                        <a href="javascript:void(0)" class="view-ratings-link" style="color:#3b82f6;">({{ $apartment['review_count'] ?? 0 }} reviews)</a>
                    </div>
                    @endif
                </div>

                <div class="two-columns">
                    <div class="detail-section">
                        <h3>🏠 Property Details</h3>
                        <ul class="info-list">
                            @if(!empty($apartment['unit_number']))
                            <li><strong>Unit Number:</strong> <span>{{ $apartment['unit_number'] }}</span></li>
                            @endif
                            @if(!empty($apartment['barangay_name']))
                            <li><strong>Location:</strong> <span>{{ $apartment['barangay_name'] }}, Binalonan, Pangasinan</span></li>
                            @endif
                            @if(isset($apartment['monthly_rent']) && $apartment['monthly_rent'] > 0)
                            <li><strong>Monthly Rent:</strong> <span>₱{{ number_format($apartment['monthly_rent'], 2) }}</span></li>
                            @endif
                            @if(!empty($apartment['floor_area_sqm']))
                            <li><strong>Floor Area:</strong> <span>{{ $apartment['floor_area_sqm'] }} sqm</span></li>
                            @endif
                            @if(isset($apartment['bedrooms']) && $apartment['bedrooms'] > 0)
                            <li><strong>Bedrooms:</strong> <span>{{ $apartment['bedrooms'] }}</span></li>
                            @endif
                            @if(isset($apartment['bathrooms']) && $apartment['bathrooms'] > 0)
                            <li><strong>Bathrooms:</strong> <span>{{ $apartment['bathrooms'] }}</span></li>
                            @endif
                            @if(!empty($apartment['type']))
                            <li><strong>Property Type:</strong> <span>{{ $apartment['type'] }}</span></li>
                            @endif
                            @if(!empty($apartment['description']))
                            <li><strong>Description:</strong> <span>{{ $apartment['description'] }}</span></li>
                            @endif
                        </ul>
                    </div>

                    @auth
                    <div class="detail-section">
                        <h3>👤 Host Information</h3>
                        <ul class="info-list">
                            @if(!empty($apartment['owner_name']))
                            <li><strong>Name:</strong> <span>{{ $apartment['owner_name'] }}</span></li>
                            @endif
                            @if(!empty($apartment['owner_email']))
                            <li><strong>Email:</strong> <span>{{ $apartment['owner_email'] }}</span></li>
                            @endif
                            @if(!empty($apartment['owner_phone']))
                            <li><strong>Phone:</strong> <span>{{ $apartment['owner_phone'] }}</span></li>
                            @endif
                        </ul>
                    </div>
                    @else
                    <div class="detail-section">
                        <h3>👤 Host Information</h3>
                        <div class="info-list" style="padding: 1rem; text-align: center;">
                            <i class="fas fa-lock" style="color: #3b82f6;"></i>
                            <p style="margin-top: 0.5rem; font-size: 0.85rem;">
                                <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">Log in</a> to see owner contact details.
                            </p>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>

            <!-- ========== ACTION BUTTONS ========== -->
            <div class="action-buttons">
                @auth
                    @php
                        // Direct Facebook page link (can be dynamic from $apartment['facebook_url'] if needed)
                        $facebookPageLink = 'https://www.facebook.com/kharl.luxx';
                    @endphp
                    <a href="{{ $facebookPageLink }}" target="_blank" class="btn-chat" rel="noopener noreferrer">
                        <i class="fab fa-facebook-messenger"></i> Contact on Facebook
                    </a>
                    <button class="btn-map" id="openMapBtn">
                        <i class="fa-solid fa-map-location-dot"></i> View Map
                    </button>
                @else
                    <a href="{{ route('chat.start', $apartment->owner_id) }}" class="btn btn-primary">
    💬 Chat Owner
</a>
                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn-map">
                        <i class="fa-solid fa-map-location-dot"></i> View Map
                    </a>
                @endauth
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Lightbox Modal -->
<div id="lightboxModal">
    <div style="position: relative; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
        <span id="closeLightbox" style="position: absolute; top: 20px; right: 30px; font-size: 40px; color: white; cursor: pointer; z-index: 10001;">&times;</span>
        <img id="lightboxImg" src="" style="max-width: 95vw; max-height: 95vh; width: auto; height: auto; object-fit: contain;">
        <button id="prevImageBtn" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.6); color: white; border: none; font-size: 40px; padding: 10px 20px; cursor: pointer; border-radius: 50%;">&lt;</button>
        <button id="nextImageBtn" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.6); color: white; border: none; font-size: 40px; padding: 10px 20px; cursor: pointer; border-radius: 50%;">&gt;</button>
        <div id="lightboxCounter" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); color: white; padding: 6px 18px; border-radius: 40px; font-size: 0.9rem;"></div>
    </div>
</div>

<!-- Map Modal -->
<div id="mapModal" class="map-modal">
    <div class="map-modal-content">
        <span class="close-modal" id="closeMapModal">&times;</span>
        <div id="map-container"></div>
    </div>
</div>

<!-- Ratings Modal -->
<div id="ratingsModal" class="ratings-modal">
    <div class="ratings-modal-content">
        <span class="close-modal" id="closeRatingsModal">&times;</span>
        <div id="ratingsModalBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    
    const apartment = {
        name: @json($apartment['name'] ?? ''),
        images: @json($imageUrls ?? []),
        rating: parseFloat(@json($apartment['rating'] ?? 0)),
        reviewCount: parseInt(@json($apartment['review_count'] ?? 0)),
        location: @json(($apartment['barangay_name'] ?? '') . ', Binalonan, Pangasinan'),
        latitude: @json($apartment['latitude'] ?? null),
        longitude: @json($apartment['longitude'] ?? null),
        ownerId: @json($apartment['owner_id'] ?? $apartment['ownerId'] ?? null)
    };

    let imagesArray = apartment.images.filter(img => img && img.trim());
    let currentIndex = 0;

    function renderStars(rating) {
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5;
        let starsHtml = '';
        for (let i = 0; i < fullStars; i++) starsHtml += '<i class="fa-solid fa-star"></i>';
        if (halfStar) starsHtml += '<i class="fa-solid fa-star-half-alt"></i>';
        const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
        for (let i = 0; i < emptyStars; i++) starsHtml += '<i class="fa-regular fa-star"></i>';
        return starsHtml;
    }

    if (apartment.rating > 0 && document.getElementById('starsDisplay')) {
        document.getElementById('starsDisplay').innerHTML = renderStars(apartment.rating);
    }

    // Thumbnail gallery
    function updateMainImage(index) {
        if (!imagesArray[index]) return;
        currentIndex = index;
        const mainImg = document.getElementById('mainGalleryImg');
        if (mainImg) mainImg.src = imagesArray[index];
        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }
    document.querySelectorAll('.thumbnail').forEach((thumb, idx) => {
        thumb.addEventListener('click', () => updateMainImage(idx));
    });

    // Lightbox
    const lightboxModal = document.getElementById('lightboxModal');
    const lightboxImg = document.getElementById('lightboxImg');
    const prevBtn = document.getElementById('prevImageBtn');
    const nextBtn = document.getElementById('nextImageBtn');
    const closeLightbox = document.getElementById('closeLightbox');
    const lightboxCounter = document.getElementById('lightboxCounter');
    let currentLightboxIndex = 0;
    function openLightbox(index) {
        if (!imagesArray.length) return;
        currentLightboxIndex = index;
        lightboxImg.src = imagesArray[index];
        lightboxCounter.textContent = `${index+1} / ${imagesArray.length}`;
        lightboxModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function showLightboxImage(index) {
        if (index < 0) index = imagesArray.length - 1;
        if (index >= imagesArray.length) index = 0;
        currentLightboxIndex = index;
        lightboxImg.src = imagesArray[index];
        lightboxCounter.textContent = `${index+1} / ${imagesArray.length}`;
    }
    const galleryMain = document.getElementById('galleryMain');
    if (galleryMain && imagesArray.length) {
        galleryMain.addEventListener('click', () => openLightbox(currentIndex));
    }
    prevBtn?.addEventListener('click', (e) => { e.stopPropagation(); showLightboxImage(currentLightboxIndex - 1); });
    nextBtn?.addEventListener('click', (e) => { e.stopPropagation(); showLightboxImage(currentLightboxIndex + 1); });
    closeLightbox?.addEventListener('click', () => { lightboxModal.style.display = 'none'; document.body.style.overflow = ''; });
    lightboxModal?.addEventListener('click', (e) => { if (e.target === lightboxModal) { lightboxModal.style.display = 'none'; document.body.style.overflow = ''; } });
    document.addEventListener('keydown', (e) => {
        if (lightboxModal.style.display !== 'flex') return;
        if (e.key === 'ArrowLeft') { e.preventDefault(); showLightboxImage(currentLightboxIndex - 1); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); showLightboxImage(currentLightboxIndex + 1); }
        else if (e.key === 'Escape') { lightboxModal.style.display = 'none'; document.body.style.overflow = ''; }
    });

    // ========== GOOGLE MAPS INITIALIZATION ==========
    let mapInstance = null;
    let googleMapsApiLoaded = false;
    
    function initGoogleMap() {
        const mapContainer = document.getElementById('map-container');
        if (!mapContainer || mapInstance) return;
        
        // Get coordinates from apartment data
        let lat = apartment.latitude ? parseFloat(apartment.latitude) : 16.0489;
        let lng = apartment.longitude ? parseFloat(apartment.longitude) : 120.3364;
        
        // Check if coordinates are valid
        if (isNaN(lat) || isNaN(lng)) {
            lat = 16.0489;
            lng = 120.3364;
        }
        
        const location = { lat: lat, lng: lng };
        
        // Create map with improved settings
        mapInstance = new google.maps.Map(mapContainer, {
            center: location,
            zoom: 16,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true,
            zoomControl: true,
            gestureHandling: 'greedy',
            mapTypeId: 'roadmap'
        });
        
        // Add marker with animation
        const marker = new google.maps.Marker({
            position: location,
            map: mapInstance,
            title: apartment.name || 'Apartment Location',
            animation: google.maps.Animation.DROP
        });
        
        // Add info window
        const infoWindow = new google.maps.InfoWindow({
            content: `<div style="padding: 10px; font-size: 0.9rem;"><strong>${apartment.name || 'Location'}</strong><br>${apartment.location || ''}</div>`
        });
        
        marker.addListener('click', () => {
            infoWindow.open(mapInstance, marker);
        });
        
        // Trigger a resize event to ensure map displays properly
        setTimeout(() => {
            google.maps.event.trigger(mapInstance, 'resize');
            mapInstance.setCenter(location);
        }, 100);
    }
    
    function loadGoogleMapsApi() {
        // If already loaded, just init the map
        if (googleMapsApiLoaded) {
            initGoogleMap();
            return;
        }
        
        // If Google Maps API already exists globally
        if (typeof google !== 'undefined' && google.maps) {
            googleMapsApiLoaded = true;
            initGoogleMap();
            return;
        }
        
        // Load the API dynamically
        const apiKey = @json(config('services.google_maps.key'));
        if (!apiKey) {
            console.error('Google Maps API key not configured');
            return;
        }
        
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}`;
        script.async = true;
        script.defer = true;
        
        script.onload = () => {
            googleMapsApiLoaded = true;
            initGoogleMap();
        };
        
        script.onerror = () => {
            console.error('Failed to load Google Maps API');
        };
        
        document.head.appendChild(script);
    }
    
    // ========== MAP MODAL EVENTS ==========
    const mapModal = document.getElementById('mapModal');
    const openMapBtn = document.getElementById('openMapBtn');
    
    if (openMapBtn) {
        openMapBtn.addEventListener('click', (e) => {
            e.preventDefault();
            mapModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Wait for modal to be visible before initializing map
            setTimeout(() => {
                loadGoogleMapsApi();
            }, 250);
        });
    }
    
    if (document.getElementById('closeMapModal')) {
        document.getElementById('closeMapModal').addEventListener('click', () => {
            mapModal.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    if (mapModal) {
        mapModal.addEventListener('click', (e) => {
            if (e.target === mapModal) {
                mapModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Ratings modal
    function showRatingsModal() {
        const rating = apartment.rating || 0;
        const modalBody = document.getElementById('ratingsModalBody');
        if (modalBody) {
            modalBody.innerHTML = `
                <h3>⭐ Ratings & Reviews</h3>
                <div style="font-size: 2rem; font-weight:800;">${rating.toFixed(1)}</div>
                <div style="color:#f4b117; font-size:1.2rem;">${renderStars(rating)}</div>
                <div>Based on ${apartment.reviewCount} reviews</div>
                <div><button id="mockReviewBtn" style="margin-top:1rem; background:#eef2ff; border:none; padding:6px 16px; border-radius:40px; cursor:pointer;">Leave a review</button></div>
            `;
        }
        const ratingsModalEl = document.getElementById('ratingsModal');
        ratingsModalEl.classList.add('active');
        document.body.style.overflow = 'hidden';
        document.getElementById('mockReviewBtn')?.addEventListener('click', () => alert('Review feature coming soon!'));
    }
    document.querySelector('.view-ratings-link')?.addEventListener('click', showRatingsModal);
    document.getElementById('closeRatingsModal')?.addEventListener('click', () => {
        document.getElementById('ratingsModal').classList.remove('active');
        document.body.style.overflow = '';
    });
    document.getElementById('ratingsModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('ratingsModal')) {
            document.getElementById('ratingsModal').classList.remove('active');
            document.body.style.overflow = '';
        }
    });
</script>
@endsection
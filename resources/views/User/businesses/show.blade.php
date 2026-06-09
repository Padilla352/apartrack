@extends('layouts.app')

@section('title', $business->business_name . ' - Business Space Details')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #eff6ff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    }

    .detail-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--white);
        color: var(--primary-dark);
        padding: 0.6rem 1.2rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        border: 1px solid var(--gray-200);
        transition: all 0.2s;
        margin-bottom: 1.5rem;
    }
    .back-btn:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        transform: translateX(-4px);
    }

    .business-card {
        background: var(--white);
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-200);
    }

    .gallery-section {
        padding: 1.5rem;
    }

    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 1.5rem;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .main-image:hover {
        transform: scale(1.01);
    }

    .thumbnail-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }
    .thumbnail {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 1rem;
        cursor: pointer;
        transition: 0.2s;
        border: 2px solid transparent;
    }
    .thumbnail.active {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }
    .thumbnail:hover {
        opacity: 0.85;
    }

    .info-section {
        padding: 1.5rem 2rem 2rem;
    }

    .business-name {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 0.5rem;
    }

    .type-badge {
        display: inline-block;
        background: var(--primary-light);
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 1rem;
        border-radius: 30px;
        margin-bottom: 1rem;
    }

    .price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin: 1rem 0;
    }
    .price small {
        font-size: 0.9rem;
        font-weight: 400;
        color: var(--gray-600);
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin: 1.5rem 0;
        background: var(--gray-50);
        padding: 1.5rem;
        border-radius: 1.5rem;
    }
    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 0.9rem;
        color: var(--gray-700);
    }
    .detail-item i {
        width: 2rem;
        color: var(--primary);
        font-size: 1.2rem;
    }
    .detail-item strong {
        font-weight: 600;
        color: var(--gray-900);
    }

    .description {
        margin: 1.5rem 0;
        line-height: 1.6;
        color: var(--gray-700);
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    .btn-chat, .btn-map {
        padding: 0.8rem 1.8rem;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
    }
    .btn-chat {
        background: var(--primary);
        color: white;
        box-shadow: var(--shadow-sm);
    }
    .btn-chat:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }
    .btn-map {
        background: var(--gray-100);
        color: var(--primary-dark);
        border: 1px solid var(--gray-200);
    }
    .btn-map:hover {
        background: var(--gray-200);
        transform: translateY(-2px);
    }

    /* Lightbox */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.95);
        backdrop-filter: blur(8px);
        z-index: 2000;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .lightbox.active {
        display: flex;
    }
    .lightbox-content {
        max-width: 90vw;
        max-height: 80vh;
        position: relative;
    }
    .lightbox-img {
        max-width: 100%;
        max-height: 80vh;
        border-radius: 1rem;
    }
    .lb-prev, .lb-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        font-size: 2rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
        border-radius: 50%;
        transition: 0.2s;
    }
    .lb-prev { left: -70px; }
    .lb-next { right: -70px; }
    .lb-prev:hover, .lb-next:hover {
        background: var(--primary);
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
    }

    @media (max-width: 768px) {
        .detail-container { margin: 1rem auto; padding: 0 1rem; }
        .main-image { height: 250px; }
        .thumbnail-grid { grid-template-columns: repeat(3, 1fr); }
        .info-section { padding: 1rem; }
        .business-name { font-size: 1.5rem; }
        .price { font-size: 1.5rem; }
        .detail-grid { grid-template-columns: 1fr; }
        .lb-prev { left: 10px; }
        .lb-next { right: 10px; }
    }
</style>
@endsection

@section('content')
<div class="detail-container">
    <a href="{{ url()->previous() }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="business-card">
        {{-- Gallery Section --}}
        @php
            $images = [];
            if ($business->images) {
                if (is_array($business->images)) {
                    $images = $business->images;
                } else {
                    try {
                        $decoded = json_decode($business->images, true);
                        $images = is_array($decoded) ? $decoded : [$business->images];
                    } catch(\Exception $e) {
                        $images = [$business->images];
                    }
                }
            }
            if (empty($images)) {
                $images = ['https://placehold.co/800x500/2563eb/white?text=Business+Space'];
            }
            // Convert storage paths to full URLs
            $images = array_map(function($img) {
                if (str_starts_with($img, 'http')) return $img;
                if (str_starts_with($img, '/')) return $img;
                return asset('storage/' . ltrim($img, '/'));
            }, $images);
        @endphp

        <div class="gallery-section">
            <img src="{{ $images[0] }}" alt="Main image" class="main-image" id="mainImage">
            @if(count($images) > 1)
                <div class="thumbnail-grid">
                    @foreach($images as $index => $img)
                        <img src="{{ $img }}" class="thumbnail {{ $index == 0 ? 'active' : '' }}" data-index="{{ $index }}" onclick="setMainImage({{ $index }})">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="info-section">
            <h1 class="business-name">{{ $business->business_name }}</h1>
            <div class="type-badge">
                <i class="fas fa-store"></i> {{ $business->type ?? 'Commercial Space' }}
            </div>

            <div class="price">
                ₱{{ number_format($business->monthly_rent, 2) }}<small>/month</small>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div><strong>Barangay:</strong> {{ $business->barangay_name ?? 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-ruler-combined"></i>
                    <div><strong>Floor Area:</strong> {{ $business->floor_area_sqm ?? '?' }} sqm</div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-user-circle"></i>
                    <div><strong>Owned by:</strong> {{ $business->owner_name ?? 'Owner' }}</div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-check-circle"></i>
                    <div><strong>Status:</strong> Available</div>
                </div>
            </div>

            @if($business->description)
                <div class="description">
                    <h3><i class="fas fa-info-circle"></i> Description</h3>
                    <p>{{ nl2br(e($business->description)) }}</p>
                </div>
            @endif

            <div class="action-buttons">
                @php
                    $contact = $business->contact_number ?? $business->owner_email ?? null;
                @endphp
                @if($contact)
                    @php
                        $isPhone = preg_match('/^\+?[0-9\s\-\(\)]+$/', trim($contact));
                    @endphp
                    @if($isPhone)
                        <a href="https://wa.me/{{ preg_replace('/\s+/', '', $contact) }}?text={{ urlencode('Hello, I am interested in your business space: ' . $business->business_name) }}" target="_blank" class="btn-chat">
                            <i class="fab fa-whatsapp"></i> Contact via WhatsApp
                        </a>
                    @elseif(filter_var($contact, FILTER_VALIDATE_EMAIL))
                        <a href="mailto:{{ $contact }}?subject={{ urlencode('Inquiry about ' . $business->business_name) }}&body={{ urlencode('Hello, I am interested in your business space. Please send me more details.') }}" class="btn-chat">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
                    @else
                        <button class="btn-chat" onclick="alert('Contact: {{ addslashes($contact) }}')">
                            <i class="fas fa-phone-alt"></i> Contact Host
                        </button>
                    @endif
                @else
                    <button class="btn-chat" onclick="alert('Contact information not available. Please login to request details.')">
                        <i class="fas fa-comment"></i> Request Contact
                    </button>
                @endif

                <button class="btn-map" onclick="openMap()">
                    <i class="fas fa-map"></i> View on Map
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox Modal --}}
<div id="lightbox" class="lightbox">
    <button class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
    <div class="lightbox-content">
        <button class="lb-prev" onclick="prevImage()"><i class="fas fa-chevron-left"></i></button>
        <img id="lightboxImg" class="lightbox-img" src="">
        <button class="lb-next" onclick="nextImage()"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="lb-counter" id="lbCounter"></div>
</div>

<script>
    // Gallery images array from PHP
    const galleryImages = @json($images);
    let currentIndex = 0;

    function setMainImage(index) {
        currentIndex = index;
        document.getElementById('mainImage').src = galleryImages[index];
        // Update active thumbnail styling
        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
            if (i == index) thumb.classList.add('active');
            else thumb.classList.remove('active');
        });
    }

    // Lightbox functions
    function openLightbox(index) {
        currentIndex = index;
        const imgElement = document.getElementById('lightboxImg');
        const counter = document.getElementById('lbCounter');
        imgElement.src = galleryImages[currentIndex];
        counter.innerText = `${currentIndex+1} / ${galleryImages.length}`;
        document.getElementById('lightbox').classList.add('active');
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % galleryImages.length;
        document.getElementById('lightboxImg').src = galleryImages[currentIndex];
        document.getElementById('lbCounter').innerText = `${currentIndex+1} / ${galleryImages.length}`;
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        document.getElementById('lightboxImg').src = galleryImages[currentIndex];
        document.getElementById('lbCounter').innerText = `${currentIndex+1} / ${galleryImages.length}`;
    }

    // Open lightbox when main image clicked
    document.getElementById('mainImage').addEventListener('click', () => openLightbox(currentIndex));

    // Close lightbox when clicking outside
    document.getElementById('lightbox').addEventListener('click', (e) => {
        if (e.target === document.getElementById('lightbox')) closeLightbox();
    });

    // Map function
    function openMap() {
        const address = "{{ addslashes($business->barangay_name ?? '') }}, Binalonan, Pangasinan";
        if (address) {
            const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
            window.open(mapsUrl, '_blank');
        } else {
            alert('Location not available.');
        }
    }
</script>
@endsection
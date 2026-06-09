@extends('layouts.admin')

@section('title', $apartment->name ?? 'Apartment Details')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* GLOBAL RESET */
    * {
        font-family: 'Inter', 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    body {
        background: #000 !important; /* Force black background */
        margin: 0;
        color: #e2e8f0;
    }

    .apartment-detail-wrapper {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* BREADCRUMB */
    .breadcrumb-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px 0;
    }

    .breadcrumb-links {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .breadcrumb-links a {
        color: #f5b81b;
        text-decoration: none;
        font-weight: 600;
    }

    /* MAIN CARD */
    .apartment-card-detail {
        background: #0f172a; /* Dark blue-grey card */
        border-radius: 16px;
        border: 1px solid rgba(245, 184, 27, 0.2);
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    /* GALLERY SYSTEM - THE FIXED DESIGN */
    .gallery-section {
        background: #050505;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .gallery-main {
        width: 100%;
        height: 450px; /* Nilimitahan ang height para hindi kainin ang buong screen */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #000;
        position: relative;
    }

    .main-image {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ito ang nag-aayos para mag-scale nang tama ang portrait images */
        object-position: center; /* Gitna lagi ang focus */
        transition: opacity 0.3s ease-in-out;
    }

    .thumbnail-strip {
        display: flex;
        flex-direction: row; 
        gap: 12px;
        padding: 15px;
        overflow-x: auto; 
        background: #0f172a;
        border-top: 1px solid rgba(245, 184, 27, 0.1);
    }

    .thumbnail-strip::-webkit-scrollbar {
        height: 6px;
    }

    .thumbnail-strip::-webkit-scrollbar-thumb {
        background: #f5b81b;
        border-radius: 10px;
    }

    .thumbnail {
        width: 120px;
        height: 80px;
        flex: 0 0 auto; 
        border-radius: 8px;
        cursor: pointer;
        object-fit: cover;
        opacity: 0.5;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .thumbnail.active {
        opacity: 1;
        border-color: #f5b81b;
        transform: scale(1.05);
    }

    /* CONTENT LAYOUT */
    .detail-content {
        padding: 30px;
    }

    .apartment-title {
        font-size: 2rem;
        color: #fff;
        margin-bottom: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .status-approved { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
    .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }

    .two-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .detail-section {
        background: rgba(255,255,255,0.03);
        padding: 20px;
        border-radius: 12px;
    }

    .info-list {
        list-style: none;
        padding: 0;
    }

    .info-list li {
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex;
        justify-content: space-between;
    }

    .info-list strong { color: #94a3b8; }
    .rent-price { color: #f5b81b; font-weight: bold; font-size: 1.2rem; }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .two-columns { grid-template-columns: 1fr; }
        .gallery-main { height: 280px; } /* Mas maliit na height kapag mobile screen */
    }
</style>
@endsection

@section('content')
<div class="apartment-detail-wrapper">
    @if(empty($apartment))
        <div class="no-data-message" style="text-align: center; padding: 50px;">
            <h3>Apartment Not Found</h3>
            <a href="{{ route('admin.apartments.barangay') }}" style="color: #f5b81b;">Back to List</a>
        </div>
    @else
    <div class="breadcrumb-bar">
        <div class="breadcrumb-links">
            <a href="{{ route('admin.apartments.barangay') }}">Barangays</a>
            <i class="fas fa-chevron-right" style="margin: 0 10px; font-size: 0.8rem;"></i>
            <span>{{ $apartment->name }}</span>
        </div>
        <a href="{{ route('admin.apartments.barangay') }}" style="color: #f5b81b; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="apartment-card-detail">
        <div class="gallery-section">
            @php
                $images = is_string($apartment->images) ? json_decode($apartment->images, true) : $apartment->images;
                $images = is_array($images) ? array_values(array_filter($images)) : [];
            @endphp

            @if(!empty($images))
                <div class="gallery-main">
                    @php
                        $firstImg = (strpos($images[0], 'http') === 0) ? $images[0] : asset('storage/' . ltrim($images[0], '/'));
                    @endphp
                    <img id="mainGalleryImg" class="main-image" src="{{ $firstImg }}" alt="Main View">
                </div>

                <div class="thumbnail-strip">
                    @foreach($images as $index => $img)
                        @php
                            $url = (strpos($img, 'http') === 0) ? $img : asset('storage/' . ltrim($img, '/'));
                        @endphp
                        <img src="{{ $url }}" 
                             class="thumbnail {{ $index == 0 ? 'active' : '' }}" 
                             onclick="changeImage(this)"
                             alt="Thumb">
                    @endforeach
                </div>
            @else
                <div class="gallery-main">
                    <i class="fas fa-image fa-5x" style="color: #334155;"></i>
                </div>
            @endif
        </div>

        <div class="detail-content">
            <h1 class="apartment-title">{{ $apartment->name }}</h1>
            
            <div class="status-badge status-{{ $apartment->verification_status ?? 'pending' }}">
                {{ ucfirst($apartment->verification_status ?? 'pending') }} Verification
            </div>

            <div class="two-columns">
                <div class="detail-section">
                    <h3 style="color: #f5b81b;"><i class="fas fa-info-circle"></i> Property Details</h3>
                    <ul class="info-list">
                        <li><strong>Unit:</strong> <span>{{ $apartment->unit_number ?? 'N/A' }}</span></li>
                        <li><strong>Rent:</strong> <span class="rent-price">₱{{ number_format($apartment->monthly_rent ?? 0, 2) }}</span></li>
                        <li><strong>Size:</strong> <span>{{ $apartment->floor_area_sqm ?? 0 }} sqm</span></li>
                        <li><strong>Type:</strong> <span>{{ $apartment->type ?? 'N/A' }}</span></li>
                    </ul>
                </div>

                <div class="detail-section">
                    <h3 style="color: #f5b81b;"><i class="fas fa-user"></i> Host</h3>
                    <ul class="info-list">
                        <li><strong>Name:</strong> <span>{{ $apartment->owner_name ?? 'N/A' }}</span></li>
                        <li><strong>Phone:</strong> <span>{{ $apartment->owner_phone ?? 'N/A' }}</span></li>
                        <li><strong>Email:</strong> <span>{{ $apartment->owner_email ?? 'N/A' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function changeImage(element) {
        const mainImg = document.getElementById('mainGalleryImg');
        if (mainImg) {
            mainImg.style.opacity = '0.5';
            
            setTimeout(() => {
                mainImg.src = element.src;
                mainImg.style.opacity = '1';
            }, 150);

            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            element.classList.add('active');
        }
    }
</script>
@endsection
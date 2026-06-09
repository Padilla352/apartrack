@extends('owner.layouts.app')

@section('title', 'View Details')
@section('page-title', 'view details')

@section('content')
<a href="{{ route('owner.apartments.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> back to MY LISTINGS
</a>

<div class="details-card">
    <h2 class="apartment-title">{{ $apartment->name }}</h2>
    
    <div class="image-gallery-section">
        <div class="gallery-container">
            @php
                // Get raw images (could be array or JSON string)
                $imagesRaw = $apartment->images ?? [];
                if (is_string($imagesRaw)) {
                    $imagesRaw = json_decode($imagesRaw, true) ?? [];
                }
                
                // Ensure it's an array and filter out empty values
                $imagesRaw = is_array($imagesRaw) ? $imagesRaw : [];
                $imagesRaw = array_filter($imagesRaw, fn($path) => !empty($path));
                
                // Convert stored paths to public URLs
                $allImages = [];
                foreach ($imagesRaw as $path) {
                    // Remove any accidental 'storage/' prefix from DB
                    $cleanPath = str_replace('storage/', '', $path);
                    // Generate correct URL using Storage facade
                    $url = \Storage::url($cleanPath);
                    $allImages[] = $url;
                }
                
                $hasImages = count($allImages) > 0;
                $mainImage = $hasImages ? $allImages[0] : null;
            @endphp
            
            @if($hasImages)
                <div class="main-image">
                    <img src="{{ $mainImage }}" alt="{{ $apartment->name }}" id="mainGalleryImage">
                    @if(count($allImages) > 1)
                        <div class="image-counter">
                            <i class="fas fa-images"></i> {{ count($allImages) }} photos
                        </div>
                    @endif
                </div>
                
                @if(count($allImages) > 1)
                    <div class="thumbnail-gallery">
                        @foreach($allImages as $index => $imageUrl)
                            <div class="thumbnail {{ $index == 0 ? 'active' : '' }}" 
                                 onclick="changeImage('{{ $imageUrl }}', this)">
                                <img src="{{ $imageUrl }}" alt="Thumbnail {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="main-image placeholder">
                    <i class="fas fa-building"></i>
                    <p>No images available</p>
                    <small>Click "UPDATE DETAILS" to add photos</small>
                </div>
            @endif
        </div>
    </div>
    
    <div class="details-grid">
        <div class="details-section">
            <h3 class="section-title">APARTMENT DETAILS</h3>
            <div class="details-list">
                <div class="detail-row">
                    <span class="detail-label">APARTMENT NAME:</span>
                    <span class="detail-value">{{ $apartment->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">UNIT NUMBER:</span>
                    <span class="detail-value">{{ $apartment->unit_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">LOCATION:</span>
                    <span class="detail-value">{{ $apartment->address }}, {{ $apartment->barangay->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">PRICE:</span>
                    <span class="detail-value">₱{{ number_format($apartment->monthly_rent, 2) }}/Monthly</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">FEATURES:</span>
                    <span class="detail-value">
                        {{ $apartment->type }} type, 
                        {{ $apartment->bedrooms }} bedroom(s), 
                        {{ $apartment->bathrooms }} bathroom(s)
                        @if($apartment->amenities && count($apartment->amenities) > 0)
                            , {{ implode(', ', $apartment->amenities) }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="details-section">
            <h3 class="section-title">OWNER DETAILS</h3>
            <div class="details-list">
                <div class="detail-row">
                    <span class="detail-label">OWNER NAME:</span>
                    <span class="detail-value">{{ Auth::guard('owner')->user()->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">EMAIL:</span>
                    <span class="detail-value">{{ Auth::guard('owner')->user()->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">PERMIT NUMBER:</span>
                    <span class="detail-value">{{ $apartment->permit_number ?? 'Not specified' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">STATUS:</span>
                    <span class="detail-value">{{ $apartment->status }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="action-buttons">
        <a href="{{ route('owner.apartments.edit', $apartment->id) }}" class="btn-update">
            <i class="fas fa-edit"></i> UPDATE DETAILS
        </a>
        <form action="{{ route('owner.apartments.destroy', $apartment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this listing?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">
                <i class="fas fa-trash"></i> DELETE LISTING
            </button>
        </form>
    </div>
</div>

<style>
    /* Show Page Styles - Pure CSS */
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #3b82f6;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 24px;
        transition: all 0.2s;
    }
    
    .back-link:hover {
        gap: 12px;
        color: #2563eb;
    }
    
    .details-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    .apartment-title {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .image-gallery-section {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .gallery-container {
        width: 100%;
    }
    
    .main-image {
        position: relative;
        width: 100%;
        height: 400px;
        background: #f9fafb;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: white;
    }
    
    .image-counter {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .image-counter i {
        margin-right: 6px;
    }
    
    .main-image.placeholder {
        flex-direction: column;
        color: #9ca3af;
        height: 250px;
        text-align: center;
        background: #f9fafb;
    }
    
    .main-image.placeholder i {
        font-size: 48px;
        margin-bottom: 12px;
    }
    
    .main-image.placeholder small {
        font-size: 11px;
        margin-top: 8px;
        color: #3b82f6;
    }
    
    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 6px;
    }
    
    .thumbnail-gallery::-webkit-scrollbar {
        height: 4px;
    }
    
    .thumbnail-gallery::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    .thumbnail-gallery::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    .thumbnail {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
        flex-shrink: 0;
        background: #f9fafb;
    }
    
    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .thumbnail:hover {
        transform: scale(1.05);
        border-color: #cbd5e1;
    }
    
    .thumbnail.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    /* WHITE BACKGROUND SECTIONS - FORCED WHITE */
    .details-section {
        background: white !important;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827 !important;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3b82f6;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .details-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .detail-row {
        display: flex;
        align-items: flex-start;
        line-height: 1.4;
    }
    
    .detail-label {
        width: 130px;
        font-weight: 600;
        color: #6b7280 !important;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        flex-shrink: 0;
    }
    
    .detail-value {
        flex: 1;
        color: #111827 !important;
        font-size: 13px;
        font-weight: 500;
        word-break: break-word;
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-update {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-update:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        color: white;
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-delete:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }
    
    /* Dark Mode Support - Keep backgrounds WHITE */
    @media (prefers-color-scheme: dark) {
        body {
            background: #ffffff;
        }
        
        .details-card {
            background: white !important;
            border-color: #e5e7eb;
        }
        
        .apartment-title {
            color: #111827 !important;
            border-bottom-color: #e5e7eb;
        }
        
        .image-gallery-section {
            border-bottom-color: #e5e7eb;
        }
        
        .main-image {
            background: #f9fafb;
        }
        
        .main-image img {
            background: white;
        }
        
        .details-section {
            background: white !important;
            border-color: #e5e7eb;
        }
        
        .section-title {
            color: #111827 !important;
            border-bottom-color: #3b82f6;
        }
        
        .detail-label {
            color: #6b7280 !important;
        }
        
        .detail-value {
            color: #111827 !important;
        }
        
        .thumbnail {
            background: #f9fafb;
        }
        
        .action-buttons {
            border-top-color: #e5e7eb;
        }
    }
    
    /* Responsive */
    @media (max-width: 900px) {
        .details-card {
            padding: 20px;
        }
        
        .details-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .main-image {
            height: 300px;
        }
        
        .thumbnail {
            width: 55px;
            height: 55px;
        }
        
        .apartment-title {
            font-size: 20px;
        }
    }
    
    @media (max-width: 600px) {
        .details-card {
            padding: 16px;
        }
        
        .apartment-title {
            font-size: 18px;
        }
        
        .detail-row {
            flex-direction: column;
            gap: 4px;
        }
        
        .detail-label {
            width: 100%;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-update, .btn-delete {
            justify-content: center;
            width: 100%;
        }
        
        .details-section {
            padding: 16px;
        }
        
        .main-image {
            height: 200px;
        }
        
        .thumbnail {
            width: 50px;
            height: 50px;
        }
        
        .section-title {
            font-size: 13px;
        }
        
        .detail-label {
            font-size: 11px;
        }
        
        .detail-value {
            font-size: 12px;
        }
    }
</style>

<script>
    function changeImage(imageUrl, element) {
        const mainImg = document.getElementById('mainGalleryImage');
        if (mainImg) {
            mainImg.src = imageUrl;
        }
        // Update active thumbnail class
        document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
        element.classList.add('active');
    }
</script>
@endsection
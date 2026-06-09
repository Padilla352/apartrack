@extends('layouts.admin')

@section('content')
<div class="apartment-detail-container">
    <div class="apartment-detail-wrapper">
        
        <div class="apartment-detail-header">
            <a href="{{ route('admin.apartments.barangay') }}" class="back-button-apartment">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Barangays</span>
            </a>
            <div class="header-title-section">
                <h1 class="page-title-apartment-detail">Listings of {{ $barangayName }}</h1>
                <p class="page-subtitle-apartment-detail">Viewing all available properties in this area</p>
            </div>
        </div>

        @if($apartments->isEmpty())
        <div class="empty-state-container">
            <div class="empty-state-card">
                <div class="empty-state-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="empty-state-title">No Properties Yet</h3>
                <p class="empty-state-message">There are no registered properties in {{ $barangayName }} yet.</p>
                <p class="empty-state-hint">Once property owners add their listings, they will appear here.</p>
            </div>
        </div>
        @else
        @php
            // Define image helper function once (guarded to avoid redeclaration)
            if (!function_exists('getImageUrl')) {
                function getImageUrl($path) {
                    if (empty($path)) return null;
                    
                    // If already a full URL
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        return $path;
                    }
                    
                    // Remove any leading slashes
                    $cleanPath = ltrim($path, '/');
                    
                    // Return storage URL
                    return url('/storage/' . $cleanPath);
                }
            }
            
            // Placeholder SVG for missing images
            $placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%231a1a1a'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23666' font-size='14'%3ENo Image%3C/text%3E%3C/svg%3E";
        @endphp

        <div class="apartments-grid">
            @foreach($apartments as $apartment)
            @php
                // ========== SIMPLIFIED FIXED IMAGE HANDLING ==========
                $images = [];
                
                // Parse images from database
                if (isset($apartment->images) && !empty($apartment->images)) {
                    if (is_string($apartment->images)) {
                        // Try to decode as JSON
                        $decoded = json_decode($apartment->images, true);
                        if (is_array($decoded) && !empty($decoded)) {
                            $images = $decoded;
                        } else {
                            // Not JSON, treat as single image path
                            $images = [$apartment->images];
                        }
                    } elseif (is_array($apartment->images)) {
                        $images = $apartment->images;
                    }
                }
                
                // Clean up images array
                $images = array_values(array_filter($images));
                
                $hasImages = !empty($images);
                
                // Get images for display using the helper function
                $mainImageUrl = $hasImages ? getImageUrl($images[0]) : null;
                $sideImage1Url = $hasImages && count($images) > 1 ? getImageUrl($images[1]) : null;
                $sideImage2Url = $hasImages && count($images) > 2 ? getImageUrl($images[2]) : null;
            @endphp
            <div class="apartment-card">
                <div class="apartment-image-grid">
                    <div class="main-image">
                        @if($mainImageUrl)
                            <img src="{{ $mainImageUrl }}" 
                                 class="apartment-img" 
                                 alt="{{ $apartment->name }}" 
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ $placeholder }}'">
                        @else
                            <img src="{{ $placeholder }}" 
                                 class="apartment-img" 
                                 alt="No Image">
                        @endif
                        <div class="image-overlay"></div>
                    </div>
                    <div class="side-images">
                        <div class="side-image">
                            @if($sideImage1Url)
                                <img src="{{ $sideImage1Url }}" 
                                     class="apartment-img" 
                                     alt="Interior" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ $placeholder }}'">
                            @else
                                <img src="{{ $placeholder }}" 
                                     class="apartment-img" 
                                     alt="No Image">
                            @endif
                        </div>
                        <div class="side-image">
                            @if($sideImage2Url)
                                <img src="{{ $sideImage2Url }}" 
                                     class="apartment-img" 
                                     alt="Additional" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='{{ $placeholder }}'">
                            @else
                                <img src="{{ $placeholder }}" 
                                     class="apartment-img" 
                                     alt="No Image">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="apartment-info">
                    <h3 class="apartment-title">{{ $apartment->name ?? 'Unnamed Property' }}</h3>
                    
                    <div class="price-range">
                        <i class="fas fa-tag"></i>
                        <span>₱ {{ number_format($apartment->monthly_rent ?? 0, 2) }}</span>
                        <small>/ month</small>
                    </div>
                    
                    <div class="apartment-features">
                        <span class="feature"><i class="fas fa-bed"></i> {{ $apartment->bedrooms ?? 0 }} BR</span>
                        <span class="feature"><i class="fas fa-bath"></i> {{ $apartment->bathrooms ?? 0 }} T&B</span>
                        <span class="feature"><i class="fas fa-ruler-combined"></i> {{ $apartment->floor_area_sqm ?? 0 }} sqm</span>
                    </div>

                    <div class="apartment-footer">
                        <div class="status-badge status-{{ strtolower($apartment->status ?? 'Vacant') == 'vacant' ? 'green' : 'orange' }}">
                            <span class="status-dot"></span>
                            <span>{{ $apartment->status ?? 'Vacant' }}</span>
                        </div>
                        
                        <a href="{{ route('admin.apartments.details', ['id' => $apartment->id, 'barangay' => $barangayName]) }}" class="detail-button">
                            <span>View Details</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(method_exists($apartments, 'links'))
            <div class="pagination-container">
                {{ $apartments->links() }}
            </div>
        @endif
        @endif
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.apartment-detail-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1rem;
}

.apartment-detail-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.apartment-detail-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 640px) {
    .apartment-detail-header {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.header-title-section {
    flex: 1;
}

.back-button-apartment {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 1.25rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    color: #cbd5e1;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.back-button-apartment:hover {
    border-color: #f5b81b;
    color: #f5b81b;
    transform: translateX(-2px);
}

.page-title-apartment-detail {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0;
}

.page-subtitle-apartment-detail {
    color: #94a3b8;
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
}

.apartments-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.75rem;
}

@media (min-width: 768px) {
    .apartments-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .apartments-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.apartment-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.12);
    overflow: hidden;
    transition: all 0.3s ease;
}

.apartment-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.5);
}

.apartment-image-grid {
    display: flex;
    gap: 0.5rem;
    height: 200px;
    padding: 1rem 1rem 0 1rem;
}

.main-image {
    flex: 2;
    border-radius: 18px;
    overflow: hidden;
    background: #0a0c10;
    position: relative;
}

.main-image .apartment-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.apartment-card:hover .main-image .apartment-img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.3) 100%);
    pointer-events: none;
}

.side-images {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.side-image {
    flex: 1;
    border-radius: 14px;
    overflow: hidden;
    background: #0a0c10;
}

.side-image .apartment-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.apartment-card:hover .side-image .apartment-img {
    transform: scale(1.05);
}

.apartment-info {
    padding: 1.25rem;
}

.apartment-title {
    font-size: 1.1rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ef4444, #f97316);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

.price-range {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.price-range i {
    color: #f5b81b;
    font-size: 0.875rem;
}

.price-range span {
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
}

.price-range small {
    font-size: 0.75rem;
    color: #64748b;
}

.apartment-features {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.1);
}

.feature {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    color: #94a3b8;
}

.feature i {
    color: #f5b81b;
    font-size: 0.7rem;
}

.apartment-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.3125rem 0.75rem;
    border-radius: 60px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status-green {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}

.status-orange {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
}

.status-dot {
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 50%;
    background: currentColor;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.detail-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.125rem;
    background: rgba(245, 184, 27, 0.12);
    border: 1px solid rgba(245, 184, 27, 0.3);
    border-radius: 60px;
    font-size: 0.6875rem;
    font-weight: 800;
    color: #f5b81b;
    text-decoration: none;
    transition: all 0.3s ease;
}

.detail-button:hover {
    background: rgba(245, 184, 27, 0.22);
    transform: translateX(4px);
    color: #f5b81b;
}

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 2.5rem;
}

.pagination-container .pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
}

.pagination-container .page-item .page-link {
    padding: 0.5rem 1rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 8px;
    color: #cbd5e1;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination-container .page-item .page-link:hover {
    border-color: #f5b81b;
    color: #f5b81b;
}

.pagination-container .page-item.active .page-link {
    background: #f5b81b;
    color: #0a0c10;
    border-color: #f5b81b;
}

.empty-state-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400px;
}

.empty-state-card {
    text-align: center;
    max-width: 400px;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.12);
}

.empty-state-icon {
    font-size: 4rem;
    color: #f5b81b;
    margin-bottom: 1.5rem;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.75rem;
}

.empty-state-message {
    color: #94a3b8;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.empty-state-hint {
    color: #64748b;
    font-size: 0.75rem;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #0f1115;
}

::-webkit-scrollbar-thumb {
    background: #f5b81b;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #e5a800;
}

/* Responsive Adjustments */
@media (max-width: 640px) {
    .apartment-image-grid {
        height: 180px;
    }
    
    .apartment-title {
        font-size: 0.95rem;
    }
    
    .price-range span {
        font-size: 1rem;
    }
    
    .feature {
        font-size: 0.65rem;
    }
}
</style>

{{-- Debug Script to help identify image loading issues --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Admin Barangay Apartments Page Loaded');
        const images = document.querySelectorAll('.apartment-img');
        let brokenImages = 0;
        let loadedImages = 0;
        
        console.log('Total images found:', images.length);
        
        images.forEach((img, index) => {
            img.addEventListener('load', function() {
                loadedImages++;
                console.log('✓ Image ' + (index + 1) + ' loaded:', this.src);
            });
            img.addEventListener('error', function() {
                brokenImages++;
                console.warn('✗ Image ' + (index + 1) + ' failed to load:', this.src);
            });
        });
        
        setTimeout(() => {
            if (brokenImages > 0) {
                console.warn(brokenImages + ' images failed to load out of ' + images.length);
                console.log('💡 Make sure you have run: php artisan storage:link');
                console.log('💡 Check if images exist in: storage/app/public/apartments/');
            } else if (loadedImages > 0) {
                console.log('✓✓✓ All ' + loadedImages + ' images loaded successfully! ✓✓✓');
            } else if (images.length === 0) {
                console.log('No images found on this page');
            }
        }, 2000);
    });
</script>
@endsection
@extends('owner.layouts.app')

@section('title', 'Business Details')
@section('page-title', '')

@section('content')
<div class="business-show-container">
    <div class="business-show-wrapper">
        
        {{-- Header Actions --}}
        <div class="business-show-header">
            <a href="{{ route('owner.business-spaces.index') }}" class="back-button-business-show">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Business Listings</span>
            </a>
            
            @if(isset($business->verification_status) && $business->verification_status == 'rejected')
                <a href="{{ route('owner.business-spaces.revise', $business->id) }}" class="revise-button-business-show">
                    <i class="fas fa-edit"></i>
                    <span>Revise & Resubmit</span>
                </a>
            @endif
        </div>

        {{-- Main Grid Container --}}
        <div class="business-show-grid">
            
            {{-- Left Sidebar Card --}}
            <div class="business-sidebar-card-show">
                <div class="glow-top-bar-business-show"></div>
                <div class="sidebar-content">
                    <div class="business-icon-large-show">
                        <i class="fas fa-store"></i>
                    </div>

                    <div class="status-badge-business-show">
                        <span class="status-dot-business-show {{ ($business->status ?? 'Available') == 'Available' ? 'active' : 'inactive' }}"></span>
                        {{ ucfirst($business->status ?? 'Available') }} Business
                    </div>

                    <h1 class="business-name-title-show">
                        {{ $business->business_name }}
                    </h1>
                    <p class="business-category-badge-show">
                        {{ $business->type ?? 'N/A' }}
                    </p>
                </div>
                
                <div class="contact-section-business-show">
                    <h4 class="contact-title-business-show">Verification Status</h4>
                    <div class="verification-status-show">
                        @php $verifStatus = $business->verification_status ?? 'pending'; @endphp
                        @if($verifStatus == 'approved')
                            <span class="verification-badge verified">
                                <i class="fas fa-check-circle"></i> APPROVED
                            </span>
                            @if($business->verified_at)
                                <small class="verified-date">Verified on {{ date('M d, Y', strtotime($business->verified_at)) }}</small>
                            @endif
                        @elseif($verifStatus == 'rejected')
                            <span class="verification-badge rejected">
                                <i class="fas fa-times-circle"></i> REJECTED
                            </span>
                            @if($business->rejection_reason)
                                <div class="rejection-reason-show">
                                    <i class="fas fa-info-circle"></i>
                                    <span>{{ $business->rejection_reason }}</span>
                                </div>
                            @endif
                        @else
                            <span class="verification-badge pending">
                                <i class="fas fa-clock"></i> PENDING
                            </span>
                            <small class="pending-hint">Awaiting admin verification</small>
                        @endif
                    </div>
                </div>

                <div class="contact-section-business-show">
                    <h4 class="contact-title-business-show">Permit Information</h4>
                    <div class="contact-list-show">
                        <div class="contact-item-business-show">
                            <i class="fas fa-id-card contact-icon-business-show"></i>
                            <div>
                                <p class="contact-label-business-show">Permit Number</p>
                                <p class="contact-text-business-show">{{ $business->permit_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="contact-item-business-show">
                            <i class="fas fa-calendar-alt contact-icon-business-show"></i>
                            <div>
                                <p class="contact-label-business-show">Date Submitted</p>
                                <p class="contact-text-business-show">{{ $business->created_at ? date('M d, Y', strtotime($business->created_at)) : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Content Card --}}
            <div class="business-profile-card-show">
                <div class="profile-header-business-show">
                    <h3 class="profile-title-business-show">Business Profile</h3>
                    <div class="profile-header-glow-business-show"></div>
                </div>

                <div class="profile-info-grid-business-show">
                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-building"></i> Business Name
                        </label>
                        <p class="field-value-business-show">{{ $business->business_name }}</p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-tag"></i> Business Type
                        </label>
                        <p class="field-value-business-show">{{ $business->type ?? 'N/A' }}</p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-hashtag"></i> Unit Number
                        </label>
                        <p class="field-value-business-show">{{ $business->unit_number ?? 'N/A' }}</p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-money-bill-wave"></i> Monthly Rent
                        </label>
                        <p class="field-value-business-show">₱{{ number_format($business->monthly_rent ?? 0, 2) }}</p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-arrows-alt"></i> Floor Area
                        </label>
                        <p class="field-value-business-show">{{ $business->floor_area_sqm ?? 'N/A' }} sqm</p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-map-marker-alt"></i> Status
                        </label>
                        <p class="field-value-business-show">
                            @if($business->status == 'Occupied')
                                <span class="badge-status occupied">OCCUPIED</span>
                            @elseif($business->status == 'Reserved')
                                <span class="badge-status reserved">RESERVED</span>
                            @elseif($business->status == 'Maintenance')
                                <span class="badge-status maintenance">MAINTENANCE</span>
                            @else
                                <span class="badge-status available">AVAILABLE</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="location-section-business-show">
                    <h4 class="section-title-business-show">
                        <i class="fas fa-location-dot"></i> Location
                    </h4>
                    <div class="location-info-show">
                        <p class="address-text-show">
                            <strong>Address:</strong> {{ $business->address ?? 'N/A' }}
                        </p>
                        <p class="barangay-text-show">
                            <strong>Barangay:</strong> {{ $business->barangay_name ?? 'N/A' }}
                        </p>
                        <p class="municipality-text-show">
                            <strong>Municipality:</strong> Binalonan, Pangasinan
                        </p>
                    </div>
                </div>

                <div class="description-section-business-show">
                    <h4 class="section-title-business-show">
                        <i class="fas fa-align-left"></i> Description
                    </h4>
                    <div class="description-box-business-show">
                        <i class="fas fa-quote-left quote-icon-business-show"></i>
                        <p class="description-text-business-show">
                            {{ $business->description ?? 'No description available for this business.' }}
                        </p>
                    </div>
                </div>

                @php
                    // FIXED: Get amenities and features safely
                    $amenities = $business->amenities ?? [];
                    if (is_string($amenities)) {
                        $amenities = json_decode($amenities, true) ?: [];
                    }
                    
                    $features = $business->business_features ?? [];
                    if (is_string($features)) {
                        $features = json_decode($features, true) ?: [];
                    }
                @endphp
                
                @if((count($amenities) > 0) || (count($features) > 0))
                <div class="amenities-section-business-show">
                    <h4 class="section-title-business-show">
                        <i class="fas fa-cogs"></i> Amenities & Features
                    </h4>
                    <div class="amenities-grid-show">
                        @if(count($amenities) > 0)
                            <div class="amenity-group">
                                <h5>Amenities:</h5>
                                <div class="amenity-tags">
                                    @foreach($amenities as $amenity)
                                        <span class="amenity-tag"><i class="fas fa-check-circle"></i> {{ $amenity }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(count($features) > 0)
                            <div class="feature-group">
                                <h5>Business Features:</h5>
                                <div class="feature-tags">
                                    @foreach($features as $feature)
                                        <span class="feature-tag"><i class="fas fa-star"></i> {{ $feature }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                @php
                    // FIXED: Get images safely
                    $images = $business->images ?? [];
                    if (is_string($images)) {
                        $images = json_decode($images, true) ?: [];
                    }
                @endphp
                
                @if(count($images) > 0)
                <div class="images-section-business-show">
                    <h4 class="section-title-business-show">
                        <i class="fas fa-images"></i> Business Photos
                    </h4>
                    <div class="images-gallery-show">
                        @foreach($images as $image)
                            <div class="gallery-image-item">
                                <img src="{{ Storage::url($image) }}" alt="Business image" onclick="openImageModal(this.src)">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal" style="display: none;">
    <span class="close-modal" onclick="closeImageModal()">&times;</span>
    <img class="modal-content-img" id="modalImage">
</div>

<style>
/* ========== BUSINESS SHOW PAGE STYLES ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.business-show-container {
    min-height: 100vh;
    background: #f5f7fa;
    font-family: 'Inter', sans-serif;
    padding: 1.5rem;
}

@media (min-width: 768px) {
    .business-show-container {
        padding: 2rem;
    }
}

@media (min-width: 1024px) {
    .business-show-container {
        padding: 2.5rem;
    }
}

.business-show-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.business-show-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.back-button-business-show {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 1.25rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #1f2937;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.back-button-business-show:hover {
    border-color: #f5b81b;
    color: #f5b81b;
    transform: translateX(-4px);
    text-decoration: none;
}

.revise-button-business-show {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 1.25rem;
    background: #f59e0b;
    border: none;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.revise-button-business-show:hover {
    background: #d97706;
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.business-show-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    align-items: start;
}

@media (min-width: 1024px) {
    .business-show-grid {
        grid-template-columns: 380px 1fr;
    }
}

/* Sidebar Card */
.business-sidebar-card-show {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.glow-top-bar-business-show {
    height: 4px;
    background: linear-gradient(90deg, #f5b81b, #f5b81b);
}

.sidebar-content {
    padding: 2rem 1.5rem;
    text-align: center;
}

.business-icon-large-show {
    width: 80px;
    height: 80px;
    background: #fef3c7;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}

.business-icon-large-show i {
    font-size: 2rem;
    color: #f5b81b;
}

.status-badge-business-show {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #059669;
    margin-bottom: 1rem;
}

.status-dot-business-show {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot-business-show.active {
    background: #10b981;
    animation: pulse 1.5s infinite;
}

.status-dot-business-show.inactive {
    background: #ef4444;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.business-name-title-show {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.business-category-badge-show {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f3f4f6;
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
}

.contact-section-business-show {
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    padding: 1.25rem;
}

.contact-title-business-show {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    margin: 0 0 1rem 0;
}

.contact-list-show {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contact-item-business-show {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.contact-icon-business-show {
    width: 28px;
    color: #f5b81b;
    font-size: 0.875rem;
}

.contact-label-business-show {
    font-size: 0.625rem;
    font-weight: 600;
    color: #9ca3af;
    margin: 0;
    text-transform: uppercase;
}

.contact-text-business-show {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

/* Verification Badges */
.verification-status-show {
    text-align: center;
}

.verification-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 700;
}

.verification-badge.verified {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}

.verification-badge.pending {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
}

.verification-badge.rejected {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.verified-date, .pending-hint {
    display: block;
    font-size: 0.6875rem;
    color: #9ca3af;
    margin-top: 0.5rem;
}

.rejection-reason-show {
    background: #fef2f2;
    border-radius: 12px;
    padding: 0.75rem;
    margin-top: 0.75rem;
    font-size: 0.75rem;
    color: #dc2626;
    text-align: left;
}

.rejection-reason-show i {
    margin-right: 0.5rem;
}

/* Right Card */
.business-profile-card-show {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.profile-header-business-show {
    padding: 1.25rem 1.75rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.profile-title-business-show {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.profile-info-grid-business-show {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    padding: 1.5rem 1.75rem;
    border-bottom: 1px solid #e5e7eb;
}

@media (min-width: 640px) {
    .profile-info-grid-business-show {
        grid-template-columns: repeat(2, 1fr);
    }
}

.info-field-business-show {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.field-label-business-show {
    font-size: 0.625rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #9ca3af;
}

.field-label-business-show i {
    margin-right: 0.25rem;
    font-size: 0.625rem;
}

.field-value-business-show {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

/* Status Badges */
.badge-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 60px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-status.available {
    background: #dbeafe;
    color: #1e40af;
}

.badge-status.occupied {
    background: #fee2e2;
    color: #991b1b;
}

.badge-status.reserved {
    background: #fef3c7;
    color: #92400e;
}

.badge-status.maintenance {
    background: #fed7aa;
    color: #9a3412;
}

/* Sections */
.location-section-business-show,
.description-section-business-show,
.amenities-section-business-show,
.images-section-business-show {
    padding: 1.5rem 1.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.section-title-business-show {
    font-size: 0.875rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title-business-show i {
    color: #f5b81b;
}

.location-info-show p {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    color: #4b5563;
}

.location-info-show strong {
    color: #1f2937;
}

.description-box-business-show {
    background: #f9fafb;
    border-radius: 16px;
    padding: 1.25rem;
    position: relative;
}

.quote-icon-business-show {
    position: absolute;
    top: 1rem;
    left: 1rem;
    font-size: 1rem;
    color: #d1d5db;
}

.description-text-business-show {
    font-size: 0.875rem;
    line-height: 1.6;
    color: #4b5563;
    margin: 0;
    padding-left: 1.5rem;
}

/* Amenities Grid */
.amenities-grid-show {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.amenity-group h5, .feature-group h5 {
    font-size: 0.75rem;
    font-weight: 700;
    color: #6b7280;
    margin: 0 0 0.75rem 0;
    text-transform: uppercase;
}

.amenity-tags, .feature-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.amenity-tag, .feature-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    background: #f3f4f6;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 500;
    color: #1f2937;
}

.amenity-tag i, .feature-tag i {
    font-size: 0.6875rem;
}

.amenity-tag i {
    color: #10b981;
}

.feature-tag i {
    color: #f5b81b;
}

/* Images Gallery */
.images-gallery-show {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
}

.gallery-image-item {
    aspect-ratio: 1/1;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.gallery-image-item:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.gallery-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Image Modal */
.image-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 35px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.modal-content-img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

/* Responsive */
@media (max-width: 768px) {
    .business-show-container {
        padding: 1rem;
    }
    
    .profile-info-grid-business-show,
    .location-section-business-show,
    .description-section-business-show,
    .amenities-section-business-show,
    .images-section-business-show {
        padding: 1rem 1.25rem;
    }
    
    .business-name-title-show {
        font-size: 1.25rem;
    }
    
    .sidebar-content {
        padding: 1.5rem 1rem;
    }
    
    .business-show-header {
        flex-direction: column;
    }
    
    .back-button-business-show,
    .revise-button-business-show {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
    function openImageModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.style.display = 'flex';
        modalImg.src = src;
    }
    
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('%c🏪 APARTrack Business Details | Ready', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
    
    // Close modal when clicking outside the image
    document.getElementById('imageModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
</script>
@endsection
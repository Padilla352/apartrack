@extends('layouts.admin')

@section('content')
<div class="business-show-container">
    <div class="business-show-wrapper">
        
        {{-- Header Actions --}}
        <div class="business-show-header">
            <a href="{{ route('admin.business.index') }}" class="back-button-business-show">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Businesses</span>
            </a>
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
                        <span class="status-dot-business-show {{ ($business->status ?? 'active') == 'active' ? 'active' : 'inactive' }}"></span>
                        {{ ucfirst($business->status ?? 'Active') }} Business
                    </div>

                    <h1 class="business-name-title-show">
                        {{ $business->business_name ?? $business->name ?? 'N/A' }}
                    </h1>
                    <p class="business-category-badge-show">
                        {{ $business->type ?? $business->category ?? 'N/A' }}
                    </p>
                </div>
                
                <div class="contact-section-business-show">
                    <h4 class="contact-title-business-show">Contact Information</h4>
                    <div class="contact-list-show">
                        <div class="contact-item-business-show">
                            <i class="fas fa-phone-alt contact-icon-business-show"></i>
                            <p class="contact-text-business-show">{{ $business->phone ?? $business->contact_number ?? 'N/A' }}</p>
                        </div>
                        <div class="contact-item-business-show">
                            <i class="fas fa-envelope contact-icon-business-show"></i>
                            <p class="contact-text-business-show">{{ $business->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Content Card --}}
            <div class="business-profile-card-show">
                <div class="profile-header-business-show">
                    <h3 class="profile-title-business-show">Establishment Profile</h3>
                    <div class="profile-header-glow-business-show"></div>
                </div>

                <div class="profile-info-grid-business-show">
                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-map-marker-alt"></i> Barangay Location
                        </label>
                        <p class="field-value-business-show">
                            {{ $business->barangay_name ?? $business->barangay ?? 'N/A' }}
                        </p>
                        <p class="field-sub-business-show">Binalonan, Pangasinan</p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-tag"></i> Business Type
                        </label>
                        <p class="field-value-business-show">
                            {{ $business->type ?? $business->category ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-id-card"></i> Business Permit
                        </label>
                        <p class="field-value-business-show permit-number-show">
                            {{ $business->permit_number ?? 'Not available' }}
                        </p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-user"></i> Owner Name
                        </label>
                        <p class="field-value-business-show">
                            {{ $business->owner_name ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-calendar-alt"></i> Date Added
                        </label>
                        <p class="field-value-business-show">
                            {{ $business->created_at ? date('M d, Y', strtotime($business->created_at)) : 'N/A' }}
                        </p>
                    </div>

                    <div class="info-field-business-show">
                        <label class="field-label-business-show">
                            <i class="fas fa-check-circle"></i> Verification Status
                        </label>
                        <p class="field-value-business-show">
                            @php $verifStatus = $business->verification_status ?? 'pending'; @endphp
                            @if($verifStatus == 'approved')
                                <span class="badge-verified">✓ Approved</span>
                            @elseif($verifStatus == 'rejected')
                                <span class="badge-rejected">✗ Rejected</span>
                            @else
                                <span class="badge-pending">⏳ Pending</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="description-section-business-show">
                    <label class="field-label-business-show">
                        <i class="fas fa-align-left"></i> Description
                    </label>
                    <div class="description-box-business-show">
                        <i class="fas fa-quote-left quote-icon-business-show"></i>
                        <p class="description-text-business-show">
                            {{ $business->description ?? 'No description available for this business.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========== GLOW DARK THEME - BUSINESS SHOW PAGE ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.business-show-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1.5rem;
}

html, body, #app {
    background-color: #0a0c10;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #0a0c10;
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
    max-width: 1152px;
    margin: 0 auto;
}

.business-show-header {
    margin-bottom: 2rem;
}

.back-button-business-show {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.5rem 1.25rem;
    background: rgba(15, 17, 21, 0.9);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    color: #cbd5e1;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
}

.back-button-business-show:hover {
    border-color: #f5b81b;
    color: #f5b81b;
    transform: translateX(-4px);
    box-shadow: 0 0 20px rgba(245, 184, 27, 0.15);
    text-decoration: none;
}

.business-show-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    align-items: stretch;
}

@media (min-width: 1024px) {
    .business-show-grid {
        grid-template-columns: 1fr 2fr;
    }
}

.business-sidebar-card-show {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.business-sidebar-card-show:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-2px);
    box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
}

.glow-top-bar-business-show {
    height: 3px;
    background: linear-gradient(90deg, #f5b81b, #00e5ff, #f5b81b);
    background-size: 200% 100%;
    animation: shimmer 3s infinite linear;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.sidebar-content {
    padding: 2rem 1.5rem;
    text-align: center;
    flex: 1;
}

.business-icon-large-show {
    width: 80px;
    height: 80px;
    background: rgba(245, 184, 27, 0.12);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    border: 1px solid rgba(245, 184, 27, 0.25);
    transition: all 0.3s ease;
}

.business-sidebar-card-show:hover .business-icon-large-show {
    transform: scale(1.05);
    border-color: #f5b81b;
    box-shadow: 0 0 20px rgba(245, 184, 27, 0.2);
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
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #10b981;
    margin-bottom: 1rem;
}

.status-dot-business-show {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot-business-show.active {
    background: #10b981;
    box-shadow: 0 0 8px #10b981;
    animation: pulse 1.5s infinite;
}

.status-dot-business-show.inactive {
    background: #ef4444;
    box-shadow: 0 0 8px #ef4444;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.business-name-title-show {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin-bottom: 0.5rem;
    letter-spacing: -0.3px;
}

.business-category-badge-show {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(245, 184, 27, 0.08);
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #94a3b8;
}

.contact-section-business-show {
    background: rgba(0, 0, 0, 0.3);
    border-top: 1px solid rgba(245, 184, 27, 0.1);
    padding: 1.5rem;
    margin-top: auto;
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
    padding: 0.375rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.contact-icon-business-show {
    width: 28px;
    color: #f5b81b;
    font-size: 0.875rem;
}

.contact-text-business-show {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #cbd5e1;
    margin: 0;
}

.business-profile-card-show {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.business-profile-card-show:hover {
    border-color: rgba(245, 184, 27, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.profile-header-business-show {
    padding: 1.5rem 1.75rem 1rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.1);
    position: relative;
}

.profile-title-business-show {
    font-size: 1.125rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.profile-header-glow-business-show {
    position: absolute;
    bottom: 0;
    left: 1.75rem;
    right: 1.75rem;
    height: 2px;
    background: linear-gradient(90deg, #f5b81b, transparent);
}

.profile-info-grid-business-show {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    padding: 1.5rem 1.75rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.08);
}

@media (min-width: 640px) {
    .profile-info-grid-business-show {
        grid-template-columns: repeat(2, 1fr);
    }
}

.info-field-business-show {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.field-label-business-show {
    font-size: 0.625rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.8;
}

.field-label-business-show i {
    margin-right: 0.25rem;
    font-size: 0.625rem;
}

.field-value-business-show {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.permit-number-show {
    color: #f5b81b;
    font-family: monospace;
    letter-spacing: 0.5px;
}

.field-sub-business-show {
    font-size: 0.6875rem;
    color: #64748b;
    margin-top: 0.125rem;
}

.description-section-business-show {
    padding: 0 1.75rem 1.5rem;
    flex-grow: 1;
}

.description-box-business-show {
    background: rgba(0, 0, 0, 0.35);
    border-radius: 20px;
    padding: 1.25rem;
    border: 1px solid rgba(245, 184, 27, 0.1);
    position: relative;
    margin-top: 0.5rem;
}

.quote-icon-business-show {
    position: absolute;
    top: 1rem;
    left: 1rem;
    font-size: 1rem;
    color: rgba(245, 184, 27, 0.2);
}

.description-text-business-show {
    font-size: 0.875rem;
    line-height: 1.6;
    color: #cbd5e6;
    margin: 0;
    padding-left: 1.5rem;
    font-style: normal;
}

/* Badge Styles */
.badge-verified {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.badge-rejected {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.badge-pending {
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

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

::selection {
    background: #f5b81b;
    color: #0a0c10;
}

@media (max-width: 768px) {
    .business-show-container {
        padding: 1rem;
    }
    
    .back-button-business-show span {
        display: none;
    }
    
    .back-button-business-show {
        padding: 0.5rem 1rem;
    }
    
    .profile-header-business-show,
    .profile-info-grid-business-show,
    .description-section-business-show {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
    }
    
    .description-text-business-show {
        font-size: 0.8125rem;
        padding-left: 1rem;
    }
    
    .business-name-title-show {
        font-size: 1.25rem;
    }
    
    .sidebar-content {
        padding: 1.5rem 1rem;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('%c🏪 APARTrack Business Details | Ready for Real Data', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
</script>
@endsection
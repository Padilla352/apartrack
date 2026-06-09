@extends('layouts.admin')

@section('content')
<div class="business-listings-container">
    <div class="business-content-wrapper">
        
        {{-- Header and Search Section --}}
        <div class="business-header">
            <div class="business-title-section">
                <h1 class="business-page-title">Business Listings</h1>
                <p class="business-page-subtitle">Manage and monitor all registered businesses</p>
            </div>

            <div class="search-wrapper">
                <input type="text" id="searchInput" placeholder="Search business..." class="search-input">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="business-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Businesses</span>
                    <span class="stat-number">{{ $totalBusinesses ?? 0 }}</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Active</span>
                    <span class="stat-number">{{ $activeBusinesses ?? 0 }}</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Categories</span>
                    <span class="stat-number">{{ $categoryCount ?? 0 }}</span>
                </div>
            </div>
        </div>

        {{-- Business List --}}
        <div id="businessList" class="business-list">
            @forelse($businesses ?? [] as $biz)
            <div class="business-card" data-name="{{ strtolower($biz->business_name ?? $biz->name ?? '') }}">
                <div class="business-icon">
                    <i class="fas fa-store"></i>
                </div>

                <div class="business-info">
                    <h3 class="business-name">{{ $biz->business_name ?? $biz->name ?? 'N/A' }}</h3>
                    <div class="business-meta">
                        <span class="meta-tag">
                            <i class="fas fa-tag"></i> {{ $biz->type ?? $biz->category ?? 'N/A' }}
                        </span>
                        <span class="meta-tag">
                            <i class="fas fa-map-marker-alt"></i> {{ $biz->barangay_name ?? $biz->barangay ?? 'N/A' }}
                        </span>
                        <span class="meta-tag">
                            <i class="fas fa-user"></i> Owner ID: {{ $biz->owner_id ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="business-status">
                    <div class="status-dot {{ ($biz->status ?? 'active') == 'active' ? 'active' : 'inactive' }}"></div>
                    <span class="status-text">{{ ucfirst($biz->status ?? 'Active') }}</span>
                </div>

                <div class="business-action">
                    <a href="{{ route('admin.business.show', $biz->id) }}" class="details-button">
                        Details
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="empty-state-business">
                <i class="fas fa-store-slash"></i>
                <p>No businesses registered yet</p>
                <span>Add businesses to see them here</span>
            </div>
            @endforelse

            <div id="noResults" class="no-results hidden">
                <i class="fas fa-search"></i>
                <p>No business found</p>
                <span>Try a different search term</span>
            </div>
        </div>
    </div>
</div>

<style>
/* ========== GLOW DARK THEME - BUSINESS LISTINGS ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

.business-listings-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1rem;
}

html, body, #app {
    background-color: #0a0c10;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #0a0c10;
}

@media (min-width: 768px) {
    .business-listings-container {
        padding: 2rem;
    }
}

.business-content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.business-header {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .business-header {
        flex-direction: row;
        align-items: flex-end;
    }
}

.business-title-section {
    flex-shrink: 0;
}

.business-page-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: -0.3px;
    margin: 0 0 0.25rem 0;
}

.business-page-subtitle {
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 500;
    margin: 0;
}

.search-wrapper {
    position: relative;
    width: 100%;
}

@media (min-width: 768px) {
    .search-wrapper {
        width: 300px;
    }
}

.search-input {
    width: 100%;
    padding: 0.875rem 1.25rem 0.875rem 3rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    font-size: 0.875rem;
    color: #e2e8f0;
    outline: none;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.search-input:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 0 3px rgba(245, 184, 27, 0.1);
}

.search-input::placeholder {
    color: #64748b;
}

.search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 0.875rem;
    pointer-events: none;
}

.search-input:focus + .search-icon,
.search-wrapper:hover .search-icon {
    color: #f5b81b;
}

.business-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(245, 184, 27, 0.12);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #f5b81b;
}

.stat-card:hover .stat-icon {
    background: rgba(245, 184, 27, 0.22);
    box-shadow: 0 0 15px rgba(245, 184, 27, 0.2);
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    display: block;
    margin-bottom: 0.25rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: #f5b81b;
    display: block;
    line-height: 1;
}

.business-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.business-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border: 1px solid rgba(245, 184, 27, 0.12);
    border-radius: 24px;
    padding: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: fadeInUp 0.4s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.business-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-4px);
    box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
}

.business-icon {
    width: 56px;
    height: 56px;
    background: rgba(245, 184, 27, 0.1);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.business-icon i {
    font-size: 1.5rem;
    color: #f5b81b;
    transition: all 0.3s ease;
}

.business-card:hover .business-icon {
    background: rgba(245, 184, 27, 0.2);
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(245, 184, 27, 0.15);
}

.business-card:hover .business-icon i {
    color: #ffcc44;
}

.business-info {
    flex: 1;
    min-width: 0;
}

.business-name {
    font-size: 1rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
}

.business-card:hover .business-name {
    color: #f5b81b;
}

.business-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem 1.25rem;
}

.meta-tag {
    font-size: 0.6875rem;
    font-weight: 600;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.meta-tag i {
    color: #f5b81b;
    font-size: 0.75rem;
}

.business-card:hover .meta-tag {
    color: #cbd5e1;
}

.business-status {
    display: none;
    align-items: center;
    gap: 0.5rem;
    padding: 0 1rem;
    flex-shrink: 0;
}

@media (min-width: 768px) {
    .business-status {
        display: flex;
    }
}

.status-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
}

.status-dot.active {
    background-color: #10b981;
    box-shadow: 0 0 6px #10b981;
    animation: pulse 2s ease-in-out infinite;
}

.status-dot.inactive {
    background-color: #ef4444;
    box-shadow: 0 0 6px #ef4444;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}

.status-text {
    font-size: 0.625rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.business-action {
    flex-shrink: 0;
}

.details-button {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(245, 184, 27, 0.1);
    border: 1px solid rgba(245, 184, 27, 0.25);
    color: #f5b81b;
    padding: 0.75rem 1.5rem;
    border-radius: 60px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    text-decoration: none;
    transition: all 0.3s ease;
}

.details-button i {
    font-size: 0.625rem;
    transition: transform 0.2s ease;
}

.details-button:hover {
    background: rgba(245, 184, 27, 0.2);
    border-color: #f5b81b;
    color: #ffcc44;
    transform: translateX(4px);
    text-decoration: none;
    box-shadow: 0 0 15px rgba(245, 184, 27, 0.2);
}

.details-button:hover i {
    transform: translateX(4px);
}

.empty-state-business {
    text-align: center;
    padding: 3rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 28px;
    border: 1px solid rgba(245, 184, 27, 0.1);
}

.empty-state-business i {
    font-size: 3rem;
    color: #64748b;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state-business p {
    font-weight: 700;
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.empty-state-business span {
    font-size: 0.75rem;
    color: #64748b;
}

.no-results {
    text-align: center;
    padding: 3rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 28px;
    border: 1px solid rgba(245, 184, 27, 0.1);
}

.no-results i {
    font-size: 3rem;
    color: #64748b;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-results p {
    font-weight: 700;
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.no-results span {
    font-size: 0.75rem;
    color: #64748b;
}

.hidden {
    display: none;
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

@media (max-width: 640px) {
    .business-stats {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .business-card {
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem;
    }
    
    .business-action {
        width: 100%;
    }
    
    .details-button {
        width: 100%;
        justify-content: center;
    }
    
    .business-name {
        font-size: 0.875rem;
    }
    
    .business-page-title {
        font-size: 1.5rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.business-card');
        const noResults = document.getElementById('noResults');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let hasVisibleCards = false;

                cards.forEach(card => {
                    const businessName = card.getAttribute('data-name') || '';
                    
                    if (businessName.includes(searchTerm) || searchTerm === '') {
                        card.style.display = "flex";
                        hasVisibleCards = true;
                    } else {
                        card.style.display = "none";
                    }
                });

                if (hasVisibleCards) {
                    noResults.classList.add('hidden');
                } else {
                    noResults.classList.remove('hidden');
                }
            });
        }

        console.log('%c🏢 APARTrack Business Listings | Ready for Real Data', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
</script>
@endsection
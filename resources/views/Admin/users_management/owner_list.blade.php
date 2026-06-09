@extends('layouts.admin')

@section('content')
<div class="owners-list-page">
    <div class="owners-list-wrapper">
        
        {{-- Header Section --}}
        <div class="owners-header-section">
            <div class="owners-header-left">
                <a href="{{ route('users-management.index') }}" class="back-button-owners">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
                <div>
                    <h1 class="page-title-owners">Property Owners</h1>
                    <p class="page-subtitle-owners">Manage and monitor registered property owners</p>
                </div>
            </div>

            <div class="owners-header-right">
                <div class="stats-badge-owners">
                    <span class="stats-dot"></span>
                    <span id="total-owners-count">{{ $owners->count() }} Total Owners</span>
                </div>

                <div class="search-wrapper-owners">
                    <i class="fas fa-search search-icon-owners"></i>
                    <input type="text" 
                           id="ownerSearchInput"
                           placeholder="Search by name, contact, or address..." 
                           class="search-input-owners">
                </div>
            </div>
        </div>

        {{-- Table Header (Desktop) --}}
        <div class="table-header-owners">
            <div class="header-cell-owners">Owner Name</div>
            <div class="header-cell-owners text-center">Contact Number</div>
            <div class="header-cell-owners text-center">Email Address</div>
            <div class="header-cell-owners text-center">Listings</div>
            <div class="header-cell-owners text-center">Account Status</div>
        </div>

        {{-- Owners List --}}
        <div id="owner-list-container" class="owners-list-container">
            @forelse($owners as $owner)
            <div class="owner-card" data-name="{{ strtolower($owner->name ?? '') }}" 
                 data-contact="{{ $owner->contact_number ?? '' }}" 
                 data-email="{{ strtolower($owner->email ?? '') }}"
                 data-address="{{ strtolower($owner->address ?? '') }}"
                 data-status="{{ $owner->status ?? 'pending' }}"
                 data-listings="{{ $owner->listings_count ?? 0 }}">
                <div class="owner-card-inner">
                    {{-- Owner Info --}}
                    <div class="owner-info-cell">
                        <div class="owner-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="owner-name">{{ $owner->name ?? 'N/A' }}</div>
                            <div class="owner-address-mobile">{{ $owner->address ?? 'Address not set' }}</div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="owner-contact-cell">
                        <span class="mobile-label-owners">Contact</span>
                        <div class="contact-number">
                            <i class="fas fa-phone-alt"></i>
                            {{ $owner->contact_number ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="owner-email-cell">
                        <span class="mobile-label-owners">Email</span>
                        <div class="email-address">
                            <i class="fas fa-envelope"></i>
                            {{ $owner->email ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Listings --}}
                    <div class="owner-listings-cell">
                        <span class="mobile-label-owners">Listings</span>
                        <span class="listings-badge">{{ $owner->listings_count ?? 0 }} Units</span>
                    </div>

                    {{-- Status --}}
                    <div class="owner-status-cell">
                        <span class="mobile-label-owners">Status</span>
                        @if(($owner->status ?? 'pending') == 'active')
                            <span class="status-badge-owners status-active">
                                <span class="status-dot active-dot"></span>
                                Active
                            </span>
                        @elseif(($owner->status ?? 'pending') == 'verified')
                            <span class="status-badge-owners status-active">
                                <span class="status-dot active-dot"></span>
                                Verified
                            </span>
                        @elseif(($owner->status ?? 'pending') == 'pending')
                            <span class="status-badge-owners status-pending">
                                <span class="status-dot pending-dot"></span>
                                Pending
                            </span>
                        @else
                            <span class="status-badge-owners status-banned">
                                <span class="status-dot banned-dot"></span>
                                {{ ucfirst($owner->status ?? 'Inactive') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state-owners">
                <i class="fas fa-user-slash"></i>
                <p>No owners found</p>
                <span>Add property owners to see them here</span>
            </div>
            @endforelse

            {{-- No Results Message --}}
            <div id="noResultsOwners" class="empty-state-owners hidden">
                <i class="fas fa-user-slash"></i>
                <p>No owners found</p>
                <span>Try a different search term</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('ownerSearchInput');
        const cards = document.querySelectorAll('.owner-card');
        const noResults = document.getElementById('noResultsOwners');
        const totalCountSpan = document.getElementById('total-owners-count');

        function updateStats() {
            let visibleCount = 0;
            
            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    visibleCount++;
                }
            });
            
            if (totalCountSpan) totalCountSpan.innerText = `${visibleCount} Owner${visibleCount !== 1 ? 's' : ''}`;
        }

        function filterOwners() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let hasVisibleCards = false;
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const contact = card.getAttribute('data-contact') || '';
                const email = card.getAttribute('data-email') || '';
                const address = card.getAttribute('data-address') || '';
                
                const matches = name.includes(searchTerm) || 
                               contact.includes(searchTerm) || 
                               email.includes(searchTerm) ||
                               address.includes(searchTerm) ||
                               searchTerm === '';
                
                if (matches) {
                    card.style.display = 'block';
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (hasVisibleCards || cards.length === 0) {
                noResults.classList.add('hidden');
            } else {
                noResults.classList.remove('hidden');
            }
            
            updateStats();
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', filterOwners);
        }
        
        updateStats();
        
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.03}s`;
            card.classList.add('animate-in');
        });
        
        console.log('%c👥 APARTrack Owners List | Ready for Real Data', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
</script>

<style>
/* Same CSS as before - keeping all styles */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.owners-list-page {
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
    .owners-list-page {
        padding: 2rem;
    }
}

.owners-list-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.owners-header-section {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .owners-header-section {
        flex-direction: row;
        align-items: center;
    }
}

.owners-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.owners-header-right {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    width: 100%;
}

@media (min-width: 768px) {
    .owners-header-right {
        flex-direction: row;
        align-items: center;
        width: auto;
    }
}

.back-button-owners {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.125rem;
    background: rgba(15, 17, 21, 0.9);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    color: #cbd5e1;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.back-button-owners:hover {
    border-color: #f5b81b;
    color: #f5b81b;
    transform: translateX(-4px);
    text-decoration: none;
}

.page-title-owners {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0 0 0.25rem 0;
}

@media (min-width: 768px) {
    .page-title-owners {
        font-size: 1.75rem;
    }
}

.page-subtitle-owners {
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 500;
    margin: 0;
}

.stats-badge-owners {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 1rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #cbd5e1;
}

.stats-dot {
    width: 0.5rem;
    height: 0.5rem;
    background: #f5b81b;
    border-radius: 50%;
    box-shadow: 0 0 6px #f5b81b;
    animation: pulseMain 1.5s infinite;
}

@keyframes pulseMain {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.search-wrapper-owners {
    position: relative;
    width: 100%;
}

@media (min-width: 768px) {
    .search-wrapper-owners {
        width: 320px;
    }
}

.search-input-owners {
    width: 100%;
    padding: 0.75rem 1.25rem 0.75rem 3rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    font-size: 0.875rem;
    color: #e2e8f0;
    outline: none;
    transition: all 0.3s ease;
}

.search-input-owners:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 0 3px rgba(245, 184, 27, 0.1);
}

.search-input-owners::placeholder {
    color: #64748b;
}

.search-icon-owners {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 0.875rem;
    pointer-events: none;
}

.table-header-owners {
    display: none;
    grid-template-columns: 1.8fr 1.2fr 1.5fr 0.8fr 0.8fr;
    padding: 0.75rem 1.25rem;
    background: rgba(15, 17, 21, 0.8);
    border-radius: 1rem;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(245, 184, 27, 0.1);
}

@media (min-width: 1024px) {
    .table-header-owners {
        display: grid;
    }
}

.header-cell-owners {
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.7;
}

.text-center {
    text-align: center;
}

.owners-list-container {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 0.25rem;
}

.owners-list-container::-webkit-scrollbar {
    width: 5px;
}

.owners-list-container::-webkit-scrollbar-track {
    background: rgba(15, 17, 21, 0.5);
    border-radius: 10px;
}

.owners-list-container::-webkit-scrollbar-thumb {
    background: #f5b81b;
    border-radius: 10px;
}

.owner-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.12);
    transition: all 0.3s ease;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
}

.owner-card.animate-in {
    animation: fadeInUp 0.4s ease-out forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.owner-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}

.owner-card-inner {
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

@media (min-width: 1024px) {
    .owner-card-inner {
        display: grid;
        grid-template-columns: 1.8fr 1.1fr 1.6fr 0.7fr 0.8fr;
        padding: 1rem 1.25rem;
        align-items: center;
        gap: 0;
    }
}

.mobile-label-owners {
    display: inline-block;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.6;
    margin-bottom: 0.375rem;
    width: 80px;
}

@media (min-width: 1024px) {
    .mobile-label-owners {
        display: none;
    }
}

.owner-info-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.owner-avatar {
    width: 44px;
    height: 44px;
    background: rgba(245, 184, 27, 0.12);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    color: #f5b81b;
}

.owner-name {
    font-weight: 700;
    color: #ffffff;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
}

.owner-address-mobile {
    font-size: 0.6875rem;
    color: #94a3b8;
    display: block;
}

@media (min-width: 1024px) {
    .owner-address-mobile {
        display: none;
    }
}

.owner-contact-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .owner-contact-cell {
        align-items: center;
    }
}

.contact-number {
    font-size: 0.875rem;
    font-weight: 600;
    color: #cbd5e1;
    font-family: monospace;
}

.contact-number i {
    color: #f5b81b;
    font-size: 0.75rem;
    margin-right: 0.375rem;
}

.owner-email-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .owner-email-cell {
        align-items: center;
    }
}

.email-address {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #00e5ff;
    word-break: break-all;
    background: rgba(0, 229, 255, 0.08);
    padding: 0.25rem 0.625rem;
    border-radius: 40px;
    display: inline-block;
}

.email-address i {
    color: #f5b81b;
    font-size: 0.6875rem;
    margin-right: 0.375rem;
}

.owner-listings-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .owner-listings-cell {
        align-items: center;
    }
}

.listings-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(0, 229, 255, 0.1);
    border: 1px solid rgba(0, 229, 255, 0.25);
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #00e5ff;
    width: fit-content;
}

.owner-status-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .owner-status-cell {
        align-items: center;
    }
}

.status-badge-owners {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.3125rem 0.75rem;
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: fit-content;
}

.status-active {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}

.status-pending {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
}

.status-banned {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.status-dot {
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 50%;
}

.active-dot {
    background: #10b981;
    box-shadow: 0 0 6px #10b981;
}

.pending-dot {
    background: #f59e0b;
    box-shadow: 0 0 6px #f59e0b;
}

.banned-dot {
    background: #ef4444;
}

.empty-state-owners {
    text-align: center;
    padding: 3rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 28px;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-top: 1rem;
}

.empty-state-owners i {
    font-size: 3rem;
    color: #64748b;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state-owners p {
    font-weight: 700;
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.empty-state-owners span {
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

@media (max-width: 768px) {
    .back-button-owners span {
        display: none;
    }
    
    .back-button-owners {
        padding: 0.5rem 0.875rem;
    }
    
    .page-title-owners {
        font-size: 1.25rem;
    }
    
    .email-address {
        font-size: 0.75rem;
        word-break: break-all;
    }
}
</style>
@endsection
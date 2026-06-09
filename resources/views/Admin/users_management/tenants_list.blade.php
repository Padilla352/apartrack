@extends('layouts.admin')

@section('content')
<div class="tenants-list-page">
    <div class="tenants-list-container-wrapper">
        
        {{-- Header Section --}}
        <div class="tenants-header-section">
            <div class="tenants-header-left">
                <a href="{{ route('users-management.index') }}" class="back-button-tenants">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
                <div>
                    <h2 class="page-title-tenants">Tenants List</h2>
                    <p class="page-subtitle-tenants">Manage and monitor all registered tenants</p>
                </div>
            </div>
            
            <div class="tenants-header-right">
                <div class="stats-badge-tenants">
                    <span class="stats-dot-tenants"></span>
                    <span id="total-tenants-count">{{ $tenants->count() }} Total Tenants</span>
                </div>

                <div class="search-wrapper-tenants">
                    <i class="fas fa-search search-icon-tenants"></i>
                    <input type="text" id="tenantSearchInput" placeholder="Search by name, contact, or email..." class="search-input-tenants">
                </div>
            </div>
        </div>

        {{-- Table Header (Desktop) --}}
        <div class="table-header-tenants">
            <div class="header-cell-tenants">Name</div>
            <div class="header-cell-tenants text-center">Contact #</div>
            <div class="header-cell-tenants">Email</div>
            <div class="header-cell-tenants text-center">Status</div>
            <div class="header-cell-tenants text-center">Actions</div>
        </div>

        {{-- Tenants List --}}
        <div id="tenant-list-container" class="tenants-list-scroll-container">
            @forelse($tenants as $index => $tenant)
            <div class="tenant-card" data-name="{{ strtolower($tenant->name) }}" 
                 data-contact="{{ $tenant->contact ?? '' }}" 
                 data-email="{{ strtolower($tenant->email) }}"
                 data-status="{{ $tenant->status ?? 'pending' }}">
                <div class="tenant-card-inner">
                    {{-- Name --}}
                    <div class="tenant-name-cell">
                        <div class="tenant-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="tenant-name">{{ $tenant->name }}</div>
                            <div class="tenant-id">ID: TEN-{{ str_pad($tenant->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="tenant-contact-cell">
                        <span class="mobile-label-tenants">Contact</span>
                        <div class="contact-number-tenants">
                            <i class="fas fa-phone-alt"></i>
                            {{ $tenant->contact ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="tenant-email-cell">
                        <span class="mobile-label-tenants">Email</span>
                        <div class="email-address-enhanced">
                            <i class="fas fa-envelope"></i>
                            <span class="email-text">{{ $tenant->email }}</span>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="tenant-status-cell">
                        <span class="mobile-label-tenants">Status</span>
                        @if(($tenant->status ?? 'pending') == 'active')
                            <span class="status-badge-tenants status-active-tenants">
                                <span class="status-dot-tenants active-dot"></span>
                                Active
                            </span>
                        @elseif(($tenant->status ?? 'pending') == 'pending')
                            <span class="status-badge-tenants status-pending-tenants">
                                <span class="status-dot-tenants pending-dot"></span>
                                Pending
                            </span>
                        @else
                            <span class="status-badge-tenants status-inactive-tenants">
                                <span class="status-dot-tenants inactive-dot"></span>
                                Inactive
                            </span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="tenant-actions-cell">
                        <span class="mobile-label-tenants">Actions</span>
                        <div class="action-buttons-tenants">
                            <!-- FIXED: Changed from 'tenants.view' to 'users-management.tenants.view' -->
                            <a href="{{ route('users-management.tenants.view', ['id' => $tenant->id]) }}" class="action-btn view-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                                <span>View</span>
                            </a>
                            <!-- FIXED: Changed from 'tenants.edit' to 'users-management.tenants.edit' -->
                            <a href="{{ route('users-management.tenants.edit', ['id' => $tenant->id]) }}" class="action-btn edit-btn" title="Edit Tenant">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state-tenants">
                <i class="fas fa-user-slash"></i>
                <p>No tenants found</p>
                <span>Add tenants to see them here</span>
            </div>
            @endforelse

            {{-- No Results Message for Search --}}
            <div id="noResultsTenants" class="empty-state-tenants hidden">
                <i class="fas fa-user-slash"></i>
                <p>No tenants found</p>
                <span>Try a different search term</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('tenantSearchInput');
        const cards = document.querySelectorAll('.tenant-card');
        const noResults = document.getElementById('noResultsTenants');
        const totalCountSpan = document.getElementById('total-tenants-count');
        const allCardsContainer = document.getElementById('tenant-list-container');
        const emptyStateOriginal = document.querySelector('.empty-state-tenants:not(.hidden)');

        function updateStats() {
            let visibleCount = 0;
            
            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    visibleCount++;
                }
            });
            
            if (totalCountSpan) totalCountSpan.innerText = `${visibleCount} Tenant${visibleCount !== 1 ? 's' : ''}`;
        }

        function filterTenants() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let hasVisibleCards = false;
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const contact = card.getAttribute('data-contact') || '';
                const email = card.getAttribute('data-email') || '';
                
                const matches = name.includes(searchTerm) || 
                               contact.includes(searchTerm) || 
                               email.includes(searchTerm) ||
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
            searchInput.addEventListener('input', filterTenants);
        }
        
        updateStats();
        
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.03}s`;
            card.classList.add('animate-in');
        });
        
        console.log('%c👥 APARTrack Tenants List | Ready for Real Data', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
</script>

<style>
/* Same CSS as before - keeping all styles */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.tenants-list-page {
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
    .tenants-list-page {
        padding: 2rem;
    }
}

.tenants-list-container-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.tenants-header-section {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .tenants-header-section {
        flex-direction: row;
        align-items: center;
    }
}

.tenants-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tenants-header-right {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    width: 100%;
}

@media (min-width: 768px) {
    .tenants-header-right {
        flex-direction: row;
        align-items: center;
        width: auto;
    }
}

.back-button-tenants {
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
    font-family: 'Inter', sans-serif;
}

.back-button-tenants:hover {
    border-color: #f5b81b;
    color: #f5b81b;
    transform: translateX(-4px);
    text-decoration: none;
}

.page-title-tenants {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0 0 0.25rem 0;
}

@media (min-width: 768px) {
    .page-title-tenants {
        font-size: 1.75rem;
    }
}

.page-subtitle-tenants {
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 500;
    margin: 0;
}

.stats-badge-tenants {
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

.stats-dot-tenants {
    width: 0.5rem;
    height: 0.5rem;
    background: #f5b81b;
    border-radius: 50%;
    box-shadow: 0 0 6px #f5b81b;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.search-wrapper-tenants {
    position: relative;
    width: 100%;
}

@media (min-width: 768px) {
    .search-wrapper-tenants {
        width: 320px;
    }
}

.search-input-tenants {
    width: 100%;
    padding: 0.75rem 1.25rem 0.75rem 3rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    font-size: 0.875rem;
    color: #e2e8f0;
    outline: none;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.search-input-tenants:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 0 3px rgba(245, 184, 27, 0.1);
}

.search-input-tenants::placeholder {
    color: #64748b;
}

.search-icon-tenants {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 0.875rem;
    pointer-events: none;
}

.table-header-tenants {
    display: none;
    grid-template-columns: 2fr 1.2fr 1.8fr 0.8fr 1fr;
    padding: 0.75rem 1.25rem;
    background: rgba(15, 17, 21, 0.8);
    border-radius: 1rem;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(245, 184, 27, 0.1);
}

@media (min-width: 1024px) {
    .table-header-tenants {
        display: grid;
    }
}

.header-cell-tenants {
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

.tenants-list-scroll-container {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 0.25rem;
}

.tenants-list-scroll-container::-webkit-scrollbar {
    width: 5px;
}

.tenants-list-scroll-container::-webkit-scrollbar-track {
    background: rgba(15, 17, 21, 0.5);
    border-radius: 10px;
}

.tenants-list-scroll-container::-webkit-scrollbar-thumb {
    background: #f5b81b;
    border-radius: 10px;
}

.tenant-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.12);
    transition: all 0.3s ease;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
}

.tenant-card.animate-in {
    animation: fadeInUp 0.4s ease-out forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tenant-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}

.tenant-card-inner {
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

@media (min-width: 1024px) {
    .tenant-card-inner {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.8fr 0.8fr 1fr;
        padding: 1rem 1.25rem;
        align-items: center;
        gap: 0;
    }
}

.mobile-label-tenants {
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
    .mobile-label-tenants {
        display: none;
    }
}

.tenant-name-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.tenant-avatar {
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

.tenant-name {
    font-weight: 700;
    color: #ffffff;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
}

.tenant-id {
    font-size: 0.625rem;
    color: #94a3b8;
    font-family: monospace;
}

.tenant-contact-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .tenant-contact-cell {
        align-items: center;
    }
}

.contact-number-tenants {
    font-size: 0.875rem;
    font-weight: 600;
    color: #cbd5e1;
    font-family: monospace;
}

.contact-number-tenants i {
    color: #f5b81b;
    font-size: 0.75rem;
    margin-right: 0.375rem;
}

.tenant-email-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .tenant-email-cell {
        align-items: center;
    }
}

.email-address-enhanced {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(0, 229, 255, 0.1);
    border: 1px solid rgba(0, 229, 255, 0.25);
    border-radius: 40px;
    padding: 0.25rem 0.875rem;
}

.email-address-enhanced i {
    color: #00e5ff;
    font-size: 0.75rem;
}

.email-text {
    font-size: 0.8125rem;
    font-weight: 500;
    color: #cbd5e1;
    word-break: break-all;
}

.tenant-status-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .tenant-status-cell {
        align-items: center;
    }
}

.status-badge-tenants {
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

.status-active-tenants {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
}

.status-pending-tenants {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
}

.status-inactive-tenants {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.status-dot-tenants {
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

.inactive-dot {
    background: #ef4444;
}

.tenant-actions-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

@media (min-width: 1024px) {
    .tenant-actions-cell {
        align-items: center;
    }
}

.action-buttons-tenants {
    display: flex;
    gap: 0.75rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 40px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.view-btn {
    background: rgba(0, 229, 255, 0.08);
    border: 1px solid rgba(0, 229, 255, 0.25);
    color: #00e5ff;
}

.view-btn:hover {
    background: rgba(0, 229, 255, 0.18);
    border-color: #00e5ff;
    text-decoration: none;
    color: #88f0ff;
}

.edit-btn {
    background: rgba(245, 184, 27, 0.08);
    border: 1px solid rgba(245, 184, 27, 0.25);
    color: #f5b81b;
}

.edit-btn:hover {
    background: rgba(245, 184, 27, 0.18);
    border-color: #f5b81b;
    text-decoration: none;
    color: #ffcc44;
}

.empty-state-tenants {
    text-align: center;
    padding: 3rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 28px;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-top: 1rem;
}

.empty-state-tenants i {
    font-size: 3rem;
    color: #64748b;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state-tenants p {
    font-weight: 700;
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.empty-state-tenants span {
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
    .back-button-tenants span {
        display: none;
    }
    
    .back-button-tenants {
        padding: 0.5rem 0.875rem;
    }
    
    .page-title-tenants {
        font-size: 1.25rem;
    }
    
    .action-buttons-tenants {
        width: 100%;
        justify-content: flex-start;
    }
    
    .email-address-enhanced {
        padding: 0.1875rem 0.625rem;
    }
    
    .email-text {
        font-size: 0.75rem;
    }
}
</style>
@endsection
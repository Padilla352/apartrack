@extends('layouts.admin')

@section('content')
<div class="users-management-container">
    <div class="content-wrapper">
        
        {{-- Header Section --}}
        <div class="header-section">
            <h2 class="page-title-users">User Management</h2>
            <p class="page-subtitle-users">List of tenants and property owners</p>
        </div>

        {{-- Users Cards Grid --}}
        <div class="users-cards-grid">
            
            <!-- Tenants Card -->
            <div class="user-type-card tenants-card">
                <div class="card-glow-bar"></div>
                <div class="card-icon-wrapper">
                    <div class="card-icon tenants-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                
                <div class="card-content">
                    <h3 class="card-title">Tenants</h3>
                    <p class="card-description">Manage all registered tenants, view their profiles, and track their rental history.</p>
                    
                    <div class="card-stats">
                        <div class="stat-chip">
                            <span class="stat-number">{{ $totalTenants ?? 0 }}</span>
                            <span class="stat-label-chip">Total</span>
                        </div>
                        <div class="stat-chip">
                            <span class="stat-number">{{ $activeTenants ?? 0 }}</span>
                            <span class="stat-label-chip">Active</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('users-management.tenants.list') }}" class="card-button tenants-btn">
                        <span>View All Tenants</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Owners Card -->
            <div class="user-type-card owners-card">
                <div class="card-glow-bar"></div>
                <div class="card-icon-wrapper">
                    <div class="card-icon owners-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                
                <div class="card-content">
                    <h3 class="card-title">Property Owners</h3>
                    <p class="card-description">Manage property owners, verify documents, and oversee property listings.</p>
                    
                    <div class="card-stats">
                        <div class="stat-chip">
                            <span class="stat-number">{{ $totalOwners ?? 0 }}</span>
                            <span class="stat-label-chip">Total</span>
                        </div>
                        <div class="stat-chip">
                            <span class="stat-number">{{ $verifiedOwners ?? 0 }}</span>
                            <span class="stat-label-chip">Verified</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('users-management.owners.list') }}" class="card-button owners-btn">
                        <span>View All Owners</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Same CSS as before - keeping all styles */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.users-management-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    background-attachment: fixed;
    font-family: 'Inter', sans-serif;
}

html, body, #app {
    background-color: #0a0c10;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #0a0c10;
}

.content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 1.5rem;
}

@media (min-width: 1024px) {
    .content-wrapper {
        padding: 2.5rem;
    }
}

.header-section {
    margin-bottom: 3rem;
}

.page-title-users {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: -0.3px;
    margin-bottom: 0.5rem;
    text-align: center;
}

@media (min-width: 768px) {
    .page-title-users {
        text-align: left;
    }
}

.page-subtitle-users {
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
}

@media (min-width: 768px) {
    .page-subtitle-users {
        text-align: left;
    }
}

.users-cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .users-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.user-type-card {
    position: relative;
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 2rem;
    transition: all 0.3s ease;
    overflow: hidden;
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

.tenants-card {
    animation-delay: 0.1s;
}

.owners-card {
    animation-delay: 0.2s;
}

.user-type-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-4px);
    box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-glow-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background-size: 200% 100%;
    animation: shimmer 3s infinite linear;
}

.tenants-card .card-glow-bar {
    background: linear-gradient(90deg, #f5b81b, #ffcc44, #f5b81b);
}

.owners-card .card-glow-bar {
    background: linear-gradient(90deg, #00e5ff, #4d9eff, #00e5ff);
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.card-icon-wrapper {
    margin-bottom: 1.5rem;
}

.card-icon {
    width: 70px;
    height: 70px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    transition: all 0.3s ease;
}

.user-type-card:hover .card-icon {
    transform: scale(1.05);
}

.tenants-icon {
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
    box-shadow: 0 0 20px rgba(245, 184, 27, 0.1);
}

.owners-icon {
    background: rgba(0, 229, 255, 0.12);
    color: #00e5ff;
    box-shadow: 0 0 20px rgba(0, 229, 255, 0.1);
}

.card-content {
    flex: 1;
}

.card-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 0.5rem;
    letter-spacing: -0.3px;
}

.card-description {
    font-size: 0.875rem;
    color: #94a3b8;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}

.card-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.stat-chip {
    background: rgba(15, 17, 21, 0.8);
    border: 1px solid rgba(245, 184, 27, 0.15);
    border-radius: 60px;
    padding: 0.5rem 1rem;
    display: inline-flex;
    align-items: baseline;
    gap: 0.5rem;
}

.stat-number {
    font-size: 1.125rem;
    font-weight: 800;
    color: #ffffff;
}

.stat-label-chip {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
}

.card-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    width: 100%;
    padding: 0.75rem 1.25rem;
    border-radius: 60px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.tenants-btn {
    background: rgba(245, 184, 27, 0.12);
    border: 1px solid rgba(245, 184, 27, 0.3);
    color: #f5b81b;
}

.tenants-btn:hover {
    background: rgba(245, 184, 27, 0.22);
    border-color: #f5b81b;
    transform: translateX(4px);
    text-decoration: none;
    color: #ffcc44;
}

.owners-btn {
    background: rgba(0, 229, 255, 0.12);
    border: 1px solid rgba(0, 229, 255, 0.3);
    color: #00e5ff;
}

.owners-btn:hover {
    background: rgba(0, 229, 255, 0.22);
    border-color: #00e5ff;
    transform: translateX(4px);
    text-decoration: none;
    color: #88f0ff;
}

.card-button i {
    transition: transform 0.2s ease;
}

.card-button:hover i {
    transform: translateX(4px);
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
    .user-type-card {
        padding: 1.5rem;
    }
    
    .card-title {
        font-size: 1.25rem;
    }
    
    .card-icon {
        width: 55px;
        height: 55px;
        font-size: 1.5rem;
        border-radius: 12px;
    }
    
    .stat-number {
        font-size: 1rem;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('%c👥 APARTrack Users Management | Ready for Real Data', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
    });
</script>
@endsection
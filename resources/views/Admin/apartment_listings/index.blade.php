@extends('layouts.admin')

@section('content')
<div class="apartment-listings-container">
    <div class="apartment-content-wrapper">
        
        {{-- Header Section --}}
        <div class="apartment-header">
            <h1 class="page-title-apartment">Apartment Listings</h1>
            <p class="page-subtitle-apartment">View all listings per barangay in Binalonan</p>
        </div>

        {{-- Barangays Grid --}}
        <div class="barangays-grid">
            @php
                use Illuminate\Support\Str;

                $barangays = [
                    'Balangobong' => 'balangobong.png',
                    'Bued' => 'bued.png',
                    'Bugayong' => 'bugayong.png',
                    'Camangaan' => 'camangaan.png',
                    'Canarvacanan' => 'canarvacanan.png',
                    'Capas' => 'capas.png',
                    'Cili' => 'cili.png',
                    'Dumayat' => 'dumayat.png',
                    'Linmansangan' => 'linmansangan.png',
                    'Mangcasuy' => 'mangcasuy.png',
                    'Moreno' => 'moreno.png',
                    'Pasileng Norte' => 'pasileng_norte.png',
                    'Pasileng Sur' => 'pasileng_sur.png',
                    'Poblacion' => 'poblacion.png',
                    'San Felipe Central' => 'sanfelipe_central.png',
                    'San Felipe Sur' => 'sanfelipe_sur.png',
                    'San Pablo' => 'sanpablo.png',
                    'Santiago' => 'santiago.png',
                    'Santo Niño' => 'santonino.png',
                    'Sta. Catalina' => 'stacatalina.png',
                    'Sta. Maria Norte' => 'stamaria_norte.png',
                    'Sumabnit' => 'sumabnit.png',
                    'Tabuyoc' => 'tabuyoc.png',
                    'Vacante' => 'vacante.png',
                ];
            @endphp

            @foreach($barangays as $name => $logo)
                <a href="{{ route('admin.apartments.barangay.show', Str::slug($name)) }}" 
                   class="barangay-card">
                    
                    {{-- Logo Container --}}
                    <div class="barangay-logo">
                        <img src="{{ asset('admin/brgy_logo/' . $logo) }}" 
                             alt="{{ $name }}" 
                             class="barangay-img"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=f5b81b&color=fff&bold=true'">
                    </div>

                    {{-- Barangay Name --}}
                    <span class="barangay-name">{{ $name }}</span>

                    {{-- Hover Badge --}}
                    <div class="barangay-hover-badge">
                        <span class="badge-text">
                            <i class="fas fa-arrow-right"></i> View Details
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
/* ========== GLOW DARK THEME - APARTMENT LISTINGS ========== */
/* MATCHING DASHBOARD THEME */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

/* Main Container - FIXED: Matching dashboard background */
.apartment-listings-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    background-attachment: fixed;
    font-family: 'Inter', sans-serif;
}

/* Match the exact same body background as dashboard */
html, body, #app {
    background-color: #0a0c10;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #0a0c10;
}

.apartment-content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 1.5rem;
}

@media (min-width: 1024px) {
    .apartment-content-wrapper {
        padding: 2.5rem;
    }
}

/* Page Header */
.apartment-header {
    margin-bottom: 2.5rem;
}

.page-title-apartment {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: -0.3px;
    margin-bottom: 0.5rem;
}

.page-subtitle-apartment {
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Barangays Grid */
.barangays-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

@media (min-width: 640px) {
    .barangays-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 768px) {
    .barangays-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 1024px) {
    .barangays-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* Barangay Card - Matching dashboard card style */
.barangay-card {
    position: relative;
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1.75rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    overflow: hidden;
    min-height: 220px;
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

/* Stagger animation delays */
.barangay-card:nth-child(1) { animation-delay: 0.02s; }
.barangay-card:nth-child(2) { animation-delay: 0.04s; }
.barangay-card:nth-child(3) { animation-delay: 0.06s; }
.barangay-card:nth-child(4) { animation-delay: 0.08s; }
.barangay-card:nth-child(5) { animation-delay: 0.10s; }
.barangay-card:nth-child(6) { animation-delay: 0.12s; }
.barangay-card:nth-child(7) { animation-delay: 0.14s; }
.barangay-card:nth-child(8) { animation-delay: 0.16s; }
.barangay-card:nth-child(9) { animation-delay: 0.18s; }
.barangay-card:nth-child(10) { animation-delay: 0.20s; }
.barangay-card:nth-child(11) { animation-delay: 0.22s; }
.barangay-card:nth-child(12) { animation-delay: 0.24s; }
.barangay-card:nth-child(13) { animation-delay: 0.26s; }
.barangay-card:nth-child(14) { animation-delay: 0.28s; }
.barangay-card:nth-child(15) { animation-delay: 0.30s; }
.barangay-card:nth-child(16) { animation-delay: 0.32s; }
.barangay-card:nth-child(17) { animation-delay: 0.34s; }
.barangay-card:nth-child(18) { animation-delay: 0.36s; }
.barangay-card:nth-child(19) { animation-delay: 0.38s; }
.barangay-card:nth-child(20) { animation-delay: 0.40s; }
.barangay-card:nth-child(21) { animation-delay: 0.42s; }
.barangay-card:nth-child(22) { animation-delay: 0.44s; }
.barangay-card:nth-child(23) { animation-delay: 0.46s; }
.barangay-card:nth-child(24) { animation-delay: 0.48s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.barangay-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #f5b81b, #00e5ff, #f5b81b);
    background-size: 200% 100%;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.barangay-card:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-4px);
    box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.5);
}

.barangay-card:hover::before {
    opacity: 1;
    animation: shimmer 2s infinite linear;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Logo Container - Matching dashboard icon style */
.barangay-logo {
    width: 90px;
    height: 90px;
    background: rgba(15, 17, 21, 0.8);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(245, 184, 27, 0.2);
}

.barangay-card:hover .barangay-logo {
    transform: scale(1.05);
    border-color: rgba(245, 184, 27, 0.5);
    box-shadow: 0 0 20px rgba(245, 184, 27, 0.15);
}

.barangay-img {
    width: 70px;
    height: 70px;
    object-fit: contain;
    border-radius: 14px;
    transition: all 0.3s ease;
}

/* Barangay Name */
.barangay-name {
    font-size: 0.875rem;
    font-weight: 700;
    color: #e2e8f0;
    text-align: center;
    letter-spacing: -0.2px;
    transition: all 0.3s ease;
    max-width: 120px;
    line-height: 1.3;
}

.barangay-card:hover .barangay-name {
    color: #f5b81b;
}

/* Hover Badge */
.barangay-hover-badge {
    position: absolute;
    bottom: 1.25rem;
    left: 50%;
    transform: translateX(-50%) translateY(10px);
    opacity: 0;
    transition: all 0.3s ease;
    pointer-events: none;
}

.barangay-card:hover .barangay-hover-badge {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.badge-text {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(245, 184, 27, 0.12);
    border: 1px solid rgba(245, 184, 27, 0.3);
    border-radius: 60px;
    padding: 5px 12px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #f5b81b;
    white-space: nowrap;
}

.badge-text i {
    font-size: 0.55rem;
    transition: transform 0.2s ease;
}

.barangay-card:hover .badge-text i {
    transform: translateX(3px);
}

/* Custom Scrollbar - Matching dashboard */
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

/* Text Selection - Matching dashboard */
::selection {
    background: #f5b81b;
    color: #0a0c10;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .barangay-card {
        padding: 1.25rem 0.75rem;
        min-height: 180px;
    }
    
    .barangay-logo {
        width: 70px;
        height: 70px;
        border-radius: 16px;
    }
    
    .barangay-img {
        width: 55px;
        height: 55px;
        border-radius: 12px;
    }
    
    .barangay-name {
        font-size: 0.75rem;
    }
    
    .page-title-apartment {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .apartment-content-wrapper {
        padding: 1rem;
    }
    
    .barangays-grid {
        gap: 0.75rem;
    }
    
    .barangay-card {
        padding: 1rem 0.5rem;
        min-height: 150px;
        border-radius: 20px;
    }
    
    .barangay-logo {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        margin-bottom: 0.75rem;
    }
    
    .barangay-img {
        width: 42px;
        height: 42px;
        border-radius: 10px;
    }
    
    .barangay-name {
        font-size: 0.7rem;
    }
    
    .badge-text {
        font-size: 0.55rem;
        padding: 3px 8px;
    }
    
    .page-title-apartment {
        font-size: 1.25rem;
    }
    
    .page-subtitle-apartment {
        font-size: 0.75rem;
    }
}

/* Print Styles */
@media print {
    .apartment-listings-container {
        background: white !important;
    }
    
    .barangay-hover-badge {
        display: none !important;
    }
    
    .barangay-card {
        border: 1px solid #ddd;
        background: white !important;
        break-inside: avoid;
        box-shadow: none;
    }
    
    .barangay-name {
        color: black !important;
    }
    
    .barangay-card::before {
        display: none !important;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('%c🏘️ APARTrack Apartment Listings | Matching Dashboard Theme', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
        
        // Optional: Add any hover interactions or analytics
        const cards = document.querySelectorAll('.barangay-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                // Placeholder for any custom behavior
            });
        });
    });
</script>
@endsection
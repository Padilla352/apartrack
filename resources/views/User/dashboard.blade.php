@extends('layouts.app')

@section('title', 'Dashboard - APARTrack')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* ========== GLOBAL RESET & BASE ========== */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(145deg, #f4f9ff 0%, #e9f2fa 100%); min-height: 100vh; }
    .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 2rem 1.8rem; animation: fadeInUp 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .guest-sections { display: flex; flex-direction: column; gap: 2.5rem; }
    .guest-section { display: flex; flex-direction: column; gap: 1.2rem; }
    .section-heading { display: inline-flex; align-items: center; gap: 0.6rem; font-size: 1.3rem; font-weight: 700; color: #1e3a8a; margin: 0; letter-spacing: -0.3px; border-left: 4px solid #3b82f6; padding-left: 0.9rem; text-decoration: none; }
    .section-heading-button { background: none; border: none; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
    .section-heading-button:hover { transform: translateX(6px); color: #2563eb; }
    .section-heading-button i { font-size: 0.9rem; color: #3b82f6; transition: transform 0.2s; }
    .section-heading-button:hover i { transform: translateX(4px); }
    .listing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
    .barangay-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.2rem; }
    .section-divider { border-top: 2px solid rgba(59, 130, 246, 0.2); margin: 1rem 0 0.5rem 0; }
    .listing-card { background: #ffffff; border-radius: 28px; overflow: hidden; cursor: pointer; transition: all 0.3s cubic-bezier(0.2, 0, 0, 1); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02), 0 2px 6px rgba(0, 0, 0, 0.05); border: 1px solid rgba(59, 130, 246, 0.15); }
    .listing-card:hover { transform: translateY(-6px); box-shadow: 0 20px 30px -12px rgba(59, 130, 246, 0.25); border-color: #3b82f640; }
    .listing-thumb { width: 100%; aspect-ratio: 1 / 0.9; object-fit: cover; display: block; transition: transform 0.4s ease; }
    .listing-card:hover .listing-thumb { transform: scale(1.02); }
    .listing-name { font-size: 0.95rem; font-weight: 700; color: #0c4a6e; margin: 0.9rem 1rem 0.3rem; line-height: 1.3; }
    .listing-price { font-size: 0.9rem; font-weight: 700; color: #3b82f6; margin: 0.3rem 1rem 0.5rem; display: flex; align-items: center; gap: 0.3rem; }
    .listing-price i { font-size: 0.8rem; color: #3b82f6; }
    .listing-meta { display: flex; justify-content: space-between; align-items: center; margin: 0 1rem 1rem 1rem; }
    .listing-link { font-size: 0.7rem; font-weight: 600; color: #3b82f6; text-decoration: none; letter-spacing: 0.3px; transition: all 0.2s; }
    .listing-link:hover { text-decoration: underline; color: #1e40af; }
    .listing-rating { font-size: 0.7rem; color: #fbbf24; letter-spacing: 1px; }
    .compact-barangay-card { background: white; border-radius: 24px; padding: 1.2rem 0.8rem; display: flex; flex-direction: column; align-items: center; text-align: center; cursor: pointer; transition: all 0.25s ease; border: 1px solid rgba(59, 130, 246, 0.12); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02); }
    .compact-barangay-card:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 16px 24px -10px rgba(59, 130, 246, 0.2); background: #fafcff; }
    .compact-barangay-card .brgy-logo { width: 85px; height: 85px; object-fit: contain; border-radius: 50%; background: #f0f7ff; padding: 6px; margin-bottom: 0.8rem; transition: transform 0.2s; }
    .compact-barangay-card:hover .brgy-logo { transform: scale(1.02); }
    .compact-barangay-card .brgy-name { font-size: 0.85rem; font-weight: 700; color: #1e3a8a; margin: 0; letter-spacing: -0.2px; }
    .skeleton { background: linear-gradient(90deg, #eef2f8 25%, #f8fafc 50%, #eef2f8 75%); background-size: 200% 100%; animation: shimmer 1.2s infinite; border-radius: 20px; }
    @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    .skeleton-card { background: #ffffffcc; border-radius: 28px; padding: 1rem; border: 1px solid rgba(59,130,246,0.1); }
    .skeleton-thumb { width: 100%; aspect-ratio: 1 / 0.9; border-radius: 24px; margin-bottom: 0.8rem; }
    .skeleton-title { height: 14px; width: 70%; border-radius: 30px; margin-bottom: 0.5rem; }
    .skeleton-meta { height: 12px; width: 50%; border-radius: 30px; }
    .skeleton-compact { background: #ffffffcc; border-radius: 24px; padding: 1rem; display: flex; flex-direction: column; align-items: center; gap: 0.7rem; border: 1px solid rgba(59,130,246,0.1); }
    .skeleton-logo { width: 75px; height: 75px; border-radius: 50%; }
    .skeleton-compact-title { width: 70px; height: 12px; border-radius: 20px; }
    .toast-notification { position: fixed; bottom: 2rem; right: 2rem; background: white; border-left: 4px solid #3b82f6; padding: 0.9rem 1.5rem; border-radius: 60px; box-shadow: 0 12px 24px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 0.8rem; z-index: 1100; font-weight: 500; font-size: 0.85rem; color: #0c4a6e; animation: slideInRight 0.3s ease; backdrop-filter: blur(8px); background: rgba(255,255,255,0.96); }
    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .notification-popup { background: linear-gradient(105deg, #3b82f6, #1e40af); border-radius: 20px; padding: 0.9rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; color: white; box-shadow: 0 8px 18px rgba(59,130,246,0.2); }
    .notification-content { display: flex; align-items: center; gap: 1rem; }
    .notification-icon { font-size: 1.3rem; background: rgba(255,255,255,0.2); width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .notification-text { font-weight: 500; font-size: 0.9rem; }
    .notification-close { background: none; border: none; color: white; font-size: 1.6rem; cursor: pointer; opacity: 0.8; transition: all 0.2s; line-height: 1; }
    .notification-close:hover { opacity: 1; transform: scale(1.05); }
    .notification-popup.hidden { display: none; }

    /* === Modals === */
    .modal { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(32, 40, 80, 0.5); align-items: center; justify-content: center; padding: 1rem; }
    .modal.show { display: flex; }
    .modal-content { width: min(800px, 96vw); border-radius: 28px; overflow: hidden; background: transparent; }
    .modal-body { padding: 0; }

    /* Apartment detail card */
    .apartment-detail-card { background: white; border-radius: 28px; padding: 1.8rem; box-shadow: 0 25px 45px -12px rgba(0,0,0,0.2); border: 1px solid rgba(59,130,246,0.2); }
    .apartment-title { margin: 0 0 0.5rem; color: #1e3a8a; font-weight: 800; font-size: 1.6rem; letter-spacing: -0.3px; }
    .rating-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem; }
    .view-ratings-link { font-size: 0.75rem; color: #3b82f6; text-decoration: underline; cursor: pointer; }
    .stars { color: #fbbf24; font-size: 0.9rem; letter-spacing: 2px; }
    .rating-count { color: #475569; font-size: 0.75rem; margin-left: 0.4rem; }
    .apartment-gallery { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.8rem; margin-bottom: 1.5rem; }
    .gallery-thumb { width: 100%; aspect-ratio: 1 / 0.8; border-radius: 16px; object-fit: cover; transition: 0.2s; }
    .details-features-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    .detail-section h3 { color: #3b82f6; font-size: 0.85rem; font-weight: 700; letter-spacing: 0.5px; margin: 0 0 0.6rem; text-transform: uppercase; }
    .detail-list { list-style: none; padding: 0; margin: 0; }
    .detail-list li { color: #1e293b; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.3rem; display: flex; gap: 0.5rem; }
    .detail-list li strong { font-weight: 700; color: #0f172a; min-width: 70px; }
    .feature-text { color: #1e293b; font-size: 0.85rem; font-weight: 500; line-height: 1.5; }
    .action-buttons { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 0.5rem; }
    .btn-chat, .btn-map { border: none; border-radius: 40px; padding: 0.5rem 1.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-chat { background: #3b82f6; color: white; box-shadow: 0 2px 6px rgba(59,130,246,0.3); }
    .btn-chat:hover { background: #1e40af; transform: translateY(-2px); }
    .btn-map { background: #eef2ff; color: #1e40af; border: 1px solid #3b82f640; }
    .btn-map:hover { background: #e0e7ff; transform: translateY(-2px); }

    /* Ratings Modal Styles */
    .ratings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.2rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #eef2ff;
    }
    .ratings-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e3a8a;
        margin: 0;
    }
    .close-ratings {
        background: none;
        border: none;
        font-size: 1.8rem;
        cursor: pointer;
        color: #94a3b8;
        transition: all 0.2s;
        line-height: 1;
    }
    .close-ratings:hover {
        color: #1e40af;
        transform: scale(1.05);
    }
    .review-item {
        background: #f8fafc;
        border-radius: 20px;
        padding: 1rem 1.2rem;
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .review-item:hover {
        background: white;
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .reviewer-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9rem;
    }
    .review-date {
        font-size: 0.7rem;
        color: #64748b;
    }
    .review-stars {
        color: #fbbf24;
        font-size: 0.75rem;
        letter-spacing: 2px;
        margin: 0.3rem 0;
    }
    .review-comment {
        font-size: 0.85rem;
        color: #1e293b;
        line-height: 1.4;
        margin-top: 0.5rem;
    }
    .no-reviews {
        text-align: center;
        padding: 2rem;
        color: #64748b;
        font-style: italic;
    }

    @media (max-width: 1100px) { .barangay-grid { grid-template-columns: repeat(4, 1fr); } .listing-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 800px) { .dashboard-container { padding: 1.2rem; } .barangay-grid { grid-template-columns: repeat(3, 1fr); gap: 1rem; } .listing-grid { grid-template-columns: repeat(2, 1fr); } .section-heading { font-size: 1.1rem; } .apartment-gallery { grid-template-columns: repeat(2, 1fr); } .details-features-row { grid-template-columns: 1fr; gap: 1rem; } }
    @media (max-width: 550px) { .barangay-grid { grid-template-columns: repeat(2, 1fr); } .listing-grid { grid-template-columns: 1fr; } .compact-barangay-card .brgy-logo { width: 65px; height: 65px; } .action-buttons { justify-content: stretch; } .btn-chat, .btn-map { flex: 1; text-align: center; } }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div id="guestExploreView">
        <div class="guest-sections">
            <!-- Boarding section -->
            <section class="guest-section">
                <a href="{{ route('boarding.nearby') }}" id="boardingHeading" class="section-heading section-heading-button">
                    <span>Popular boarding near UEP & WCC</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <div class="listing-grid" id="featuredBoardingGrid"></div>
            </section>

            <!-- Commercial section -->
            <section class="guest-section">
                <a href="{{ route('commercial.nearby') }}" id="commercialHeading" class="section-heading section-heading-button" style="text-decoration: none;">
                    <span>Commercial spaces at Poblacion & Market</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <div class="listing-grid" id="featuredCommercialGrid"></div>
            </section>

            <div class="section-divider"></div>

            <div id="barangaySection" class="guest-section">
                <h2 class="section-heading">Apartment / Commercial per Barangay</h2>
                <div class="barangay-grid" id="compactBarangayGrid"></div>
            </div>
        </div>
    </div>
</div>

<!-- Apartment Details Modal -->
<div id="apartmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<!-- Ratings Modal -->
<div id="ratingsModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-body" id="ratingsModalBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
window.isAuthenticated = @auth true @else false @endauth;

// ========== OPTIMIZED DATA (pre-generated, no function calls during render) ==========
const masterBarangays = [
    { name: "Balangobong", file: "balangobong.png", available: 12, total: 24 },
    { name: "Bued", file: "bued.png", available: 8, total: 18 },
    { name: "Bugayong", file: "bugayong.png", available: 15, total: 30 },
    { name: "Camangaan", file: "camangaan.png", available: 6, total: 20 },
    { name: "Canarvacanan", file: "canarvacanan.png", available: 10, total: 22 },
    { name: "Capas", file: "capas.png", available: 14, total: 28 },
    { name: "Cili", file: "cili.png", available: 9, total: 26 },
    { name: "Dumayat", file: "dumayat.png", available: 11, total: 24 },
    { name: "Linmansangan", file: "linmansangan.png", available: 13, total: 25 },
    { name: "Mangcasuy", file: "mangcasuy.png", available: 7, total: 19 },
    { name: "Moreno", file: "moreno.png", available: 16, total: 32 },
    { name: "Pasileng Norte", file: "pasileng_norte.png", available: 5, total: 15 },
    { name: "Pasileng Sur", file: "pasileng_sur.png", available: 10, total: 21 },
    { name: "Poblacion", file: "poblacion.png", available: 18, total: 35 },
    { name: "San Felipe Central", file: "sanfelipe_central.png", available: 8, total: 17 },
    { name: "San Felipe Sur", file: "sanfelipe_sur.png", available: 12, total: 23 },
    { name: "San Pablo", file: "sanpablo.png", available: 9, total: 20 },
    { name: "Santiago", file: "santiago.png", available: 14, total: 27 },
    { name: "Santonino", file: "santonino.png", available: 11, total: 22 },
    { name: "Sta. Catalina", file: "stacatalina.png", available: 7, total: 18 },
    { name: "Sta. Maria Norte", file: "stamaria_norte.png", available: 13, total: 26 },
    { name: "Sumabnit", file: "sumabnit.png", available: 10, total: 24 },
    { name: "Tabuyoc", file: "tabuyoc.png", available: 15, total: 29 },
    { name: "Vacante", file: "vacante.png", available: 4, total: 12 }
];

// Base listings with pre-generated reviews (no function calls at render time)
const createMockReviews = () => {
    const reviewers = ["Maria R.", "John C.", "Kristine L.", "Mark D.", "Sarah V.", "Renz M.", "Angelica P.", "Joshua T.", "Megan S.", "Patrick G."];
    const comments = [
        "Very clean and quiet place. The owner is responsive. Highly recommended!",
        "Good value for money. A bit far from the main road but overall okay.",
        "The room is spacious but the shared bathroom needs improvement.",
        "Perfect for students! Near UEP and affordable.",
        "I love the ambiance. Modern and well-maintained.",
        "The security is good (CCTV), and the neighbors are friendly.",
        "Water pressure is low sometimes, but the host is helpful.",
        "Excellent location, walking distance to market and church.",
        "The unit is exactly as described. No hidden issues.",
        "Parking is tight, but manageable. Would rent again."
    ];
    
    return (avgRating, title) => {
        const numReviews = Math.floor(Math.random() * 4) + 3;
        const reviews = [];
        for (let i = 0; i < numReviews; i++) {
            let rating = avgRating + (Math.random() * 1.2 - 0.6);
            rating = Math.min(5, Math.max(1, Math.round(rating * 2) / 2));
            const date = new Date();
            date.setDate(date.getDate() - Math.floor(Math.random() * 90));
            const formattedDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            reviews.push({
                reviewer: reviewers[Math.floor(Math.random() * reviewers.length)],
                rating: rating,
                comment: comments[Math.floor(Math.random() * comments.length)],
                date: formattedDate
            });
        }
        return reviews.sort((a,b) => new Date(b.date) - new Date(a.date));
    };
};

const generateReviews = createMockReviews();

const masterBoardingData = [
    { id: 1, title: 'Jesus And Elnora Boarding House Rental', area: 'Poblacion', type: 'boarding', rating: 4, image: 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=600&h=500&fit=crop', price: '₱2,500 / month', features: 'Studio type, bathroom, cabinet, aircon, shared kitchen, CCTV', address: 'Near UEP & WCC, Binalonan', contact: '+639171234567' },
    { id: 2, title: 'NVE Boarding', area: 'UEP side', type: 'boarding', rating: 3, image: 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=600&h=500&fit=crop', price: '₱2,800 / month', features: '1BR, bathroom, kitchen, parking, CCTV', address: 'UEP Campus vicinity, Binalonan', contact: '+639178765432' },
    { id: 3, title: 'Comiso Dorm Rental', area: 'WCC side', type: 'boarding', rating: 5, image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&h=500&fit=crop', price: '₱3,000 / month', features: 'Dorm type, shared bathroom, study area, WiFi, water heater', address: 'Near WCC, Binalonan', contact: 'comiso.dorm@example.com' },
    { id: 4, title: 'Marina Comiso Rental', area: 'Town proper', type: 'boarding', rating: 4, image: 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600&h=500&fit=crop', price: '₱3,500 / month', features: 'Studio, modern interior, aircon, own kitchen, balcony', address: 'Town proper, Binalonan', contact: '+639123456789' }
].map(item => ({
    ...item,
    reviews: generateReviews(item.rating, item.title)
}));

const masterCommercialData = [
    { id: 5, title: 'Marietta & Leon Commercial Space', area: 'Market road', type: 'commercial', rating: 4, image: 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?w=600&h=500&fit=crop', price: '₱5,000 / month', features: 'Commercial stall, 20 sqm, high foot traffic, display window', address: 'Market road, Poblacion, Binalonan', contact: 'marietta.leon@commercial.com' },
    { id: 6, title: 'Rosewil Retail Space', area: 'Centro', type: 'commercial', rating: 3, image: 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?w=600&h=500&fit=crop', price: '₱4,500 / month', features: 'Retail space, glass front, near church, 18 sqm', address: 'Centro, Binalonan', contact: '+639876543210' },
    { id: 7, title: 'RN Commercial Unit', area: 'Highway', type: 'commercial', rating: 5, image: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=500&fit=crop', price: '₱6,000 / month', features: 'Commercial unit, 25 sqm, parking included, AC ready', address: 'National Highway, Binalonan', contact: 'rn.commercial@example.com' },
    { id: 8, title: 'Celedonia’s Shop Space', area: 'Barangay hall', type: 'commercial', rating: 4, image: 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?w=600&h=500&fit=crop', price: '₱3,800 / month', features: 'Small shop space, secure area, 15 sqm', address: 'Near Barangay Hall, Binalonan', contact: '+639112233445' }
].map(item => ({
    ...item,
    reviews: generateReviews(item.rating, item.title)
}));

// ========== HELPER FUNCTIONS ==========
function generateStars(rating) {
    let stars = '';
    const full = Math.floor(rating);
    const half = rating % 1 !== 0;
    for (let i = 1; i <= 5; i++) {
        if (i <= full) stars += '<i class="fa-solid fa-star"></i>';
        else if (i === full+1 && half) stars += '<i class="fa-solid fa-star-half-alt"></i>';
        else stars += '<i class="fa-regular fa-star"></i>';
    }
    return stars;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
        return c;
    });
}

function generatePlaceholder(name) {
    const initial = name.charAt(0).toUpperCase();
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ec489a', '#14b8a6'];
    const colorIndex = name.length % colors.length;
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="${colors[colorIndex]}"/><text x="50" y="67" font-size="42" text-anchor="middle" fill="white" font-family='Poppins, sans-serif' font-weight="600">${initial}</text></svg>`;
    return 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svg)));
}

let toastTimeout = null;
function showToast(message) {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();
    if (toastTimeout) clearTimeout(toastTimeout);
    
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `<i style="color: #3b82f6;">✓</i><span>${message}</span>`;
    document.body.appendChild(toast);
    toastTimeout = setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s reverse';
        setTimeout(() => toast.remove(), 300);
        toastTimeout = null;
    }, 3000);
}

function closeModal() {
    document.getElementById('apartmentModal')?.classList.remove('show');
}

function openMap(address) {
    if (!address) { showToast('Address not available for this listing.'); return; }
    const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
    window.open(mapsUrl, '_blank');
}

function contactHost(contact, title) {
    if (!contact) { showToast('Contact information not available.'); return; }
    const phonePattern = /^\+?[0-9\s\-\(\)]+$/;
    if (phonePattern.test(contact.trim())) {
        const phone = contact.replace(/\s/g, '');
        const message = `Hello, I'm interested in "${title}". Could you provide more details?`;
        const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    } else if (contact.includes('@')) {
        const subject = `Inquiry about ${title}`;
        const body = `Hello, I am interested in "${title}". Please send me more information.`;
        const mailtoUrl = `mailto:${contact}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.location.href = mailtoUrl;
    } else {
        alert(`Contact host:\n${contact}\n\nYou can reach out via this number/address.`);
    }
}

function showRatingsModal(item) {
    const modal = document.getElementById('ratingsModal');
    const modalBody = document.getElementById('ratingsModalBody');
    if (!modal || !modalBody) return;

    const reviewsHtml = item.reviews && item.reviews.length > 0
        ? item.reviews.map(rev => `
            <div class="review-item">
                <div class="review-header">
                    <span class="reviewer-name">${escapeHtml(rev.reviewer)}</span>
                    <span class="review-date">${escapeHtml(rev.date)}</span>
                </div>
                <div class="review-stars">${generateStars(rev.rating)}</div>
                <div class="review-comment">${escapeHtml(rev.comment)}</div>
            </div>
        `).join('')
        : '<div class="no-reviews">No written reviews yet. Be the first to rate!</div>';

    const overallStars = generateStars(item.rating);
    const content = `
        <div class="apartment-detail-card" style="padding: 1.5rem;">
            <div class="ratings-header">
                <h3 class="ratings-title">Ratings & Reviews • ${escapeHtml(item.title)}</h3>
                <button class="close-ratings" id="closeRatingsBtn">&times;</button>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div><span style="font-size: 1.8rem; font-weight: 800;">${item.rating}</span> <span style="color: #475569;">/5</span></div>
                    <div class="review-stars" style="font-size: 1.1rem;">${overallStars}</div>
                    <div style="font-size: 0.75rem; color: #475569;">Based on ${item.reviews.length} review(s)</div>
                </div>
            </div>
            <div style="max-height: 60vh; overflow-y: auto; padding-right: 0.5rem;">
                ${reviewsHtml}
            </div>
        </div>
    `;
    modalBody.innerHTML = content;
    modal.classList.add('show');
    document.getElementById('closeRatingsBtn')?.addEventListener('click', () => modal.classList.remove('show'));
}

function showListingDetails(item) {
    const modalBody = document.getElementById('modalBody');
    const galleryImages = [
        item.image,
        item.image + '&auto=format&fit=crop&w=600&h=500&1',
        item.image + '&auto=format&fit=crop&w=600&h=500&2',
        item.image + '&auto=format&fit=crop&w=600&h=500&3'
    ];
    const galleryHtml = galleryImages.map(img => `<img src="${img}" class="gallery-thumb" onerror="this.src=generatePlaceholder('${item.title}')" alt="Gallery">`).join('');
    const detailsHtml = `
        <div class="apartment-detail-card" id="modalDetailCard">
            <h2 class="apartment-title">${escapeHtml(item.title)}</h2>
            <div class="rating-row">
                <div><span class="stars">${generateStars(item.rating)}</span><span class="rating-count">(${item.rating} avg · ${item.reviews.length} reviews)</span></div>
                <span class="view-ratings-link" id="viewRatingsLink">View all ratings →</span>
            </div>
            <div class="apartment-gallery">${galleryHtml}</div>
            <div class="details-features-row">
                <div class="detail-section"><h3><i class="fas fa-info-circle"></i> Details</h3><ul class="detail-list"><li><strong>Price:</strong> ${item.price}</li><li><strong>Type:</strong> ${item.type === 'boarding' ? 'Boarding House' : 'Commercial Space'}</li><li><strong>Area:</strong> ${item.area}</li></ul></div>
                <div class="detail-section"><h3><i class="fas fa-check-circle"></i> Features & Amenities</h3><div class="feature-text">${escapeHtml(item.features)}</div></div>
            </div>
            <div class="detail-section"><h3><i class="fas fa-map-marker-alt"></i> Address</h3><div class="feature-text">${escapeHtml(item.address)}</div></div>
            <div class="action-buttons">
                <button class="btn-map" data-address="${escapeHtml(item.address)}"><i class="fas fa-map"></i> View on map</button>
                <button class="btn-chat" data-contact="${escapeHtml(item.contact)}" data-title="${escapeHtml(item.title)}"><i class="fas fa-comment"></i> Contact host</button>
            </div>
        </div>
    `;
    modalBody.innerHTML = detailsHtml;
    const mapBtn = modalBody.querySelector('.btn-map');
    const contactBtn = modalBody.querySelector('.btn-chat');
    const ratingsLink = modalBody.querySelector('#viewRatingsLink');
    if (mapBtn) mapBtn.addEventListener('click', () => openMap(mapBtn.getAttribute('data-address')));
    if (contactBtn) contactBtn.addEventListener('click', () => contactHost(contactBtn.getAttribute('data-contact'), contactBtn.getAttribute('data-title')));
    if (ratingsLink) ratingsLink.addEventListener('click', (e) => {
        e.stopPropagation();
        showRatingsModal(item);
    });
    document.getElementById('apartmentModal').classList.add('show');
}

function goToBarangayDetails(brgyName, available, total, logoUrl) {
    const params = new URLSearchParams({ name: brgyName, available, total, logo: logoUrl });
    window.location.href = `/barangay-details?${params.toString()}`;
}

// ========== RENDERING FUNCTIONS (OPTIMIZED) ==========
function renderListingGrid(containerId, items) {
    const container = document.getElementById(containerId);
    if (!container) return;
    if (!items.length) {
        container.innerHTML = '<div class="no-results" style="grid-column:1/-1; text-align:center; padding:2rem;">No listings found.</div>';
        return;
    }
    
    // Direct HTML generation without JSON parsing overhead
    let html = '';
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        html += `
            <article class="listing-card" data-id="${item.id}" data-type="${item.type}">
                <img src="${item.image}" class="listing-thumb" onerror="this.src=generatePlaceholder('${item.title}')" alt="${item.title}" loading="lazy">
                <h3 class="listing-name">${escapeHtml(item.title)}</h3>
                <div class="listing-price"><i class="fas fa-tag"></i> ${item.price}</div>
                <div class="listing-meta"><span class="listing-link">view details →</span><span class="listing-rating">${generateStars(item.rating)}</span></div>
            </article>
        `;
    }
    container.innerHTML = html;
    
    // Attach event listeners efficiently
    const cards = container.querySelectorAll('.listing-card');
    for (let i = 0; i < cards.length; i++) {
        const card = cards[i];
        const id = parseInt(card.getAttribute('data-id'));
        const type = card.getAttribute('data-type');
        const item = type === 'boarding' 
            ? masterBoardingData.find(d => d.id === id)
            : masterCommercialData.find(d => d.id === id);
        if (item) {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                if (window.isAuthenticated) showListingDetails(item);
                else showToast('Please log in to view details');
            });
        }
    }
}

function renderBarangayGrid(barangays, filterType) {
    const container = document.getElementById('compactBarangayGrid');
    if (!container) return;
    let sortedBarangays = [...barangays];
    if (filterType === 'name_asc') sortedBarangays.sort((a,b) => a.name.localeCompare(b.name));
    else if (filterType === 'name_desc') sortedBarangays.sort((a,b) => b.name.localeCompare(a.name));
    
    if (!sortedBarangays.length) {
        container.innerHTML = '<div class="no-results" style="grid-column:1/-1; text-align:center; padding:2rem;">No barangays match.</div>';
        return;
    }
    
    let html = '';
    for (let i = 0; i < sortedBarangays.length; i++) {
        const brgy = sortedBarangays[i];
        const imgPath = `{{ asset('images/brgy_logo/') }}/${brgy.file}`;
        html += `
            <div class="compact-barangay-card" data-brgy-name="${escapeHtml(brgy.name)}" data-available="${brgy.available}" data-total="${brgy.total}" data-logo="${imgPath}">
                <img src="${imgPath}" class="brgy-logo" onerror="this.src=generatePlaceholder('${brgy.name}')" alt="${brgy.name}" loading="lazy">
                <h3 class="brgy-name">${escapeHtml(brgy.name)}</h3>
            </div>
        `;
    }
    container.innerHTML = html;
    
    // Attach event listeners
    const cards = container.querySelectorAll('.compact-barangay-card');
    for (let i = 0; i < cards.length; i++) {
        const card = cards[i];
        const name = card.getAttribute('data-brgy-name');
        const available = card.getAttribute('data-available');
        const total = card.getAttribute('data-total');
        const logo = card.getAttribute('data-logo');
        card.addEventListener('click', () => goToBarangayDetails(name, available, total, logo));
    }
}

// ========== FILTER FUNCTIONS ==========
function filterListingsBySearch(items, searchTerm) {
    if (!searchTerm?.trim()) return items;
    const term = searchTerm.toLowerCase().trim();
    return items.filter(i => i.title.toLowerCase().includes(term) || (i.area && i.area.toLowerCase().includes(term)));
}

function filterBarangaysBySearch(barangays, searchTerm) {
    if (!searchTerm?.trim()) return barangays;
    const term = searchTerm.toLowerCase().trim();
    return barangays.filter(b => b.name.toLowerCase().includes(term));
}

function toggleHeadingsBasedOnFilter(filterType) {
    const boardingHeading = document.getElementById('boardingHeading');
    const commercialHeading = document.getElementById('commercialHeading');
    const isFilterActive = filterType && filterType !== '';
    if (boardingHeading) boardingHeading.style.display = isFilterActive ? 'none' : 'inline-flex';
    if (commercialHeading) commercialHeading.style.display = isFilterActive ? 'none' : 'inline-flex';
}

// Immediate render without setTimeout delays
function updateDashboard(searchTerm, filterType) {
    toggleHeadingsBasedOnFilter(filterType);

    const barangaySection = document.getElementById('barangaySection');
    const isPriceFilter = (filterType === 'price_low' || filterType === 'price_high');
    if (barangaySection) {
        barangaySection.style.display = isPriceFilter ? 'none' : '';
    }

    // Filter and sort data
    let boardingFiltered = filterListingsBySearch(masterBoardingData, searchTerm);
    let commercialFiltered = filterListingsBySearch(masterCommercialData, searchTerm);
    let filteredBarangays = filterBarangaysBySearch(masterBarangays, searchTerm);
    
    if (filterType && filterType !== '') {
        if (filterType === 'price_low') {
            boardingFiltered.sort((a,b) => parseFloat(a.price.replace(/[^0-9.-]/g,'')) - parseFloat(b.price.replace(/[^0-9.-]/g,'')));
            commercialFiltered.sort((a,b) => parseFloat(a.price.replace(/[^0-9.-]/g,'')) - parseFloat(b.price.replace(/[^0-9.-]/g,'')));
        } else if (filterType === 'price_high') {
            boardingFiltered.sort((a,b) => parseFloat(b.price.replace(/[^0-9.-]/g,'')) - parseFloat(a.price.replace(/[^0-9.-]/g,'')));
            commercialFiltered.sort((a,b) => parseFloat(b.price.replace(/[^0-9.-]/g,'')) - parseFloat(a.price.replace(/[^0-9.-]/g,'')));
        } else if (filterType === 'name_asc') {
            boardingFiltered.sort((a,b) => a.title.localeCompare(b.title));
            commercialFiltered.sort((a,b) => a.title.localeCompare(b.title));
        } else if (filterType === 'name_desc') {
            boardingFiltered.sort((a,b) => b.title.localeCompare(a.title));
            commercialFiltered.sort((a,b) => b.title.localeCompare(a.title));
        }
    }
    
    // Render immediately
    renderListingGrid('featuredBoardingGrid', boardingFiltered);
    renderListingGrid('featuredCommercialGrid', commercialFiltered);
    renderBarangayGrid(filteredBarangays, filterType);
    
    // Show search results message if needed
    if (searchTerm && searchTerm.trim() !== '') {
        const totalMatches = boardingFiltered.length + commercialFiltered.length + filteredBarangays.length;
        if (totalMatches === 0) showToast(`No results found for "${searchTerm}"`);
        else if (totalMatches > 0) showToast(`Found ${totalMatches} result(s) for "${searchTerm}"`);
    }
    
    // Update URL without reload
    const url = new URL(window.location.href);
    if (searchTerm) url.searchParams.set('search', searchTerm); else url.searchParams.delete('search');
    if (filterType) url.searchParams.set('filter', filterType); else url.searchParams.delete('filter');
    window.history.pushState({}, '', url);
}

function setupSearchAndFilter() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const filterRadios = document.querySelectorAll('input[name="filter"]');
    if (!searchForm) return;
    
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const term = searchInput?.value || '';
        const activeFilter = document.querySelector('input[name="filter"]:checked')?.value || '';
        updateDashboard(term, activeFilter);
    });
    
    filterRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const term = searchInput?.value || '';
            const activeFilter = document.querySelector('input[name="filter"]:checked')?.value || '';
            updateDashboard(term, activeFilter);
            const dropdown = document.getElementById('filterDropdown');
            if (dropdown) dropdown.classList.remove('show');
        });
    });
    
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') searchForm.dispatchEvent(new Event('submit')); });
    }
}

function initNotification() {
    const notif = document.getElementById('guestNotification');
    const closeBtn = document.getElementById('closeNotificationBtn');
    if (closeBtn) closeBtn.addEventListener('click', () => { notif?.classList.add('hidden'); localStorage.setItem('dashboardNotifDismissed', 'true'); });
    if (localStorage.getItem('dashboardNotifDismissed') === 'true') notif?.classList.add('hidden');
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const initialSearch = urlParams.get('search') || '';
    const initialFilter = urlParams.get('filter') || '';
    
    // Initial render - no skeletons, direct render for speed
    toggleHeadingsBasedOnFilter(initialFilter);
    updateDashboard(initialSearch, initialFilter);
    
    setupSearchAndFilter();
    initNotification();
    
    // Modal close handlers
    const modal = document.getElementById('apartmentModal');
    if (modal) {
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    }
    
    const ratingsModal = document.getElementById('ratingsModal');
    if (ratingsModal) {
        ratingsModal.addEventListener('click', (e) => { if (e.target === ratingsModal) ratingsModal.classList.remove('show'); });
    }
    
    // Welcome toast with slight delay but not blocking render
    if (!initialSearch && !initialFilter) {
        setTimeout(() => showToast('🏡 Welcome back to APARTrack — find your next home or commercial space'), 500);
    }
});
</script>
@endsection
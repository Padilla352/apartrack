@extends('layouts.app')

@section('title', 'Commercial Spaces at Poblacion, Market - APARTrack')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    :root {
        --primary: #3b82f6;
        --primary-dark: #1e40af;
        --primary-light: #eff6ff;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-600: #475569;
        --gray-800: #1e293b;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: linear-gradient(145deg, var(--gray-50) 0%, var(--primary-light) 100%);
        min-height: 100vh;
    }

    .dashboard-container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .back-header {
        margin-bottom: 2rem;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background: var(--white);
        color: var(--primary-dark);
        padding: 0.7rem 1.5rem;
        border-radius: 48px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--gray-200);
        transition: all 0.25s ease;
        box-shadow: var(--shadow-sm);
    }
    .back-link i {
        font-size: 0.9rem;
        transition: transform 0.2s;
    }
    .back-link:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        transform: translateX(-4px);
        box-shadow: var(--shadow-md);
    }
    .back-link:hover i {
        transform: translateX(-2px);
    }

    .listing-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.8rem;
    }

    .listing-card {
        background: var(--white);
        border-radius: 1.5rem;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
    }
    .listing-card:hover {
        transform: translateY(-6px);
        border-color: var(--primary);
        box-shadow: var(--shadow-xl);
    }

    .listing-thumb {
        width: 100%;
        aspect-ratio: 1 / 0.85;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .listing-card:hover .listing-thumb {
        transform: scale(1.02);
    }

    .listing-info {
        padding: 1rem 1rem 1.2rem;
    }

    .listing-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-800);
        margin: 0 0 0.25rem;
        line-height: 1.3;
    }

    .listing-subtitle {
        font-size: 0.75rem;
        color: var(--gray-600);
        font-weight: 500;
        margin: 0 0 0.6rem;
        text-transform: capitalize;
    }

    .listing-price {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .listing-price i {
        font-size: 0.85rem;
        color: var(--primary);
    }

    .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 2000;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal.show {
        display: flex;
    }
    .modal-content {
        width: min(920px, 96vw);
        border: none;
        border-radius: 1.5rem;
        padding: 0;
        overflow: hidden;
        background: transparent;
        box-shadow: none;
    }
    .modal-body {
        padding: 0;
    }
    .apartment-detail-card {
        background: var(--white);
        border-radius: 1.5rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--gray-200);
    }
    .apartment-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.9rem;
        margin-bottom: 0.65rem;
        flex-wrap: wrap;
    }
    .apartment-title {
        margin: 0;
        color: var(--primary-dark);
        font-weight: 800;
        font-size: 1.8rem;
        letter-spacing: -0.02em;
    }
    .rating-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .view-ratings-link {
        font-size: 0.75rem;
        color: var(--primary);
        text-decoration: underline;
        cursor: pointer;
    }
    .stars {
        color: #f4b117;
        font-size: 0.9rem;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .apartment-gallery {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.72rem;
        margin-bottom: 1rem;
    }
    .gallery-thumb {
        width: 100%;
        aspect-ratio: 1 / 0.8;
        border-radius: 1rem;
        object-fit: cover;
    }
    .detail-content-row {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 1.2rem;
        margin-bottom: 1rem;
    }
    .detail-block h4 {
        margin: 0 0 0.5rem;
        color: var(--primary);
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .detail-table {
        display: grid;
        grid-template-columns: 100px 1fr;
        row-gap: 0.28rem;
        column-gap: 0.55rem;
        color: var(--gray-800);
        font-size: 0.8rem;
    }
    .detail-table .label {
        color: var(--gray-600);
        font-weight: 600;
        text-align: right;
    }
    .detail-table .value {
        font-weight: 700;
    }
    .feature-text {
        margin: 0;
        color: var(--gray-800);
        font-size: 0.8rem;
        font-weight: 500;
        line-height: 1.55;
    }
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
    }
    .btn-chat, .btn-map {
        border: none;
        border-radius: 40px;
        padding: 0.55rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-chat {
        background: var(--primary);
        color: white;
    }
    .btn-chat:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }
    .btn-map {
        background: var(--primary-light);
        color: var(--primary-dark);
        border: 1px solid var(--primary);
    }
    .btn-map:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    /* Ratings Modal Styles */
    .ratings-modal-content {
        background: var(--white);
        border-radius: 1.5rem;
        width: min(500px, 90vw);
        max-height: 80vh;
        overflow-y: auto;
        padding: 1.5rem;
        box-shadow: var(--shadow-xl);
    }
    .ratings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--gray-200);
    }
    .ratings-header h3 {
        color: var(--primary-dark);
        font-size: 1.3rem;
    }
    .close-ratings {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--gray-600);
    }
    .average-rating {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .rating-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-dark);
    }
    .rating-stars {
        font-size: 1.2rem;
        color: #f4b117;
        margin: 0.3rem 0;
    }
    .review-count {
        color: var(--gray-600);
        font-size: 0.8rem;
    }
    .reviews-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .review-item {
        padding: 0.8rem;
        background: var(--gray-50);
        border-radius: 1rem;
        border-left: 3px solid var(--primary);
    }
    .review-author {
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 0.2rem;
    }
    .review-stars {
        font-size: 0.7rem;
        color: #f4b117;
        margin-bottom: 0.4rem;
    }
    .review-text {
        font-size: 0.8rem;
        color: var(--gray-600);
        line-height: 1.4;
    }

    .login-prompt {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 3000;
        padding: 1rem;
    }
    .login-prompt-card {
        max-width: 420px;
        width: 90%;
        background: var(--white);
        border-radius: 2rem;
        padding: 1.8rem;
        box-shadow: var(--shadow-xl);
        text-align: center;
        animation: fadeSlideUp 0.2s ease;
    }
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .login-prompt-card h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 0.5rem;
    }
    .login-prompt-card p {
        color: var(--gray-600);
        margin-bottom: 1.5rem;
    }
    .login-prompt-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    .btn-cancel, .btn-login-redirect {
        border: none;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-cancel {
        background: var(--gray-100);
        color: var(--gray-600);
    }
    .btn-cancel:hover {
        background: var(--gray-200);
    }
    .btn-login-redirect {
        background: var(--primary);
        color: white;
    }
    .btn-login-redirect:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    @media (max-width: 1100px) {
        .listing-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .apartment-title { font-size: 1.5rem; }
    }
    @media (max-width: 820px) {
        .listing-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .apartment-gallery { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .detail-content-row { grid-template-columns: 1fr; gap: 0.9rem; }
        .action-buttons { justify-content: stretch; flex-direction: column; }
        .btn-chat, .btn-map { width: 100%; }
    }
    @media (max-width: 550px) {
        .listing-grid { gap: 0.9rem; }
        .listing-info { padding: 0.7rem; }
        .listing-name { font-size: 0.9rem; }
        .listing-subtitle { font-size: 0.65rem; }
        .listing-price { font-size: 0.85rem; }
        .back-link { font-size: 0.9rem; padding: 0.5rem 1rem; }
        .apartment-title { font-size: 1.3rem; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div class="back-header">
        <a href="{{ url('/explore') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Commercial spaces at Poblacion, Market
        </a>
    </div>
    <div class="listing-grid" id="commercialGrid"></div>
</div>

<!-- Apartment Detail Modal -->
<div id="apartmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<!-- Ratings Modal -->
<div id="ratingsModal" class="modal">
    <div class="ratings-modal-content" id="ratingsContent">
        <!-- Content dynamically inserted -->
    </div>
</div>

<!-- Login Prompt Overlay -->
<div id="loginPromptOverlay" class="login-prompt">
    <div class="login-prompt-card">
        <h3>Continue to explore?</h3>
        <p>Please log in first to continue viewing details.</p>
        <div class="login-prompt-buttons">
            <button class="btn-cancel" id="cancelLoginPrompt">CANCEL</button>
            <a href="#" id="loginRedirectBtn" class="btn-login-redirect">LOG IN</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Authentication flag from Laravel
    window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

    // Helper: Generate star icons
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
        }
        return stars;
    }

    // Helper: Placeholder for broken images
    function generatePlaceholder(name) {
        const initial = name.charAt(0).toUpperCase();
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect width="100" height="100" fill="#3b82f6" rx="50"/>
            <text x="50" y="67" font-size="45" text-anchor="middle" fill="white"
            font-family="Poppins, sans-serif" font-weight="bold">${initial}</text></svg>`;
        return 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svg)));
    }

    // Commercial data with ratings & reviews
    const commercialData = [
        { 
            id: 5, title: 'SJE Boarding House', subtitle: 'Carisville Subd', price: '₱5,000 / month', 
            image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/sjeboardinghouse',
            rating: 4.5,
            reviews: [
                { author: 'Mark L.', rating: 5, text: 'Great location near the market. Clean and affordable.' },
                { author: 'Carla M.', rating: 4, text: 'Spacious common area. Friendly owner.' }
            ]
        },
        { 
            id: 6, title: 'LK18 Bagusto Boarding House Rental', subtitle: '90 casa bagusto', price: '₱5,500 / month', 
            image: 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/lk18boarding',
            rating: 4.2,
            reviews: [
                { author: 'John D.', rating: 4, text: 'Good value for money. Secure and quiet.' }
            ]
        },
        { 
            id: 7, title: 'LPM Boarding House', subtitle: 'Zone 1 Canarvacanan', price: '₱4,800 / month', 
            image: 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/lpmboarding',
            rating: 3.8,
            reviews: [
                { author: 'Rica P.', rating: 3, text: 'Basic but okay for students. Needs better lighting.' },
                { author: 'Ben T.', rating: 4, text: 'Affordable and close to school.' }
            ]
        },
        { 
            id: 8, title: 'Jocelyn Tandoc Bedspace Rental', subtitle: 'cabero street', price: '₱4,500 / month', 
            image: 'https://images.unsplash.com/photo-1600585152220-90363fe7e115?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/jocelyntandoc',
            rating: 4.0,
            reviews: [
                { author: 'Anna R.', rating: 5, text: 'Very kind host. Clean bedspace.' }
            ]
        },
        { 
            id: 13, title: 'Cabero R Boarding House', subtitle: 'commercial space', price: '₱5,200 / month', 
            image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/caberorboarding',
            rating: 4.3,
            reviews: [
                { author: 'Lea S.', rating: 4, text: 'Good for small businesses. Accessible.' }
            ]
        },
        { 
            id: 14, title: 'Jetess Bedspace', subtitle: 'commercial space', price: '₱5,800 / month', 
            image: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/jetessbedspace',
            rating: 4.7,
            reviews: [
                { author: 'Mike C.', rating: 5, text: 'Excellent facilities. Highly recommended.' },
                { author: 'Nina G.', rating: 4, text: 'Comfortable and well-maintained.' }
            ]
        },
        { 
            id: 15, title: 'Buniel Boarding House', subtitle: 'commercial space', price: '₱6,000 / month', 
            image: 'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/bunielboarding',
            rating: 4.1,
            reviews: [
                { author: 'Paolo R.', rating: 4, text: 'Spacious rooms. Near the public market.' }
            ]
        },
        { 
            id: 16, title: 'Marebel Boarding House', subtitle: 'Cabida', price: '₱4,200 / month', 
            image: 'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/marebelboarding',
            rating: 3.5,
            reviews: [
                { author: 'Grace L.', rating: 3, text: 'Okay for tight budget. Could be cleaner.' }
            ]
        },
        { 
            id: 17, title: 'Delos Reyes Boarding House', subtitle: 'commercial space', price: '₱5,500 / month', 
            image: 'https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?w=800&h=600&fit=crop', 
            facebookUrl: 'https://www.facebook.com/delosreyesboarding',
            rating: 4.4,
            reviews: [
                { author: 'Victor A.', rating: 5, text: 'Friendly staff, safe environment.' }
            ]
        }
    ];

    // Transform raw data to detailed apartment object (includes reviews)
    function getCommercialData(item) {
        return {
            id: item.id,
            name: item.title,
            apartmentName: item.title,
            location: 'Poblacion, Binalonan',
            price: item.price,
            features: 'Dormitory / commercial space, shared kitchen, common area, CCTV, walking distance to market',
            rating: item.rating,
            photos: [
                item.image,
                'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1600566752355-35792bedcfea?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=800&h=600&fit=crop'
            ],
            facebookUrl: item.facebookUrl,
            reviews: item.reviews
        };
    }

    // Show ratings modal
    function showRatings(property) {
        const modal = document.getElementById('ratingsModal');
        const content = document.getElementById('ratingsContent');
        
        const avgRating = property.rating;
        const totalReviews = property.reviews.length;
        
        let reviewsHtml = '';
        property.reviews.forEach(review => {
            reviewsHtml += `
                <div class="review-item">
                    <div class="review-author">${review.author}</div>
                    <div class="review-stars">${generateStars(review.rating)}</div>
                    <div class="review-text">${review.text}</div>
                </div>
            `;
        });
        
        content.innerHTML = `
            <div class="ratings-header">
                <h3>${property.name} - Ratings & Reviews</h3>
                <button class="close-ratings" id="closeRatingsBtn">&times;</button>
            </div>
            <div class="average-rating">
                <div class="rating-number">${avgRating.toFixed(1)} / 5.0</div>
                <div class="rating-stars">${generateStars(avgRating)}</div>
                <div class="review-count">Based on ${totalReviews} review${totalReviews !== 1 ? 's' : ''}</div>
            </div>
            <div class="reviews-list">
                ${reviewsHtml || '<p>No reviews yet. Be the first to review!</p>'}
            </div>
        `;
        
        modal.classList.add('show');
        
        document.getElementById('closeRatingsBtn').addEventListener('click', () => {
            modal.classList.remove('show');
        });
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    }

    // ----- FACEBOOK CONTACT FUNCTION -----
    function contactOnFacebook(apartmentId, facebookUrl) {
        if (!window.isAuthenticated) {
            localStorage.setItem('redirectAfterLogin', facebookUrl);
            showLoginPrompt();
            return;
        }
        window.open(facebookUrl, '_blank');
    }

    // View map function
    function viewMap(location) {
        const encodedLocation = encodeURIComponent(location);
        window.open(`https://www.google.com/maps/search/${encodedLocation}`, '_blank');
    }

    // Show login prompt overlay
    function showLoginPrompt() {
        document.getElementById('loginPromptOverlay').style.display = 'flex';
    }

    function hideLoginPrompt() {
        document.getElementById('loginPromptOverlay').style.display = 'none';
    }

    // Display apartment details modal (for authenticated users)
    function showApartmentDetails(apartment) {
        const modal = document.getElementById('apartmentModal');
        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = `
            <div class="apartment-detail-card">
                <div class="apartment-title-row">
                    <h2 class="apartment-title">${apartment.name}</h2>
                    <div class="rating-wrapper">
                        <a href="javascript:void(0)" class="view-ratings-link" data-property='${JSON.stringify(apartment)}'>View Ratings</a>
                        <span class="stars">${generateStars(apartment.rating)}</span>
                    </div>
                </div>
                <div class="apartment-gallery">
                    ${apartment.photos.map(src => `<img src="${src}" class="gallery-thumb" onerror="this.src=generatePlaceholder('${apartment.name}')">`).join('')}
                </div>
                <div class="detail-content-row">
                    <div class="detail-block">
                        <h4>APARTMENT DETAILS</h4>
                        <div class="detail-table">
                            <div class="label">NAME:</div><div class="value">${apartment.apartmentName}</div>
                            <div class="label">LOCATION:</div><div class="value">${apartment.location}</div>
                            <div class="label">PRICE:</div><div class="value">${apartment.price}</div>
                        </div>
                    </div>
                    <div class="detail-block">
                        <h4>FEATURES:</h4>
                        <p class="feature-text">${apartment.features}</p>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="btn-chat" onclick="contactOnFacebook(${apartment.id}, '${apartment.facebookUrl}')">📘 Contact on Facebook</button>
                    <button class="btn-map" onclick="viewMap('${apartment.location}')">🗺️ View Map</button>
                </div>
            </div>
        `;
        modal.classList.add('show');

        // Attach ratings link listener
        const ratingsLink = document.querySelector('.view-ratings-link');
        if (ratingsLink) {
            ratingsLink.addEventListener('click', (e) => {
                e.preventDefault();
                const propData = JSON.parse(ratingsLink.getAttribute('data-property'));
                showRatings(propData);
            });
        }
    }

    // Render the grid of commercial spaces
    function renderCommercialList() {
        const container = document.getElementById('commercialGrid');
        if (!container) return;
        container.innerHTML = '';

        commercialData.forEach((item) => {
            const card = document.createElement('article');
            card.className = 'listing-card';
            card.innerHTML = `
                <img src="${item.image}" class="listing-thumb" onerror="this.src=generatePlaceholder('${item.title}')">
                <div class="listing-info">
                    <h3 class="listing-name">${item.title}</h3>
                    <p class="listing-subtitle">${item.subtitle}</p>
                    <p class="listing-price"><i class="fas fa-tag"></i> ${item.price}</p>
                </div>
            `;
            card.addEventListener('click', (e) => {
                e.preventDefault();
                const aptData = getCommercialData(item);
                if (window.isAuthenticated) {
                    showApartmentDetails(aptData);
                } else {
                    showLoginPrompt();
                }
            });
            container.appendChild(card);
        });
    }

    // DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        renderCommercialList();

        // Modal close on background click
        const modal = document.getElementById('apartmentModal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('show');
        });

        // Login prompt cancel button
        document.getElementById('cancelLoginPrompt').addEventListener('click', hideLoginPrompt);
        document.getElementById('loginPromptOverlay').addEventListener('click', (e) => {
            if (e.target === document.getElementById('loginPromptOverlay')) hideLoginPrompt();
        });

        // Login redirect button - pass stored Facebook URL as redirect parameter
        const loginBtn = document.getElementById('loginRedirectBtn');
        if (loginBtn) {
            loginBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let redirectUrl = localStorage.getItem('redirectAfterLogin') || window.location.href;
                window.location.href = '{{ route("login", ["role" => "tenant"]) }}?redirect=' + encodeURIComponent(redirectUrl);
            });
        }
    });
</script>
@endsection
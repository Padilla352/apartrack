@extends('owner.layouts.app')

@section('title', 'My Listings')
@section('page-title', '')

@section('content')
<!-- Header Section with Title -->
<div class="header-section">
    <div class="header-left">
        <p class="page-description">Manage your properties (Apartments & Commercial Spaces)</p>
    </div>
</div>

<!-- ACCOUNT VERIFICATION STATUS WARNING (top banner) -->
@if(isset($verificationStatus) && $verificationStatus != 'approved')
<div class="alert alert-warning">
    <i class="fas fa-clock"></i>
    <div>
        <strong>Account Pending Approval</strong>
        <p>Your account is waiting for admin verification. You cannot add, edit, or delete listings until your permit number is approved.</p>
        <small>Please wait for the admin to verify your permit number.</small>
    </div>
</div>
@endif

<!-- Add Listings Buttons - FIXED: Business button only for 'both' owners -->
<div class="add-listing-wrapper">
    <div class="btn-group">
        <button class="btn-add-listing-airbnb" id="showAddModalBtn">
            Add Apartment
        </button>
        
        {{-- ONLY SHOW BUSINESS BUTTON FOR 'BOTH' PROPERTY TYPE --}}
        @if(isset($propertyType) && $propertyType == 'both')
        <button class="btn-add-listing-business" id="showBusinessModalBtn">
            Add Commercial Space
        </button>
        @endif
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- Table -->
<div class="table-container">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>TITLE</th>
                    <th>TYPE</th>
                    <th>PRICE</th>
                    <th>VERIFICATION</th>
                    <th>STATUS</th>
                    <th class="text-center">ADDITIONAL DETAILS</th>
                 </thead>
            </thead>
            <tbody>
                @if(isset($listings) && $listings->count() > 0)
                    @foreach($listings as $listing)
                    <tr class="table-row">
                        <td class="property-cell">
                            @if(isset($listing->business_name))
                                <!-- Business Listing -->
                                <div class="property-title">
                                    {{ $listing->business_name }}
                                </div>
                                <div class="property-unit">Unit: {{ $listing->unit_number ?? 'N/A' }}</div>
                            @else
                                <!-- Apartment Listing -->
                                <div class="property-title">
                                    {{ $listing->name }}
                                </div>
                                <div class="property-unit">Unit: {{ $listing->unit_number }}</div>
                            @endif
                        </td>
                        <td class="property-type">
                            @if(isset($listing->business_name))
                                {{ $listing->type ?? 'N/A' }}
                            @else
                                {{ $listing->type ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="property-price">₱{{ number_format($listing->monthly_rent ?? 0, 2) }}</td>
                        
                        <!-- VERIFICATION STATUS COLUMN -->
                        <td class="property-verification">
                            @php 
                                $verifStatus = $listing->verification_status ?? 'pending'; 
                            @endphp
                            @if($verifStatus == 'approved')
                                <span class="verification-badge verified">VERIFIED</span>
                                <small class="verification-hint">Approved by admin</small>
                            @elseif($verifStatus == 'rejected')
                                <div class="rejected-container">
                                    <span class="verification-badge rejected">REJECTED</span>
                                    @if($listing->rejection_reason)
                                        <small class="verification-hint rejected-hint" title="{{ $listing->rejection_reason }}">
                                            Reason: {{ Str::limit($listing->rejection_reason, 60) }}
                                        </small>
                                    @endif
                                    <div class="revise-action mt-2">
                                        @if(isset($listing->business_name))
                                            <a href="{{ route('owner.business-spaces.revise', $listing->id) }}" class="btn-revise">
                                                Revise & Resubmit
                                            </a>
                                        @else
                                            <a href="{{ route('owner.apartments.revise', $listing->id) }}" class="btn-revise">
                                                Revise & Resubmit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="verification-badge pending">PENDING</span>
                                <small class="verification-hint">Awaiting admin verification</small>
                            @endif
                        </td>
                        
                        <!-- STATUS COLUMN -->
                        <td class="property-status">
                            @if(isset($listing->business_name))
                                @if($listing->status == 'Occupied')
                                    <span class="badge badge-occupied">OCCUPIED</span>
                                @elseif($listing->status == 'Reserved')
                                    <span class="badge badge-reserved">RESERVED</span>
                                @elseif($listing->status == 'Maintenance')
                                    <span class="badge badge-maintenance">MAINTENANCE</span>
                                @else
                                    <span class="badge badge-vacant">AVAILABLE</span>
                                @endif
                            @else
                                @if($listing->status == 'Occupied')
                                    <span class="badge badge-occupied">OCCUPIED</span>
                                @elseif($listing->status == 'Reserved')
                                    <span class="badge badge-reserved">RESERVED</span>
                                @else
                                    <span class="badge badge-vacant">VACANT</span>
                                @endif
                            @endif
                        </td>
                        
                        <td class="property-action text-center">
                            @if(isset($listing->business_name))
                                <a href="{{ route('owner.business-spaces.show', $listing->id) }}" class="btn-link">View Details</a>
                            @else
                                <a href="{{ route('owner.apartments.show', $listing->id) }}" class="btn-link">View Details</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="empty-state">
                            <p>No listings yet</p>
                            <span>Click "Add Apartment" or "Add Commercial Space" to create your first property listing.</span>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(isset($listings) && $listings->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $listings->firstItem() ?? 0 }} to {{ $listings->lastItem() ?? 0 }} of {{ $listings->total() ?? 0 }} listings
        </div>
        <div class="pagination-links">
            {{ $listings->links() }}
        </div>
    </div>
    @endif
</div>

<style>
    /* Keep all your existing styles - they are fine */
    body {
        background: url('{{ asset("images/BINALONAN TOWNHALL.jpg") }}') no-repeat center center fixed;
        background-size: cover;
        position: relative;
    }
    
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }
    
    .main-content {
        background: transparent;
        padding: 16px 20px;
    }
    
    .alert {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.5);
        color: #10b981;
    }
    
    .alert-error {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.5);
        color: #ef4444;
    }
    
    .alert-warning {
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.4);
        color: #d97706;
        margin-bottom: 20px;
    }
    
    .alert-warning strong {
        display: block;
        margin-bottom: 5px;
    }
    
    .alert-warning p {
        margin: 5px 0;
        font-size: 13px;
    }
    
    .alert-warning small {
        font-size: 11px;
        opacity: 0.8;
    }
    
    .header-section {
        margin-bottom: 16px;
    }
    
    .add-listing-wrapper {
        margin-bottom: 20px;
    }
    
    .btn-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .btn-add-listing-airbnb {
        background: #B4E662;
        border: none;
        padding: 10px 20px;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        color: #000333;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .btn-add-listing-business {
        background: #00A2FF;
        border: none;
        padding: 10px 20px;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        color: white;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .btn-add-listing-airbnb:hover {
        background: #00A2FF;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 162, 255, 0.3);
    }
    
    .btn-add-listing-business:hover {
        background: #B4E662;
        color: #000333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(180, 230, 98, 0.3);
    }
    
    .btn-revise {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        border-radius: 40px;
        font-size: 11px;
        font-weight: 600;
        color: white;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-top: 6px;
    }
    
    .btn-revise:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-1px);
        text-decoration: none;
        color: white;
        box-shadow: 0 2px 8px rgba(217, 119, 6, 0.4);
    }
    
    .rejected-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .table-container {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow-x: auto;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        margin: 0 auto;
    }
    
    .overflow-x-auto {
        overflow-x: auto;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 750px;
        background: transparent;
    }
    
    .data-table th {
        text-align: left;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.9);
        font-weight: 800;
        font-size: 15px;
        color: #1f2937;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #3b82f6;
    }
    
    .data-table th.text-center {
        text-align: center;
    }
    
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(243, 244, 246, 0.4);
        font-size: 15px;
        color: #1f2937;
        vertical-align: middle;
        background: transparent;
    }
    
    .data-table td.text-center {
        text-align: center;
    }
    
    .table-row:hover td {
        background: rgba(255, 255, 255, 0.3);
    }
    
    .property-title {
        font-weight: 700;
        font-size: 17px;
        color: #111827;
        margin-bottom: 2px;
    }
    
    .property-unit {
        font-size: 13px;
        color: #6b7280;
    }
    
    .property-type {
        color: #4b5563;
        font-size: 15px;
        font-weight: 500;
    }
    
    .property-price {
        color: #111827;
        font-weight: 700;
        font-size: 17px;
    }
    
    .verification-badge {
        display: inline-flex;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
    }
    
    .verification-badge.verified {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    
    .verification-badge.pending {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde047;
    }
    
    .verification-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .verification-hint {
        display: block;
        font-size: 10px;
        color: #6b7280;
        margin-top: 4px;
    }
    
    .rejected-hint {
        color: #dc2626;
        cursor: help;
    }
    
    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .badge-vacant {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .badge-occupied {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .badge-reserved {
        background: #fef9c3;
        color: #854d0e;
    }
    
    .badge-maintenance {
        background: #fed7aa;
        color: #9a3412;
    }
    
    .btn-link {
        color: #3b82f6;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        padding: 4px 8px;
        display: inline-block;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 8px;
    }
    
    .btn-link:hover {
        text-decoration: underline;
        background: rgba(59, 130, 246, 0.15);
    }
    
    .empty-state {
        text-align: center;
        padding: 32px 20px;
    }
    
    .empty-state p {
        font-weight: 500;
        color: #4b5563;
        margin: 0 0 6px 0;
    }
    
    .empty-state span {
        font-size: 13px;
        color: #6b7280;
    }
    
    .pagination-container {
        border-top: 1px solid rgba(243, 244, 246, 0.4);
        padding: 10px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    
    .pagination-info {
        font-size: 12px;
        color: #374151;
    }
    
    .pagination-links {
        display: flex;
        gap: 4px;
    }
    
    .pagination-links a,
    .pagination-links span {
        padding: 4px 8px;
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        font-size: 12px;
    }
    
    .pagination-links a:hover {
        background-color: rgba(255, 255, 255, 0.6);
    }
    
    .pagination-links .active span {
        background-color: #3b82f6;
        color: white;
    }
    
    .text-center {
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .main-content { padding: 12px; }
        .data-table th, .data-table td { padding: 8px 10px; font-size: 13px; }
        .data-table th { font-size: 12px; }
        .property-title { font-size: 15px; }
        .property-unit { font-size: 11px; }
        .property-price { font-size: 15px; }
        .property-type { font-size: 13px; }
        .btn-link { font-size: 12px; }
        .btn-add-listing-airbnb, .btn-add-listing-business { padding: 8px 16px; font-size: 12px; }
        .verification-badge { font-size: 10px; padding: 3px 8px; }
        .badge { font-size: 9px; padding: 3px 8px; }
        .btn-revise { padding: 4px 10px; font-size: 10px; }
        .pagination-container { flex-direction: column; text-align: center; }
        .btn-group { flex-direction: column; width: 100%; }
        .btn-add-listing-airbnb, .btn-add-listing-business { width: 100%; justify-content: center; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const apartmentModal = document.getElementById('addListingModal');
        const businessModal = document.getElementById('addBusinessModal');
        const showApartmentBtn = document.getElementById('showAddModalBtn');
        const showBusinessBtn = document.getElementById('showBusinessModalBtn');
        const closeApartmentModalBtn = document.getElementById('closeModalBtn');
        const closeBusinessModalBtn = document.getElementById('closeBusinessModalBtn');
        const ownerApproved = @json(isset($verificationStatus) && $verificationStatus == 'approved');
        
        // Get property type from PHP
        const propertyType = @json($propertyType ?? 'apartment');
        
        // Ensure modals are hidden initially
        if (apartmentModal) {
            apartmentModal.style.display = 'none';
        }
        if (businessModal) {
            businessModal.style.display = 'none';
        }
        
        function openApartmentModal() {
            if (apartmentModal) apartmentModal.style.display = 'flex';
        }
        
        function closeApartmentModal() {
            if (apartmentModal) apartmentModal.style.display = 'none';
        }
        
        function openBusinessModal() {
            if (businessModal) businessModal.style.display = 'flex';
        }
        
        function closeBusinessModal() {
            if (businessModal) businessModal.style.display = 'none';
        }
        
        // Apartment Button Handler
        if (showApartmentBtn) {
            const newApartmentBtn = showApartmentBtn.cloneNode(true);
            showApartmentBtn.parentNode.replaceChild(newApartmentBtn, showApartmentBtn);
            
            newApartmentBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!ownerApproved) {
                    Swal.fire({
                        title: 'Cannot Proceed Listing',
                        text: 'Your permit is still pending admin approval.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        background: '#ffffff',
                        color: '#333333',
                        iconColor: '#f59e0b'
                    });
                    if (apartmentModal) apartmentModal.style.display = 'none';
                } else {
                    openApartmentModal();
                }
            });
        }
        
        // Business Button Handler (only if button exists)
        if (showBusinessBtn && propertyType == 'both') {
            const newBusinessBtn = showBusinessBtn.cloneNode(true);
            showBusinessBtn.parentNode.replaceChild(newBusinessBtn, showBusinessBtn);
            
            newBusinessBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!ownerApproved) {
                    Swal.fire({
                        title: 'Cannot Proceed Listing',
                        text: 'Your permit is still pending admin approval.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        background: '#ffffff',
                        color: '#333333',
                        iconColor: '#f59e0b'
                    });
                    if (businessModal) businessModal.style.display = 'none';
                } else {
                    openBusinessModal();
                }
            });
        }
        
        // Close apartment modal
        if (closeApartmentModalBtn) {
            closeApartmentModalBtn.addEventListener('click', closeApartmentModal);
        }
        
        // Close business modal
        if (closeBusinessModalBtn) {
            closeBusinessModalBtn.addEventListener('click', closeBusinessModal);
        }
        
        // Close modals when clicking outside
        if (apartmentModal) {
            apartmentModal.addEventListener('click', function(e) {
                if (e.target === apartmentModal) closeApartmentModal();
            });
        }
        
        if (businessModal) {
            businessModal.addEventListener('click', function(e) {
                if (e.target === businessModal) closeBusinessModal();
            });
        }
        
        // Expose functions globally for any other use
        window.openBusinessModal = openBusinessModal;
        window.closeBusinessModal = closeBusinessModal;
    });
</script>
@endsection
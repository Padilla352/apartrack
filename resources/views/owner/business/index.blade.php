@extends('owner.layouts.app')

@section('title', 'Business Listings')
@section('page-title', '')

@section('content')
<div class="header-section">
    <div class="header-left">
        <p class="page-description">Manage your business spaces</p>
    </div>
</div>

<!-- ACCOUNT VERIFICATION STATUS WARNING -->
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

<!-- Add Business Button -->
<div class="add-listing-wrapper">
    <button class="btn-add-listing-business" id="showBusinessModalBtn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 9L12 3L21 9L12 15L3 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 9V19H19V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Add Business Space
    </button>
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

<!-- Business Table -->
<div class="table-container">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>BUSINESS NAME</th>
                    <th>TYPE</th>
                    <th>PRICE</th>
                    <th>VERIFICATION</th>
                    <th>STATUS</th>
                    <th class="text-center">DETAILS</th>
                 </thead>
            </thead>
            <tbody>
                @if(isset($businessSpaces) && $businessSpaces->count() > 0)
                    @foreach($businessSpaces as $business)
                    <tr class="table-row">
                        <td class="property-cell">
                            <div class="property-title">{{ $business->business_name }}</div>
                            <div class="property-unit">{{ $business->unit_number ?? 'N/A' }}</div>
                        </td>
                        <td class="property-type">{{ $business->type ?? 'N/A' }}</td>
                        <td class="property-price">₱{{ number_format($business->monthly_rent ?? 0, 2) }}</td>
                        
                        <td class="property-verification">
                            @php $verifStatus = $business->verification_status ?? 'pending'; @endphp
                            @if($verifStatus == 'approved')
                                <span class="verification-badge verified"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                <small class="verification-hint">✓ Approved by admin</small>
                            @elseif($verifStatus == 'rejected')
                                <div class="rejected-container">
                                    <span class="verification-badge rejected"><i class="fas fa-times-circle"></i> REJECTED</span>
                                    @if($business->rejection_reason)
                                        <small class="verification-hint rejected-hint" title="{{ $business->rejection_reason }}">
                                            <i class="fas fa-info-circle"></i> {{ Str::limit($business->rejection_reason, 60) }}
                                        </small>
                                    @endif
                                    <div class="revise-action mt-2">
                                        <a href="{{ route('owner.business-spaces.revise', $business->id) }}" class="btn-revise">
                                            <i class="fas fa-edit"></i> Revise & Resubmit
                                        </a>
                                    </div>
                                </div>
                            @else
                                <span class="verification-badge pending"><i class="fas fa-clock"></i> PENDING</span>
                                <small class="verification-hint">Awaiting admin verification</small>
                            @endif
                        </td>
                        
                        <td class="property-status">
                            @if($business->status == 'Occupied')
                                <span class="badge badge-occupied">OCCUPIED</span>
                            @elseif($business->status == 'Reserved')
                                <span class="badge badge-reserved">RESERVED</span>
                            @elseif($business->status == 'Maintenance')
                                <span class="badge badge-maintenance">MAINTENANCE</span>
                            @else
                                <span class="badge badge-vacant">VACANT</span>
                            @endif
                        </td>
                        
                        <td class="property-action text-center">
                            <a href="{{ route('owner.business-spaces.show', $business->id) }}" class="btn-link">view details</a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fas fa-store empty-icon"></i>
                            <h5 class="empty-title">No business listings yet</h5>
                            <p class="empty-description">Click "Add Business Space" to create your first business listing.</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    @if(isset($businessSpaces) && $businessSpaces->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Showing {{ $businessSpaces->firstItem() ?? 0 }} to {{ $businessSpaces->lastItem() ?? 0 }} of {{ $businessSpaces->total() ?? 0 }} listings
        </div>
        <div class="pagination-links">
            {{ $businessSpaces->links() }}
        </div>
    </div>
    @endif
</div>

<style>
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
    
    .add-listing-wrapper {
        margin-bottom: 20px;
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
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .btn-add-listing-business:hover {
        background: #B4E662;
        color: #000333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(180, 230, 98, 0.3);
    }
    
    .btn-add-listing-business svg {
        stroke: white;
    }
    
    .btn-add-listing-business:hover svg {
        stroke: #000333;
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
    
    .table-container {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow-x: auto;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
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
    
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(243, 244, 246, 0.4);
        font-size: 15px;
        color: #1f2937;
        vertical-align: middle;
        background: transparent;
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
    
    .verification-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
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
    
    .empty-icon {
        font-size: 40px;
        color: #9ca3af;
        margin-bottom: 10px;
        display: block;
    }
    
    .empty-title {
        font-size: 16px;
        font-weight: 500;
        color: #4b5563;
        margin: 0 0 6px 0;
    }
    
    .empty-description {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
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
    }
    
    .pagination-info {
        font-size: 12px;
        color: #374151;
    }
    
    .pagination-links {
        display: flex;
        gap: 4px;
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
        .btn-add-listing-business { padding: 8px 16px; font-size: 12px; }
        .verification-badge { font-size: 10px; padding: 3px 8px; }
        .badge { font-size: 9px; padding: 3px 8px; }
        .btn-revise { padding: 4px 10px; font-size: 10px; }
        .pagination-container { flex-direction: column; text-align: center; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const businessModal = document.getElementById('addBusinessModal');
        const showBusinessBtn = document.getElementById('showBusinessModalBtn');
        const closeBusinessModalBtn = document.getElementById('closeBusinessModalBtn');
        const ownerApproved = @json(isset($verificationStatus) && $verificationStatus == 'approved');
        
        function openBusinessModal() {
            if (businessModal) businessModal.style.display = 'flex';
        }
        
        function closeBusinessModal() {
            if (businessModal) businessModal.style.display = 'none';
        }
        
        if (showBusinessBtn) {
            const newBusinessBtn = showBusinessBtn.cloneNode(true);
            showBusinessBtn.parentNode.replaceChild(newBusinessBtn, showBusinessBtn);
            
            newBusinessBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!ownerApproved) {
                    Swal.fire({
                        title: 'Cannot Proceed Listing',
                        text: 'Your business permit is still pending admin approval.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    if (businessModal) businessModal.style.display = 'none';
                } else {
                    openBusinessModal();
                }
            });
        }
        
        if (closeBusinessModalBtn) {
            closeBusinessModalBtn.addEventListener('click', closeBusinessModal);
        }
        
        if (businessModal) {
            businessModal.addEventListener('click', function(e) {
                if (e.target === businessModal) closeBusinessModal();
            });
        }
    });
</script>
@endsection
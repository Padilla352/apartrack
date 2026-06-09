@extends('owner.layouts.app')

@section('title', 'Tenant Details')

@section('page-title', 'tenant details')

@section('content')
<div class="tenant-details-container">
    <!-- Back Button -->
    <div class="back-nav">
        <a href="{{ route('owner.tenants.index') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Tenants
        </a>
        <div class="action-buttons">
            <a href="{{ route('owner.tenants.edit', $tenant->id) }}" class="btn-edit">
                <i class="fas fa-edit"></i> Edit Tenant
            </a>
            <button type="button" class="btn-delete" onclick="confirmDelete('delete-form-{{ $tenant->id }}', 'Are you sure you want to delete this tenant?')">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>

    <!-- Tenant Profile Header - Grey Background -->
    <div class="profile-header-card">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-info">
            <h1 class="profile-name">{{ $tenant->first_name ?? '' }} {{ $tenant->last_name ?? '' }}</h1>
            <div class="profile-meta">
                <span class="meta-badge">
                    <i class="fas fa-envelope"></i> {{ $tenant->email ?? 'No email' }}
                </span>
                <span class="meta-badge">
                    <i class="fas fa-phone-alt"></i> {{ $tenant->phone ?? 'N/A' }}
                </span>
                <span class="status-badge status-{{ strtolower($tenant->status ?? 'active') }}">
                    {{ $tenant->status ?? 'Active' }}
                </span>
            </div>
        </div>
        <div class="profile-actions">
            <span class="registered-date">
                <i class="fas fa-calendar-alt"></i> Registered: {{ isset($tenant->created_at) ? $tenant->created_at->format('M d, Y h:i A') : 'N/A' }}
            </span>
        </div>
    </div>

    <!-- Details Grid - 2 Columns -->
    <div class="details-grid">
        <!-- Personal Information -->
        <div class="info-card">
            <div class="card-header">
                <i class="fas fa-user info-icon"></i>
                <h3 class="card-title">Personal Information</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value">{{ $tenant->first_name ?? '' }} {{ $tenant->last_name ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $tenant->email ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $tenant->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alternate Phone:</span>
                    <span class="info-value">{{ $tenant->alternate_phone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ strtolower($tenant->status ?? 'active') }}">
                        {{ $tenant->status ?? 'Active' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="info-card">
            <div class="card-header">
                <i class="fas fa-phone-alt info-icon"></i>
                <h3 class="card-title">Emergency Contact</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $tenant->emergency_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Relationship:</span>
                    <span class="info-value">{{ $tenant->emergency_relationship ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $tenant->emergency_phone ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Apartment Information -->
        <div class="info-card">
            <div class="card-header">
                <i class="fas fa-building info-icon"></i>
                <h3 class="card-title">Apartment Information</h3>
            </div>
            <div class="card-body">
                @if(isset($tenant->apartment) && $tenant->apartment)
                    <div class="info-row">
                        <span class="info-label">Unit Number:</span>
                        <span class="info-value">{{ $tenant->apartment->unit_number ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Property Type:</span>
                        <span class="info-value">{{ $tenant->apartment->type ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Property Name:</span>
                        <span class="info-value">{{ $tenant->apartment->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Monthly Rent:</span>
                        <span class="info-value">₱{{ number_format($tenant->apartment->monthly_rent ?? 0, 2) }}</span>
                    </div>
                @else
                    <div class="empty-state-small">
                        <i class="fas fa-home"></i>
                        <p>No apartment assigned</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Address Information -->
        <div class="info-card">
            <div class="card-header">
                <i class="fas fa-map-marker-alt info-icon"></i>
                <h3 class="card-title">Address Information</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Street Address:</span>
                    <span class="info-value">{{ $tenant->address ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Barangay:</span>
                    <span class="info-value">{{ $tenant->barangay->name ?? $tenant->barangay_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Lease Information -->
        <div class="info-card">
            <div class="card-header">
                <i class="fas fa-file-contract info-icon"></i>
                <h3 class="card-title">Lease Information</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Move-in Date:</span>
                    <span class="info-value">{{ isset($tenant->move_in_date) ? date('M d, Y', strtotime($tenant->move_in_date)) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lease End Date:</span>
                    <span class="info-value">{{ isset($tenant->lease_end_date) ? date('M d, Y', strtotime($tenant->lease_end_date)) : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Security Deposit:</span>
                    <span class="info-value">₱{{ number_format($tenant->security_deposit ?? 0, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Monthly Rent:</span>
                    <span class="info-value">₱{{ number_format($tenant->monthly_rent ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="info-card">
            <div class="card-header">
                <i class="fas fa-sticky-note info-icon"></i>
                <h3 class="card-title">Additional Notes</h3>
            </div>
            <div class="card-body">
                <div class="notes-content">
                    {{ $tenant->notes ?? 'No additional notes' }}
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="system-info-card">
        <div class="card-header">
            <i class="fas fa-info-circle info-icon"></i>
            <h3 class="card-title">System Information</h3>
        </div>
        <div class="card-body">
            <div class="system-info-grid">
                <div class="system-info-item">
                    <span class="system-label">Registered On:</span>
                    <span class="system-value">{{ isset($tenant->created_at) ? $tenant->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                </div>
                <div class="system-info-item">
                    <span class="system-label">Last Updated:</span>
                    <span class="system-value">{{ isset($tenant->updated_at) ? $tenant->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form-{{ $tenant->id }}" action="{{ route('owner.tenants.destroy', $tenant->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
    /* Tenant Details Page - APARTRACK Design System with Grey Background */
    .tenant-details-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    /* Back Navigation */
    .back-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #007BFF;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .back-link:hover {
        color: #0056b3;
        transform: translateX(-4px);
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
    }
    
    .btn-edit {
        background: #FFC107;
        color: #000333;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-edit:hover {
        background: #FFB300;
        transform: translateY(-2px);
        color: #000333;
    }
    
    .btn-delete {
        background: #D90404;
        color: white;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-delete:hover {
        background: #B00303;
        transform: translateY(-2px);
    }
    
    /* Profile Header Card - Grey Background */
    .profile-header-card {
        background: #F0F0F0;
        border-radius: 24px;
        padding: 32px;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
        border: 1px solid #E0E0E0;
    }
    
    .profile-avatar i {
        font-size: 80px;
        color: #007BFF;
        opacity: 0.8;
    }
    
    .profile-info {
        flex: 1;
    }
    
    .profile-name {
        font-size: 28px;
        font-weight: 700;
        color: #000333;
        margin: 0 0 8px 0;
    }
    
    .profile-meta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    
    .meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        color: #333;
        border: 1px solid #E0E0E0;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-active {
        background: #28a745;
        color: white;
    }
    
    .status-inactive {
        background: #6c757d;
        color: white;
    }
    
    .status-pending {
        background: #ffc107;
        color: #000333;
    }
    
    .profile-actions {
        text-align: right;
    }
    
    .registered-date {
        font-size: 12px;
        color: #6B7280;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    /* Details Grid */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }
    
    /* Info Card */
    .info-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        background: #F9FAFB;
        padding: 16px 20px;
        border-bottom: 1px solid #E0E0E0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .info-icon {
        font-size: 20px;
        color: #007BFF;
    }
    
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #000333;
        margin: 0;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #F0F0F0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        width: 140px;
        font-size: 13px;
        font-weight: 600;
        color: #6B7280;
    }
    
    .info-value {
        flex: 1;
        font-size: 14px;
        color: #333333;
        font-weight: 500;
    }
    
    /* Empty State Small */
    .empty-state-small {
        text-align: center;
        padding: 32px 20px;
    }
    
    .empty-state-small i {
        font-size: 48px;
        color: #D1D5DB;
        margin-bottom: 12px;
        display: block;
    }
    
    .empty-state-small p {
        color: #6B7280;
        margin: 0;
        font-size: 14px;
    }
    
    /* Notes Content */
    .notes-content {
        padding: 8px 0;
        font-size: 14px;
        color: #333333;
        line-height: 1.6;
        font-style: italic;
    }
    
    /* System Info Card */
    .system-info-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        overflow: hidden;
    }
    
    .system-info-grid {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }
    
    .system-info-item {
        flex: 1;
    }
    
    .system-label {
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 4px;
    }
    
    .system-value {
        font-size: 14px;
        color: #333333;
        font-weight: 500;
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .profile-header-card {
            background: #F0F0F0;
            border-color: #E0E0E0;
        }
        
        .profile-name {
            color: #000333;
        }
        
        .meta-badge {
            background: white;
            color: #333;
            border-color: #E0E0E0;
        }
        
        .registered-date {
            color: #6B7280;
        }
        
        .info-card {
            background: white;
            border-color: #E0E0E0;
        }
        
        .card-header {
            background: #F9FAFB;
            border-bottom-color: #E0E0E0;
        }
        
        .card-title {
            color: #000333;
        }
        
        .info-row {
            border-bottom-color: #F0F0F0;
        }
        
        .info-label {
            color: #6B7280;
        }
        
        .info-value {
            color: #333333;
        }
        
        .notes-content {
            color: #333333;
        }
        
        .system-info-card {
            background: white;
            border-color: #E0E0E0;
        }
        
        .system-label {
            color: #6B7280;
        }
        
        .system-value {
            color: #333333;
        }
        
        .empty-state-small i {
            color: #D1D5DB;
        }
        
        .empty-state-small p {
            color: #6B7280;
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .profile-header-card {
            padding: 24px;
            text-align: center;
            justify-content: center;
        }
        
        .profile-name {
            font-size: 22px;
        }
        
        .profile-meta {
            justify-content: center;
        }
        
        .profile-actions {
            text-align: center;
            width: 100%;
        }
        
        .info-row {
            flex-direction: column;
        }
        
        .info-label {
            width: 100%;
            margin-bottom: 4px;
        }
        
        .back-nav {
            flex-direction: column;
            align-items: stretch;
        }
        
        .action-buttons {
            justify-content: center;
        }
        
        .system-info-grid {
            flex-direction: column;
            gap: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .profile-avatar i {
            font-size: 60px;
        }
        
        .profile-name {
            font-size: 18px;
        }
        
        .meta-badge {
            font-size: 11px;
        }
        
        .card-header {
            padding: 12px 16px;
        }
        
        .card-body {
            padding: 16px;
        }
        
        .info-label {
            font-size: 12px;
        }
        
        .info-value {
            font-size: 13px;
        }
    }
    
    /* Print Styles */
    @media print {
        .back-nav,
        .action-buttons,
        .profile-actions {
            display: none;
        }
        
        .profile-header-card {
            background: #F0F0F0;
            border: 1px solid #ddd;
        }
        
        .info-card,
        .system-info-card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .status-badge {
            print-color-adjust: exact;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    function confirmDelete(formId, message) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D90404',
            cancelButtonColor: '#333333',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush
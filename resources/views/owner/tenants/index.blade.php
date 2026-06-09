@extends('owner.layouts.app')

@section('title', 'Tenant Management')

@section('page-title', 'tenant management')

@section('content')
<div class="tenants-container">
    <!-- Statistics Cards - Matching APARTRACK Design -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-circle">
                <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="stat-label">Total Tenants</div>
            <div class="stat-trend">
                <i class="fas fa-users"></i> All registered tenants
            </div>
            <div class="click-hint">View all tenants</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number">{{ $stats['active'] ?? 0 }}</div>
            </div>
            <div class="stat-label">Active Tenants</div>
            <div class="stat-trend">
                <i class="fas fa-user-check trend-up"></i> Currently active
            </div>
            <div class="click-hint">View active tenants</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="stat-number">{{ $stats['expiring_soon'] ?? 0 }}</div>
            </div>
            <div class="stat-label">Expiring Soon</div>
            <div class="stat-trend">
                <i class="fas fa-calendar-alt"></i> Next 30 days
            </div>
            <div class="click-hint">View expiring leases</div>
        </div>

        <div class="stat-card">
            <div class="stat-circle" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <div class="stat-number">{{ $stats['pending'] ?? 0 }}</div>
            </div>
            <div class="stat-label">Pending</div>
            <div class="stat-trend">
                <i class="fas fa-clock"></i> Awaiting approval
            </div>
            <div class="click-hint">View pending requests</div>
        </div>
    </div>

    <!-- Add Listing Button Style (matches your design) -->
    <div class="add-listing-container">
        <a href="{{ route('owner.tenants.create') }}" class="btn-add-listing-airbnb">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Add New Tenant
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="filter-section">
        <form method="GET" action="{{ route('owner.tenants.index') }}" class="filter-form">
            <div class="filter-group">
                <i class="fas fa-search filter-icon"></i>
                <input type="text" 
                       name="search" 
                       class="filter-input" 
                       placeholder="Search by name, email, or phone..." 
                       value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Evicted" {{ request('status') == 'Evicted' ? 'selected' : '' }}>Evicted</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="apartment_id" class="filter-select">
                    <option value="">All Apartments</option>
                    @if(isset($apartments) && $apartments->count() > 0)
                        @foreach($apartments as $apartment)
                            <option value="{{ $apartment->id }}" {{ request('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                {{ $apartment->unit_number }} - {{ $apartment->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="{{ route('owner.tenants.index') }}" class="btn-reset">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </form>
    </div>

    <!-- Tenants Table -->
    <div class="data-table-container">
        <div class="table-header">
            <h3 class="table-title">Tenants List</h3>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name & Email</th>
                        <th>Contact</th>
                        <th>Apartment</th>
                        <th>Move-in Date</th>
                        <th>Lease End</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($tenants) && $tenants->count() > 0)
                        @foreach($tenants as $index => $tenant)
                            <tr>
                                <td class="text-center">{{ $tenants->firstItem() + $index }}</td>
                                <td>
                                    <div class="tenant-info">
                                        <strong>{{ $tenant->first_name ?? '' }} {{ $tenant->last_name ?? '' }}</strong>
                                        <br>
                                        <small class="email-text">{{ $tenant->email ?? 'No email' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-phone-alt contact-icon"></i> {{ $tenant->phone ?? 'N/A' }}
                                    @if(!empty($tenant->alternate_phone))
                                        <br>
                                        <small class="alt-phone">
                                            <i class="fas fa-phone-alt contact-icon"></i> {{ $tenant->alternate_phone }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($tenant->apartment) && $tenant->apartment)
                                        <span class="apartment-badge">{{ $tenant->apartment->unit_number ?? 'N/A' }}</span>
                                        <br>
                                        <small class="apartment-type">{{ $tenant->apartment->type ?? '' }}</small>
                                    @else
                                        <span class="not-assigned">Not Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($tenant->move_in_date))
                                        {{ date('M d, Y', strtotime($tenant->move_in_date)) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($tenant->lease_end_date) && $tenant->lease_end_date)
                                        {{ date('M d, Y', strtotime($tenant->lease_end_date)) }}
                                        @php
                                            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($tenant->lease_end_date), false);
                                        @endphp
                                        @if($daysLeft <= 30 && $daysLeft > 0)
                                            <span class="days-left">{{ ceil($daysLeft) }} days left</span>
                                        @elseif($daysLeft <= 0)
                                            <span class="expired-badge">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($tenant->status ?? '') {
                                            'Active' => 'status-active',
                                            'Pending' => 'status-pending',
                                            'Evicted' => 'status-evicted',
                                            default => 'status-inactive'
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $tenant->status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('owner.tenants.show', $tenant->id) }}" 
                                           class="action-btn btn-view" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('owner.tenants.edit', $tenant->id) }}" 
                                           class="action-btn btn-edit" 
                                           title="Edit Tenant">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="action-btn btn-delete" 
                                                onclick="confirmDelete('delete-form-{{ $tenant->id }}', 'Are you sure you want to delete tenant: {{ $tenant->first_name ?? '' }} {{ $tenant->last_name ?? '' }}?')"
                                                title="Delete Tenant">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $tenant->id }}" 
                                          action="{{ route('owner.tenants.destroy', $tenant->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-users empty-icon"></i>
                                <h4>No tenants found</h4>
                                <p>Click "Add New Tenant" to get started.</p>
                                <a href="{{ route('owner.tenants.create') }}" class="btn-add-listing-airbnb" style="display: inline-flex;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 5v14M5 12h14"/>
                                    </svg>
                                    Add Your First Tenant
                                </a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if(isset($tenants) && $tenants->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() ?? 0 }} tenants
                </div>
                <div class="pagination-links">
                    {{ $tenants->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Tenants Index Page - APARTRACK Design System */
    .tenants-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* Stats Grid - Matching your dashboard */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: #F0F0F0;
        border-radius: 16px;
        padding: 24px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #E0E0E0;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    .stat-circle {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: white;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 13px;
        color: #333333;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .stat-trend {
        margin-top: 8px;
        font-size: 11px;
        color: #666;
    }
    
    .trend-up {
        color: #B4E662;
    }
    
    .click-hint {
        font-size: 10px;
        color: #007BFF;
        margin-top: 10px;
        opacity: 0;
        transform: translateY(5px);
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .stat-card:hover .click-hint {
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Add Listing Button - Matches your design */
    .add-listing-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 24px;
    }
    
    .btn-add-listing-airbnb {
        background: #B4E662;
        border: none;
        padding: 12px 24px;
        border-radius: 40px;
        font-size: 14px;
        font-weight: 600;
        color: #000333;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        text-decoration: none;
    }
    
    .btn-add-listing-airbnb:hover {
        background: #00A2FF;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 162, 255, 0.3);
        text-decoration: none;
    }
    
    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .filter-form {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .filter-group {
        flex: 1;
        min-width: 180px;
        position: relative;
    }
    
    .filter-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 14px;
    }
    
    .filter-input {
        width: 100%;
        padding: 12px 16px 12px 40px;
        border: 1px solid #E0E0E0;
        border-radius: 12px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
    }
    
    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #007BFF;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
    }
    
    .filter-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #E0E0E0;
        border-radius: 12px;
        font-size: 14px;
        font-family: inherit;
        background: white;
        cursor: pointer;
    }
    
    .btn-filter {
        background: #007BFF;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-filter:hover {
        background: #0056b3;
        transform: translateY(-1px);
    }
    
    .btn-reset {
        background: #F0F0F0;
        color: #333;
        border: 1px solid #E0E0E0;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-reset:hover {
        border-color: #D90404;
        background: #D90404;
        color: white;
        text-decoration: none;
    }
    
    /* Data Table */
    .data-table-container {
        background: white;
        border-radius: 20px;
        border: 1px solid #E0E0E0;
        overflow: hidden;
    }
    
    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid #E0E0E0;
    }
    
    .table-title {
        font-size: 18px;
        font-weight: 700;
        color: #000333;
        margin: 0;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        text-align: left;
        padding: 16px 20px;
        background: #F9FAFB;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #F3F4F6;
        font-size: 14px;
        color: #374151;
        vertical-align: middle;
    }
    
    .data-table tr:hover td {
        background: #F9FAFB;
    }
    
    .tenant-info strong {
        color: #000333;
    }
    
    .email-text {
        color: #6B7280;
        font-size: 12px;
    }
    
    .contact-icon {
        color: #9CA3AF;
        margin-right: 6px;
        font-size: 12px;
    }
    
    .alt-phone {
        color: #6B7280;
        font-size: 11px;
    }
    
    .apartment-badge {
        background: #E0F2FE;
        color: #0284C7;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .apartment-type {
        color: #6B7280;
        font-size: 11px;
    }
    
    .not-assigned {
        color: #9CA3AF;
        font-style: italic;
    }
    
    .text-muted {
        color: #6B7280;
    }
    
    .days-left {
        display: inline-block;
        margin-top: 4px;
        font-size: 10px;
        color: #F59E0B;
        font-weight: 600;
    }
    
    .expired-badge {
        display: inline-block;
        margin-top: 4px;
        font-size: 10px;
        color: #D90404;
        font-weight: 600;
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-active {
        background: #D1FAE5;
        color: #065F46;
    }
    
    .status-pending {
        background: #FEF3C7;
        color: #92400E;
    }
    
    .status-evicted {
        background: #FEE2E2;
        color: #991B1B;
    }
    
    .status-inactive {
        background: #F3F4F6;
        color: #374151;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }
    
    .btn-view {
        background: #E0F2FE;
        color: #0284C7;
    }
    
    .btn-view:hover {
        background: #BAE6FD;
        color: #0369A1;
        transform: translateY(-2px);
    }
    
    .btn-edit {
        background: #FEF3C7;
        color: #D97706;
    }
    
    .btn-edit:hover {
        background: #FDE68A;
        color: #B45309;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: #FEE2E2;
        color: #DC2626;
    }
    
    .btn-delete:hover {
        background: #FECACA;
        color: #B91C1C;
        transform: translateY(-2px);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 24px;
    }
    
    .empty-icon {
        font-size: 64px;
        color: #D1D5DB;
        margin-bottom: 16px;
    }
    
    .empty-state h4 {
        font-size: 18px;
        font-weight: 600;
        color: #000333;
        margin: 0 0 8px 0;
    }
    
    .empty-state p {
        color: #6B7280;
        margin-bottom: 24px;
    }
    
    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-top: 1px solid #E0E0E0;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .pagination-info {
        font-size: 14px;
        color: #6B7280;
    }
    
    .pagination-links {
        display: flex;
        gap: 4px;
    }
    
    .pagination-links a,
    .pagination-links span {
        padding: 8px 12px;
        border-radius: 8px;
        color: #4B5563;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .pagination-links a:hover {
        background: #F3F4F6;
    }
    
    .pagination-links .active span {
        background: #007BFF;
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-form {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
        
        .btn-filter,
        .btn-reset {
            width: 100%;
            justify-content: center;
        }
        
        .add-listing-container {
            justify-content: stretch;
        }
        
        .btn-add-listing-airbnb {
            width: 100%;
            justify-content: center;
        }
        
        .pagination-wrapper {
            flex-direction: column;
            text-align: center;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
        }
        
        .action-buttons {
            flex-wrap: wrap;
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
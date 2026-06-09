@extends('layouts.admin')

@section('content')
<div class="verification-container">
    <div class="verification-content-wrapper">
        
        {{-- Header Section --}}
        <div class="verification-header">
            <div class="header-left">
                <h2 class="page-title">Listing Approval</h2>
                <p class="page-subtitle">Review and manage apartment listing requests</p>
            </div>
            
            <div class="header-right">
                <div class="pending-badge">
                    <span class="pending-dot"></span>
                    <span id="pending-badge-count">{{ $pendingApartments->total() }} Pending Requests</span>
                </div>

                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search listings..." class="search-input">
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="stats-grid">
            <div class="stat-card-sm pending-card">
                <div class="stat-icon-sm icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Pending Review</span>
                    <h2 id="stat-pending" class="stat-value-sm pending-value">{{ $pendingApartments->total() }}</h2>
                </div>
            </div>
            
            <div class="stat-card-sm approved-card">
                <div class="stat-icon-sm icon-approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Approved This Month</span>
                    <h2 id="stat-approved" class="stat-value-sm approved-value">0</h2>
                </div>
            </div>
            
            <div class="stat-card-sm rejected-card">
                <div class="stat-icon-sm icon-rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Rejected</span>
                    <h2 id="stat-rejected" class="stat-value-sm rejected-value">0</h2>
                </div>
            </div>
        </div>

        {{-- Table Header --}}
        <div class="table-header">
            <div class="header-cell">LISTING / OWNER</div>
            <div class="header-cell">DATE SUBMITTED</div>
            <div class="header-cell">PERMIT NUMBER</div>
            <div class="header-cell">PERMIT STATUS</div>
            <div class="header-cell">ACTIONS</div>
        </div>

        {{-- Applicants List --}}
        <div id="verification-queue" class="verification-queue">
            @if($pendingApartments->count() > 0)
                @foreach($pendingApartments as $apartment)
                <div id="row-{{ $apartment->id }}" class="applicant-card" data-status="pending">
                    <div class="applicant-card-inner">
                        {{-- Column 1: Listing & Owner Info --}}
                        <div class="applicant-info">
                            <div class="applicant-avatar">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="applicant-details">
                                <div class="applicant-name">{{ $apartment->name }}</div>
                                <div class="applicant-email">
                                    <i class="fas fa-user"></i> {{ $apartment->owner_name }}
                                </div>
                            </div>
                        </div>

                        {{-- Column 2: Date Submitted --}}
                        <div class="date-info">
                            <div class="date-main">{{ isset($apartment->created_at) ? date('M d, Y', strtotime($apartment->created_at)) : 'N/A' }}</div>
                            <div class="date-time">{{ isset($apartment->created_at) ? date('h:i A', strtotime($apartment->created_at)) : 'N/A' }}</div>
                        </div>

                        {{-- Column 3: Permit Number --}}
                        <div class="permit-info">
                            <span class="permit-number">{{ $apartment->permit_number ?? 'N/A' }}</span>
                        </div>

                        {{-- Column 4: Permit Status --}}
                        <div class="permit-status-info">
                            @if($apartment->permit_status == 'active')
                                <span class="verify-badge verified">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @elseif($apartment->permit_status == 'used')
                                <span class="verify-badge used">
                                    <i class="fas fa-check-double"></i> Used
                                </span>
                            @else
                                <span class="verify-badge not-verified">
                                    <i class="fas fa-times-circle"></i> Not in System
                                </span>
                            @endif
                        </div>

                        {{-- Column 5: Actions --}}
                        <div class="action-buttons-wrapper">
                            <button type="button" onclick="openDetailsModal({{ $apartment->id }})" class="action-btn view-btn">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button type="button" onclick="handleVerification('{{ $apartment->id }}', 'approve')" class="action-btn approve-btn">
                                <i class="fas fa-check-circle"></i> Approve
                            </button>
                            <button type="button" onclick="openRejectModalFromRow({{ $apartment->id }})" class="action-btn reject-btn">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- VIEW DETAILS MODAL - CENTERED POPUP -->
                <div id="detailsModal{{ $apartment->id }}" class="modal-overlay" style="display: none;">
                    <div class="modal-container">
                        <div class="modal-header">
                            <h3 class="modal-title">{{ $apartment->name }}</h3>
                            <button type="button" class="modal-close" onclick="closeDetailsModal({{ $apartment->id }})">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="details-section">
                                        <h6><i class="fas fa-building"></i> Property Information</h6>
                                        <table class="details-table">
                                            <tr><td class="label">Unit Number:</td><td class="value">{{ $apartment->unit_number ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Property Type:</td><td class="value">{{ $apartment->type ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Bedrooms:</td><td class="value">{{ $apartment->bedrooms ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Bathrooms:</td><td class="value">{{ $apartment->bathrooms ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Floor Area:</td><td class="value">{{ $apartment->floor_area_sqm ?? 'N/A' }} sqm</td></tr>
                                            <tr><td class="label">Monthly Rent:</td><td class="value price">₱{{ number_format($apartment->monthly_rent ?? 0, 2) }}</td></tr>
                                            <tr><td class="label">Barangay:</td><td class="value">{{ $apartment->barangay_name ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Address:</td><td class="value">{{ $apartment->address ?? 'N/A' }}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="details-section">
                                        <h6><i class="fas fa-user-circle"></i> Owner Information</h6>
                                        <table class="details-table">
                                            <tr><td class="label">Name:</td><td class="value">{{ $apartment->owner_name ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Email:</td><td class="value">{{ $apartment->owner_email ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Phone:</td><td class="value">{{ $apartment->owner_phone ?? 'N/A' }}</td></tr>
                                        </table>
                                    </div>
                                    
                                    <div class="details-section">
                                        <h6><i class="fas fa-id-card"></i> Permit Information</h6>
                                        <table class="details-table">
                                            <tr><td class="label">Permit Number:</td><td class="value"><code>{{ $apartment->permit_number ?? 'N/A' }}</code></td></tr>
                                            <tr>
                                                <td class="label">Permit Status:</td>
                                                <td class="value">
                                                    @if(($apartment->permit_status ?? '') == 'active')
                                                        <span class="badge-status active">✓ ACTIVE</span>
                                                    @elseif(($apartment->permit_status ?? '') == 'used')
                                                        <span class="badge-status used">⟳ USED</span>
                                                    @else
                                                        <span class="badge-status not-found">✗ NOT FOUND</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <div class="details-section">
                                        <h6><i class="fas fa-align-left"></i> Description</h6>
                                        <p class="description-text">{{ $apartment->description ?? 'No description provided.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeDetailsModal({{ $apartment->id }})">Close</button>
                            <button type="button" class="btn-approve" onclick="handleVerification('{{ $apartment->id }}', 'approve')">
                                <i class="fas fa-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn-reject-modal" onclick="handleVerificationFromModal('{{ $apartment->id }}', 'reject')">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- REJECT MODAL - CENTERED POPUP -->
                <div id="rejectModal{{ $apartment->id }}" class="modal-overlay" style="display: none;">
                    <div class="modal-container" style="max-width: 500px;">
                        <div class="modal-header">
                            <h3 class="modal-title">Reject Listing</h3>
                            <button type="button" class="modal-close" onclick="closeRejectModal({{ $apartment->id }})">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Rejection Reason <span class="text-danger">*</span></label>
                                <textarea id="rejection_reason_{{ $apartment->id }}" class="form-control" rows="4" placeholder="Please provide a reason for rejection..."></textarea>
                                <small class="form-text">This reason will be shown to the owner.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeRejectModal({{ $apartment->id }})">Cancel</button>
                            <button type="button" class="btn-reject-modal" onclick="handleVerification('{{ $apartment->id }}', 'reject')">
                                Confirm Rejection
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="pagination-container">
                    {{ $pendingApartments->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-check-double"></i>
                    <p>Queue Empty</p>
                    <span>No pending verification requests</span>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openDetailsModal(id) {
        document.getElementById('detailsModal' + id).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeDetailsModal(id) {
        document.getElementById('detailsModal' + id).style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function openRejectModalFromRow(id) {
        document.getElementById('rejectModal' + id).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function openRejectModalFromModal(id) {
        closeDetailsModal(id);
        document.getElementById('rejectModal' + id).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal(id) {
        document.getElementById('rejectModal' + id).style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function handleVerificationFromModal(id, status) {
        closeDetailsModal(id);
        handleVerification(id, status);
    }
    
    function handleVerification(id, status) {
        const actionLabel = status === 'approve' ? 'Approve' : 'Reject';
        const isApprove = status === 'approve';
        
        // Close any open modals
        if (typeof closeDetailsModal === 'function') closeDetailsModal(id);
        if (typeof closeRejectModal === 'function') closeRejectModal(id);
        
        Swal.fire({
            title: `<span class="swal-title">${actionLabel} Listing?</span>`,
            html: `<span class="swal-text">Are you sure you want to ${status} this apartment listing?</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#f5b81b' : '#ef4444',
            cancelButtonColor: '#334155',
            confirmButtonText: `<span class="swal-btn-text">Yes, ${status}</span>`,
            cancelButtonText: `<span class="swal-btn-text-cancel">Cancel</span>`,
            background: '#0f1115',
            color: '#e2e8f0'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: `Please wait while we ${status} the listing.`,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: '#0f1115',
                    color: '#e2e8f0'
                });
                
                const row = document.getElementById(`row-${id}`);
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                }
                
                // Prepare data
                let url = `/admin/apartments/${status}/${id}`;
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                
                if (!isApprove) {
                    const reasonTextarea = document.getElementById(`rejection_reason_${id}`);
                    const reason = reasonTextarea ? reasonTextarea.value : '';
                    
                    if (!reason) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Reason Required',
                            text: 'Please provide a reason for rejection.',
                            background: '#0f1115',
                            color: '#e2e8f0'
                        });
                        if (row) {
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                        }
                        return;
                    }
                    formData.append('rejection_reason', reason);
                }
                
                // Make AJAX request
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        // Animate removal
                        if (row) {
                            row.style.transform = 'translateX(50px)';
                            row.style.opacity = '0';
                        }
                        
                        setTimeout(() => {
                            if (row) row.remove();
                            
                            // Update counters
                            const pendingCountEl = document.getElementById('stat-pending');
                            if (pendingCountEl) {
                                let newCount = Math.max(0, (parseInt(pendingCountEl.innerText) || 0) - 1);
                                pendingCountEl.innerText = newCount;
                                const badgeCount = document.getElementById('pending-badge-count');
                                if (badgeCount) badgeCount.innerText = `${newCount} Pending Requests`;
                            }
                            
                            if (isApprove) {
                                const approvedEl = document.getElementById('stat-approved');
                                if (approvedEl) {
                                    let current = parseInt(approvedEl.innerText) || 0;
                                    approvedEl.innerText = current + 1;
                                }
                            } else {
                                const rejectedEl = document.getElementById('stat-rejected');
                                if (rejectedEl) {
                                    let current = parseInt(rejectedEl.innerText) || 0;
                                    rejectedEl.innerText = current + 1;
                                }
                            }
                            
                            // Reload if no rows left
                            if (document.querySelectorAll('.applicant-card').length === 0) {
                                setTimeout(() => location.reload(), 500);
                            }
                        }, 350);
                        
                        // Show success
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || `Successfully ${status}d!`,
                            showConfirmButton: false,
                            timer: 3000,
                            background: '#0f1115',
                            color: '#e2e8f0'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Something went wrong. Please try again.',
                            background: '#0f1115',
                            color: '#e2e8f0'
                        });
                        if (row) {
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to process request. Please check your connection.',
                        background: '#0f1115',
                        color: '#e2e8f0'
                    });
                    if (row) {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                    }
                });
            }
        });
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            let value = this.value.toLowerCase();
            let visibleCount = 0;
            
            document.querySelectorAll('.applicant-card').forEach(card => {
                let text = card.innerText.toLowerCase();
                const isVisible = text.includes(value);
                card.style.display = isVisible ? 'flex' : 'none';
                if (isVisible) visibleCount++;
            });
            
            const emptyState = document.querySelector('.empty-state');
            if (emptyState && visibleCount === 0 && document.querySelectorAll('.applicant-card').length > 0) {
                if (!document.querySelector('.no-results')) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results';
                    noResults.innerHTML = '<i class="fas fa-search"></i><p>No matching results</p><span>Try a different search term</span>';
                    document.getElementById('verification-queue').appendChild(noResults);
                }
            } else {
                const noResults = document.querySelector('.no-results');
                if (noResults) noResults.remove();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card-sm');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-in');
        });
        
        console.log('%c🏢 APARTrack Listing Approval | Ready', 'color: #f5b81b; font-size: 14px; font-weight: bold');
    });
</script>

<style>
/* ========== GLOW DARK THEME - LISTING APPROVAL ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.verification-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1.5rem;
}

.verification-content-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Section */
.verification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.header-left .page-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0 0 0.25rem 0;
}

.page-subtitle {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Pending Badge */
.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(245, 184, 27, 0.1);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 40px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #f5b81b;
}

.pending-dot {
    width: 0.5rem;
    height: 0.5rem;
    background: #f5b81b;
    border-radius: 50%;
    box-shadow: 0 0 8px #f5b81b;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

/* Search Wrapper */
.search-wrapper {
    position: relative;
}

.search-input {
    width: 260px;
    padding: 0.6rem 1rem 0.6rem 2.5rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 40px;
    font-size: 0.85rem;
    color: #e2e8f0;
    outline: none;
}

.search-input:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 0 2px rgba(245, 184, 27, 0.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 0.85rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-sm {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.12);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card-sm:hover {
    border-color: rgba(245, 184, 27, 0.3);
    transform: translateY(-2px);
}

.stat-icon-sm {
    width: 3rem;
    height: 3rem;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.icon-pending { background: rgba(245, 184, 27, 0.1); color: #f5b81b; }
.icon-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.icon-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.stat-label-sm {
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value-sm {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
}

.pending-value { color: #f5b81b; }
.approved-value { color: #10b981; }
.rejected-value { color: #ef4444; }

/* Table Header */
.table-header {
    display: grid;
    grid-template-columns: 2.5fr 1.2fr 1.2fr 1.2fr 1fr;
    padding: 0.75rem 1.25rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 12px;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(245, 184, 27, 0.1);
}

.header-cell {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #f5b81b;
    opacity: 0.8;
}

/* Applicant Card */
.applicant-card {
    background: #0f1115;
    border-radius: 16px;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.applicant-card:hover {
    border-color: rgba(245, 184, 27, 0.3);
}

.applicant-card-inner {
    display: grid;
    grid-template-columns: 2.5fr 1.2fr 1.2fr 1.2fr 1.2fr;
    align-items: center;
    padding: 1rem 1.25rem;
    gap: 0.5rem;
}

/* Applicant Info */
.applicant-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.applicant-avatar {
    width: 2.5rem;
    height: 2.5rem;
    background: rgba(245, 184, 27, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f5b81b;
}

.applicant-details {
    flex: 1;
}

.applicant-name {
    font-weight: 700;
    color: #ffffff;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.applicant-email {
    font-size: 0.7rem;
    color: #64748b;
    margin-bottom: 0.2rem;
}

/* Date Info */
.date-main {
    font-weight: 600;
    color: #e2e8f0;
    font-size: 0.8rem;
}

.date-time {
    font-size: 0.7rem;
    color: #64748b;
    margin-top: 0.2rem;
}

/* Permit Info */
.permit-number {
    font-weight: 700;
    color: #f5b81b;
    font-size: 0.8rem;
    font-family: monospace;
    background: rgba(245, 184, 27, 0.05);
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    display: inline-block;
}

/* Permit Status Badge */
.verify-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.8rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    width: fit-content;
}

.verify-badge.verified {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.verify-badge.used {
    background: rgba(245, 158, 11, 0.12);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.verify-badge.not-verified {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Action Buttons */
.action-buttons-wrapper {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.4rem 0.8rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s ease;
}

.view-btn {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border-color: rgba(59, 130, 246, 0.25);
}

.view-btn:hover {
    background: #3b82f6;
    color: #fff;
}

.approve-btn {
    background: rgba(245, 184, 27, 0.1);
    color: #f5b81b;
    border-color: rgba(245, 184, 27, 0.25);
}

.approve-btn:hover {
    background: #f5b81b;
    color: #0a0c10;
}

.reject-btn {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.25);
}

.reject-btn:hover {
    background: #ef4444;
    color: #fff;
}

/* MODAL OVERLAY - CENTERED POPUP */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.modal-container {
    background: #0f1115;
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.3);
    max-width: 900px;
    width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.15);
}

.modal-title {
    color: #f5b81b;
    font-weight: 700;
    font-size: 1.25rem;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 28px;
    cursor: pointer;
    transition: color 0.2s;
    line-height: 1;
}

.modal-close:hover {
    color: #f5b81b;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(245, 184, 27, 0.15);
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Modal Details */
.details-section {
    margin-bottom: 1.5rem;
}

.details-section h6 {
    color: #f5b81b;
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.15);
}

.details-section h6 i {
    margin-right: 0.5rem;
}

.details-table {
    width: 100%;
}

.details-table tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.details-table td {
    padding: 0.5rem 0;
    font-size: 0.8rem;
}

.details-table .label {
    width: 120px;
    color: #64748b;
    font-weight: 500;
}

.details-table .value {
    color: #e2e8f0;
}

.details-table .value.price {
    color: #f5b81b;
    font-weight: 700;
}

.description-text {
    color: #94a3b8;
    font-size: 0.8rem;
    line-height: 1.5;
    background: rgba(0, 0, 0, 0.3);
    padding: 0.75rem;
    border-radius: 8px;
}

.badge-status {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.badge-status.active {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.badge-status.used {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}

.badge-status.not-found {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

/* Buttons */
.btn-cancel {
    padding: 0.5rem 1rem;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(100, 116, 139, 0.15);
    border: 1px solid rgba(100, 116, 139, 0.3);
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: rgba(100, 116, 139, 0.25);
}

.btn-approve {
    padding: 0.5rem 1rem;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(245, 184, 27, 0.15);
    border: 1px solid rgba(245, 184, 27, 0.3);
    color: #f5b81b;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-approve:hover {
    background: #f5b81b;
    color: #0a0c10;
}

.btn-reject-modal {
    padding: 0.5rem 1rem;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-reject-modal:hover {
    background: #ef4444;
    color: #fff;
}

/* Form Styles */
.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #e2e8f0;
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.6rem 1rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 12px;
    font-size: 0.85rem;
    color: #e2e8f0;
}

.form-control:focus {
    outline: none;
    border-color: #f5b81b;
}

.form-text {
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 0.25rem;
    display: block;
}

/* Pagination */
.pagination-container {
    margin-top: 1.5rem;
    padding: 1rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 16px;
}

.pagination-container nav {
    display: flex;
    justify-content: center;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 28px;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-top: 1rem;
}

.empty-state i {
    font-size: 3rem;
    color: #64748b;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-weight: 700;
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.empty-state span {
    font-size: 0.75rem;
    color: #64748b;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 2rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 20px;
    margin-top: 1rem;
}

/* Scrollbar */
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

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.col-md-6 {
    flex: 1;
    min-width: 250px;
}

/* Responsive */
@media (max-width: 1024px) {
    .applicant-card-inner {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .table-header {
        display: none;
    }
    
    .applicant-info {
        flex-direction: row;
    }
    
    .date-info, .permit-info, .permit-status-info, .action-buttons-wrapper {
        padding-left: 3.25rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .verification-container {
        padding: 1rem;
    }
    
    .verification-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-right {
        width: 100%;
        justify-content: space-between;
    }
    
    .search-input {
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card-sm {
        padding: 1rem;
    }
    
    .stat-value-sm {
        font-size: 1.25rem;
    }
    
    .row {
        flex-direction: column;
    }
    
    .action-buttons-wrapper {
        justify-content: flex-start;
    }
}
</style>
@endsection
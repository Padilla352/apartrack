@extends('layouts.admin')

@section('content')
<div class="verification-container">
    <div class="verification-content-wrapper">
        
        {{-- Header Section --}}
        <div class="verification-header">
            <div class="header-left">
                <h2 class="page-title">Business Space Approval</h2>
                <p class="page-subtitle">Review and manage business space listing requests</p>
            </div>
            
            <div class="header-right">
                <div class="pending-badge">
                    <span class="pending-dot"></span>
                    <span id="pending-badge-count">{{ $stats['total_pending'] ?? 0 }} Pending Requests</span>
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
                <div class="stat-icon-sm icon-pending"><i class="fas fa-clock"></i></div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Pending Review</span>
                    <h2 class="stat-value-sm pending-value">{{ $stats['total_pending'] ?? 0 }}</h2>
                </div>
            </div>
            <div class="stat-card-sm approved-card">
                <div class="stat-icon-sm icon-approved"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Approved This Month</span>
                    <h2 class="stat-value-sm approved-value">{{ $stats['approved_this_month'] ?? 0 }}</h2>
                </div>
            </div>
            <div class="stat-card-sm rejected-card">
                <div class="stat-icon-sm icon-rejected"><i class="fas fa-times-circle"></i></div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Rejected</span>
                    <h2 class="stat-value-sm rejected-value">{{ $stats['total_rejected'] ?? 0 }}</h2>
                </div>
            </div>
        </div>

        {{-- Table Header --}}
        <div class="table-header">
            <div class="header-cell">BUSINESS / OWNER</div>
            <div class="header-cell">TYPE</div>
            <div class="header-cell">MONTHLY RENT</div>
            <div class="header-cell">DATE SUBMITTED</div>
            <div class="header-cell">ACTIONS</div>
        </div>

        {{-- Applicants List --}}
        <div id="verification-queue" class="verification-queue">
            @if($pendingBusinesses->count() > 0)
                @foreach($pendingBusinesses as $business)
                <div id="row-{{ $business->id }}" class="applicant-card">
                    <div class="applicant-card-inner">
                        {{-- Business & Owner Info --}}
                        <div class="applicant-info">
                            <div class="applicant-avatar"><i class="fas fa-store"></i></div>
                            <div class="applicant-details">
                                <div class="applicant-name">{{ $business->business_name }}</div>
                                <div class="applicant-email"><i class="fas fa-user"></i> {{ $business->owner_name ?? 'N/A' }}</div>
                                <div class="applicant-location"><i class="fas fa-map-marker-alt"></i> {{ $business->barangay_name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        {{-- Business Type --}}
                        <div class="business-type">
                            <span class="type-badge">{{ $business->type ?? 'N/A' }}</span>
                        </div>

                        {{-- Monthly Rent --}}
                        <div class="rent-info">
                            <div class="rent-amount">₱{{ number_format($business->monthly_rent ?? 0, 2) }}</div>
                        </div>

                        {{-- Date Submitted --}}
                        <div class="date-info">
                            <div class="date-main">{{ isset($business->created_at) ? date('M d, Y', strtotime($business->created_at)) : 'N/A' }}</div>
                            <div class="date-time">{{ isset($business->created_at) ? date('h:i A', strtotime($business->created_at)) : 'N/A' }}</div>
                        </div>

                        {{-- Actions --}}
                        <div class="action-buttons-wrapper">
                            <button type="button" onclick="openDetailsModal({{ $business->id }})" class="action-btn view-btn">
                                <i class="fas fa-eye"></i> View Details
                            </button>
                            <button type="button" onclick="handleVerification('{{ $business->id }}', 'approve')" class="action-btn approve-btn">
                                <i class="fas fa-check-circle"></i> Approve
                            </button>
                            <button type="button" onclick="openRejectModal({{ $business->id }})" class="action-btn reject-btn">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- View Details Modal -->
                <div id="detailsModal{{ $business->id }}" class="modal-overlay" style="display: none;">
                    <div class="modal-container" style="max-width: 900px;">
                        <div class="modal-header">
                            <h3 class="modal-title">{{ $business->business_name }}</h3>
                            <button type="button" class="modal-close" onclick="closeDetailsModal({{ $business->id }})">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="details-section">
                                        <h6><i class="fas fa-store"></i> Business Information</h6>
                                        <table class="details-table">
                                            <tr><td class="label">Business Name:</td><td class="value">{{ $business->business_name }}</td></tr>
                                            <tr><td class="label">Unit Number:</td><td class="value">{{ $business->unit_number ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Business Type:</td><td class="value">{{ $business->type ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Monthly Rent:</td><td class="value price">₱{{ number_format($business->monthly_rent ?? 0, 2) }}</td></tr>
                                            <tr><td class="label">Floor Area:</td><td class="value">{{ $business->floor_area_sqm ?? 'N/A' }} sqm</td></tr>
                                            <tr><td class="label">Location:</td><td class="value">{{ $business->address }}, {{ $business->barangay_name ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Permit Number:</td><td class="value">{{ $business->permit_number ?? 'N/A' }}</td></tr>
                                        </table>
                                    </div>
                                    <div class="details-section">
                                        <h6><i class="fas fa-user-circle"></i> Owner Information</h6>
                                        <table class="details-table">
                                            <tr><td class="label">Name:</td><td class="value">{{ $business->owner_name ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Email:</td><td class="value">{{ $business->owner_email ?? 'N/A' }}</td></tr>
                                            <tr><td class="label">Phone:</td><td class="value">{{ $business->owner_phone ?? 'N/A' }}</td></tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="details-section">
                                        <h6><i class="fas fa-images"></i> Business Images</h6>
                                        @php
                                            $images = [];
                                            if ($business->images) {
                                                $images = is_string($business->images) ? json_decode($business->images, true) : $business->images;
                                            }
                                        @endphp
                                        @if(!empty($images))
                                            <div class="image-preview-grid">
                                                @foreach($images as $img)
                                                    <div class="image-preview-item">
                                                        <img src="{{ asset('storage/' . ltrim($img, '/')) }}" alt="Business image" onclick="openImageModal(this.src)">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="no-images-text">No images uploaded</p>
                                        @endif
                                    </div>
                                    <div class="details-section">
                                        <h6><i class="fas fa-align-left"></i> Description</h6>
                                        <p class="description-text">{{ $business->description ?? 'No description provided.' }}</p>
                                    </div>
                                    @if($business->amenities)
                                    <div class="details-section">
                                        <h6><i class="fas fa-cogs"></i> Amenities</h6>
                                        <div class="amenities-list">
                                            @foreach(json_decode($business->amenities, true) ?? [] as $amenity)
                                                <span class="amenity-tag">{{ $amenity }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeDetailsModal({{ $business->id }})">Close</button>
                            <button type="button" class="btn-approve" onclick="handleVerification('{{ $business->id }}', 'approve')">
                                <i class="fas fa-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn-reject-modal" onclick="handleVerificationFromModal('{{ $business->id }}', 'reject')">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div id="rejectModal{{ $business->id }}" class="modal-overlay" style="display: none;">
                    <div class="modal-container" style="max-width: 500px;">
                        <div class="modal-header">
                            <h3 class="modal-title">Reject Business Listing</h3>
                            <button type="button" class="modal-close" onclick="closeRejectModal({{ $business->id }})">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Rejection Reason <span class="text-danger">*</span></label>
                                <textarea id="rejection_reason_{{ $business->id }}" class="form-control" rows="4" placeholder="Please provide a reason for rejection..."></textarea>
                                <small class="form-text">This reason will be shown to the owner.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeRejectModal({{ $business->id }})">Cancel</button>
                            <button type="button" class="btn-reject-modal" onclick="handleVerification('{{ $business->id }}', 'reject')">
                                Confirm Rejection
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="pagination-container">
                    {{ $pendingBusinesses->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-check-double"></i>
                    <p>Queue Empty</p>
                    <span>No pending business space verification requests</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="modal-overlay" style="display: none;">
    <div class="image-preview-container">
        <span class="image-preview-close" onclick="closeImageModal()">&times;</span>
        <img id="previewImage" src="" alt="Preview">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentBusinessId = null;
    
    function openImageModal(src) {
        document.getElementById('previewImage').src = src;
        document.getElementById('imagePreviewModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        document.getElementById('imagePreviewModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function openDetailsModal(id) {
        document.getElementById('detailsModal' + id).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeDetailsModal(id) {
        document.getElementById('detailsModal' + id).style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function openRejectModal(id) {
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
        const isApprove = status === 'approve';
        
        closeDetailsModal(id);
        closeRejectModal(id);
        
        Swal.fire({
            title: `${isApprove ? 'Approve' : 'Reject'} Business Listing?`,
            text: `Are you sure you want to ${status} this business space?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#f5b81b' : '#ef4444',
            confirmButtonText: `Yes, ${status}`,
            cancelButtonText: 'Cancel',
            background: '#0f1115',
            color: '#e2e8f0'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: `Please wait while we ${status} the listing.`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    background: '#0f1115'
                });
                
                const row = document.getElementById(`row-${id}`);
                if (row) row.style.opacity = '0.5';
                
                let url = `/admin/business/${status}/${id}`;
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                
                if (!isApprove) {
                    const reason = document.getElementById(`rejection_reason_${id}`)?.value;
                    if (!reason) {
                        Swal.fire({ icon: 'error', title: 'Reason Required', text: 'Please provide a reason for rejection.', background: '#0f1115' });
                        if (row) row.style.opacity = '1';
                        return;
                    }
                    formData.append('rejection_reason', reason);
                }
                
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        if (row) row.remove();
                        const pendingEl = document.getElementById('stat-pending');
                        if (pendingEl) pendingEl.innerText = Math.max(0, (parseInt(pendingEl.innerText) || 0) - 1);
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000, background: '#0f1115' });
                        if (document.querySelectorAll('.applicant-card').length === 0) setTimeout(() => location.reload(), 500);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message, background: '#0f1115' });
                        if (row) row.style.opacity = '1';
                    }
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process request.', background: '#0f1115' });
                    if (row) row.style.opacity = '1';
                });
            }
        });
    }
    
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let visibleCount = 0;
        document.querySelectorAll('.applicant-card').forEach(card => {
            let text = card.innerText.toLowerCase();
            let isVisible = text.includes(value);
            card.style.display = isVisible ? 'flex' : 'none';
            if (isVisible) visibleCount++;
        });
    });
</script>

<style>
/* ========== GLOW DARK THEME - BUSINESS VERIFICATION ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

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

.verification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0;
}

.page-subtitle {
    font-size: 0.8rem;
    color: #64748b;
}

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
    animation: pulse 1.5s infinite;
}

.search-wrapper {
    position: relative;
}

.search-input {
    width: 260px;
    padding: 0.6rem 1rem 0.6rem 2.5rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 40px;
    color: #e2e8f0;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
}

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
}

.stat-value-sm {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
}

.pending-value { color: #f5b81b; }
.approved-value { color: #10b981; }
.rejected-value { color: #ef4444; }

.table-header {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1.2fr 1.2fr;
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
    color: #f5b81b;
}

.applicant-card {
    background: #0f1115;
    border-radius: 16px;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-bottom: 0.75rem;
}

.applicant-card-inner {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1.2fr 1.2fr;
    align-items: center;
    padding: 1rem 1.25rem;
    gap: 0.5rem;
}

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

.applicant-name {
    font-weight: 700;
    color: #ffffff;
    font-size: 0.9rem;
}

.applicant-email, .applicant-location {
    font-size: 0.7rem;
    color: #64748b;
}

.type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(245, 184, 27, 0.1);
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #f5b81b;
}

.rent-amount {
    font-weight: 700;
    color: #10b981;
    font-size: 0.9rem;
}

.date-main {
    font-weight: 600;
    color: #e2e8f0;
    font-size: 0.8rem;
}

.date-time {
    font-size: 0.7rem;
    color: #64748b;
}

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
}

.view-btn { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.25); }
.view-btn:hover { background: #3b82f6; color: #fff; }
.approve-btn { background: rgba(245, 184, 27, 0.1); color: #f5b81b; border-color: rgba(245, 184, 27, 0.25); }
.approve-btn:hover { background: #f5b81b; color: #0a0c10; }
.reject-btn { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.25); }
.reject-btn:hover { background: #ef4444; color: #fff; }

.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
    z-index: 10000;
    justify-content: center;
    align-items: center;
}

.modal-container {
    background: #0f1115;
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.3);
    width: 90%;
    max-width: 900px;
    max-height: 85vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.15);
}

.modal-title { color: #f5b81b; font-weight: 700; font-size: 1.25rem; margin: 0; }
.modal-close { background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer; }

.modal-body { padding: 1.5rem; max-height: calc(85vh - 140px); overflow-y: auto; }
.modal-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(245, 184, 27, 0.15); display: flex; justify-content: flex-end; gap: 0.75rem; }

.details-section { margin-bottom: 1.5rem; }
.details-section h6 { color: #f5b81b; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.75rem; border-bottom: 1px solid rgba(245, 184, 27, 0.15); padding-bottom: 0.5rem; }
.details-table { width: 100%; }
.details-table td { padding: 0.5rem 0; font-size: 0.8rem; }
.details-table .label { width: 120px; color: #64748b; }
.details-table .value { color: #e2e8f0; }
.details-table .value.price { color: #f5b81b; font-weight: 700; }

.image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
.image-preview-item { aspect-ratio: 1/1; border-radius: 8px; overflow: hidden; cursor: pointer; border: 1px solid rgba(245, 184, 27, 0.2); }
.image-preview-item img { width: 100%; height: 100%; object-fit: cover; }

.image-preview-container {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
}
.image-preview-container img { max-width: 100%; max-height: 90vh; border-radius: 12px; }
.image-preview-close {
    position: absolute;
    top: -40px;
    right: 0;
    font-size: 32px;
    color: white;
    cursor: pointer;
}

.amenities-list { display: flex; flex-wrap: wrap; gap: 8px; }
.amenity-tag { padding: 4px 10px; background: rgba(245, 184, 27, 0.1); border-radius: 20px; font-size: 0.7rem; color: #f5b81b; }

.description-text { color: #94a3b8; font-size: 0.8rem; line-height: 1.5; background: rgba(0,0,0,0.3); padding: 0.75rem; border-radius: 8px; }
.no-images-text { color: #64748b; font-size: 0.8rem; text-align: center; padding: 1rem; }

.btn-cancel, .btn-approve, .btn-reject-modal { padding: 0.5rem 1rem; border-radius: 40px; font-size: 0.75rem; font-weight: 600; cursor: pointer; }
.btn-cancel { background: rgba(100, 116, 139, 0.15); border: 1px solid rgba(100, 116, 139, 0.3); color: #94a3b8; }
.btn-approve { background: rgba(245, 184, 27, 0.15); border: 1px solid rgba(245, 184, 27, 0.3); color: #f5b81b; }
.btn-reject-modal { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; }

.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.75rem; font-weight: 600; color: #e2e8f0; margin-bottom: 0.5rem; }
.form-control { width: 100%; padding: 0.6rem 1rem; background: rgba(15, 17, 21, 0.9); border: 1px solid rgba(245, 184, 27, 0.2); border-radius: 12px; color: #e2e8f0; }
.form-text { font-size: 0.65rem; color: #64748b; margin-top: 0.25rem; }

.pagination-container { margin-top: 1.5rem; padding: 1rem; background: rgba(15, 17, 21, 0.6); border-radius: 16px; }
.empty-state { text-align: center; padding: 3rem; background: rgba(15, 17, 21, 0.6); border-radius: 28px; }
.empty-state i { font-size: 3rem; color: #64748b; margin-bottom: 1rem; opacity: 0.5; }
.empty-state p { font-weight: 700; color: #cbd5e1; }
.empty-state span { font-size: 0.75rem; color: #64748b; }

@media (max-width: 1024px) {
    .applicant-card-inner, .table-header { grid-template-columns: 1fr; gap: 0.75rem; }
    .table-header { display: none; }
    .stats-grid { grid-template-columns: 1fr; }
    .applicant-info { flex-direction: row; }
    .action-buttons-wrapper { justify-content: flex-start; }
}

@keyframes pulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.2); } }
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #0f1115; }
::-webkit-scrollbar-thumb { background: #f5b81b; border-radius: 10px; }
</style>
@endsection
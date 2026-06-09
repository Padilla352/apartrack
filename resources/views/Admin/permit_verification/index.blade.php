@extends('layouts.admin')

@section('content')
<div class="verification-container">
    <div class="verification-content-wrapper">
        
        {{-- Header Section --}}
        <div class="verification-header">
            <div class="header-left">
                <h2 class="page-title">Permit & Owner Verification</h2>
            </div>
            
            <div class="header-right">
                <div class="pending-badge">
                    <span class="pending-dot"></span>
                    <span id="pending-badge-count">{{ $pendingCount ?? 0 }} Pending Requests</span>
                </div>

                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search applicant..." class="search-input">
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
                    <h2 id="stat-pending" class="stat-value-sm pending-value">{{ $pendingCount ?? 0 }}</h2>
                </div>
            </div>
            
            <div class="stat-card-sm approved-card">
                <div class="stat-icon-sm icon-approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Approved Today</span>
                    <h2 id="stat-approved" class="stat-value-sm approved-value">{{ $approvedToday ?? 0 }}</h2>
                </div>
            </div>
            
            <div class="stat-card-sm rejected-card">
                <div class="stat-icon-sm icon-rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content-sm">
                    <span class="stat-label-sm">Rejected</span>
                    <h2 id="stat-rejected" class="stat-value-sm rejected-value">{{ $rejectedCount ?? 0 }}</h2>
                </div>
            </div>
        </div>

        {{-- Table Header (Desktop) --}}
        <div class="table-header">
            <div class="header-cell">Applicant / Owner</div>
            <div class="header-cell text-center">Property</div>
            <div class="header-cell">Date Submitted</div>
            <div class="header-cell text-center">Permit Number</div>
            <div class="header-cell text-center">Permit Status</div>
            <div class="header-cell text-center">Verification</div>
        </div>

        {{-- Applicants List --}}
        <div id="verification-queue" class="verification-queue">
            @forelse($applicants ?? [] as $applicant)
            @php
                $canApprove = isset($applicant->can_approve) && $applicant->can_approve == 1;
                $applicantName = $applicant->applicant_name ?? ($applicant->owner_name ?? ($applicant->name ?? 'N/A'));
                $applicantEmail = $applicant->email ?? ($applicant->owner_email ?? 'No email');
                $propertyName = $applicant->property_name ?? 'N/A';
                $permitNumber = $applicant->permit_number ?? 'N/A';
                $createdAt = $applicant->created_at ?? null;
            @endphp
            <div id="row-{{ $applicant->id ?? '' }}" class="applicant-card" data-id="{{ $applicant->id ?? '' }}" data-status="{{ $applicant->status ?? 'pending' }}">
                <div class="applicant-card-inner">
                    <div class="applicant-info">
                        <div class="applicant-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="applicant-name">{{ $applicantName }}</div>
                            <div class="applicant-email">{{ $applicantEmail }}</div>
                        </div>
                    </div>

                    <div class="property-info">
                        <span class="mobile-label">Property</span>
                        <span class="property-name">{{ $propertyName }}</span>
                    </div>

                    <div class="date-info">
                        <span class="mobile-label">Submitted</span>
                        <div class="date-main">{{ $createdAt ? date('M d, Y', strtotime($createdAt)) : 'N/A' }}</div>
                        <div class="date-time">{{ $createdAt ? date('h:i A', strtotime($createdAt)) : 'N/A' }}</div>
                    </div>

                    <div class="permit-info">
                        <span class="mobile-label">Permit Number</span>
                        <span class="permit-number">{{ $permitNumber }}</span>
                    </div>

                    <div class="permit-status-info">
                        <span class="mobile-label">Permit Status</span>
                        @if($canApprove)
                            <span class="verify-badge verified">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        @else
                            <span class="verify-badge not-verified">
                                <i class="fas fa-times-circle"></i> Not in System
                            </span>
                        @endif
                    </div>

                    <div class="action-buttons-wrapper">
                        <span class="mobile-label">Action</span>
                        <div class="action-buttons">
                            <button onclick="handleVerification('{{ $applicant->id ?? '' }}', 'reject')" class="action-btn reject-btn">
                                <i class="fas fa-times"></i>
                                <span>Reject</span>
                            </button>
                            <button onclick="handleVerification('{{ $applicant->id ?? '' }}', 'approve')" 
                                    class="action-btn approve-btn"
                                    @if(!$canApprove) disabled @endif>
                                <i class="fas fa-check"></i>
                                <span>Approve</span>
                            </button>
                        </div>
                        @if(!$canApprove)
                            <div class="approve-disabled-hint">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Cannot approve: Permit number not in system
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" id="empty-state-main">
                <i class="fas fa-check-double"></i>
                <p>Queue Empty</p>
                <span>No verification requests</span>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Track if we're already processing to prevent duplicate actions
    let isProcessing = false;
    
    function handleVerification(id, status) {
        if (!id) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Invalid application ID',
                background: '#0f1115',
                color: '#e2e8f0'
            });
            return;
        }
        
        if (isProcessing) {
            Swal.fire({
                icon: 'warning',
                title: 'Please wait',
                text: 'Another action is being processed',
                background: '#0f1115',
                color: '#e2e8f0',
                timer: 1500,
                showConfirmButton: false
            });
            return;
        }
        
        const actionLabel = status === 'approve' ? 'Approve' : 'Reject';
        const isApprove = status === 'approve';
        
        // For reject, ask for reason
        if (!isApprove) {
            Swal.fire({
                title: 'Reject Application',
                html: `
                    <div class="swal-text" style="margin-bottom: 15px;">Please provide a reason for rejection:</div>
                    <textarea id="rejection-reason" class="swal2-textarea" placeholder="Enter rejection reason..." rows="3" style="background: #1e293b; color: #e2e8f0; border-color: #f5b81b;"></textarea>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#334155',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel',
                background: '#0f1115',
                color: '#e2e8f0',
                preConfirm: () => {
                    const reason = document.getElementById('rejection-reason').value;
                    if (!reason || reason.trim() === '') {
                        Swal.showValidationMessage('Please enter a rejection reason');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    processVerification(id, status, result.value);
                }
            });
            return;
        }
        
        // For approve, simple confirmation
        Swal.fire({
            title: `<span class="swal-title">${actionLabel} Application?</span>`,
            html: `<span class="swal-text">Confirming ${status} for this permit application.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f5b81b',
            cancelButtonColor: '#334155',
            confirmButtonText: `<span class="swal-btn-text">Yes, ${status}</span>`,
            cancelButtonText: `<span class="swal-btn-text-cancel">Cancel</span>`,
            background: '#0f1115',
            color: '#e2e8f0',
            customClass: {
                popup: 'swal-glow-popup',
                title: 'swal-glow-title',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processVerification(id, status, null);
            }
        });
    }
    
    function processVerification(id, status, rejectReason = null) {
        isProcessing = true;
        
        const row = document.getElementById(`row-${id}`);
        if (row) {
            row.style.opacity = '0.5';
            row.style.pointerEvents = 'none';
        }
        
        // FIXED: Removed /admin/ prefix - matches your route definition
        const url = status === 'approve' 
            ? `/permit-verification/${id}/approve` 
            : `/permit-verification/${id}/reject`;
        
        const fetchOptions = {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'
            },
            body: JSON.stringify({ rejection_reason: rejectReason })
        };
        
        fetch(url, fetchOptions)
            .then(res => res.json())
            .then(data => {
                isProcessing = false;
                
                if (data.success) {
                    // Remove the row with animation
                    if (row) {
                        row.style.transform = 'translateX(50px)';
                        row.style.opacity = '0';
                    }
                    
                    setTimeout(() => {
                        if (row) row.remove();
                        
                        // Get current counts from DOM
                        let currentPending = parseInt(document.getElementById('stat-pending').innerText) || 0;
                        let currentApproved = parseInt(document.getElementById('stat-approved').innerText) || 0;
                        let currentRejected = parseInt(document.getElementById('stat-rejected').innerText) || 0;
                        
                        // Update counts based on action
                        if (status === 'approve') {
                            currentPending = Math.max(0, currentPending - 1);
                            currentApproved = currentApproved + 1;
                        } else {
                            currentPending = Math.max(0, currentPending - 1);
                            currentRejected = currentRejected + 1;
                        }
                        
                        // Update DOM with new counts
                        document.getElementById('stat-pending').innerText = currentPending;
                        const pendingBadge = document.getElementById('pending-badge-count');
                        if (pendingBadge) {
                            pendingBadge.innerText = `${currentPending} Pending Requests`;
                        }
                        document.getElementById('stat-approved').innerText = currentApproved;
                        document.getElementById('stat-rejected').innerText = currentRejected;
                        
                        // Check remaining visible rows
                        const remainingRows = document.querySelectorAll('.applicant-card:not([style*="display: none"])').length;
                        
                        if (remainingRows === 0) {
                            // Clear the queue container completely
                            const queueContainer = document.getElementById('verification-queue');
                            if (queueContainer) {
                                // Remove all child nodes
                                while (queueContainer.firstChild) {
                                    queueContainer.removeChild(queueContainer.firstChild);
                                }
                                // Add empty state
                                const emptyDiv = document.createElement('div');
                                emptyDiv.className = 'empty-state';
                                emptyDiv.innerHTML = `
                                    <i class="fas fa-check-double"></i>
                                    <p>Queue Empty</p>
                                    <span>No verification requests</span>
                                `;
                                queueContainer.appendChild(emptyDiv);
                            }
                        }
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: `Successfully ${status}d!`,
                            text: data.message || `Application has been ${status}d.`,
                            background: '#0f1115',
                            color: '#e2e8f0',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                    }, 350);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Something went wrong',
                        background: '#0f1115',
                        color: '#e2e8f0'
                    });
                    if (row) {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                        row.style.transform = '';
                    }
                }
            })
            .catch(error => {
                isProcessing = false;
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to process request. Please try again.',
                    background: '#0f1115',
                    color: '#e2e8f0'
                });
                if (row) {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                    row.style.transform = '';
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
                card.style.display = isVisible ? 'block' : 'none';
                if (isVisible) visibleCount++;
            });
            
            // Handle no results message
            const existingNoResults = document.querySelector('.no-results');
            if (existingNoResults) existingNoResults.remove();
            
            const existingEmptyState = document.querySelector('.empty-state');
            
            if (visibleCount === 0 && document.querySelectorAll('.applicant-card').length > 0) {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = '<i class="fas fa-search"></i><p>No matching results</p><span>Try a different search term</span>';
                document.getElementById('verification-queue').appendChild(noResults);
            } else if (visibleCount === 0 && document.querySelectorAll('.applicant-card').length === 0 && !existingEmptyState) {
                const queueContainer = document.getElementById('verification-queue');
                if (queueContainer) {
                    queueContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-check-double"></i>
                            <p>Queue Empty</p>
                            <span>No verification requests</span>
                        </div>
                    `;
                }
            }
        });
    }

    // Prevent page caching to ensure fresh data on load
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Add no-cache meta tags
        const metaNoCache = document.createElement('meta');
        metaNoCache.httpEquiv = 'Cache-Control';
        metaNoCache.content = 'no-cache, no-store, must-revalidate';
        document.head.appendChild(metaNoCache);
        
        const metaPragma = document.createElement('meta');
        metaPragma.httpEquiv = 'Pragma';
        metaPragma.content = 'no-cache';
        document.head.appendChild(metaPragma);
        
        const metaExpires = document.createElement('meta');
        metaExpires.httpEquiv = 'Expires';
        metaExpires.content = '0';
        document.head.appendChild(metaExpires);
        
        const statCards = document.querySelectorAll('.stat-card-sm');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-in');
        });
        
        console.log('%c📋 APARTrack Permit Verification | Ready', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
        
        // Check if there are any rows on page load
        const checkEmptyState = () => {
            const rows = document.querySelectorAll('.applicant-card');
            const emptyState = document.querySelector('.empty-state');
            
            if (rows.length === 0 && !emptyState) {
                const queueContainer = document.getElementById('verification-queue');
                if (queueContainer) {
                    queueContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-check-double"></i>
                            <p>Queue Empty</p>
                            <span>No verification requests</span>
                        </div>
                    `;
                }
            }
        };
        
        checkEmptyState();
    });
</script>

<style>
/* ========== GLOW DARK THEME - PERMIT VERIFICATION ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.verification-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1rem;
}

.verification-content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

/* Header Section */
.verification-header {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .verification-header {
        flex-direction: row;
        align-items: center;
    }
}

.header-right {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    width: 100%;
}

@media (min-width: 768px) {
    .header-right {
        flex-direction: row;
        align-items: center;
        width: auto;
    }
}

.page-title {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin: 0;
}

/* Pending Badge */
.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 1rem;
    background: rgba(245, 184, 27, 0.08);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #f5b81b;
}

.pending-dot {
    width: 0.5rem; height: 0.5rem;
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
    width: 100%;
}

@media (min-width: 768px) { .search-wrapper { width: 240px; } }

.search-input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.5rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 40px;
    font-size: 0.85rem;
    color: #e2e8f0;
    outline: none;
}

.search-input:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 10px rgba(245, 184, 27, 0.2);
}

.search-icon {
    position: absolute;
    left: 1rem; top: 50%;
    transform: translateY(-50%);
    color: #64748b;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }

.stat-card-sm {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
}

.stat-card-sm.animate-in { opacity: 1; transform: translateY(0); }

.stat-card-sm:hover {
    border-color: rgba(245, 184, 27, 0.3);
    transform: translateY(-2px);
}

.stat-icon-sm {
    width: 3rem; height: 3rem;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}

.icon-pending { background: rgba(245, 184, 27, 0.1); color: #f5b81b; }
.icon-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.icon-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

.stat-label-sm { font-size: 0.625rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-value-sm { font-size: 1.75rem; font-weight: 800; margin: 0; line-height: 1; }
.pending-value { color: #f5b81b; }
.approved-value { color: #10b981; }
.rejected-value { color: #ef4444; }

/* Table Section */
.table-header {
    display: none;
    grid-template-columns: 2fr 1.5fr 1.2fr 1.3fr 1fr 1fr;
    padding: 0.75rem 1.25rem;
    color: #f5b81b;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px solid rgba(245, 184, 27, 0.2);
    margin-bottom: 0.5rem;
}

@media (min-width: 1024px) { .table-header { display: grid; } }

.applicant-card {
    background: #0f1115;
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.applicant-card:hover { 
    border-color: rgba(245, 184, 27, 0.3);
    transform: translateX(4px);
}

.applicant-card-inner {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

@media (min-width: 1024px) {
    .applicant-card-inner {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.2fr 1.3fr 1fr 1fr;
        align-items: center;
        padding: 1rem 1.25rem;
    }
}

.applicant-info { display: flex; align-items: center; gap: 0.75rem; }
.applicant-avatar { 
    width: 2.5rem; height: 2.5rem; 
    background: rgba(245, 184, 27, 0.1); 
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #f5b81b;
}

.applicant-name { font-weight: 700; color: #fff; font-size: 0.9rem; }
.applicant-email { font-size: 0.7rem; color: #64748b; }
.property-name { color: #00e5ff; font-weight: 600; font-size: 0.85rem; }
.date-main { font-weight: 600; color: #e2e8f0; font-size: 0.8rem; }
.date-time { font-size: 0.7rem; color: #64748b; }
.permit-number { font-weight: 700; color: #f5b81b; font-size: 0.8rem; }

/* Permit Status Badge */
.verify-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.8rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.verify-badge.verified {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.verify-badge.not-verified {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Buttons */
.action-buttons { display: flex; gap: 0.75rem; }
.action-btn {
    padding: 0.4rem 0.8rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid transparent;
    display: flex; align-items: center; gap: 0.4rem;
    transition: all 0.2s ease;
}

.reject-btn { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2); }
.approve-btn { background: rgba(245, 184, 27, 0.1); color: #f5b81b; border-color: rgba(245, 184, 27, 0.2); }

.reject-btn:hover { background: #ef4444; color: #fff; transform: scale(1.02); }
.approve-btn:hover { background: #f5b81b; color: #000; transform: scale(1.02); }

.action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.approve-disabled-hint {
    margin-top: 5px;
}

.approve-disabled-hint .text-muted {
    font-size: 0.65rem;
    color: #ef4444;
}

.mobile-label { display: none; }
@media (max-width: 1023px) {
    .mobile-label { display: inline-block; width: 100px; color: #f5b81b; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
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

.no-results i {
    font-size: 2rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

.no-results p {
    font-weight: 700;
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.no-results span {
    font-size: 0.7rem;
    color: #64748b;
}

/* Custom Scrollbar */
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

::-webkit-scrollbar-thumb:hover {
    background: #e5a800;
}

/* Text Selection */
::selection {
    background: #f5b81b;
    color: #0a0c10;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .page-title {
        font-size: 1.25rem;
    }
    
    .stat-value-sm {
        font-size: 1.25rem;
    }
    
    .stat-icon-sm {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .verification-container {
        padding: 0.75rem;
    }
    
    .pending-badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.75rem;
    }
    
    .search-input {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem 0.5rem 2rem;
    }
    
    .stat-card-sm {
        padding: 0.75rem;
    }
}
</style>
@endsection
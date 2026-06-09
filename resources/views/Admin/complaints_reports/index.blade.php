@extends('layouts.admin')

@section('content')
<div class="complaints-container" x-data="{ 
    search: '',
    showViewModal: false,
    showResolveModal: false,
    selectedComplaint: null,
    complaints: {{ json_encode($complaints ?? []) }},
    
    get stats() {
        const pending = this.complaints.filter(c => c.status === 'Pending').length;
        const urgent = this.complaints.filter(c => c.priority === 'Urgent').length;
        const resolved = this.complaints.filter(c => c.status === 'Resolved').length;
        return {
            pending: pending,
            urgent: urgent,
            resolved: resolved,
            total: this.complaints.length
        };
    },
    
    get filteredComplaints() {
        if (!this.search) return this.complaints;
        const term = this.search.toLowerCase();
        return this.complaints.filter(c => 
            c.id.toString().includes(term) ||
            (c.tenant && c.tenant.toLowerCase().includes(term)) ||
            (c.property && c.property.toLowerCase().includes(term)) ||
            (c.subject && c.subject.toLowerCase().includes(term))
        );
    },
    
    openView(complaint) {
        this.selectedComplaint = complaint;
        this.showViewModal = true;
    },
    
    openResolve(complaint) {
        this.selectedComplaint = complaint;
        this.showResolveModal = true;
    },
    
    async resolveNow() {
        if (!this.selectedComplaint) return;
        
        try {
            const response = await fetch(`/complaints/${this.selectedComplaint.id}/resolve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                const index = this.complaints.findIndex(c => c.id === this.selectedComplaint.id);
                if (index !== -1) {
                    this.complaints[index].status = 'Resolved';
                }
                this.showResolveModal = false;
                this.selectedComplaint = null;
            }
        } catch (error) {
            console.error('Error resolving complaint:', error);
        }
    }
}">
    <div class="complaints-content-wrapper">
        
        {{-- Header Section --}}
        <div class="complaints-header">
            <div>
                <h2 class="page-title-complaints">Complaints & Reports</h2>
                <p class="page-subtitle-complaints">System ticket management dashboard</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="stats-grid-complaints">
            <div class="stat-card-complaints">
                <div class="stat-icon-complaints icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content-complaints">
                    <span class="stat-label-complaints">Active</span>
                    <h2 class="stat-value-complaints pending-value" x-text="stats.pending">0</h2>
                </div>
            </div>

            <div class="stat-card-complaints">
                <div class="stat-icon-complaints icon-urgent">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content-complaints">
                    <span class="stat-label-complaints">Urgent</span>
                    <h2 class="stat-value-complaints urgent-value" x-text="stats.urgent">0</h2>
                </div>
            </div>

            <div class="stat-card-complaints">
                <div class="stat-icon-complaints icon-resolved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content-complaints">
                    <span class="stat-label-complaints">Resolved</span>
                    <h2 class="stat-value-complaints resolved-value" x-text="stats.resolved">0</h2>
                </div>
            </div>

            <div class="stat-card-complaints">
                <div class="stat-icon-complaints icon-total">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content-complaints">
                    <span class="stat-label-complaints">Total</span>
                    <h2 class="stat-value-complaints total-value" x-text="stats.total">0</h2>
                </div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="search-container-complaints">
            <div class="search-wrapper-complaints">
                <i class="fas fa-search search-icon-complaints"></i>
                <input type="text" x-model="search" placeholder="Search by ticket #, tenant, property, or subject..." class="search-input-complaints">
            </div>
        </div>

        {{-- Table Header (Desktop) --}}
        <div class="table-header-complaints">
            <div class="header-cell-complaints">Ticket / Tenant</div>
            <div class="header-cell-complaints">Property</div>
            <div class="header-cell-complaints">Subject</div>
            <div class="header-cell-complaints">Status</div>
            <div class="header-cell-complaints text-center">Actions</div>
        </div>

        {{-- Complaints List --}}
        <div class="complaints-list">
            <template x-for="complaint in filteredComplaints" :key="complaint.id">
                <div class="complaint-card">
                    <div class="complaint-card-inner">
                        {{-- Ticket Info --}}
                        <div class="ticket-info">
                            <div class="ticket-avatar" :class="complaint.status === 'Pending' ? 'bg-pending' : 'bg-resolved'">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <div class="ticket-id" x-text="'#' + complaint.id"></div>
                                <div class="ticket-tenant" x-text="complaint.tenant || 'Unknown'"></div>
                            </div>
                        </div>

                        {{-- Property --}}
                        <div class="property-info">
                            <span class="mobile-label">Property</span>
                            <span class="property-name" x-text="complaint.property || 'N/A'"></span>
                        </div>

                        {{-- Subject --}}
                        <div class="subject-info">
                            <span class="mobile-label">Subject</span>
                            <span class="subject-text" x-text="complaint.subject"></span>
                        </div>

                        {{-- Status --}}
                        <div class="status-info">
                            <span class="mobile-label">Status</span>
                            <div class="status-badge" :class="complaint.status === 'Pending' ? 'status-pending' : 'status-resolved'">
                                <span class="status-dot" :class="complaint.status === 'Pending' ? 'dot-pending' : 'dot-resolved'"></span>
                                <span x-text="complaint.status"></span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="action-buttons">
                            <span class="mobile-label">Actions</span>
                            <div class="action-btns-wrapper">
                                <button @click="openView(complaint)" class="action-btn view-btn">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </button>
                                <button x-show="complaint.status === 'Pending'" @click="openResolve(complaint)" class="action-btn resolve-btn">
                                    <i class="fas fa-check-double"></i>
                                    <span>Resolve</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            {{-- Empty State --}}
            <div x-show="filteredComplaints.length === 0 && complaints.length > 0" class="empty-state">
                <i class="fas fa-search"></i>
                <p>No matching complaints found</p>
                <span>Try a different search term</span>
            </div>
            
            <div x-show="complaints.length === 0" class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No complaints yet</p>
                <span>When tenants submit complaints, they will appear here</span>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div x-show="showViewModal" class="modal-overlay" x-cloak x-transition.opacity>
        <div class="modal-container" @click.away="showViewModal = false">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" x-text="'TICKET #' + (selectedComplaint?.id || '')"></h3>
                    <p class="modal-subtitle">Detailed Information</p>
                </div>
                <button @click="showViewModal = false" class="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="detail-field">
                    <label class="detail-label">Tenant</label>
                    <p class="detail-value" x-text="selectedComplaint?.tenant || 'Unknown'"></p>
                </div>
                
                <div class="detail-row">
                    <div class="detail-field">
                        <label class="detail-label">Property</label>
                        <p class="detail-value property-detail" x-text="selectedComplaint?.property || 'N/A'"></p>
                    </div>
                    <div class="detail-field">
                        <label class="detail-label">Priority</label>
                        <p class="detail-value" :class="selectedComplaint?.priority === 'Urgent' ? 'text-pending' : ''" x-text="selectedComplaint?.priority || 'Normal'"></p>
                    </div>
                </div>
                
                <div class="detail-field">
                    <label class="detail-label">Status</label>
                    <p class="detail-value" :class="selectedComplaint?.status === 'Pending' ? 'text-pending' : 'text-resolved'" x-text="selectedComplaint?.status"></p>
                </div>
                
                <div class="detail-field">
                    <label class="detail-label">Subject</label>
                    <p class="detail-value subject-detail" x-text="selectedComplaint?.subject"></p>
                </div>
                
                <div class="description-box">
                    <label class="detail-label">Tenant's Message</label>
                    <p class="description-text" x-text="selectedComplaint?.description || 'No detailed description provided.'"></p>
                </div>
                
                <div class="detail-field">
                    <label class="detail-label">Submitted</label>
                    <p class="detail-value" x-text="selectedComplaint?.created_at ? new Date(selectedComplaint.created_at).toLocaleString() : 'N/A'"></p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button @click="showViewModal = false" class="modal-btn secondary">Close</button>
            </div>
        </div>
    </div>

    {{-- Resolve Modal --}}
    <div x-show="showResolveModal" class="modal-overlay" x-cloak x-transition.opacity>
        <div class="modal-container resolve-modal" @click.away="showResolveModal = false">
            <div class="resolve-icon">
                <i class="fas fa-check-double"></i>
            </div>
            <h3 class="resolve-title">Mark as Resolved?</h3>
            <p class="resolve-text">
                This action will update the status of ticket 
                <span class="ticket-highlight" x-text="'#' + (selectedComplaint?.id || '')"></span> 
                and mark it as resolved.
            </p>
            
            <div class="resolve-buttons">
                <button @click="resolveNow()" class="resolve-confirm-btn">
                    <i class="fas fa-check"></i>
                    Confirm Resolution
                </button>
                <button @click="showResolveModal = false" class="resolve-cancel-btn">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

<style>
/* ========== GLOW DARK THEME - COMPLAINTS PAGE ========== */
/* All your existing CSS remains exactly the same - keeping all styles */
/* (Keep all the CSS from your original file - it's already perfect) */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.complaints-container {
    min-height: 100vh;
    background: #0a0c10 !important;
    font-family: 'Inter', sans-serif;
    padding: 1rem;
}

html, body, #app {
    background-color: #0a0c10;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #0a0c10;
}

@media (min-width: 768px) {
    .complaints-container {
        padding: 2rem;
    }
}

.complaints-content-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

[x-cloak] { display: none !important; }

/* Page Header */
.complaints-header {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

.page-title-complaints {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffffff, #f5b81b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    letter-spacing: -0.3px;
    margin-bottom: 0.25rem;
}

.page-subtitle-complaints {
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Stats Grid */
.stats-grid-complaints {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .stats-grid-complaints {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card-complaints {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
}

@media (min-width: 768px) {
    .stat-card-complaints {
        padding: 1.25rem;
        gap: 1rem;
    }
}

.stat-card-complaints:hover {
    border-color: rgba(245, 184, 27, 0.35);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.stat-icon-complaints {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

@media (min-width: 768px) {
    .stat-icon-complaints {
        width: 48px;
        height: 48px;
    }
}

.icon-pending {
    background: rgba(245, 184, 27, 0.15);
    color: #f5b81b;
}

.icon-urgent {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.icon-resolved {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.icon-total {
    background: rgba(0, 229, 255, 0.15);
    color: #00e5ff;
}

.stat-content-complaints {
    flex: 1;
}

.stat-label-complaints {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    display: block;
    margin-bottom: 0.125rem;
}

.stat-value-complaints {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0;
}

@media (min-width: 768px) {
    .stat-value-complaints {
        font-size: 1.75rem;
    }
}

.pending-value { color: #f5b81b; }
.urgent-value { color: #ef4444; }
.resolved-value { color: #10b981; }
.total-value { color: #00e5ff; }

/* Search Container */
.search-container-complaints {
    margin-bottom: 1.5rem;
}

.search-wrapper-complaints {
    position: relative;
    width: 100%;
}

.search-input-complaints {
    width: 100%;
    padding: 0.875rem 1.25rem 0.875rem 3rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.25);
    border-radius: 60px;
    font-size: 0.875rem;
    color: #e2e8f0;
    outline: none;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.search-input-complaints:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 0 3px rgba(245, 184, 27, 0.1);
}

.search-input-complaints::placeholder {
    color: #64748b;
}

.search-icon-complaints {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 0.875rem;
    pointer-events: none;
}

/* Table Header */
.table-header-complaints {
    display: none;
    grid-template-columns: 2fr 1.5fr 2fr 1fr 1fr;
    padding: 0.75rem 1.25rem;
    background: rgba(15, 17, 21, 0.8);
    border-radius: 1rem;
    margin-bottom: 0.75rem;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(245, 184, 27, 0.1);
}

@media (min-width: 1024px) {
    .table-header-complaints {
        display: grid;
    }
}

.header-cell-complaints {
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.7;
}

.text-center {
    text-align: center;
}

/* Complaints List */
.complaints-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Complaint Card */
.complaint-card {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 20px;
    border: 1px solid rgba(245, 184, 27, 0.12);
    transition: all 0.3s ease;
    overflow: hidden;
}

.complaint-card:hover {
    border-color: rgba(245, 184, 27, 0.3);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    transform: translateY(-2px);
}

.complaint-card-inner {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.875rem;
}

@media (min-width: 1024px) {
    .complaint-card-inner {
        display: grid;
        grid-template-columns: 2fr 1.5fr 2fr 1fr 1fr;
        padding: 1rem 1.25rem;
        align-items: center;
        gap: 0;
    }
}

/* Mobile Label */
.mobile-label {
    display: inline-block;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.6;
    margin-bottom: 0.25rem;
    width: 80px;
}

@media (min-width: 1024px) {
    .mobile-label {
        display: none;
    }
}

/* Ticket Info */
.ticket-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ticket-avatar {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
}

.bg-pending {
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
}

.bg-resolved {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
}

.ticket-id {
    font-weight: 800;
    background: linear-gradient(135deg, #f5b81b, #ffcc44);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    font-size: 0.875rem;
}

.ticket-tenant {
    font-weight: 600;
    color: #e2e8f0;
    font-size: 0.875rem;
    margin-top: 0.125rem;
}

/* Property Info */
.property-info {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.property-name {
    font-weight: 600;
    color: #00e5ff;
    font-size: 0.875rem;
    font-style: italic;
}

/* Subject Info */
.subject-info {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.subject-text {
    font-size: 0.875rem;
    color: #94a3b8;
    font-weight: 500;
}

/* Status Badge */
.status-info {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
    border: 1px solid rgba(245, 184, 27, 0.25);
}

.status-resolved {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.25);
}

.status-dot {
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 50%;
}

.dot-pending {
    background: #f5b81b;
    box-shadow: 0 0 6px #f5b81b;
    animation: pulse 1.5s infinite;
}

.dot-resolved {
    background: #10b981;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
}

@media (min-width: 1024px) {
    .action-buttons {
        justify-content: center;
    }
}

.action-btns-wrapper {
    display: flex;
    gap: 0.75rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 40px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    background: transparent;
    font-family: 'Inter', sans-serif;
}

.view-btn {
    color: #00e5ff;
    background: rgba(0, 229, 255, 0.08);
    border: 1px solid rgba(0, 229, 255, 0.25);
}

.view-btn:hover {
    background: rgba(0, 229, 255, 0.18);
    border-color: #00e5ff;
    transform: translateY(-1px);
}

.resolve-btn {
    color: #10b981;
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.25);
}

.resolve-btn:hover {
    background: rgba(16, 185, 129, 0.18);
    border-color: #10b981;
    transform: translateY(-1px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: rgba(15, 17, 21, 0.6);
    border-radius: 28px;
    border: 1px solid rgba(245, 184, 27, 0.1);
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

/* Modal Styles */
.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
}

.modal-container {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 32px;
    width: 100%;
    max-width: 550px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.5rem 1.75rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.1);
}

.modal-title {
    font-size: 1.125rem;
    font-weight: 800;
    background: linear-gradient(135deg, #f5b81b, #ffcc44);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    margin-bottom: 0.25rem;
}

.modal-subtitle {
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
}

.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
}

.modal-close:hover {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.modal-body {
    padding: 1.5rem 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.detail-field {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.detail-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.detail-label {
    font-size: 0.625rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f5b81b;
    opacity: 0.7;
}

.detail-value {
    font-weight: 600;
    color: #e2e8f0;
    font-size: 0.875rem;
    margin: 0;
}

.property-detail {
    color: #00e5ff;
    font-style: italic;
}

.subject-detail {
    font-weight: 500;
}

.text-pending { color: #f5b81b; }
.text-resolved { color: #10b981; }

.description-box {
    background: rgba(0, 0, 0, 0.35);
    border-radius: 20px;
    padding: 1rem;
    border: 1px solid rgba(245, 184, 27, 0.1);
    margin-top: 0.25rem;
}

.description-text {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.modal-footer {
    padding: 1rem 1.75rem 1.75rem;
}

.modal-btn {
    width: 100%;
    padding: 0.875rem;
    border-radius: 60px;
    font-weight: 800;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}

.modal-btn.secondary {
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
    border: 1px solid rgba(245, 184, 27, 0.25);
}

.modal-btn.secondary:hover {
    background: rgba(245, 184, 27, 0.2);
    transform: translateY(-1px);
}

/* Resolve Modal */
.resolve-modal {
    text-align: center;
    max-width: 420px;
}

.resolve-icon {
    width: 70px;
    height: 70px;
    background: rgba(16, 185, 129, 0.12);
    border-radius: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1.5rem auto 0;
    font-size: 2rem;
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.resolve-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    margin: 1.25rem 0 0.5rem;
}

.resolve-text {
    font-size: 0.875rem;
    color: #94a3b8;
    padding: 0 1rem;
    margin-bottom: 1.5rem;
}

.ticket-highlight {
    color: #f5b81b;
    font-weight: 700;
}

.resolve-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 0 1.5rem 1.5rem;
}

.resolve-confirm-btn {
    width: 100%;
    padding: 0.875rem;
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    border-radius: 60px;
    color: #ffffff;
    font-weight: 800;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.resolve-confirm-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}

.resolve-cancel-btn {
    width: 100%;
    padding: 0.875rem;
    background: transparent;
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 60px;
    color: #ef4444;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.resolve-cancel-btn:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
}

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

@media (max-width: 768px) {
    .resolve-buttons {
        padding: 0 1rem 1.25rem;
    }
    
    .modal-body {
        padding: 1.25rem;
    }
    
    .modal-header {
        padding: 1.25rem;
    }
    
    .detail-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .modal-title {
        font-size: 1rem;
    }
}
</style>
@endsection
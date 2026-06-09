@extends('layouts.admin')

@section('content')
<div class="permit-numbers-container">
    <div class="permit-numbers-wrapper">
        <div class="permit-numbers-header">
            <div>
                <h2 class="page-title">Permit Numbers Management</h2>
                <p class="page-subtitle">Manage valid permit numbers for owner registration</p>
            </div>
            <button class="add-permit-btn" id="addPermitBtn">
                Add Permit Number
            </button>
        </div>

        <div class="permits-table-container">
            <table class="permits-table">
                <thead>
                    <tr>
                        <th>Permit Number</th>
                        <th>Permit Type</th>
                        <th>Owner Name</th>
                        <th>Property Name</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permits ?? [] as $permit)
                    <tr>
                        <td class="permit-number">{{ $permit->permit_number }}</td>
                        <td>
                            @if($permit->permit_type == 'residential')
                                <span class="type-badge residential">Residential</span>
                            @elseif($permit->permit_type == 'business')
                                <span class="type-badge business">Business</span>
                            @else
                                <span class="type-badge unknown">Unknown</span>
                            @endif
                        </td>
                        <td>{{ $permit->owner_name }}</td>
                        <td>{{ $permit->property_name ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge {{ $permit->status == 'active' ? 'status-active' : 'status-used' }}">
                                {{ ucfirst($permit->status) }}
                            </span>
                        </td>
                        <td>{{ date('M d, Y', strtotime($permit->created_at)) }}</td>
                        <td>
                            @if($permit->status == 'active')
                            <button onclick="showDeleteConfirmation({{ $permit->id }})" class="action-btn delete-btn">
                                Delete
                            </button>
                            @else
                            <span class="used-badge">Used</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <p>No permit numbers added yet</p>
                            <span>Click "Add Permit Number" to get started</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Permit Modal --}}
<div id="addPermitModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Permit Number</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('permit-numbers.store') }}">
            @csrf
            <div class="form-group">
                <label>Permit Number *</label>
                <input type="text" name="permit_number" id="permit_number" placeholder="2026-0105512000-0374" required>
                <small>Format: YYYY-XXXXXXXXXX-XXXX (e.g., 2026-0105512000-0374)</small>
            </div>
            <div class="form-group">
                <label>Permit Type *</label>
                <select name="permit_type" id="permit_type" class="form-select" required>
                    <option value="">Select Permit Type</option>
                    <option value="residential">Residential / Apartment Permit</option>
                    <option value="business">Business / Commercial Permit</option>
                </select>
            </div>
            <div class="form-group">
                <label>Owner Name *</label>
                <input type="text" name="owner_name" placeholder="Full name of owner" required>
            </div>
            <div class="form-group">
                <label>Property Name (Optional)</label>
                <input type="text" name="property_name" placeholder="Name of property or business">
            </div>
            <div class="form-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit-btn">Add Permit</button>
            </div>
        </form>
    </div>
</div>

{{-- Password Confirmation Modal for Delete --}}
<div id="passwordConfirmModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Admin Verification Required</h3>
            <button class="modal-close" onclick="closePasswordModal()">&times;</button>
        </div>
        <div class="form-group">
            <label>Enter Admin Password</label>
            <input type="password" id="admin_password" placeholder="Enter your admin password" autocomplete="off">
            <small>Admin password is required to delete permit numbers</small>
        </div>
        <div class="form-actions">
            <button type="button" class="cancel-btn" onclick="closePasswordModal()">Cancel</button>
            <button type="button" class="submit-btn" id="confirmDeleteBtn" onclick="confirmDelete()">
                Confirm Delete
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let pendingDeleteId = null;
    const modal = document.getElementById('addPermitModal');
    const passwordModal = document.getElementById('passwordConfirmModal');
    const addBtn = document.getElementById('addPermitBtn');

    // Auto-format permit number input
    const permitNumberInput = document.getElementById('permit_number');
    if (permitNumberInput) {
        permitNumberInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            let formatted = '';
            
            if (value.length > 0) {
                if (value.length >= 4) {
                    formatted += value.substring(0, 4);
                    if (value.length >= 14) {
                        formatted += '-' + value.substring(4, 14);
                        if (value.length >= 18) {
                            formatted += '-' + value.substring(14, 18);
                        } else if (value.length > 14) {
                            formatted += '-' + value.substring(14);
                        }
                    } else if (value.length > 4) {
                        formatted += '-' + value.substring(4);
                    }
                } else {
                    formatted = value;
                }
                this.value = formatted;
            }
        });
    }

    addBtn.onclick = function(e) {
        e.preventDefault();
        modal.style.display = 'flex';
        const form = modal.querySelector('form');
        if (form) form.reset();
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function closePasswordModal() {
        passwordModal.style.display = 'none';
        document.getElementById('admin_password').value = '';
        pendingDeleteId = null;
    }

    function showDeleteConfirmation(id) {
        pendingDeleteId = id;
        passwordModal.style.display = 'flex';
        setTimeout(() => {
            document.getElementById('admin_password').focus();
        }, 100);
    }

    function confirmDelete() {
        const password = document.getElementById('admin_password').value;
        
        if (!password.trim()) {
            Swal.fire({
                title: 'Password Required',
                text: 'Please enter your admin password to delete this permit number.',
                icon: 'warning',
                confirmButtonColor: '#f5b81b',
                background: '#0f1115',
                color: '#e2e8f0'
            });
            document.getElementById('admin_password').focus();
            return;
        }

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = 'Verifying...';
        confirmBtn.disabled = true;

        fetch(`/permit-numbers/${pendingDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ admin_password: password })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Deleted!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#f5b81b',
                    background: '#0f1115',
                    color: '#e2e8f0',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
                closePasswordModal();
            } else {
                Swal.fire({
                    title: 'Verification Failed',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    background: '#0f1115',
                    color: '#e2e8f0'
                });
                document.getElementById('admin_password').value = '';
                document.getElementById('admin_password').focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                background: '#0f1115',
                color: '#e2e8f0'
            });
        })
        .finally(() => {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        });
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
        if (event.target == passwordModal) {
            closePasswordModal();
        }
    }

    const passwordInput = document.getElementById('admin_password');
    if (passwordInput) {
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                confirmDelete();
            }
        });
    }
</script>

<style>
.permit-numbers-container {
    min-height: 100vh;
    background: #0a0c10;
    padding: 1rem;
}

@media (min-width: 768px) {
    .permit-numbers-container {
        padding: 2rem;
    }
}

.permit-numbers-wrapper {
    max-width: 1280px;
    margin: 0 auto;
}

.permit-numbers-header {
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
    margin-bottom: 0.25rem;
}

.page-subtitle {
    color: #94a3b8;
    font-size: 0.875rem;
}

.add-permit-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.65rem 1.25rem;
    background: #f5b81b;
    color: #0a0c10;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 800;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-permit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(245, 184, 27, 0.3);
}

.permits-table-container {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border-radius: 24px;
    border: 1px solid rgba(245, 184, 27, 0.15);
    overflow-x: auto;
}

.permits-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.permits-table th {
    text-align: left;
    padding: 1rem 1.25rem;
    color: #f5b81b;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(245, 184, 27, 0.1);
}

.permits-table td {
    padding: 1rem 1.25rem;
    color: #e2e8f0;
    font-size: 0.875rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.05);
}

.permit-number {
    font-weight: 700;
    color: #f5b81b;
    font-family: monospace;
}

.type-badge {
    display: inline-flex;
    padding: 0.25rem 0.75rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.type-badge.residential {
    background: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.type-badge.business {
    background: rgba(245, 184, 27, 0.12);
    color: #f5b81b;
    border: 1px solid rgba(245, 184, 27, 0.3);
}

.type-badge.unknown {
    background: rgba(100, 116, 139, 0.12);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.3);
}

.status-badge {
    display: inline-flex;
    padding: 0.25rem 0.75rem;
    border-radius: 40px;
    font-size: 0.7rem;
    font-weight: 700;
}

.status-active {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-used {
    background: rgba(100, 116, 139, 0.12);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.3);
}

.used-badge {
    color: #64748b;
    font-size: 0.7rem;
}

.action-btn {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.7rem;
    font-weight: 600;
}

.action-btn:hover {
    background: #ef4444;
    color: #fff;
    transform: scale(1.05);
}

.empty-state {
    text-align: center;
    padding: 3rem;
}

.empty-state p {
    color: #cbd5e1;
    margin-bottom: 0.25rem;
}

.empty-state span {
    font-size: 0.75rem;
    color: #64748b;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 24px;
    width: 90%;
    max-width: 450px;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(245, 184, 27, 0.1);
}

.modal-header h3 {
    color: #f5b81b;
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: #64748b;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-close:hover {
    color: #ef4444;
    transform: scale(1.1);
}

.form-group {
    padding: 0.75rem 1.5rem;
}

.form-group label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #f5b81b;
    margin-bottom: 0.5rem;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 0.75rem 1rem;
    background: rgba(15, 17, 21, 0.9);
    border: 1px solid rgba(245, 184, 27, 0.2);
    border-radius: 12px;
    color: #e2e8f0;
    font-size: 0.85rem;
    outline: none;
    transition: all 0.2s;
}

.form-group input:focus, .form-group select:focus {
    border-color: #f5b81b;
    box-shadow: 0 0 0 2px rgba(245, 184, 27, 0.1);
}

.form-group select option {
    background: #0f1115;
    color: #e2e8f0;
}

.form-group small {
    display: block;
    margin-top: 5px;
    font-size: 0.7rem;
    color: #64748b;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid rgba(245, 184, 27, 0.1);
}

.cancel-btn {
    flex: 1;
    padding: 0.75rem;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 40px;
    color: #ef4444;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 700;
    font-size: 0.8rem;
}

.cancel-btn:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-1px);
}

.submit-btn {
    flex: 1;
    padding: 0.75rem;
    background: #f5b81b;
    border: none;
    border-radius: 40px;
    color: #0a0c10;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.8rem;
}

.submit-btn:hover {
    background: #e5a800;
    transform: translateY(-1px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
</style>
@endsection
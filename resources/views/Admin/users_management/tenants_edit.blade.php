@extends('layouts.admin')

@section('content')
<div class="tenant-edit-container">
    {{-- Header with breadcrumb --}}
    <div class="edit-header">
        <div class="breadcrumb">
            <a href="{{ route('users-management.tenants.view', $tenant->id) }}" class="breadcrumb-link">
                <i class="fas fa-chevron-left"></i> Back to Profile
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Edit Tenant</span>
        </div>
        <h1 class="edit-title">Edit Tenant Profile</h1>
        <p class="edit-subtitle">Update tenant account information</p>
    </div>

    {{-- Main Card --}}
    <div class="edit-card">
        <form id="editTenantForm" class="edit-form">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            {{-- Avatar Section (Icon only) --}}
            <div class="form-avatar-section">
                <div class="form-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="form-avatar-text">
                    <p class="avatar-label">Tenant Avatar</p>
                    <p class="avatar-hint">Avatar management coming soon</p>
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" name="name" id="name" value="{{ $tenant->name }}" 
                           class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" name="email" id="email" value="{{ $tenant->email }}" 
                           class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" name="password" id="password" 
                           placeholder="Leave blank to keep current" class="form-input">
                    <p class="form-hint">Only fill if you want to change the password</p>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar-alt"></i> Registered Date
                    </label>
                    <div class="form-static">{{ $tenant->created_at->format('F d, Y h:i A') }}</div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="form-actions">
                <button type="submit" id="saveBtn" class="btn btn-primary">
                    <span id="btnText">Save Changes</span>
                    <i id="loadingIcon" class="fas fa-circle-notch fa-spin hidden"></i>
                </button>
                <a href="{{ route('users-management.tenants.view', $tenant->id) }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
            <div id="save-status" class="save-status hidden">Saving...</div>
        </form>
    </div>
</div>

<style>
    .tenant-edit-container {
        padding: 1.5rem;
        max-width: 900px;
        margin: 0 auto;
    }

    /* Header */
    .edit-header {
        margin-bottom: 2rem;
    }
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .breadcrumb-link {
        color: #9ca3af;
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-link:hover {
        color: #f5b81b;
    }
    .breadcrumb-separator {
        color: #4b5563;
    }
    .breadcrumb-current {
        color: #f5b81b;
        font-weight: 500;
    }
    .edit-title {
        font-size: 1.875rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff, #f5b81b);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 0.25rem;
    }
    .edit-subtitle {
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Main Card */
    .edit-card {
        background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
        border-radius: 1.5rem;
        border: 1px solid rgba(245, 184, 27, 0.2);
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        padding: 2rem;
    }

    /* Avatar Section - Icon only */
    .form-avatar-section {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid rgba(245, 184, 27, 0.1);
    }
    .form-avatar {
        width: 80px;
        height: 80px;
        background: #1e1e2a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #f5b81b;
        overflow: hidden;
    }
    /* Icon styling */
    .form-avatar i {
        font-size: 2.5rem;
        color: #f5b81b;
    }
    .avatar-label {
        font-weight: 700;
        color: white;
        margin-bottom: 0.25rem;
    }
    .avatar-hint {
        font-size: 0.75rem;
        color: #6b7280;
    }

    /* Form Grid (unchanged) */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    @media (min-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .form-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #f5b81b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-label i {
        font-size: 0.75rem;
    }
    .form-input {
        background: rgba(15, 17, 21, 0.9);
        border: 1px solid rgba(245, 184, 27, 0.2);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        color: #e5e7eb;
        font-size: 0.875rem;
        transition: all 0.2s;
        outline: none;
    }
    .form-input:focus {
        border-color: #f5b81b;
        box-shadow: 0 0 0 2px rgba(245, 184, 27, 0.2);
    }
    .form-input::placeholder {
        color: #4b5563;
    }
    .form-static {
        background: rgba(15, 17, 21, 0.6);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        color: #9ca3af;
        font-size: 0.875rem;
    }
    .form-hint {
        font-size: 0.7rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    /* Action Buttons (unchanged) */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(245, 184, 27, 0.1);
    }
    .btn {
        padding: 0.6rem 1.5rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-primary {
        background: #f5b81b;
        color: #0a0c10;
    }
    .btn-primary:hover {
        background: #e6a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 184, 27, 0.3);
    }
    .btn-secondary {
        background: rgba(100, 116, 139, 0.2);
        border: 1px solid rgba(100, 116, 139, 0.3);
        color: #cbd5e1;
    }
    .btn-secondary:hover {
        background: rgba(100, 116, 139, 0.4);
        transform: translateY(-2px);
    }
    .save-status {
        text-align: right;
        margin-top: 0.5rem;
        font-size: 0.7rem;
        color: #f5b81b;
    }
    .hidden {
        display: none;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('editTenantForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('saveBtn');
        const btnText = document.getElementById('btnText');
        const loadingIcon = document.getElementById('loadingIcon');
        const statusMsg = document.getElementById('save-status');

        btn.disabled = true;
        btnText.innerText = 'Saving';
        loadingIcon.classList.remove('hidden');
        statusMsg.classList.remove('hidden');

        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            _method: 'PUT',
            _token: '{{ csrf_token() }}'
        };

        fetch("{{ route('users-management.tenants.update', $tenant->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerText = 'Save Changes';
            loadingIcon.classList.add('hidden');
            statusMsg.classList.add('hidden');

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '<span class="text-gold">Success!</span>',
                    text: 'Tenant profile updated successfully.',
                    confirmButtonColor: '#f5b81b',
                    confirmButtonText: 'OK',
                    background: '#0f1115',
                    color: '#fff',
                    customClass: { popup: 'rounded-2xl border border-gold/30' }
                }).then(() => {
                    window.location.href = "{{ route('users-management.tenants.view', $tenant->id) }}";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Update failed. Please try again.',
                    confirmButtonColor: '#f5b81b',
                    background: '#0f1115',
                    color: '#fff'
                });
            }
        })
        .catch(error => {
            console.error(error);
            btn.disabled = false;
            btnText.innerText = 'Save Changes';
            loadingIcon.classList.add('hidden');
            statusMsg.classList.add('hidden');
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Something went wrong. Please try again later.',
                confirmButtonColor: '#f5b81b',
                background: '#0f1115',
                color: '#fff'
            });
        });
    });
</script>
@endsection
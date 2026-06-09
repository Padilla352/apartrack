@extends('owner.layouts.app')

@section('title', 'Edit Tenant')

@section('content')
<div class="tenant-form-container">
    <div class="page-header-flex">
        <div class="header-left">
            <h2 class="page-title">Edit Tenant</h2>
            <p class="page-description">Update tenant information</p>
        </div>
        <div class="header-right">
            <a href="{{ route('owner.tenants.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Tenants
            </a>
        </div>
    </div>

    <div class="form-card">
        <div class="card-header-custom">
            <h5 class="card-title-custom">Edit Tenant: {{ isset($tenant) ? $tenant->first_name . ' ' . $tenant->last_name : '' }}</h5>
        </div>
        <div class="card-body-custom">
            <form action="{{ route('owner.tenants.update', isset($tenant) ? $tenant->id : '') }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Personal Information -->
                <div class="form-section">
                    <h6 class="section-title">Personal Information</h6>
                </div>
                
                <div class="form-row-grid">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name <span class="required-star">*</span></label>
                        <input type="text" 
                               class="form-control-custom @error('first_name') is-invalid @enderror" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', isset($tenant) ? $tenant->first_name : '') }}" 
                               required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name <span class="required-star">*</span></label>
                        <input type="text" 
                               class="form-control-custom @error('last_name') is-invalid @enderror" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', isset($tenant) ? $tenant->last_name : '') }}" 
                               required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span class="required-star">*</span></label>
                        <input type="email" 
                               class="form-control-custom @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', isset($tenant) ? $tenant->email : '') }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number <span class="required-star">*</span></label>
                        <input type="text" 
                               class="form-control-custom @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', isset($tenant) ? $tenant->phone : '') }}" 
                               required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="alternate_phone" class="form-label">Alternate Phone</label>
                        <input type="text" 
                               class="form-control-custom @error('alternate_phone') is-invalid @enderror" 
                               id="alternate_phone" 
                               name="alternate_phone" 
                               value="{{ old('alternate_phone', isset($tenant) ? $tenant->alternate_phone : '') }}">
                        @error('alternate_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Apartment Assignment -->
                <div class="form-section">
                    <h6 class="section-title">Apartment Assignment</h6>
                </div>
                
                <div class="form-row-grid">
                    <div class="form-group">
                        <label for="apartment_id" class="form-label">Assign to Apartment</label>
                        <select class="form-control-custom @error('apartment_id') is-invalid @enderror" 
                                id="apartment_id" 
                                name="apartment_id">
                            <option value="">Select Apartment (Optional)</option>
                            @if(isset($apartments) && $apartments->count() > 0)
                                @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" 
                                        {{ old('apartment_id', isset($tenant) ? $tenant->apartment_id : '') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->unit_number }} - {{ $apartment->name }} 
                                        (₱{{ number_format($apartment->monthly_rent ?? 0, 2) }}/month)
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('apartment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="monthly_rent" class="form-label">Monthly Rent</label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control-custom @error('monthly_rent') is-invalid @enderror" 
                               id="monthly_rent" 
                               name="monthly_rent" 
                               value="{{ old('monthly_rent', isset($tenant) ? $tenant->monthly_rent : '') }}">
                        @error('monthly_rent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Address Information -->
                <div class="form-section">
                    <h6 class="section-title">Address Information</h6>
                </div>
                
                <div class="form-row-grid">
                    <div class="form-group">
                        <label for="address" class="form-label">Street Address</label>
                        <input type="text" 
                               class="form-control-custom @error('address') is-invalid @enderror" 
                               id="address" 
                               name="address" 
                               value="{{ old('address', isset($tenant) ? $tenant->address : '') }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="barangay_id" class="form-label">Barangay</label>
                        <select class="form-control-custom @error('barangay_id') is-invalid @enderror" 
                                id="barangay_id" 
                                name="barangay_id">
                            <option value="">Select Barangay</option>
                            @if(isset($barangays) && $barangays->count() > 0)
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->id }}" 
                                        {{ old('barangay_id', isset($tenant) ? $tenant->barangay_id : '') == $barangay->id ? 'selected' : '' }}>
                                        {{ $barangay->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('barangay_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Lease Information -->
                <div class="form-section">
                    <h6 class="section-title">Lease Information</h6>
                </div>
                
                <div class="form-row-grid-3">
                    <div class="form-group">
                        <label for="move_in_date" class="form-label">Move-in Date <span class="required-star">*</span></label>
                        <input type="date" 
                               class="form-control-custom @error('move_in_date') is-invalid @enderror" 
                               id="move_in_date" 
                               name="move_in_date" 
                               value="{{ old('move_in_date', isset($tenant) && $tenant->move_in_date ? date('Y-m-d', strtotime($tenant->move_in_date)) : '') }}" 
                               required>
                        @error('move_in_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="lease_end_date" class="form-label">Lease End Date</label>
                        <input type="date" 
                               class="form-control-custom @error('lease_end_date') is-invalid @enderror" 
                               id="lease_end_date" 
                               name="lease_end_date" 
                               value="{{ old('lease_end_date', isset($tenant) && $tenant->lease_end_date ? date('Y-m-d', strtotime($tenant->lease_end_date)) : '') }}">
                        @error('lease_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="security_deposit" class="form-label">Security Deposit</label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control-custom @error('security_deposit') is-invalid @enderror" 
                               id="security_deposit" 
                               name="security_deposit" 
                               value="{{ old('security_deposit', isset($tenant) ? $tenant->security_deposit : '') }}">
                        @error('security_deposit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status <span class="required-star">*</span></label>
                        <select class="form-control-custom @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="Active" {{ old('status', isset($tenant) ? $tenant->status : '') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status', isset($tenant) ? $tenant->status : '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Pending" {{ old('status', isset($tenant) ? $tenant->status : '') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Evicted" {{ old('status', isset($tenant) ? $tenant->status : '') == 'Evicted' ? 'selected' : '' }}>Evicted</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Emergency Contact -->
                <div class="form-section">
                    <h6 class="section-title">Emergency Contact</h6>
                </div>
                
                @php
                    // Get emergency contact - it might be array or string
                    $emergency = isset($tenant) && $tenant->emergency_contact ? $tenant->emergency_contact : [];
                    
                    // If it's a string, decode it, otherwise use as is
                    if (is_string($emergency)) {
                        $emergency = json_decode($emergency, true) ?: [];
                    }
                    
                    // Ensure it's an array
                    if (!is_array($emergency)) {
                        $emergency = [];
                    }
                @endphp
                
                <div class="form-row-grid-3">
                    <div class="form-group">
                        <label for="emergency_name" class="form-label">Emergency Contact Name</label>
                        <input type="text" 
                               class="form-control-custom" 
                               id="emergency_name" 
                               name="emergency_contact[name]" 
                               value="{{ old('emergency_contact.name', isset($emergency['name']) ? $emergency['name'] : '') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="emergency_relationship" class="form-label">Relationship</label>
                        <input type="text" 
                               class="form-control-custom" 
                               id="emergency_relationship" 
                               name="emergency_contact[relationship]" 
                               value="{{ old('emergency_contact.relationship', isset($emergency['relationship']) ? $emergency['relationship'] : '') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="emergency_phone" class="form-label">Emergency Phone</label>
                        <input type="text" 
                               class="form-control-custom" 
                               id="emergency_phone" 
                               name="emergency_contact[phone]" 
                               value="{{ old('emergency_contact.phone', isset($emergency['phone']) ? $emergency['phone'] : '') }}">
                    </div>
                </div>
                
                <div class="form-group-full">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-textarea-custom @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3">{{ old('notes', isset($tenant) ? $tenant->notes : '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('owner.tenants.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Update Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Tenant Edit Form Styles - Pure CSS */
    .tenant-form-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    /* Page Header */
    .page-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #000333;
        margin-bottom: 0.25rem;
    }
    
    .page-description {
        color: #6b7280;
        margin: 0;
    }
    
    .btn-back {
        background: #6c757d;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    .btn-back:hover {
        background: #5a6268;
        transform: translateY(-1px);
        color: white;
    }
    
    /* Form Card */
    .form-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .card-header-custom {
        padding: 1rem 1.5rem;
        background: white;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .card-title-custom {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: #1f2937;
    }
    
    .card-body-custom {
        padding: 1.5rem;
    }
    
    /* Form Sections */
    .form-section {
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1rem;
    }
    
    /* Form Layouts */
    .form-row-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-row-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-group-full {
        margin-top: 1rem;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }
    
    .required-star {
        color: #ef4444;
    }
    
    .form-control-custom {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        width: 100%;
    }
    
    .form-control-custom:focus {
        outline: none;
        border-color: #007BFF;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
    }
    
    .form-textarea-custom {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        resize: vertical;
        font-family: inherit;
    }
    
    .form-textarea-custom:focus {
        outline: none;
        border-color: #007BFF;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
    }
    
    /* Validation Feedback */
    .invalid-feedback {
        font-size: 0.75rem;
        color: #dc2626;
        margin-top: 0.25rem;
    }
    
    .is-invalid {
        border-color: #dc2626 !important;
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-cancel:hover {
        background: #e5e7eb;
    }
    
    .btn-save {
        background: #007BFF;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-save:hover {
        background: #0056b3;
        transform: translateY(-1px);
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .page-title {
            color: #EDEDEC;
        }
        
        .page-description {
            color: #9ca3af;
        }
        
        .form-card {
            background: #1f2937;
            border-color: #374151;
        }
        
        .card-header-custom {
            background: #1f2937;
            border-bottom-color: #374151;
        }
        
        .card-title-custom {
            color: #f3f4f6;
        }
        
        .card-body-custom {
            background: #1f2937;
        }
        
        .section-title {
            color: #e5e7eb;
            border-bottom-color: #374151;
        }
        
        .form-label {
            color: #e5e7eb;
        }
        
        .form-control-custom,
        .form-textarea-custom {
            background: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }
        
        .form-control-custom:focus,
        .form-textarea-custom:focus {
            border-color: #007BFF;
        }
        
        .form-control-custom option {
            background: #374151;
        }
        
        .form-actions {
            border-top-color: #374151;
        }
        
        .btn-cancel {
            background: #374151;
            color: #e5e7eb;
        }
        
        .btn-cancel:hover {
            background: #4b5563;
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .form-row-grid,
        .form-row-grid-3 {
            grid-template-columns: 1fr;
        }
        
        .card-body-custom {
            padding: 1rem;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-cancel,
        .btn-save {
            justify-content: center;
            width: 100%;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
    }
</style>
@endsection
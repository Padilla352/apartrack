@extends('owner.layouts.app')

@section('title', 'Revise Apartment Listing')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>Revise Apartment Listing</h3>
                    <p class="text-muted">Please review the rejection reason and update your listing.</p>
                </div>
                <div class="card-body">
                    @if($apartment->rejection_reason)
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-info-circle"></i> Rejection Reason:</strong>
                            <p class="mt-2 mb-0">{{ $apartment->rejection_reason }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('owner.apartments.resubmit', $apartment->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="name">Property Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $apartment->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="unit_number">Unit Number <span class="text-danger">*</span></label>
                            <input type="text" name="unit_number" id="unit_number" class="form-control" value="{{ old('unit_number', $apartment->unit_number) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="type">Property Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="Apartment" {{ $apartment->type == 'Apartment' ? 'selected' : '' }}>Apartment</option>
                                <option value="Studio" {{ $apartment->type == 'Studio' ? 'selected' : '' }}>Studio</option>
                                <option value="Loft" {{ $apartment->type == 'Loft' ? 'selected' : '' }}>Loft</option>
                                <option value="Condominium" {{ $apartment->type == 'Condominium' ? 'selected' : '' }}>Condominium</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="bedrooms">Bedrooms</label>
                                    <input type="number" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', $apartment->bedrooms) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="bathrooms">Bathrooms</label>
                                    <input type="number" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', $apartment->bathrooms) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="floor_area_sqm">Floor Area (sqm)</label>
                                    <input type="number" name="floor_area_sqm" id="floor_area_sqm" class="form-control" value="{{ old('floor_area_sqm', $apartment->floor_area_sqm) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="monthly_rent">Monthly Rent <span class="text-danger">*</span></label>
                            <input type="number" name="monthly_rent" id="monthly_rent" class="form-control" value="{{ old('monthly_rent', $apartment->monthly_rent) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="barangay_name">Barangay <span class="text-danger">*</span></label>
                            <select name="barangay_name" id="barangay_name" class="form-control" required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->name }}" {{ $apartment->barangay_name == $barangay->name ? 'selected' : '' }}>
                                        {{ $barangay->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address">Complete Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2" required>{{ old('address', $apartment->address) }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="permit_number">Permit Number <span class="text-danger">*</span></label>
                            <input type="text" name="permit_number" id="permit_number" class="form-control" value="{{ old('permit_number', $apartment->permit_number) }}" required>
                            <small class="text-muted">Your business/property permit number</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $apartment->description) }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label>Current Images</label>
                            <div class="row">
                                @if(is_array($apartment->images) && count($apartment->images) > 0)
                                    @foreach($apartment->images as $image)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No images uploaded</p>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="images">Add New Images</label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                            <small class="text-muted">You can add multiple images (max 2MB each)</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Resubmit for Review
                            </button>
                            <a href="{{ route('owner.apartments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .form-control:focus {
        border-color: #f5b81b;
        box-shadow: 0 0 0 0.2rem rgba(245, 184, 27, 0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #f5b81b, #d4af37);
        border: none;
        color: #1a1a1a;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #d4af37, #f5b81b);
        transform: translateY(-1px);
    }
</style>
@endsection
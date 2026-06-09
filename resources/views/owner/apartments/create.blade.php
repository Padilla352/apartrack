@extends('owner.layouts.app')

@section('title', 'Add New Apartment')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Add New Apartment</h2>
        <p class="text-muted">Register a new apartment unit</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('owner.apartments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Apartments
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Apartment Information</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.apartments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-12 mb-3">
                    <h6 class="border-bottom pb-2">Basic Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="unit_number" class="form-label">Unit Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('unit_number') is-invalid @enderror" id="unit_number" name="unit_number" value="{{ old('unit_number') }}" required>
                    @error('unit_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Apartment Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="type" class="form-label">Apartment Type <span class="text-danger">*</span></label>
                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="Studio" {{ old('type') == 'Studio' ? 'selected' : '' }}>Studio</option>
                        <option value="1BR" {{ old('type') == '1BR' ? 'selected' : '' }}>1 Bedroom</option>
                        <option value="2BR" {{ old('type') == '2BR' ? 'selected' : '' }}>2 Bedroom</option>
                        <option value="3BR" {{ old('type') == '3BR' ? 'selected' : '' }}>3 Bedroom</option>
                        <option value="Penthouse" {{ old('type') == 'Penthouse' ? 'selected' : '' }}>Penthouse</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="Vacant" {{ old('status') == 'Vacant' ? 'selected' : '' }}>Vacant</option>
                        <option value="Occupied" {{ old('status') == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Reserved" {{ old('status') == 'Reserved' ? 'selected' : '' }}>Reserved</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="images" class="form-label">Apartment Images <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple onchange="previewMultipleImages(this)">
                    @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">You can select multiple images. Max 2MB each.</small>
                    <div id="multiImagePreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>
                
                <!-- Financial Information -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Financial Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="monthly_rent" class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('monthly_rent') is-invalid @enderror" id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent') }}" required>
                    @error('monthly_rent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <!-- Location Information -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Location Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="barangay_name" class="form-label">Barangay <span class="text-danger">*</span></label>
                    <select class="form-control @error('barangay_name') is-invalid @enderror" id="barangay_name" name="barangay_name" required>
                        <option value="">Select Barangay</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay->name }}" {{ old('barangay_name') == $barangay->name ? 'selected' : '' }}>{{ $barangay->name }}</option>
                        @endforeach
                    </select>
                    @error('barangay_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">Street Address <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- ========== GOOGLE MAPS LOCATION PICKER ========== -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Exact Location (Drag the marker or click on map) <span class="text-danger">*</span></label>
                    <div id="locationPickerMap" style="height: 400px; width: 100%; border-radius: 12px; border: 1px solid #ddd;"></div>
                    <small class="text-muted">Click or drag the marker to set the exact location of the apartment.</small>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                    <div id="coordDisplay" class="mt-2 text-muted small"></div>
                </div>
                
                <!-- Specifications -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Specifications</h6>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="bedrooms" class="form-label">Bedrooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('bedrooms') is-invalid @enderror" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', 1) }}" min="0" required>
                    @error('bedrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="bathrooms" class="form-label">Bathrooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('bathrooms') is-invalid @enderror" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', 1) }}" min="0" required>
                    @error('bathrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="floor_area_sqm" class="form-label">Floor Area (sqm)</label>
                    <input type="number" class="form-control @error('floor_area_sqm') is-invalid @enderror" id="floor_area_sqm" name="floor_area_sqm" value="{{ old('floor_area_sqm') }}" min="0">
                    @error('floor_area_sqm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <!-- Amenities -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Amenities</h6>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="row">
                        @php $amenitiesList = ['Pool', 'Gym', 'Parking', 'Balcony', 'Garden', 'Security', 'CCTV', 'Backup Power', 'Water Tank', 'Elevator', 'Clubhouse', 'Playground']; @endphp
                        @foreach($amenitiesList as $amenity)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" id="amenity_{{ $loop->index }}" {{ is_array(old('amenities')) && in_array($amenity, old('amenities')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="amenity_{{ $loop->index }}">{{ $amenity }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('amenities')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                
                <!-- Description -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Description</h6>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Apartment Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('owner.apartments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Apartment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewMultipleImages(input) {
        const previewDiv = document.getElementById('multiImagePreview');
        previewDiv.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxHeight = '100px';
                    img.style.margin = '5px';
                    img.classList.add('img-thumbnail');
                    previewDiv.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // ========== GOOGLE MAPS LOCATION PICKER ==========
    let map, marker;
    let defaultLat = 16.0489;   // Binalonan center
    let defaultLng = 120.3364;

    function initLocationPicker() {
        const mapDiv = document.getElementById('locationPickerMap');
        if (!mapDiv) return;

        const initialLat = {{ old('latitude', 'null') }} ? parseFloat({{ old('latitude', 'null') }}) : defaultLat;
        const initialLng = {{ old('longitude', 'null') }} ? parseFloat({{ old('longitude', 'null') }}) : defaultLng;
        
        const mapOptions = {
            center: { lat: initialLat, lng: initialLng },
            zoom: 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: true,
            fullscreenControl: true
        };
        map = new google.maps.Map(mapDiv, mapOptions);

        // Create draggable marker
        marker = new google.maps.Marker({
            position: { lat: initialLat, lng: initialLng },
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: 'Drag to set exact location'
        });

        // Update hidden fields when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function(event) {
            updatePosition(event.latLng.lat(), event.latLng.lng());
        });

        // Also update when clicking on map
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            updatePosition(event.latLng.lat(), event.latLng.lng());
        });

        function updatePosition(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('coordDisplay').innerHTML = `📍 Selected location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        // Set initial values if present
        if ({{ old('latitude', 'null') }} && {{ old('longitude', 'null') }}) {
            updatePosition(initialLat, initialLng);
        } else {
            updatePosition(defaultLat, defaultLng);
        }
    }

    // Load Google Maps API
    function loadGoogleMapsApi() {
        if (typeof google !== 'undefined' && google.maps) {
            initLocationPicker();
            return;
        }
        const apiKey = @json(config('services.google_maps.key'));
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initLocationPicker`;
        script.async = true;
        script.defer = true;
        window.initLocationPicker = initLocationPicker;
        document.head.appendChild(script);
    }

    document.addEventListener('DOMContentLoaded', loadGoogleMapsApi);
</script>
@endpush
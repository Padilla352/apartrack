@extends('owner.layouts.app')

@section('title', 'Edit Apartment - ' . $apartment->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Edit Apartment</h2>
        <p class="text-muted">Update apartment details</p>
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
        <form id="apartmentForm" action="{{ route('owner.apartments.update', $apartment->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-12 mb-3">
                    <h6 class="border-bottom pb-2">Basic Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="unit_number" class="form-label">Unit Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="unit_number" name="unit_number" value="{{ old('unit_number', $apartment->unit_number) }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Apartment Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $apartment->name) }}" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="type" class="form-label">Apartment Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="Studio" {{ old('type', $apartment->type) == 'Studio' ? 'selected' : '' }}>Studio</option>
                        <option value="1BR" {{ old('type', $apartment->type) == '1BR' ? 'selected' : '' }}>1 Bedroom</option>
                        <option value="2BR" {{ old('type', $apartment->type) == '2BR' ? 'selected' : '' }}>2 Bedroom</option>
                        <option value="3BR" {{ old('type', $apartment->type) == '3BR' ? 'selected' : '' }}>3 Bedroom</option>
                        <option value="Penthouse" {{ old('type', $apartment->type) == 'Penthouse' ? 'selected' : '' }}>Penthouse</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Vacant" {{ old('status', $apartment->status) == 'Vacant' ? 'selected' : '' }}>Vacant</option>
                        <option value="Occupied" {{ old('status', $apartment->status) == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="Maintenance" {{ old('status', $apartment->status) == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Reserved" {{ old('status', $apartment->status) == 'Reserved' ? 'selected' : '' }}>Reserved</option>
                    </select>
                </div>
                
                <!-- Existing Images -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Current Images</label>
                    <div id="existingImages" class="d-flex flex-wrap gap-2">
                        @php $images = is_string($apartment->images) ? json_decode($apartment->images, true) : $apartment->images; @endphp
                        @if(!empty($images))
                            @foreach($images as $img)
                                <div class="position-relative existing-img-item" data-path="{{ $img }}">
                                    <img src="{{ Storage::url($img) }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                    <button type="button" class="btn btn-danger btn-sm remove-existing-img" style="position: absolute; top: -5px; right: -5px; border-radius: 50%;">×</button>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No images uploaded yet.</p>
                        @endif
                    </div>
                    <input type="hidden" name="removed_images" id="removedImages" value="">
                </div>
                
                <!-- New Images Upload -->
                <div class="col-md-12 mb-3">
                    <label for="new_images" class="form-label">Add New Images</label>
                    <input type="file" class="form-control" id="new_images" name="new_images[]" accept="image/*" multiple onchange="previewNewImages(this)">
                    <div id="newImagePreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>
                
                <!-- Financial Information -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Financial Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="monthly_rent" class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent', $apartment->monthly_rent) }}" required>
                </div>
                
                <!-- Location Information -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Location Information</h6>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="barangay_name" class="form-label">Barangay <span class="text-danger">*</span></label>
                    <select class="form-control" id="barangay_name" name="barangay_name" required>
                        <option value="">Select Barangay</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay->name }}" {{ old('barangay_name', $apartment->barangay_name) == $barangay->name ? 'selected' : '' }}>{{ $barangay->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">Street Address <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $apartment->address) }}" required>
                </div>

                <!-- ========== GOOGLE MAPS LOCATION PICKER ========== -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Exact Location (Drag the marker or click on map)</label>
                    <div id="locationPickerMap" style="height: 400px; width: 100%; border-radius: 12px; border: 1px solid #ddd;"></div>
                    <small class="text-muted">Click or drag the marker to set the exact location of the apartment.</small>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $apartment->latitude) }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $apartment->longitude) }}">
                    <div id="coordDisplay" class="mt-2 text-muted small"></div>
                </div>
                
                <!-- Specifications -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Specifications</h6>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="bedrooms" class="form-label">Bedrooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $apartment->bedrooms ?? 1) }}" min="0" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="bathrooms" class="form-label">Bathrooms <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $apartment->bathrooms ?? 1) }}" min="0" required>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="floor_area_sqm" class="form-label">Floor Area (sqm)</label>
                    <input type="number" class="form-control" id="floor_area_sqm" name="floor_area_sqm" value="{{ old('floor_area_sqm', $apartment->floor_area_sqm) }}" min="0">
                </div>
                
                <!-- Amenities -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Amenities</h6>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="row">
                        @php 
                            $amenitiesList = ['Pool', 'Gym', 'Parking', 'Balcony', 'Garden', 'Security', 'CCTV', 'Backup Power', 'Water Tank', 'Elevator', 'Clubhouse', 'Playground'];
                            $currentAmenities = is_array($apartment->amenities) ? $apartment->amenities : (json_decode($apartment->amenities, true) ?? []);
                        @endphp
                        @foreach($amenitiesList as $amenity)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" id="amenity_{{ $loop->index }}" {{ in_array($amenity, $currentAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="amenity_{{ $loop->index }}">{{ $amenity }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Description -->
                <div class="col-md-12 mt-3 mb-3">
                    <h6 class="border-bottom pb-2">Description</h6>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Apartment Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $apartment->description) }}</textarea>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('owner.apartments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Apartment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewNewImages(input) {
        const previewDiv = document.getElementById('newImagePreview');
        previewDiv.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxHeight = '80px';
                    img.style.margin = '5px';
                    img.classList.add('img-thumbnail');
                    previewDiv.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // Handle removal of existing images
    let removedImages = [];
    document.querySelectorAll('.remove-existing-img').forEach(btn => {
        btn.addEventListener('click', function() {
            const parent = this.closest('.existing-img-item');
            const imgPath = parent.dataset.path;
            removedImages.push(imgPath);
            document.getElementById('removedImages').value = JSON.stringify(removedImages);
            parent.remove();
        });
    });

    // ========== GOOGLE MAPS LOCATION PICKER ==========
    let map, marker;
    let defaultLat = 16.0489;
    let defaultLng = 120.3364;

    function initLocationPicker() {
        const mapDiv = document.getElementById('locationPickerMap');
        if (!mapDiv) return;

        const currentLat = document.getElementById('latitude').value;
        const currentLng = document.getElementById('longitude').value;
        const initialLat = (currentLat && currentLat !== 'null') ? parseFloat(currentLat) : defaultLat;
        const initialLng = (currentLng && currentLng !== 'null') ? parseFloat(currentLng) : defaultLng;
        
        const mapOptions = {
            center: { lat: initialLat, lng: initialLng },
            zoom: 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: true,
            fullscreenControl: true
        };
        map = new google.maps.Map(mapDiv, mapOptions);

        marker = new google.maps.Marker({
            position: { lat: initialLat, lng: initialLng },
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: 'Drag to set exact location'
        });

        google.maps.event.addListener(marker, 'dragend', function(event) {
            updatePosition(event.latLng.lat(), event.latLng.lng());
        });

        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            updatePosition(event.latLng.lat(), event.latLng.lng());
        });

        function updatePosition(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('coordDisplay').innerHTML = `📍 Selected location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }

        updatePosition(initialLat, initialLng);
    }

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
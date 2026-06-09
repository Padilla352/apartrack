<!-- Add Business Space Modal - Pure CSS Design -->
<div class="modal-overlay" id="addBusinessModal" style="display: none; pointer-events: none;">
    <div class="modal-container-business">
        <div class="modal-content-business">
            <div class="modal-header-business">
                <div>
                    <h5 class="modal-title-business">
                        List your business space
                    </h5>
                    <p class="modal-subtitle-business">Rent out your commercial space</p>
                </div>
                <button type="button" class="modal-close-btn" data-modal-close="addBusinessModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('owner.business.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body-business">
                    <!-- Progress Steps -->
                    <div class="progress-steps-business">
                        <div class="flex-items-center gap-6">
                            <div class="step-item active">
                                <div class="step-circle step-active">1</div>
                                <span class="step-label-text step-label-active">Business info</span>
                            </div>
                            <div class="step-item">
                                <div class="step-circle">2</div>
                                <span class="step-label-text">Location</span>
                            </div>
                            <div class="step-item">
                                <div class="step-circle">3</div>
                                <span class="step-label-text">Features</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Content -->
                    <div class="form-content-business">
                        <div class="two-column-layout-business">
                            <!-- Left Column -->
                            <div class="form-column-left">
                                <div class="form-field">
                                    <label class="form-label-business">Business name <span class="required-star">*</span></label>
                                    <input type="text" name="business_name" class="form-input-business" placeholder="e.g., Coffee Shop, Retail Store" required>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-business">Unit number</label>
                                    <input type="text" name="unit_number" class="form-input-business" placeholder="e.g., Unit B-101">
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-business">Space type <span class="required-star">*</span></label>
                                    <div class="space-type-grid">
                                        <label class="radio-card">
                                            <input type="radio" name="type" value="Office" class="radio-hidden" required>
                                            <div class="radio-card-content">
                                                <i class="fas fa-building radio-icon"></i>
                                                <p class="radio-label-text">Office</p>
                                            </div>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="type" value="Retail" class="radio-hidden">
                                            <div class="radio-card-content">
                                                <i class="fas fa-store radio-icon"></i>
                                                <p class="radio-label-text">Retail</p>
                                            </div>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="type" value="Restaurant" class="radio-hidden">
                                            <div class="radio-card-content">
                                                <i class="fas fa-utensils radio-icon"></i>
                                                <p class="radio-label-text">Restaurant</p>
                                            </div>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="type" value="Warehouse" class="radio-hidden">
                                            <div class="radio-card-content">
                                                <i class="fas fa-warehouse radio-icon"></i>
                                                <p class="radio-label-text">Warehouse</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="two-fields-grid">
                                    <div class="form-field">
                                        <label class="form-label-business">Property price</label>
                                        <div class="currency-input-wrapper">
                                            <span class="currency-symbol-business">₱</span>
                                            <input type="number" name="price" class="form-input-business with-currency" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="form-field">
                                        <label class="form-label-business">Monthly rent</label>
                                        <div class="currency-input-wrapper">
                                            <span class="currency-symbol-business">₱</span>
                                            <input type="number" name="monthly_rent" class="form-input-business with-currency" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="form-column-right">
                                <div class="form-field">
                                    <label class="form-label-business">Location / Barangay <span class="required-star">*</span></label>
                                    <select name="barangay_id" class="form-select-business" required>
                                        <option value="">Select barangay</option>
                                        @foreach($barangays as $barangay)
                                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-business">Street address <span class="required-star">*</span></label>
                                    <input type="text" name="address" class="form-input-business" placeholder="e.g., 123 Commercial Ave" required>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-business">Floor area (sqm)</label>
                                    <input type="number" name="floor_area_sqm" class="form-input-business" placeholder="e.g., 100">
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-business">Status</label>
                                    <select name="status" class="form-select-business">
                                        <option value="Available">Available</option>
                                        <option value="Occupied">Occupied</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Reserved">Reserved</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Business Features -->
                        <div class="section-divider">
                            <h4 class="section-title-business">Business features</h4>
                            <div class="features-grid-business">
                                @php
                                    $businessFeatures = ['Loading Bay', 'Delivery Access', 'Signage Space', 'Street Frontage', 'Corner Lot', 'Drive-thru Capable', 'High Ceiling', 'Ventilation', 'Kitchen Hood', 'Storage Room'];
                                @endphp
                                @foreach($businessFeatures as $feature)
                                    <label class="feature-checkbox">
                                        <input type="checkbox" name="business_features[]" value="{{ $feature }}" class="checkbox-input">
                                        <span class="feature-label">{{ $feature }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="section-divider">
                            <label class="form-label-business">Description</label>
                            <textarea name="description" rows="4" class="form-textarea-business" placeholder="Describe the business space, ideal business types, nearby establishments..."></textarea>
                        </div>
                        
                        <!-- Image Upload -->
                        <div class="section-divider">
                            <label class="form-label-business">Business space photos</label>
                            <div class="upload-area-business" onclick="document.getElementById('businessImage').click()">
                                <input type="file" id="businessImage" name="image" class="hidden" accept="image/*">
                                <i class="fas fa-cloud-upload-alt upload-icon-business"></i>
                                <p class="upload-text-business">Click to upload photo</p>
                                <p class="upload-hint-business">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer-business">
                    <button type="button" class="btn-cancel-business" data-modal-close="addBusinessModal">Cancel</button>
                    <button type="submit" class="btn-submit-business">List business space</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Business Modal Styles - Pure CSS */
    
    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Modal Container */
    .modal-container-business {
        width: 100%;
        max-width: 1200px;
        max-height: 90vh;
        margin: 20px;
        animation: modalSlideUp 0.3s ease-out;
    }
    
    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Modal Content */
    .modal-content-business {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    
    /* Modal Header */
    .modal-header-business {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 2rem;
        border-bottom: 1px solid #f0f0f0;
        background: white;
    }
    
    .modal-title-business {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .modal-subtitle-business {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0.25rem 0 0;
    }
    
    .modal-close-btn {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        color: #9ca3af;
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-close-btn:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    /* Modal Body */
    .modal-body-business {
        flex: 1;
        overflow-y: auto;
    }
    
    /* Progress Steps */
    .progress-steps-business {
        background: #f9fafb;
        padding: 1rem 2rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .flex-items-center {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .step-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .step-circle {
        width: 2rem;
        height: 2rem;
        background: #e5e7eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
    }
    
    .step-active {
        background: #10b981;
        color: white;
    }
    
    .step-label-text {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .step-label-active {
        color: #1f2937;
        font-weight: 500;
    }
    
    /* Form Content */
    .form-content-business {
        padding: 2rem;
    }
    
    .two-column-layout-business {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    
    .form-column-left,
    .form-column-right {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    /* Form Fields */
    .form-field {
        display: flex;
        flex-direction: column;
    }
    
    .form-label-business {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .required-star {
        color: #ef4444;
    }
    
    .form-input-business {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .form-input-business:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }
    
    .form-select-business {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        background: white;
        cursor: pointer;
    }
    
    .form-select-business:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }
    
    .form-textarea-business {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        resize: vertical;
        font-family: inherit;
    }
    
    .form-textarea-business:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }
    
    /* Space Type Radio Cards */
    .space-type-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .radio-card {
        position: relative;
        cursor: pointer;
    }
    
    .radio-hidden {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .radio-card-content {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.2s;
    }
    
    .radio-card:hover .radio-card-content {
        border-color: #10b981;
    }
    
    .radio-hidden:checked + .radio-card-content {
        border-color: #10b981;
        background: #ecfdf5;
    }
    
    .radio-icon {
        font-size: 1.25rem;
        color: #9ca3af;
        margin-bottom: 0.25rem;
    }
    
    .radio-hidden:checked + .radio-card-content .radio-icon {
        color: #10b981;
    }
    
    .radio-label-text {
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0;
        color: #374151;
    }
    
    /* Two Fields Grid */
    .two-fields-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .currency-input-wrapper {
        position: relative;
    }
    
    .currency-symbol-business {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
    }
    
    .with-currency {
        padding-left: 2rem;
    }
    
    /* Section Divider */
    .section-divider {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .section-title-business {
        font-size: 1.125rem;
        font-weight: 500;
        color: #1f2937;
        margin: 0 0 1rem 0;
    }
    
    /* Features Grid */
    .features-grid-business {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .feature-checkbox {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .feature-checkbox:hover {
        border-color: #10b981;
    }
    
    .checkbox-input {
        width: 1rem;
        height: 1rem;
        accent-color: #10b981;
    }
    
    .feature-label {
        font-size: 0.875rem;
        color: #374151;
    }
    
    /* Upload Area */
    .upload-area-business {
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .upload-area-business:hover {
        border-color: #10b981;
        background: #f9fafb;
    }
    
    .upload-icon-business {
        font-size: 2rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }
    
    .upload-text-business {
        color: #6b7280;
        margin: 0;
    }
    
    .upload-hint-business {
        font-size: 0.75rem;
        color: #9ca3af;
        margin: 0.25rem 0 0;
    }
    
    /* Modal Footer */
    .modal-footer-business {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 2rem;
        border-top: 1px solid #f0f0f0;
        background: white;
    }
    
    .btn-cancel-business {
        padding: 0.625rem 1.5rem;
        background: #f3f4f6;
        border: none;
        border-radius: 0.75rem;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-cancel-business:hover {
        background: #e5e7eb;
    }
    
    .btn-submit-business {
        padding: 0.625rem 1.5rem;
        background: #10b981;
        border: none;
        border-radius: 0.75rem;
        font-weight: 500;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-submit-business:hover {
        background: #059669;
        transform: translateY(-1px);
    }
    
    .hidden {
        display: none;
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .modal-content-business {
            background: #1f2937;
        }
        
        .modal-header-business {
            background: #1f2937;
            border-bottom-color: #374151;
        }
        
        .modal-title-business {
            color: #f3f4f6;
        }
        
        .form-label-business {
            color: #e5e7eb;
        }
        
        .form-input-business,
        .form-select-business,
        .form-textarea-business {
            background: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }
        
        .radio-card-content {
            border-color: #4b5563;
        }
        
        .radio-label-text {
            color: #e5e7eb;
        }
        
        .section-title-business {
            color: #f3f4f6;
        }
        
        .feature-label {
            color: #e5e7eb;
        }
        
        .feature-checkbox {
            border-color: #4b5563;
        }
        
        .feature-checkbox:hover {
            border-color: #10b981;
        }
        
        .modal-footer-business {
            background: #1f2937;
            border-top-color: #374151;
        }
        
        .btn-cancel-business {
            background: #374151;
            color: #e5e7eb;
        }
        
        .btn-cancel-business:hover {
            background: #4b5563;
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .two-column-layout-business {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .two-fields-grid {
            grid-template-columns: 1fr;
        }
        
        .space-type-grid {
            grid-template-columns: 1fr;
        }
        
        .features-grid-business {
            grid-template-columns: 1fr;
        }
        
        .flex-items-center {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .modal-header-business {
            padding: 1rem;
        }
        
        .form-content-business {
            padding: 1rem;
        }
        
        .modal-footer-business {
            padding: 1rem;
        }
    }
</style>

<script>
    // Modal handling
    document.addEventListener('DOMContentLoaded', function() {
        // Open modal function
        window.openBusinessModal = function() {
            const modal = document.getElementById('addBusinessModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        };
        
        // Close modal function
        window.closeBusinessModal = function() {
            const modal = document.getElementById('addBusinessModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };
        
        // Close buttons
        document.querySelectorAll('[data-modal-close="addBusinessModal"]').forEach(btn => {
            btn.addEventListener('click', closeBusinessModal);
        });
        
        // Close on overlay click
        const modal = document.getElementById('addBusinessModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBusinessModal();
                }
            });
        }
    });
    
    function updateValue(field, delta) {
        const input = document.getElementById(field);
        let value = parseInt(input.value) + delta;
        if (value < 1) value = 1;
        if (value > 10) value = 10;
        input.value = value;
    }
    
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (preview && input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = preview.querySelector('img') || document.createElement('img');
                img.src = e.target.result;
                img.className = 'max-h-32 rounded-lg mx-auto';
                preview.innerHTML = '';
                preview.appendChild(img);
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
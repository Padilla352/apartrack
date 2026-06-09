<!-- Add New Property Modal - Pure CSS Design -->
<div class="modal-overlay" id="addNewPropertyModal" style="display: none;">
    <div class="modal-container-property">
        <div class="modal-content-property">
            <div class="modal-header-property">
                <div>
                    <h5 class="modal-title-property">
                        Add new property
                    </h5>
                    <p class="modal-subtitle-property">Create a new property listing</p>
                </div>
                <button type="button" class="modal-close-btn" data-modal-close="addNewPropertyModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('owner.apartments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body-property">
                    <!-- Form Content -->
                    <div class="form-content-property">
                        <div class="two-column-layout-property">
                            <!-- Left Column -->
                            <div class="form-column-left">
                                <div class="form-field">
                                    <label class="form-label-property">Property name <span class="required-star">*</span></label>
                                    <input type="text" name="name" class="form-input-property" placeholder="e.g., Modern City Apartment" required>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-property">Unit number <span class="required-star">*</span></label>
                                    <input type="text" name="unit_number" class="form-input-property" placeholder="e.g., 101, A-202" required>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-property">Property type <span class="required-star">*</span></label>
                                    <select name="type" class="form-select-property" required>
                                        <option value="">Select type</option>
                                        <option value="Studio">Studio</option>
                                        <option value="1BR">1 Bedroom</option>
                                        <option value="2BR">2 Bedroom</option>
                                        <option value="3BR">3 Bedroom</option>
                                        <option value="Penthouse">Penthouse</option>
                                    </select>
                                </div>
                                
                                <div class="two-fields-grid-property">
                                    <div class="form-field">
                                        <label class="form-label-property">Bedrooms</label>
                                        <input type="number" name="bedrooms" value="1" class="form-input-property">
                                    </div>
                                    <div class="form-field">
                                        <label class="form-label-property">Bathrooms</label>
                                        <input type="number" name="bathrooms" value="1" class="form-input-property">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="form-column-right">
                                <div class="form-field">
                                    <label class="form-label-property">Location / Barangay <span class="required-star">*</span></label>
                                    <select name="barangay_id" class="form-select-property" required>
                                        <option value="">Select barangay</option>
                                        @foreach($barangays as $barangay)
                                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-property">Street address <span class="required-star">*</span></label>
                                    <input type="text" name="address" class="form-input-property" placeholder="e.g., 123 Main Street" required>
                                </div>
                                
                                <div class="two-fields-grid-property">
                                    <div class="form-field">
                                        <label class="form-label-property">Property price</label>
                                        <div class="currency-input-wrapper">
                                            <span class="currency-symbol-property">₱</span>
                                            <input type="number" name="price" class="form-input-property with-currency" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="form-field">
                                        <label class="form-label-property">Monthly rent</label>
                                        <div class="currency-input-wrapper">
                                            <span class="currency-symbol-property">₱</span>
                                            <input type="number" name="monthly_rent" class="form-input-property with-currency" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-property">Floor area (sqm)</label>
                                    <input type="number" name="floor_area_sqm" class="form-input-property" placeholder="e.g., 45">
                                </div>
                                
                                <div class="form-field">
                                    <label class="form-label-property">Status</label>
                                    <select name="status" class="form-select-property">
                                        <option value="Vacant">Available</option>
                                        <option value="Occupied">Occupied</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Reserved">Reserved</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="section-divider-property">
                            <label class="form-label-property">Description</label>
                            <textarea name="description" rows="4" class="form-textarea-property" placeholder="Describe the property features..."></textarea>
                        </div>
                        
                        <!-- Image Upload -->
                        <div class="section-divider-property">
                            <label class="form-label-property">Property photos</label>
                            <div class="upload-area-property" onclick="document.getElementById('newPropertyImage').click()">
                                <input type="file" id="newPropertyImage" name="image" class="hidden" accept="image/*">
                                <i class="fas fa-cloud-upload-alt upload-icon-property"></i>
                                <p class="upload-text-property">Click to upload photo</p>
                                <p class="upload-hint-property">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer-property">
                    <button type="button" class="btn-cancel-property" data-modal-close="addNewPropertyModal">Cancel</button>
                    <button type="submit" class="btn-submit-property">Add property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Add New Property Modal Styles - Pure CSS */
    
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
    .modal-container-property {
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
    .modal-content-property {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    
    /* Modal Header */
    .modal-header-property {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 2rem;
        border-bottom: 1px solid #f0f0f0;
        background: white;
    }
    
    .modal-title-property {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .modal-subtitle-property {
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
    .modal-body-property {
        flex: 1;
        overflow-y: auto;
    }
    
    /* Form Content */
    .form-content-property {
        padding: 2rem;
    }
    
    .two-column-layout-property {
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
    
    .form-label-property {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .required-star {
        color: #ef4444;
    }
    
    .form-input-property {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .form-input-property:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    
    .form-select-property {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        background: white;
        cursor: pointer;
    }
    
    .form-select-property:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    
    .form-textarea-property {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        resize: vertical;
        font-family: inherit;
    }
    
    .form-textarea-property:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    
    /* Two Fields Grid */
    .two-fields-grid-property {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .currency-input-wrapper {
        position: relative;
    }
    
    .currency-symbol-property {
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
    .section-divider-property {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f0f0f0;
    }
    
    /* Upload Area */
    .upload-area-property {
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .upload-area-property:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    
    .upload-icon-property {
        font-size: 2rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .upload-text-property {
        color: #6b7280;
        margin: 0;
    }
    
    .upload-hint-property {
        font-size: 0.75rem;
        color: #9ca3af;
        margin: 0.25rem 0 0;
    }
    
    /* Modal Footer */
    .modal-footer-property {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 2rem;
        border-top: 1px solid #f0f0f0;
        background: white;
    }
    
    .btn-cancel-property {
        padding: 0.625rem 1.5rem;
        background: #f3f4f6;
        border: none;
        border-radius: 0.75rem;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-cancel-property:hover {
        background: #e5e7eb;
    }
    
    .btn-submit-property {
        padding: 0.625rem 1.5rem;
        background: #3b82f6;
        border: none;
        border-radius: 0.75rem;
        font-weight: 500;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-submit-property:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }
    
    .hidden {
        display: none;
    }
    
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .modal-content-property {
            background: #1f2937;
        }
        
        .modal-header-property {
            background: #1f2937;
            border-bottom-color: #374151;
        }
        
        .modal-title-property {
            color: #f3f4f6;
        }
        
        .modal-subtitle-property {
            color: #9ca3af;
        }
        
        .modal-close-btn {
            color: #9ca3af;
        }
        
        .modal-close-btn:hover {
            background: #374151;
            color: #f3f4f6;
        }
        
        .form-label-property {
            color: #e5e7eb;
        }
        
        .form-input-property,
        .form-select-property,
        .form-textarea-property {
            background: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }
        
        .form-input-property:focus,
        .form-select-property:focus,
        .form-textarea-property:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        
        .currency-symbol-property {
            color: #9ca3af;
        }
        
        .section-divider-property {
            border-top-color: #374151;
        }
        
        .upload-area-property {
            border-color: #4b5563;
        }
        
        .upload-area-property:hover {
            border-color: #3b82f6;
            background: #1e3a5f;
        }
        
        .upload-text-property {
            color: #9ca3af;
        }
        
        .modal-footer-property {
            background: #1f2937;
            border-top-color: #374151;
        }
        
        .btn-cancel-property {
            background: #374151;
            color: #e5e7eb;
        }
        
        .btn-cancel-property:hover {
            background: #4b5563;
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .two-column-layout-property {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .two-fields-grid-property {
            grid-template-columns: 1fr;
        }
        
        .modal-header-property {
            padding: 1rem;
        }
        
        .form-content-property {
            padding: 1rem;
        }
        
        .modal-footer-property {
            padding: 1rem;
            flex-direction: column;
        }
        
        .btn-cancel-property,
        .btn-submit-property {
            width: 100%;
        }
    }
</style>

<script>
    // Modal handling
    document.addEventListener('DOMContentLoaded', function() {
        // Open modal function
        window.openNewPropertyModal = function() {
            const modal = document.getElementById('addNewPropertyModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        };
        
        // Close modal function
        window.closeNewPropertyModal = function() {
            const modal = document.getElementById('addNewPropertyModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };
        
        // Close buttons
        document.querySelectorAll('[data-modal-close="addNewPropertyModal"]').forEach(btn => {
            btn.addEventListener('click', closeNewPropertyModal);
        });
        
        // Close on overlay click
        const modal = document.getElementById('addNewPropertyModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeNewPropertyModal();
                }
            });
        }
        
        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                closeNewPropertyModal();
            }
        });
    });
</script>
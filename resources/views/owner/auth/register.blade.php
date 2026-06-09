<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>APARTRACK - Owner Registration</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; min-height:100vh; }
        .register-background { position:fixed; inset:0; z-index:0; overflow:hidden; }
        .bg-image { position:absolute; width:100%; height:100%; object-fit:cover; object-position:center; }
        .register-background::before { content:''; position:absolute; inset:0; background:rgba(0,3,51,0.7); z-index:1; }
        .register-container { position:relative; z-index:2; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 20px; }
        .register-card { max-width:600px; width:100%; background:white; border-radius:32px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden; animation:slideUp 0.5s ease; }
        @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        .register-header { background:linear-gradient(135deg, #000333 0%, #1a1a4e 100%); padding:40px 30px; text-align:center; }
        .logo-icon { width:70px; height:70px; background:rgba(255,255,255,0.15); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; }
        .logo-icon i { font-size:36px; color:white; }
        .register-header h1 { font-size:28px; font-weight:700; color:white; margin-bottom:8px; }
        .register-header p { font-size:14px; color:rgba(255,255,255,0.7); }
        .register-body { padding:40px 30px; }
        .alert { padding:12px 16px; border-radius:12px; margin-bottom:24px; font-size:14px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .alert-danger { background:#FEF2F2; border:1px solid #FEE2E2; color:#DC2626; }
        .alert-success { background:#ECFDF5; border:1px solid #D1FAE5; color:#059669; }
        .alert ul { margin:0; padding-left:20px; width:100%; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-size:14px; font-weight:600; color:#333; margin-bottom:8px; }
        .password-field-wrapper { position:relative; display:flex; align-items:center; }
        .input-icon { position:absolute; left:16px; color:#9CA3AF; font-size:18px; pointer-events:none; z-index:1; }
        .form-control { width:100%; padding:14px 50px 14px 48px; border:1px solid #E0E0E0; border-radius:14px; font-size:15px; transition:all 0.2s; background:white; }
        .form-control:focus { outline:none; border-color:#007BFF; box-shadow:0 0 0 3px rgba(0,123,255,0.1); }
        .form-control.is-invalid { border-color:#DC2626; }
        select.form-control { 
            appearance: none; 
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E"); 
            background-repeat: no-repeat; 
            background-position: right 16px center; 
            background-size: 20px; 
            padding-right: 40px; 
            cursor: pointer;
        }
        .toggle-password { position:absolute; right:16px; background:none; border:none; cursor:pointer; color:#9CA3AF; font-size:18px; z-index:2; }
        .toggle-password:hover { color:#007BFF; }
        .password-strength-text { font-size:12px; margin-top:8px; color:#666; }
        #strengthValue { font-weight:600; }
        .strength-weak { color:#DC2626; }
        .strength-fair { color:#F59E0B; }
        .strength-good { color:#3B82F6; }
        .strength-strong { color:#10B981; }
        .char-counter { font-size:12px; margin-top:5px; transition:all 0.3s; }
        .char-counter.normal { color:#6B7280; }
        .char-counter.warning { color:#F59E0B; }
        .char-counter.exceed { color:#DC2626; }
        .match-success { color:#10B981; font-size:12px; margin-top:5px; display:flex; align-items:center; gap:5px; }
        .match-error { color:#DC2626; font-size:12px; margin-top:5px; display:flex; align-items:center; gap:5px; }
        .info-text { background:#EFF6FF; padding:12px 16px; border-radius:12px; font-size:13px; margin-top:20px; color:#1D4ED8; display:flex; align-items:center; gap:12px; }
        .info-text.warning { background:#FEF3C7; color:#D97706; }
        .info-text.error { background:#FEF2F2; color:#DC2626; }
        .btn-register { width:100%; background:linear-gradient(135deg, #B4E662 0%, #00A2FF 100%); color:#000333; border:none; padding:14px 24px; border-radius:14px; font-size:16px; font-weight:600; cursor:pointer; transition:all 0.3s; display:flex; align-items:center; justify-content:center; gap:10px; margin-top:10px; }
        .btn-register:hover { transform:translateY(-2px); box-shadow:0 10px 25px -5px rgba(0,162,255,0.4); background:linear-gradient(135deg, #00A2FF 0%, #B4E662 100%); }
        .login-link { text-align:center; margin-top:24px; padding-top:24px; border-top:1px solid #E0E0E0; }
        .login-link a { color:#007BFF; text-decoration:none; font-weight:600; }
        .text-muted { color:#6B7280; font-size:12px; display:block; margin-top:5px; }
        .text-danger { color:#DC2626; font-size:12px; display:block; margin-top:5px; }
        .required-star { color:#DC2626; margin-left:3px; }
        .permit-fields-container { background:#F8FAFC; border-radius:16px; padding:16px; margin-top:8px; border:1px solid #E2E8F0; }
        .permit-fields-container.hidden { display:none; }
        .permit-section { margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #E2E8F0; }
        .permit-section:last-child { margin-bottom:0; padding-bottom:0; border-bottom:none; }
        .permit-label { font-weight:600; font-size:13px; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
        .required-badge { background:#DC2626; color:white; font-size:10px; padding:2px 6px; border-radius:20px; }
        .optional-badge { background:#9CA3AF; color:white; font-size:10px; padding:2px 6px; border-radius:20px; }
        .field-feedback { font-size:12px; margin-top:5px; display:flex; align-items:center; gap:5px; }
        .field-feedback.error { color:#DC2626; }
        .field-feedback.success { color:#10B981; }
        @media (max-width:600px) { 
            .register-header { padding:30px 20px; } 
            .register-body { padding:30px 20px; } 
            .register-header h1 { font-size:24px; } 
            .form-control { padding:12px 45px 12px 42px; font-size:14px; }
        }
    </style>
</head>
<body>
    <div class="register-background">
        <img src="{{ asset('images/BINALONAN TOWNHALL.jpg') }}" alt="Binalonan Townhall" class="bg-image">
    </div>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-icon"><i class="fas fa-user-plus"></i></div>
                <h1>Create Account</h1>
                <p>Register as Property Owner</p>
            </div>
            <div class="register-body">
                @if(session('success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Please fix the following errors:
                        <ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('owner.register.submit') }}" id="registerForm">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Full Name <span class="required-star">*</span></label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Juan Dela Cruz">
                        </div>
                        <small class="text-muted">Only letters, spaces, periods, apostrophes, and hyphens allowed.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span class="required-star">*</span></label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="owner@example.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="09123456789" maxlength="11" inputmode="numeric">
                        </div>
                        <small class="text-muted">Enter exactly 11 digits (e.g., 09123456789). Numbers only.</small>
                    </div>
                    
                    <!-- PROPERTY TYPE DROPDOWN -->
                    <div class="form-group">
                        <label for="property_type">Property Type <span class="required-star">*</span></label>
                        <select id="property_type" name="property_type" class="form-control" required>
                            <option value="" disabled {{ old('property_type') ? '' : 'selected' }}>Select property type</option>
                            <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment (Residential)</option>
                            <option value="both" {{ old('property_type') == 'both' ? 'selected' : '' }}>Both (Apartment & Business)</option>
                        </select>
                        <small class="text-muted"><i class="fas fa-info-circle"></i> Note: Business-only registration is not allowed. You must have an apartment permit.</small>
                    </div>
                    
                    <!-- PERMIT FIELDS -->
                    <div class="permit-fields-container" id="permitFieldsContainer">
                        <!-- Residential/Apartment Permit -->
                        <div class="permit-section" id="residentialPermitSection">
                            <div class="permit-label">
                                <i class="fas fa-home"></i> Residential/Apartment Permit
                                <span id="residentialRequiredBadge" class="required-badge">REQUIRED</span>
                            </div>
                            <div class="password-field-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="residential_permit" name="residential_permit" class="form-control permit-input" value="{{ old('residential_permit') }}" placeholder="2026-0105512000-0374" maxlength="50" autocomplete="off">
                            </div>
                            <div id="residentialPermitFeedback" class="field-feedback" style="display:none;"></div>
                            <small class="text-muted">Format: YYYY-XXXXXXXXXX-XXXX (e.g., 2026-0105512000-0374)</small>
                        </div>
                        
                        <!-- Business Permit -->
                        <div class="permit-section" id="businessPermitSection" style="display: none;">
                            <div class="permit-label">
                                <i class="fas fa-store"></i> Business Permit
                                <span id="businessRequiredBadge" class="required-badge">REQUIRED</span>
                            </div>
                            <div class="password-field-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="business_permit" name="business_permit" class="form-control permit-input" value="{{ old('business_permit') }}" placeholder="2026-0105512000-0374" maxlength="50" autocomplete="off">
                            </div>
                            <div id="businessPermitFeedback" class="field-feedback" style="display:none;"></div>
                            <small class="text-muted">Format: YYYY-XXXXXXXXXX-XXXX (e.g., 2026-0105512000-0374) - Required only if you want to list business spaces</small>
                        </div>
                    </div>
                    
                    <!-- PASSWORD FIELD -->
                    <div class="form-group">
                        <label for="password">Password <span class="required-star">*</span></label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Create a password">
                            <button type="button" class="toggle-password" onclick="togglePassword()"><i class="fas fa-eye-slash" id="toggleIcon"></i></button>
                        </div>
                        <div class="char-counter normal" id="charCounter"><i class="fas fa-keyboard"></i> <span id="charCount">0</span> / 20 characters</div>
                        <div class="password-strength-text">Password Strength: <span id="strengthValue">-</span></div>
                    </div>
                    
                    <!-- CONFIRM PASSWORD -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="required-star">*</span></label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Confirm your password">
                            <button type="button" class="toggle-password" onclick="toggleConfirmPassword()"><i class="fas fa-eye-slash" id="toggleConfirmIcon"></i></button>
                        </div>
                        <div id="confirmMatchMessage" class="match-error" style="display:none;"><i class="fas fa-exclamation-circle"></i> Passwords do not match</div>
                        <div id="confirmSuccessMessage" class="match-success" style="display:none;"><i class="fas fa-check-circle"></i> Passwords match</div>
                    </div>
                    
                    <div class="info-text">
                        <i class="fas fa-envelope"></i>
                        <div><strong>Email Verification Required</strong><br><small>After registration, we'll send a 6-digit OTP code to your email. Please verify to activate your account.</small></div>
                    </div>
                    <div class="info-text warning" style="margin-top:10px;">
                        <i class="fas fa-clipboard-list"></i>
                        <div><strong>Permit Verification</strong><br><small>Your permit(s) will be verified by the admin before your account is fully activated.</small></div>
                    </div>
                    
                    <button type="submit" class="btn-register"><i class="fas fa-user-plus"></i> Register</button>
                    <div class="login-link"><p>Already have an account? <a href="{{ route('owner.login') }}">Login here</a></p></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ========================
        // 1. NAME VALIDATION
        // ========================
        const nameInput = document.getElementById('name');
        
        function cleanNameInput(value) {
            return value.replace(/[^A-Za-z\s\.\'-]/g, '');
        }
        
        function validateName(name) {
            if (name.trim() === '') return true;
            return /^[A-Za-z\s\.\'-]+$/.test(name);
        }
        
        if (nameInput) {
            nameInput.addEventListener('input', function(e) {
                let cleaned = cleanNameInput(this.value);
                if (cleaned !== this.value) this.value = cleaned;
            });
        }
        
        // ========================
        // 2. PHONE VALIDATION
        // ========================
        const phoneInput = document.getElementById('phone');
        
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let digitsOnly = this.value.replace(/\D/g, '');
                if (digitsOnly.length > 11) digitsOnly = digitsOnly.slice(0, 11);
                if (digitsOnly !== this.value) this.value = digitsOnly;
            });
        }
        
        // ========================
        // 3. PERMIT AUTO-FORMAT
        // ========================
        function formatPermitNumber(value) {
            let digits = value.replace(/\D/g, '');
            if (digits.length === 0) return '';
            let parts = [];
            if (digits.length >= 4) {
                parts.push(digits.substring(0, 4));
                let remaining = digits.substring(4);
                if (remaining.length > 0) {
                    let secondLen = Math.min(remaining.length, 10);
                    parts.push(remaining.substring(0, secondLen));
                    remaining = remaining.substring(secondLen);
                    if (remaining.length > 0) {
                        parts.push(remaining.substring(0, 4));
                    }
                }
            } else {
                parts.push(digits);
            }
            return parts.join('-');
        }
        
        function validatePermitFormat(permitNumber) {
            if (!permitNumber) return false;
            const pattern = /^\d{4}-\d{10}-\d{4}$/;
            return pattern.test(permitNumber);
        }
        
        const permitInputs = document.querySelectorAll('.permit-input');
        permitInputs.forEach(permitInput => {
            const feedbackId = permitInput.id === 'residential_permit' ? 'residentialPermitFeedback' : 'businessPermitFeedback';
            const permitFeedback = document.getElementById(feedbackId);
            
            permitInput.addEventListener('input', function(e) {
                let cursorPos = this.selectionStart;
                let oldValue = this.value;
                let formatted = formatPermitNumber(this.value);
                this.value = formatted;
                let newCursorPos = cursorPos;
                if (formatted.length > oldValue.length) {
                    newCursorPos = cursorPos + (formatted.length - oldValue.length);
                }
                this.setSelectionRange(newCursorPos, newCursorPos);
                
                const isValid = validatePermitFormat(this.value);
                if (this.value.length > 0 && !isValid && this.value.length >= 4) {
                    if (permitFeedback) {
                        permitFeedback.style.display = 'flex';
                        permitFeedback.className = 'field-feedback error';
                        permitFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Format: YYYY-XXXXXXXXXX-XXXX (4-10-4 digits)';
                    }
                    this.classList.add('is-invalid');
                } else if (this.value.length > 0 && isValid) {
                    if (permitFeedback) {
                        permitFeedback.style.display = 'flex';
                        permitFeedback.className = 'field-feedback success';
                        permitFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Valid permit format.';
                    }
                    this.classList.remove('is-invalid');
                } else {
                    if (permitFeedback) permitFeedback.style.display = 'none';
                    this.classList.remove('is-invalid');
                }
            });
        });
        
        // ========================
        // 4. PROPERTY TYPE DYNAMICS
        // ========================
        const propertyTypeSelect = document.getElementById('property_type');
        const residentialSection = document.getElementById('residentialPermitSection');
        const businessSection = document.getElementById('businessPermitSection');
        const residentialRequiredBadge = document.getElementById('residentialRequiredBadge');
        const businessRequiredBadge = document.getElementById('businessRequiredBadge');
        const residentialInputField = document.getElementById('residential_permit');
        const businessInputField = document.getElementById('business_permit');
        
        function updatePermitFields() {
            const selectedType = propertyTypeSelect.value;
            
            if (selectedType === 'apartment') {
                // ONLY show Residential Permit for Apartment
                residentialSection.style.display = 'block';
                businessSection.style.display = 'none';  // ← HIDE Business Permit
                residentialRequiredBadge.textContent = 'REQUIRED';
                residentialInputField.required = true;
                businessInputField.required = false;
                businessInputField.value = ''; // Clear business permit value
                
            } else if (selectedType === 'both') {
                // Show BOTH permits for Both
                residentialSection.style.display = 'block';
                businessSection.style.display = 'block';
                residentialRequiredBadge.textContent = 'REQUIRED';
                businessRequiredBadge.textContent = 'REQUIRED';
                residentialInputField.required = true;
                businessInputField.required = true;
                
            } else {
                // Hide both if no selection
                residentialSection.style.display = 'none';
                businessSection.style.display = 'none';
                residentialInputField.required = false;
                businessInputField.required = false;
            }
        }
        
        propertyTypeSelect.addEventListener('change', updatePermitFields);
        updatePermitFields();
        
        // ========================
        // 5. PASSWORD VALIDATION
        // ========================
        const pwd = document.getElementById('password');
        const confirmPwd = document.getElementById('password_confirmation');
        const charCountSpan = document.getElementById('charCount');
        const charCounterDiv = document.getElementById('charCounter');
        const strengthSpan = document.getElementById('strengthValue');
        const matchError = document.getElementById('confirmMatchMessage');
        const matchSuccess = document.getElementById('confirmSuccessMessage');
        
        function getStrength(password) {
            let score = 0;
            if(password.length >= 8) score++;
            if(password.length >= 12) score++;
            if(/[a-z]/.test(password)) score++;
            if(/[A-Z]/.test(password)) score++;
            if(/[0-9]/.test(password)) score++;
            if(/[$@#&!%*?]/.test(password)) score++;
            if(score <= 2) return 'Weak';
            if(score <= 4) return 'Fair';
            if(score <= 5) return 'Good';
            return 'Strong';
        }
        
        function updatePasswordUI() {
            let len = pwd.value.length;
            charCountSpan.innerText = len;
            if(len > 20) {
                charCounterDiv.className = 'char-counter exceed';
                charCounterDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span id="charCount">'+len+'</span> / 20 - Maximum exceeded!';
                pwd.style.borderColor = '#DC2626';
            } else if(len >= 18) {
                charCounterDiv.className = 'char-counter warning';
                charCounterDiv.innerHTML = '<i class="fas fa-keyboard"></i> <span id="charCount">'+len+'</span> / 20 - Getting close';
                pwd.style.borderColor = '';
            } else if(len >= 8 && len <= 20) {
                charCounterDiv.className = 'char-counter normal';
                charCounterDiv.innerHTML = '<i class="fas fa-check-circle"></i> <span id="charCount">'+len+'</span> / 20 - Good length';
                pwd.style.borderColor = '';
            } else {
                charCounterDiv.className = 'char-counter normal';
                charCounterDiv.innerHTML = '<i class="fas fa-keyboard"></i> <span id="charCount">'+len+'</span> / 20 characters';
                pwd.style.borderColor = '';
            }
            if(len > 0 && len <= 20) {
                let strength = getStrength(pwd.value);
                strengthSpan.innerText = strength;
                strengthSpan.className = 'strength-'+strength.toLowerCase();
            } else if(len === 0) {
                strengthSpan.innerText = '-';
                strengthSpan.className = '';
            }
            if(confirmPwd.value.length > 0) {
                if(pwd.value !== confirmPwd.value) {
                    matchError.style.display = 'flex';
                    matchSuccess.style.display = 'none';
                } else {
                    matchError.style.display = 'none';
                    matchSuccess.style.display = 'flex';
                }
            } else {
                matchError.style.display = 'none';
                matchSuccess.style.display = 'none';
            }
        }
        
        pwd.addEventListener('input', function(e) {
            if(this.value.length > 20) this.value = this.value.slice(0,20);
            updatePasswordUI();
        });
        confirmPwd.addEventListener('input', updatePasswordUI);
        updatePasswordUI();
        
        // ========================
        // 6. TOGGLE PASSWORD
        // ========================
        function togglePasswordField(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if(input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
        window.togglePassword = () => togglePasswordField('password', 'toggleIcon');
        window.toggleConfirmPassword = () => togglePasswordField('password_confirmation', 'toggleConfirmIcon');
        
        // ========================
        // 7. FORM SUBMIT VALIDATION
        // ========================
        const registerForm = document.getElementById('registerForm');
        
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMsg = [];
            
            const nameVal = nameInput.value.trim();
            if (nameVal === '') {
                errorMsg.push('Full name is required.');
                isValid = false;
            } else if (!validateName(nameVal)) {
                errorMsg.push('Full name can only contain letters, spaces, periods, apostrophes, or hyphens.');
                isValid = false;
            }
            
            const email = document.getElementById('email').value.trim();
            if (email === '') {
                errorMsg.push('Email address is required.');
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                errorMsg.push('Please enter a valid email address.');
                isValid = false;
            }
            
            const phoneVal = phoneInput.value.trim();
            if (phoneVal !== '' && !/^\d{11}$/.test(phoneVal)) {
                errorMsg.push('Phone number must be exactly 11 digits.');
                isValid = false;
            }
            
            const propertyType = propertyTypeSelect.value;
            if (propertyType === '') {
                errorMsg.push('Please select a property type.');
                isValid = false;
            }
            
            // Permit validation for Apartment (ONLY Residential Required)
            if (propertyType === 'apartment') {
                const residentialVal = residentialInputField.value.trim();
                if (residentialVal === '') {
                    errorMsg.push('Residential/Apartment permit number is required.');
                    isValid = false;
                } else if (!validatePermitFormat(residentialVal)) {
                    errorMsg.push('Residential permit format invalid. Format: YYYY-XXXXXXXXXX-XXXX');
                    isValid = false;
                }
            }
            
            // Permit validation for Both (BOTH Required)
            if (propertyType === 'both') {
                const residentialVal = residentialInputField.value.trim();
                const businessVal = businessInputField.value.trim();
                
                if (residentialVal === '') {
                    errorMsg.push('Residential/Apartment permit number is required.');
                    isValid = false;
                } else if (!validatePermitFormat(residentialVal)) {
                    errorMsg.push('Residential permit format invalid. Format: YYYY-XXXXXXXXXX-XXXX');
                    isValid = false;
                }
                
                if (businessVal === '') {
                    errorMsg.push('Business permit number is required for "Both" property type.');
                    isValid = false;
                } else if (!validatePermitFormat(businessVal)) {
                    errorMsg.push('Business permit format invalid. Format: YYYY-XXXXXXXXXX-XXXX');
                    isValid = false;
                }
            }
            
            const password = pwd.value;
            if (password.length === 0) {
                errorMsg.push('Password is required.');
                isValid = false;
            } else if (password.length < 8) {
                errorMsg.push('Password must be at least 8 characters.');
                isValid = false;
            } else if (password.length > 20) {
                errorMsg.push('Password cannot exceed 20 characters.');
                isValid = false;
            }
            
            if (password !== confirmPwd.value) {
                errorMsg.push('Passwords do not match.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMsg.join('\n'));
            }
        });
    </script>
</body>
</html>
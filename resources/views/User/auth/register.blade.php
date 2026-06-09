@extends('layouts.app')

@section('title', 'User Registration - APARTRACK')

@section('content')
<style>
    /* Poppins Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
    
    * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    /* Reset body to allow scrolling */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    /* Override main content area to remove any default padding/margin */
    .main-content, main, #app > main, .py-4, .container-fluid, .container {
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }

    /* Wrapper with background image – allows scrolling */
    .register-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background: url('{{ asset("images/apartrack_logo/Municipal-Hall.jpg") }}') no-repeat center center/cover;
        background-attachment: fixed;
        min-height: 100vh;
        padding: 2rem 1.5rem;
    }

    /* Overlay – soft dark gradient for better text contrast */
    .register-wrapper::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.5));
        backdrop-filter: blur(3px);
        z-index: 1;
        pointer-events: none;
    }
    
    /* Main Card – White, elegant, with blue accents */
    .register-card {
        position: relative;
        z-index: 2;
        background: #ffffff;
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.05);
        width: 100%;
        max-width: 560px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: fadeInUp 0.5s ease-out;
        border-radius: 28px;
        overflow: hidden;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .register-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(59, 130, 246, 0.1);
    }
    
    /* Logo section */
    .register-logo {
        text-align: center;
        padding-top: 2rem;
        background: white;
    }
    .register-logo img {
        max-width: 70px;
        height: auto;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05));
        transition: transform 0.2s;
    }
    .register-card:hover .register-logo img {
        transform: scale(1.02);
    }
    
    /* Header with blue gradient text */
    .register-header {
        text-align: center;
        padding: 0.75rem 2rem 1rem;
        background: white;
    }
    .register-header h3 {
        font-weight: 800;
        font-size: 1.9rem;
        margin: 0;
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        letter-spacing: -0.3px;
    }
    .register-header p {
        color: #4b5563;
        font-size: 0.9rem;
        margin-top: 0.4rem;
        font-weight: 500;
    }
    
    /* Body spacing */
    .register-body {
        padding: 0.5rem 2rem 2rem;
        background: white;
    }
    
    .form-group {
        margin-bottom: 1.4rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        display: block;
    }
    
    .form-control {
        width: 100%;
        border-radius: 1rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        outline: none;
        transform: translateY(-1px);
    }
    
    .input-group-text {
        background: #f8fafc;
        border-radius: 1rem 0 0 1rem;
        border: 1.5px solid #e2e8f0;
        border-right: none;
        color: #3b82f6;
        padding: 0 1.2rem;
        font-size: 1rem;
    }
    
    /* Password toggle */
    .password-wrapper {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        background: white;
        padding-left: 8px;
        z-index: 5;
        transition: color 0.2s;
    }
    .toggle-password:hover {
        color: #2563eb;
    }
    
    /* Strength meter */
    .strength-meter {
        width: 100%;
        margin: 0.6rem 0 0.25rem;
        height: 6px;
        background-color: #eef2ff;
        border-radius: 10px;
        overflow: hidden;
    }
    .strength-fill {
        width: 0%;
        height: 100%;
        transition: width 0.3s, background-color 0.2s;
    }
    .strength-text {
        font-size: 0.7rem;
        font-weight: 500;
        margin-top: 0.25rem;
        color: #334155;
    }
    
    /* Criteria dropdown */
    .criteria-dropdown {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out, opacity 0.3s ease, margin 0.2s;
        margin-top: 0;
    }
    .criteria-dropdown.show {
        max-height: 300px;
        opacity: 1;
        margin-top: 0.75rem;
    }
    .criteria-list {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 0.7rem 1.2rem;
        list-style: none;
        font-size: 0.75rem;
        border: 1px solid #eef2ff;
    }
    .criteria-list li {
        padding: 5px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #334155;
    }
    .criteria-list li i {
        font-style: normal;
        font-weight: 600;
        width: 22px;
        text-align: center;
        font-size: 0.85rem;
    }
    .valid-criteria {
        color: #2563eb !important;
        font-weight: 500;
    }
    .invalid-criteria {
        color: #94a3b8;
    }
    
    .char-counter {
        font-size: 0.7rem;
        margin-top: 0.4rem;
        color: #5b6e8c;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .match-status {
        font-size: 0.7rem;
        font-weight: 500;
        margin-top: 0.4rem;
    }
    .match-success {
        color: #10b981;
    }
    .match-error {
        color: #dc2626;
    }
    
    /* reCAPTCHA */
    .g-recaptcha {
        display: flex;
        justify-content: center;
        margin: 0.75rem 0;
        transform: scale(0.95);
    }
    
    /* Register Button – solid blue gradient */
    .btn-register {
        background: linear-gradient(105deg, #2563eb, #1d4ed8);
        border: none;
        border-radius: 2rem;
        padding: 0.85rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.25s;
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.25);
        width: 100%;
        color: white;
        margin-bottom: 1rem;
        cursor: pointer;
    }
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(37, 99, 235, 0.3);
        background: linear-gradient(105deg, #1d4ed8, #1e3a8a);
    }
    
    /* Divider */
    .divider {
        display: flex;
        align-items: center;
        margin: 1rem 0 1rem;
        color: #94a3b8;
        font-size: 0.75rem;
        gap: 12px;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #eef2ff;
    }
    
    /* Social buttons */
    .social-buttons-row {
        display: flex;
        gap: 14px;
        margin-bottom: 1.2rem;
    }
    .btn-social {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 0.65rem;
        border-radius: 2rem;
        background: white;
        border: 1.5px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1e293b;
        transition: all 0.25s;
        text-decoration: none;
    }
    .btn-social i {
        font-size: 1rem;
        transition: transform 0.2s;
    }
    .btn-facebook i { color: #1877F2; }
    .btn-google i { color: #DB4437; }
    .btn-social:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.04);
        background: #ffffff;
    }
    .btn-social:hover i {
        transform: scale(1.05);
    }
    
    /* Alerts */
    .alert {
        border-radius: 1rem;
        font-size: 0.8rem;
        padding: 0.8rem 1rem;
        margin-bottom: 1.2rem;
    }
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    .alert-success {
        background: #e0f2fe;
        color: #075985;
        border-left: 4px solid #0ea5e9;
    }
    
    a {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }
    a:hover {
        text-decoration: underline;
        color: #1d4ed8;
    }
    
    @media (max-width: 640px) {
        .register-body {
            padding: 0.5rem 1.2rem 1.5rem;
        }
        .register-header h3 {
            font-size: 1.6rem;
        }
        .register-logo img {
            max-width: 55px;
        }
        .btn-social span {
            font-size: 0.7rem;
        }
        .register-card {
            max-width: 95%;
            border-radius: 24px;
        }
        .g-recaptcha {
            transform: scale(0.85);
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <div class="register-logo">
            <img src="{{ asset('images/apartrack_logo/APARtrack-logo.png') }}" alt="APARTRACK Logo">
        </div>
        <div class="register-header">
            <h3>APARTRACK</h3>
            <p>Create your account</p>
        </div>
        <div class="register-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> Please fix the errors below:
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <!-- Full Name (Letters only) -->
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Enter your full name" required autofocus>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" 
                               placeholder="Enter your phone number" required>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               id="address" name="address" value="{{ old('address') }}" 
                               placeholder="Street, Barangay, City" required>
                    </div>
                </div>

                <!-- Password with strength meter -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Create a strong password" required>
                        </div>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                    
                    <div class="strength-meter">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                    
                    <div class="char-counter">
                        <i class="fas fa-keyboard"></i> <span id="charCount">0</span> / 20 characters
                    </div>
                    
                    <!-- Collapsible password criteria -->
                    <div class="criteria-dropdown" id="criteriaDropdown">
                        <ul class="criteria-list">
                            <li id="criteria-length"><i>○</i> <span>At least 8 characters</span></li>
                            <li id="criteria-uppercase"><i>○</i> <span>Uppercase letter (A-Z)</span></li>
                            <li id="criteria-lowercase"><i>○</i> <span>Lowercase letter (a-z)</span></li>
                            <li id="criteria-number"><i>○</i> <span>Number (0-9)</span></li>
                            <li id="criteria-special"><i>○</i> <span>Special character (!@#$%^&* etc.)</span></li>
                        </ul>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                        </div>
                        <span class="toggle-password" onclick="toggleConfirmPasswordVisibility()">
                            <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                        </span>
                    </div>
                    <div class="match-status" id="matchStatus"></div>
                </div>

                <!-- reCAPTCHA -->
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    @error('g-recaptcha-response')
                        <span class="text-danger small d-block mt-1 text-center">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-register">
                     Register Account
                </button>
            </form>

            <!-- Divider & Social Sign-in -->
            <div class="divider"><span>or continue with</span></div>
            <div class="social-buttons-row">
                <a href="{{ route('facebook.login') }}" class="btn-social btn-facebook">
                    <i class="fab fa-facebook-f"></i> <span>Facebook</span>
                </a>
                <a href="{{ route('google.login') }}" class="btn-social btn-google">
                    <i class="fab fa-google"></i> <span>Google</span>
                </a>
            </div>

            <div class="text-center">
                <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
    (function() {
        'use strict';

        // Full Name: letters and spaces only
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^A-Za-z\s]/g, '');
            });
            nameInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
                }, 10);
            });
        }

        // Phone: digits, spaces, +, -
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9+\-\s]/g, '');
            });
        }

        // Password strength and validation
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const matchStatus = document.getElementById('matchStatus');
        const charCountSpan = document.getElementById('charCount');
        const criteriaDropdown = document.getElementById('criteriaDropdown');
        
        const criteriaLength = document.getElementById('criteria-length');
        const criteriaUppercase = document.getElementById('criteria-uppercase');
        const criteriaLowercase = document.getElementById('criteria-lowercase');
        const criteriaNumber = document.getElementById('criteria-number');
        const criteriaSpecial = document.getElementById('criteria-special');
        
        const criteriaMap = {
            length: { element: criteriaLength, check: (pwd) => pwd.length >= 8 },
            uppercase: { element: criteriaUppercase, check: (pwd) => /[A-Z]/.test(pwd) },
            lowercase: { element: criteriaLowercase, check: (pwd) => /[a-z]/.test(pwd) },
            number: { element: criteriaNumber, check: (pwd) => /[0-9]/.test(pwd) },
            special: { element: criteriaSpecial, check: (pwd) => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pwd) }
        };
        
        function showCriteriaDropdown() {
            criteriaDropdown.classList.add('show');
        }
        
        function hideCriteriaDropdown() {
            if (passwordInput.value === '') {
                criteriaDropdown.classList.remove('show');
            }
        }
        
        function updateCriteriaUI(password) {
            let score = 0;
            for (const [key, rule] of Object.entries(criteriaMap)) {
                const isValid = rule.check(password);
                const iconSpan = rule.element.querySelector('i');
                if (isValid) {
                    iconSpan.innerHTML = '✓';
                    iconSpan.style.color = '#2563eb';
                    rule.element.classList.add('valid-criteria');
                    rule.element.classList.remove('invalid-criteria');
                    score++;
                } else {
                    iconSpan.innerHTML = '○';
                    iconSpan.style.color = '#94a3b8';
                    rule.element.classList.remove('valid-criteria');
                    rule.element.classList.add('invalid-criteria');
                }
            }
            return score;
        }
        
        function updateStrengthMeter(score) {
            const percentage = (score / 5) * 100;
            strengthFill.style.width = percentage + '%';
            
            let strengthLevel = '', bgColor = '';
            if (score === 0) { strengthLevel = 'Very Weak'; bgColor = '#dc2626'; }
            else if (score === 1) { strengthLevel = 'Weak'; bgColor = '#f97316'; }
            else if (score === 2) { strengthLevel = 'Fair'; bgColor = '#facc15'; }
            else if (score === 3) { strengthLevel = 'Good'; bgColor = '#10b981'; }
            else if (score === 4) { strengthLevel = 'Strong'; bgColor = '#3b82f6'; }
            else { strengthLevel = 'Very Strong'; bgColor = '#1e40af'; }
            
            strengthFill.style.backgroundColor = bgColor;
            strengthText.innerHTML = `<strong>${strengthLevel}</strong> password`;
        }
        
        function updateCharCounter() {
            let length = passwordInput.value.length;
            if (length > 20) {
                passwordInput.value = passwordInput.value.slice(0, 20);
                length = 20;
            }
            charCountSpan.textContent = length;
        }
        
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            if (confirm === '') {
                matchStatus.innerHTML = '';
                return;
            }
            if (password === confirm) {
                matchStatus.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                matchStatus.className = 'match-status match-success';
            } else {
                matchStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> Passwords do not match';
                matchStatus.className = 'match-status match-error';
            }
        }
        
        function handlePasswordInput() {
            let password = passwordInput.value || '';
            if (password.length > 20) {
                password = password.slice(0, 20);
                passwordInput.value = password;
            }
            
            updateCharCounter();
            
            if (password === '') {
                strengthFill.style.width = '0%';
                strengthText.innerHTML = '';
                for (const [key, rule] of Object.entries(criteriaMap)) {
                    const iconSpan = rule.element.querySelector('i');
                    iconSpan.innerHTML = '○';
                    iconSpan.style.color = '#94a3b8';
                    rule.element.classList.remove('valid-criteria');
                    rule.element.classList.add('invalid-criteria');
                }
                checkPasswordMatch();
                if (!passwordInput.matches(':focus')) hideCriteriaDropdown();
                return;
            }
            showCriteriaDropdown();
            const score = updateCriteriaUI(password);
            updateStrengthMeter(score);
            checkPasswordMatch();
        }
        
        passwordInput.addEventListener('focus', () => showCriteriaDropdown());
        passwordInput.addEventListener('blur', () => { if (passwordInput.value === '') hideCriteriaDropdown(); });
        passwordInput.addEventListener('input', handlePasswordInput);
        passwordInput.addEventListener('paste', () => setTimeout(handlePasswordInput, 10));
        
        confirmInput.addEventListener('input', checkPasswordMatch);
        
        // Initial state
        handlePasswordInput();
        criteriaDropdown.classList.remove('show');
        
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            if (password.length > 20) { e.preventDefault(); alert('Password cannot exceed 20 characters!'); return false; }
            if (password.length < 8) { e.preventDefault(); alert('Password must be at least 8 characters!'); return false; }
            if (password !== confirm) { e.preventDefault(); alert('Passwords do not match!'); return false; }
            const score = Object.values(criteriaMap).filter(rule => rule.check(password)).length;
            if (password !== '' && score < 3 && !confirm('Your password strength is weak. Continue anyway?')) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    })();
    
    function togglePasswordVisibility() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    function toggleConfirmPasswordVisibility() {
        const confirm = document.getElementById('password_confirmation');
        const icon = document.getElementById('toggleConfirmIcon');
        if (confirm.type === 'password') {
            confirm.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            confirm.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
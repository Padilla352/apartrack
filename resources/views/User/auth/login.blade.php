@extends('layouts.app')

@section('title', 'Login - APARTRACK')

@section('content')
<style>
    /* ---------- RESET & NO SCROLL ---------- */
    html, body {
        overflow: hidden;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    /* Force layout containers to use full viewport height */
    .main-content, main, #app > main, .py-4, .container-fluid, .container {
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }

    /* Wrapper takes full viewport, centers card but with upward offset */
    .login-full-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background: url('{{ asset("images/apartrack_logo/Municipal-Hall.jpg") }}') no-repeat center center/cover;
        overflow: hidden;
        min-height: 100vh;
    }

    /* Overlay – subtle */
    .login-full-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.27));
        backdrop-filter: blur(1px);
        z-index: 1;
    }

    /* Main card – moved up more */
    .login-card {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 2rem;
        width: 100%;
        max-width: 480px;
        margin: 1rem;
        box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255,255,255,0.2);
        transition: transform 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1), box-shadow 0.3s ease;
        animation: fadeUp 0.5s ease-out;
        display: flex;
        flex-direction: column;
        /* Raise the card higher – from -20px to -40px */
        transform: translateY(-40px);
    }

    /* On hover, keep the upward shift + additional lift */
    .login-card:hover {
        transform: translateY(-45px);
        box-shadow: 0 30px 55px -15px rgba(0, 0, 0, 0.5);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(-40px); }
    }

    /* Logo – bigger size */
    .login-logo {
        text-align: center;
        padding-top: 1.3rem;
        background: transparent;
        border-radius: 2rem 2rem 0 0;
    }
    .login-logo img {
        max-width: 80px;   /* was 58px, now larger */
        height: auto;
        margin-bottom: 0.2rem;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        transition: transform 0.25s ease, filter 0.3s;
    }
    .login-card:hover .login-logo img {
        transform: scale(1.03);
        filter: drop-shadow(0 6px 10px rgba(0,0,0,0.15));
    }

    /* Header */
    .login-header {
        background: transparent;
        padding: 0 1.8rem 0.2rem;
        text-align: center;
    }
    .login-header h3 {
        font-weight: 800;
        font-size: 1.85rem;
        margin: 0;
        background: linear-gradient(135deg, #1e293b, #2d3e5f);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        letter-spacing: -0.3px;
    }
    .login-header p {
        color: #5b6e8c;
        font-size: 0.82rem;
        font-weight: 500;
        margin-top: 0.2rem;
        margin-bottom: 0.5rem;
    }

    /* Body */
    .login-body {
        padding: 0.6rem 1.8rem 1.4rem;
        background: transparent;
    }

    /* Form groups */
    .form-group {
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .form-label {
        font-weight: 600;
        color: #1e2a3e;
        margin-bottom: 0.3rem;
        font-size: 0.8rem;
        display: block;
    }
    .form-control {
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        border-radius: 1.2rem;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .form-control:focus {
        border-color: #5f7ef2;
        box-shadow: 0 0 0 4px rgba(95, 126, 242, 0.15);
        transform: translateY(-1px);
        background: #fff;
    }
    .input-group:focus-within .input-group-text {
        border-color: #5f7ef2;
        background: #f0f4ff;
        color: #4f6de0;
    }
    .input-group-text {
        padding: 0 0.9rem;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-right: none;
        color: #5f7ef2;
        transition: all 0.2s;
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
        transition: color 0.2s, transform 0.2s;
    }
    .toggle-password:hover {
        color: #5f7ef2;
        transform: translateY(-50%) scale(1.08);
    }

    /* Login button */
    .btn-login {
        background: linear-gradient(105deg, #5f7ef2, #6b47b3);
        border: none;
        border-radius: 2rem;
        padding: 0.72rem;
        font-weight: 700;
        font-size: 0.9rem;
        width: 100%;
        color: white;
        box-shadow: 0 6px 16px rgba(95, 126, 242, 0.3);
        transition: all 0.25s;
        cursor: pointer;
        margin-top: 0.2rem;
        position: relative;
        overflow: hidden;
    }
    .btn-login::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.35);
        transform: translate(-50%, -50%);
        transition: width 0.5s, height 0.5s;
    }
    .btn-login:active::after {
        width: 220px;
        height: 220px;
        opacity: 0;
        transition: 0s;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(95, 126, 242, 0.45);
        background: linear-gradient(105deg, #4a6de0, #5b3e9e);
    }
    .btn-login:active {
        transform: translateY(1px);
    }

    /* Admin Link Styling */
    .admin-link-container {
        text-align: center;
        margin-top: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .admin-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #4a5568;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.25s ease;
    }
    .admin-link i {
        font-size: 0.9rem;
        color: #5f7ef2;
        transition: transform 0.2s;
    }
    .admin-link:hover {
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        color: #2d3748;
    }
    .admin-link:hover i {
        transform: scale(1.1);
    }

    /* Divider */
    .divider {
        display: flex;
        align-items: center;
        margin: 1rem 0 0.9rem;
        color: #94a3b8;
        font-size: 0.7rem;
        gap: 10px;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e9edf2;
        transition: border-color 0.2s;
    }
    .login-card:hover .divider::before,
    .login-card:hover .divider::after {
        border-color: #cbd5e1;
    }

    /* Social buttons – original brand colors */
    .social-buttons-row {
        display: flex;
        gap: 12px;
        margin-bottom: 1rem;
    }
    .btn-social {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0.6rem 0.3rem;
        border-radius: 2rem;
        background: white;
        border: 1.5px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.82rem;
        color: #1e293b;
        transition: all 0.25s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        text-decoration: none;
        cursor: pointer;
    }
    .btn-social i {
        font-size: 1rem;
        transition: transform 0.2s;
    }
    .btn-facebook i {
        color: #1877F2;
    }
    .btn-google i {
        color: #DB4437;
    }
    .btn-social:hover i {
        transform: scale(1.08);
    }
    .btn-social:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }

    /* Links */
    .register-link {
        color: #5f7ef2;
        font-weight: 700;
        text-decoration: none;
        position: relative;
        font-size: 0.82rem;
    }
    .register-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #5f7ef2;
        transition: width 0.25s ease;
    }
    .register-link:hover::after {
        width: 100%;
    }
    .forgot-link {
        color: #5f7ef2;
        font-size: 0.68rem;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s;
    }
    .forgot-link:hover {
        text-decoration: underline;
        color: #405ebe;
    }

    /* Alert */
    .alert {
        border-radius: 1rem;
        font-size: 0.75rem;
        padding: 0.45rem 0.9rem;
        margin-bottom: 0.9rem;
        background: #fee9e9;
        border-left: 4px solid #f05a5a;
        color: #ab2020;
    }

    /* Ensure "Create account" is visible */
    .text-center.mt-3 {
        margin-top: 0.5rem !important;
    }
    .text-center p {
        font-size: 0.78rem;
    }

    /* Responsive: on very short screens, reduce offset and allow internal scroll */
    @media (max-height: 620px) {
        .login-card {
            max-height: calc(100vh - 1rem);
            transform: translateY(-20px); /* less offset on tiny screens */
        }
        .login-card:hover {
            transform: translateY(-25px);
        }
        .login-body {
            overflow-y: auto;
            padding: 0.4rem 1.5rem 1rem;
            scrollbar-width: thin;
        }
        .login-body::-webkit-scrollbar {
            width: 4px;
        }
        .login-body::-webkit-scrollbar-track {
            background: #eef2f8;
            border-radius: 10px;
        }
        .login-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .login-logo img {
            max-width: 65px; /* slightly smaller on very short screens */
        }
    }

    /* Responsive adjustments */
    @media (max-height: 680px) {
        .login-logo { padding-top: 0.9rem; }
        .login-logo img { max-width: 70px; }
        .login-header h3 { font-size: 1.65rem; }
        .login-body { padding: 0.4rem 1.5rem 1rem; }
        .form-group { margin-bottom: 0.75rem; }
        .btn-login { padding: 0.6rem; }
        .divider { margin: 0.7rem 0; }
    }

    @media (max-width: 500px) {
        .login-card { max-width: 94%; margin: 0.5rem; }
        .login-body { padding: 0.4rem 1rem 0.8rem; }
        .btn-social span { font-size: 0.7rem; }
        .social-buttons-row { gap: 8px; }
        .login-logo img {
            max-width: 70px;
        }
    }
</style>

<div class="login-full-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/apartrack_logo/APARtrack-logo.png') }}" alt="APARTRACK Logo">
        </div>
        <div class="login-header">
            <h3>APARTRACK</h3>
            <p>Sign in to your account</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label">Password</label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                        </div>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Keep me logged in</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                   Log In
                </button>
            </form>

            <!-- ADMIN LINK - ADDED HERE (Below Sign In button, before divider) -->
            <div class="admin-link-container">
                <a href="{{ route('admin.login') }}" class="admin-link">
                    <i class="fas fa-user-shield"></i>
                    Are you an administrator? Click here
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="divider"><span>or continue with</span></div>

            <div class="social-buttons-row">
                <a href="{{ Route::has('facebook.login') ? route('facebook.login') : '#' }}" 
                   class="btn-social btn-facebook" 
                   @if(!Route::has('facebook.login')) onclick="event.preventDefault(); alert('Facebook login coming soon.');" @endif>
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                </a>
                <a href="{{ Route::has('google.login') ? route('google.login') : '#' }}" 
                   class="btn-social btn-google" 
                   @if(!Route::has('google.login')) onclick="event.preventDefault(); alert('Google Sign-In will be available soon.');" @endif>
                    <i class="fab fa-google"></i>
                    <span>Google</span>
                </a>
            </div>

            <div class="text-center mt-3">
                <p class="mb-0" style="color: #475569;">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="register-link">Create account</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
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

    document.querySelectorAll('.btn-social[href="#"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (btn.getAttribute('href') === '#') e.preventDefault();
        });
    });
</script>
@endsection
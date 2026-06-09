<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>APARTRACK - Owner Login</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        /* Background Image with Overlay */
        .login-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* Dark Overlay */
        .login-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 3, 51, 0.7);
            z-index: 1;
        }

        /* Main Container */
        .login-container {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Card */
        .login-card {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .login-header {
            background: linear-gradient(135deg, #000333 0%, #1a1a4e 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        /* Logo Image Styles - NO FILTER, original colors */
        .logo-image {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* No filter - original logo colors will show */
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Body */
        .login-body {
            padding: 40px 30px;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FEE2E2;
            color: #DC2626;
        }

        .alert-success {
            background: #ECFDF5;
            border: 1px solid #D1FAE5;
            color: #059669;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .password-field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #9CA3AF;
            font-size: 18px;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 14px 50px 14px 48px;
            border: 1px solid #E0E0E0;
            border-radius: 14px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        /* Password Toggle Button */
        .toggle-password {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            color: #9CA3AF;
            font-size: 18px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            z-index: 2;
        }

        .toggle-password:hover {
            color: #007BFF;
        }

        /* Checkbox */
        .checkbox-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }

        .checkbox-label input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #007BFF;
        }

        .forgot-link {
            font-size: 14px;
            color: #007BFF;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #007BFF 0%, #00A2FF 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 123, 255, 0.4);
        }

        /* Register Link */
        .register-section {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #E0E0E0;
        }

        .register-text {
            font-size: 14px;
            color: #666;
        }

        .register-link {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        /* Back Link */
        .back-link-section {
            text-align: center;
            margin-top: 16px;
            padding-top: 0;
        }

        .back-link {
            font-size: 13px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .back-link i {
            font-size: 12px;
            margin-right: 6px;
        }

        .back-link:hover {
            color: #007BFF;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .login-card {
                margin: 0 16px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }

            .logo-image {
                width: 60px;
                height: 60px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Image - Binalonan Townhall -->
    <div class="login-background">
        <img src="{{ asset('images/BINALONAN TOWNHALL.jpg') }}" alt="Binalonan Townhall" class="bg-image">
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <!-- APARTrack Logo Image - Original colors -->
                <div class="logo-image">
                    <img src="{{ asset('images/apartrack_logo/APARTrack-logo.png') }}" alt="APARTrack Logo">
                </div>
                <h1>APARTrack</h1>
                <p>Property Management System</p>
            </div>
            
            <div class="login-body">
                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('owner.login.submit') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="{{ old('email') }}" required autofocus 
                                   placeholder="enter your email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-field-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-control" 
                                   required placeholder="••••••••">
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye-slash" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember Me</span>
                        </label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        Login
                    </button>
                </form>
                
                <div class="register-section">
                    <p class="register-text">
                        Don't have an account? 
                        <a href="{{ route('owner.register') }}" class="register-link">Register here</a>
                    </p>
                </div>

                <!-- BACK LINK - Direct to Guest Page -->
                <div class="back-link-section">
                    <a href="{{ url('/') }}" class="back-link">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
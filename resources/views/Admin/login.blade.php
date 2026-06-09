<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Authentication | Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e293b; 
            --accent-color: #000000;  
            --bg-color: #f1f5f9;      
        }

        body {
            background-image: linear-gradient(rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.5)), 
                              url("{{ asset('images/bg-login.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            padding: 20px;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.17); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(6px); 
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 40px;
        }

        .login-card h2 {
            color: #0f172a;
            font-weight: 800;
            font-size: 26px;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-card p {
            color: #334155;
            text-align: center;
            font-size: 14px;
            margin-bottom: 32px;
        }

        /* Status Alerts Styling */
        .custom-alert {
            font-size: 13px; 
            border-radius: 12px; 
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 700;
            font-size: 12px;
            color: #1e293b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-custom .icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            z-index: 10;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            cursor: pointer;
            z-index: 10;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--accent-color);
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 40px 12px 48px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
            color: #0f172a;
        }

        .form-control-custom:focus {
            background-color: rgba(255, 255, 255, 0.9);
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .btn-admin-login {
            background-color: var(--primary-color);
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            margin-top: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-admin-login:hover {
            background-color: #000;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <h2>Admin Portal</h2>
            <p>Authorized Personnel Only</p>

            <!-- Success/Status Messages -->
            @if(session('status'))
                <div class="alert alert-success custom-alert py-2" style="background: rgba(34, 197, 94, 0.2); border-color: rgba(34, 197, 94, 0.3);">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success custom-alert py-2" style="background: rgba(34, 197, 94, 0.2); border-color: rgba(34, 197, 94, 0.3);">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if(session('error'))
                <div class="alert alert-danger custom-alert py-2" style="background: rgba(248, 113, 113, 0.2); border-color: rgba(248, 113, 113, 0.3);">
                    <i class="fas fa-circle-exclamation me-2"></i> {{ session('error') }}
                </div>
            @endif
            
            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger custom-alert py-2" style="background: rgba(248, 113, 113, 0.2); border-color: rgba(248, 113, 113, 0.3);">
                    <i class="fas fa-circle-exclamation me-2"></i> 
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope icon-left"></i>
                        <input type="email" name="email" class="form-control-custom" 
                               placeholder="admin@system.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label class="form-label">Password</label>
                        <!-- FIXED: Replaced popup with actual forgot password link -->
                        <a href="{{ route('admin.password.request') }}" style="font-size: 11px; text-decoration: none; color: var(--accent-color); font-weight: 600;">Forgot Password?</a>
                    </div>
                    <div class="input-group-custom">
                        <i class="fas fa-lock icon-left"></i>
                        <input type="password" name="password" id="passwordInput" class="form-control-custom" 
                               placeholder="••••••••" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size: 13px; color: #1e293b; font-weight: 600;">
                        Remember me?
                    </label>
                </div>

                <button type="submit" class="btn-admin-login">
                    Secure Sign In
                </button>
            </form>
            
            <!-- Optional: Link back to user login -->
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" style="font-size: 12px; color: #1e293b; text-decoration: none;">
                    <i class="fas fa-user"></i> Back to User Login
                </a>
            </div>
        </div>

        <div class="copyright">
            &copy; {{ date('Y') }} APARTTrack System. All rights reserved.
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#passwordInput');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function (e) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - APARTRACK</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0c10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Background with overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ asset("images/BINALONAN TOWNHALL.jpg") }}') no-repeat center center fixed;
            background-size: cover;
            opacity: 0.3;
            z-index: -1;
        }

        .verify-container {
            width: 100%;
            max-width: 500px;
            animation: fadeInUp 0.5s ease-out;
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

        .verify-card {
            background: linear-gradient(135deg, #0f1115 0%, #0b0d11 100%);
            border-radius: 28px;
            padding: 40px 32px;
            text-align: center;
            border: 1px solid rgba(245, 184, 27, 0.15);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .verify-icon {
            width: 70px;
            height: 70px;
            background: rgba(245, 184, 27, 0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }

        .verify-icon i {
            font-size: 32px;
            color: #f5b81b;
        }

        .verify-title {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #f5b81b);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 8px;
        }

        .verify-subtitle {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .email-info {
            background: rgba(245, 184, 27, 0.08);
            border-radius: 12px;
            padding: 10px 16px;
            margin-bottom: 28px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #f5b81b;
            border: 1px solid rgba(245, 184, 27, 0.2);
        }

        .email-info i {
            font-size: 14px;
        }

        .otp-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .otp-input {
            width: 55px;
            height: 65px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            background: rgba(15, 17, 21, 0.9);
            border: 2px solid rgba(245, 184, 27, 0.2);
            border-radius: 16px;
            color: #f5b81b;
            font-family: 'Inter', monospace;
            transition: all 0.2s ease;
        }

        .otp-input:focus {
            outline: none;
            border-color: #f5b81b;
            box-shadow: 0 0 0 3px rgba(245, 184, 27, 0.15);
            background: #0f1115;
        }

        .otp-input::-webkit-inner-spin-button,
        .otp-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .alert-info {
            background: rgba(245, 184, 27, 0.08);
            border: 1px solid rgba(245, 184, 27, 0.2);
            color: #f5b81b;
        }

        .btn-verify {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #f5b81b, #d4af37);
            border: none;
            border-radius: 40px;
            font-size: 15px;
            font-weight: 700;
            color: #0a0c10;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 16px;
            font-family: 'Inter', sans-serif;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 184, 27, 0.3);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .resend-section {
            border-top: 1px solid rgba(245, 184, 27, 0.1);
            padding-top: 20px;
            margin-top: 10px;
        }

        .btn-resend {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 8px 16px;
            border-radius: 40px;
            font-family: 'Inter', sans-serif;
        }

        .btn-resend:hover {
            color: #f5b81b;
            background: rgba(245, 184, 27, 0.08);
        }

        .back-link {
            display: inline-block;
            margin-top: 16px;
            color: #64748b;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #f5b81b;
        }

        /* Countdown Timer */
        .timer-text {
            font-size: 12px;
            color: #64748b;
            margin-top: 16px;
        }

        .timer-text span {
            color: #f5b81b;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 550px) {
            .verify-card {
                padding: 30px 20px;
            }

            .otp-input {
                width: 45px;
                height: 55px;
                font-size: 24px;
            }

            .otp-container {
                gap: 8px;
            }

            .verify-title {
                font-size: 20px;
            }
        }

        @media (max-width: 400px) {
            .otp-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }

            .otp-container {
                gap: 6px;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #0f1115;
        }

        ::-webkit-scrollbar-thumb {
            background: #f5b81b;
            border-radius: 10px;
        }

        ::selection {
            background: #f5b81b;
            color: #0a0c10;
        }
    </style>
</head>
<body>

<div class="verify-container">
    <div class="verify-card">
        
        <!-- Icon -->
        <div class="verify-icon">
            <i class="fas fa-envelope-open-text"></i>
        </div>

        <!-- Title -->
        <h2 class="verify-title">Verify Your Email</h2>
        <p class="verify-subtitle">Enter the 6-digit code sent to your email</p>

        <!-- Email Info -->
        <div class="email-info">
            <i class="fas fa-envelope"></i>
            <span>{{ $email ?? session('email') ?? 'your email' }}</span>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <!-- OTP Form -->
        <form id="otpForm" action="{{ route('owner.verify.otp') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? session('email') }}">
            
            <div class="otp-container">
                <input type="text" maxlength="1" class="otp-input" autofocus>
                <input type="text" maxlength="1" class="otp-input">
                <input type="text" maxlength="1" class="otp-input">
                <input type="text" maxlength="1" class="otp-input">
                <input type="text" maxlength="1" class="otp-input">
                <input type="text" maxlength="1" class="otp-input">
            </div>

            <input type="hidden" name="otp" id="combinedOtp">
            <button type="submit" class="btn-verify">
                <i class="fas fa-check-circle"></i> Verify Account
            </button>
        </form>

        <!-- Resend Section -->
        <div class="resend-section">
            <form action="{{ route('owner.verify.resend') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? session('email') }}">
                <button type="submit" class="btn-resend">
                    <i class="fas fa-redo-alt"></i> Resend OTP Code
                </button>
            </form>
        </div>

        <!-- Back to Login Link -->
        <div>
            <a href="{{ route('owner.login') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>

        <!-- Timer Hint -->
        <div class="timer-text">
            <i class="fas fa-clock"></i> Code expires in <span id="timer">10:00</span>
        </div>
    </div>
</div>

<script>
    // OTP Input Handling
    const inputs = document.querySelectorAll('.otp-input');
    const hidden = document.getElementById('combinedOtp');
    const form = document.getElementById('otpForm');

    function updateOTP() {
        let value = '';
        inputs.forEach(i => value += i.value);
        hidden.value = value;
        
        // Auto-submit when all fields are filled
        if (value.length === 6) {
            form.submit();
        }
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Allow only numbers
            if (!/^\d?$/.test(e.target.value)) {
                e.target.value = '';
                return;
            }

            // Auto-focus next input
            if (e.target.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateOTP();
        });

        input.addEventListener('keydown', function(e) {
            // Handle backspace to go to previous input
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste event
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = paste.replace(/\D/g, '').slice(0, 6).split('');
            
            numbers.forEach((num, idx) => {
                if (inputs[idx]) {
                    inputs[idx].value = num;
                }
            });
            
            updateOTP();
            
            // Focus on the next empty input or last one
            const lastFilledIndex = numbers.length - 1;
            if (lastFilledIndex < inputs.length - 1) {
                inputs[lastFilledIndex + 1].focus();
            } else {
                inputs[inputs.length - 1].focus();
            }
        });
    });

    // Countdown Timer (10 minutes = 600 seconds)
    let timeLeft = 600;
    const timerElement = document.getElementById('timer');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft > 0) {
            timeLeft--;
        } else {
            timerElement.textContent = "Expired";
            timerElement.style.color = "#ef4444";
        }
    }

    // Start timer
    setInterval(updateTimer, 1000);

    // Log to console
    console.log('%c🔐 APARTRACK OTP Verification | Ready', 'color: #f5b81b; font-size: 14px; font-weight: bold;');
</script>

</body>
</html>
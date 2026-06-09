@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')
<!-- Font Awesome & Google Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        top: -150px;
        right: -100px;
        z-index: 0;
        animation: float 8s ease-in-out infinite;
    }

    body::after {
        content: '';
        position: fixed;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        bottom: -200px;
        left: -150px;
        z-index: 0;
        animation: float 10s ease-in-out infinite reverse;
    }

    @keyframes float {
        0% { transform: translate(0, 0); }
        50% { transform: translate(30px, 20px); }
        100% { transform: translate(0, 0); }
    }

    .verification-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(2px);
        border: none;
        border-radius: 2rem;
        box-shadow: 0 25px 45px -12px rgba(0,0,0,0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
        z-index: 10;
    }

    .verification-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 55px -15px rgba(0,0,0,0.4);
    }

    .gradient-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 60px;
        height: 60px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 1rem;
        box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
    }

    .gradient-icon i {
        font-size: 1.8rem;
        color: white;
    }

    /* MAS MALIIT NA OTP BOXES */
    .otp-container {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 1.5rem 0;
        flex-wrap: nowrap;
    }

    .otp-digit {
        width: 50px;
        height: 58px;
        text-align: center;
        font-size: 1.6rem;
        font-weight: 700;
        font-family: 'Inter', monospace;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: #ffffff;
        transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        color: #1e293b;
    }

    .otp-digit:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        transform: scale(1.02);
        background: white;
    }

    .otp-digit:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .btn-verify {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 1.8rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        box-shadow: 0 6px 14px -6px rgba(102, 126, 234, 0.4);
    }

    .btn-verify:hover:not(:disabled) {
        transform: scale(1.01);
        background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
        box-shadow: 0 10px 18px -8px rgba(102, 126, 234, 0.5);
    }

    .resend-btn {
        background: none;
        border: none;
        color: #667eea;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.3rem 0.8rem;
        border-radius: 2rem;
    }

    .resend-btn:hover:not(:disabled) {
        background: #f1f5f9;
    }

    .timer {
        font-size: 0.8rem;
        background: #ffeeee;
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 40px;
        color: #ef4444;
        font-weight: 600;
    }

    .alert-custom {
        border-radius: 1rem;
        background: #fff5f5;
        border-left: 4px solid #f56565;
    }

    /* Responsive: mas maliit sa phone */
    @media (max-width: 576px) {
        .verification-card .card-body {
            padding: 1.5rem !important;
        }
        .gradient-icon {
            width: 50px;
            height: 50px;
        }
        .gradient-icon i {
            font-size: 1.4rem;
        }
        .otp-container {
            gap: 8px;
        }
        .otp-digit {
            width: 42px;
            height: 50px;
            font-size: 1.4rem;
            border-radius: 0.8rem;
        }
    }

    @media (max-width: 400px) {
        .otp-digit {
            width: 38px;
            height: 46px;
            font-size: 1.2rem;
        }
        .otp-container {
            gap: 6px;
        }
    }
</style>

<div class="container mt-4 mt-md-5 pt-4">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="card verification-card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center">
                        <div class="gradient-icon mx-auto">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: #1e293b;">Verify your email</h3>
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">
                            Enter the 6‑digit code sent to your email
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-custom mb-4 p-3" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle text-danger me-3 mt-1"></i>
                                <div>
                                    <strong class="text-danger">Verification failed</strong>
                                    <ul class="mb-0 mt-1 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li class="small">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('otp.verify.submit') }}" id="otpForm">
                        @csrf
                        <div class="text-center">
                            <div class="otp-container" id="otpContainer">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" id="otp1" autofocus>
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" id="otp2">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" id="otp3">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" id="otp4">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" id="otp5">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" id="otp6">
                            </div>
                            <input type="hidden" name="otp" id="combinedOtp">
                            
                            <div class="timer mt-2" id="timerSection">
                                <i class="fas fa-clock"></i> Code expires in <span id="countdown">10:00</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-verify text-white" id="verifyBtn">
                                <i class="fas fa-check-circle me-2"></i> Verify & Register
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <form action="{{ route('otp.verify.resend') }}" method="POST" id="resendForm" style="display: inline;">
                            @csrf
                            <button type="submit" class="resend-btn" id="resendBtn">
                                <i class="fas fa-redo-alt"></i> Resend OTP
                            </button>
                        </form>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="far fa-envelope me-1"></i> Didn't receive it? Check your spam folder
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const digits = ['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6'].map(id => document.getElementById(id));
        const combinedOtp = document.getElementById('combinedOtp');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const countdownSpan = document.getElementById('countdown');
        const timerSection = document.getElementById('timerSection');
        const otpForm = document.getElementById('otpForm');
        
        let timeLeft = 600;
        let timerInterval = null;
        let isExpired = false;
        
        function updateCombinedOtp() {
            let otpValue = '';
            digits.forEach(input => { if(input) otpValue += input.value; });
            combinedOtp.value = otpValue;
            if (otpValue.length === 6 && !isExpired) {
                otpForm.submit();
            }
        }
        
        function handleInput(e, index) {
            let val = e.target.value;
            if (val && !/^\d+$/.test(val)) {
                e.target.value = '';
                return;
            }
            if (val.length === 1 && index < digits.length - 1) {
                digits[index + 1].focus();
            }
            updateCombinedOtp();
        }
        
        function handleKeyDown(e, index) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                digits[index - 1].focus();
                digits[index - 1].value = '';
                updateCombinedOtp();
            }
        }
        
        function onPaste(e) {
            e.preventDefault();
            let pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\s/g, '').slice(0, 6);
            if (/^\d+$/.test(pasteData)) {
                for (let i = 0; i < pasteData.length && i < digits.length; i++) {
                    digits[i].value = pasteData[i];
                }
                const nextIndex = Math.min(pasteData.length, digits.length - 1);
                digits[nextIndex].focus();
                updateCombinedOtp();
            }
        }
        
        digits.forEach((input, idx) => {
            if (input) {
                input.addEventListener('input', (e) => handleInput(e, idx));
                input.addEventListener('keydown', (e) => handleKeyDown(e, idx));
            }
        });
        if (digits[0]) digits[0].addEventListener('paste', onPaste);
        
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
        
        function updateTimerDisplay() {
            if (countdownSpan) countdownSpan.innerText = formatTime(timeLeft);
        }
        
        function expireCode() {
            if (isExpired) return;
            isExpired = true;
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = null;
            timerSection.innerHTML = '<i class="fas fa-exclamation-circle"></i> Code expired. Please resend OTP.';
            digits.forEach(input => { if (input) input.disabled = true; });
            if (verifyBtn) verifyBtn.disabled = true;
            if (resendBtn) resendBtn.disabled = false;
        }
        
        function startCountdown() {
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                if (timeLeft > 0) {
                    timeLeft--;
                    updateTimerDisplay();
                    if (timeLeft === 0) expireCode();
                } else {
                    if (timerInterval) clearInterval(timerInterval);
                    timerInterval = null;
                }
            }, 1000);
        }
        
        updateTimerDisplay();
        startCountdown();
        
        if (resendBtn) {
            resendBtn.addEventListener('click', function(e) {
                if (resendBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                resendBtn.disabled = true;
                setTimeout(() => {
                    if (document.body.contains(resendBtn)) resendBtn.disabled = false;
                }, 3000);
            });
        }
        
        if (otpForm) {
            otpForm.addEventListener('submit', function(e) {
                if (isExpired) {
                    e.preventDefault();
                    alert('OTP has expired. Please request a new code.');
                }
                const otpValue = combinedOtp.value;
                if (otpValue.length !== 6) {
                    e.preventDefault();
                    alert('Please enter all 6 digits.');
                }
            });
        }
    })();
</script>
@endsection
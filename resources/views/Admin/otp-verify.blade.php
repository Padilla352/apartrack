<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Verification | AparTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .otp-card {
            background: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .card-header-custom {
            background: #f8f9fc;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #e3e6f0;
        }

        .card-header-custom i {
            font-size: 50px;
            color: #4e73df;
            margin-bottom: 15px;
        }

        .card-body {
            padding: 40px 30px;
        }

        .otp-input {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 8px;
            text-align: center;
            border: 2px solid #d1d3e2;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            outline: none;
        }

        .btn-verify {
            background-color: #4e73df;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn-verify:hover {
            background-color: #2e59d9;
            transform: translateY(-2px);
        }

        .text-muted {
            font-size: 14px;
        }

        .resend-link {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }

        .resend-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="otp-card">
        <div class="card-header-custom">
            <i class="fas fa-shield-alt"></i>
            <h4 class="m-0 font-weight-bold text-dark">Security Check</h4>
        </div>
        
        <div class="card-body">
            <p class="text-muted text-center mb-4">
                We've sent a 6-digit verification code to your registered Gmail account.
            </p>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 13px;">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.otp.verify') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="otp" class="form-label d-block text-center mb-3 fw-bold">Verification Code</label>
                    <input type="text" 
                           name="otp" 
                           id="otp"
                           class="form-control otp-input" 
                           placeholder="000000" 
                           required 
                           maxlength="6" 
                           pattern="\d{6}"
                           inputmode="numeric"
                           autofocus
                           autocomplete="one-time-code">
                </div>
                
                <button type="submit" class="btn btn-primary btn-verify w-100 mb-3">
                    Verify Account
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0 text-muted small">Didn't receive the code?</p>
                <a href="{{ route('login') }}" class="resend-link">Try Logging in again</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
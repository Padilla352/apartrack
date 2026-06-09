<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f5f7fb;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-icon {
            width: 50px;
            height: 50px;
            background: #000333;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .logo-icon i {
            color: white;
            font-size: 24px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #000333;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 10px;
            color: #007BFF;
            background: #f0f7ff;
            padding: 20px;
            border-radius: 16px;
            margin: 20px 0;
        }
        .expiry {
            text-align: center;
            color: #D90404;
            font-size: 12px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            margin-top: 30px;
        }
        h2 {
            color: #000333;
            text-align: center;
        }
        p {
            color: #334155;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="logo-text">APARTrack</div>
        </div>
        
        <h2>Email Verification</h2>
        
        <p>Hello,</p>
        
        <p>Thank you for registering with APARTRACK. Please use the OTP code below to verify your email address:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p>This OTP code will expire in <strong>10 minutes</strong>.</p>
        
        <p>If you didn't request this verification, please ignore this email.</p>
        
        <div class="expiry">
            <i class="fas fa-clock"></i> Expires at: {{ now()->addMinutes(10)->format('h:i A') }}
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} APARTRACK. All rights reserved.
        </div>
    </div>
</body>
</html>
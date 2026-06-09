<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | APARTTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .reset-card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); width: 100%; max-width: 400px; border: 1px solid #e2e8f0; }
        .btn-primary { background-color: #1e293b; border: none; padding: 12px; border-radius: 12px; font-weight: 700; width: 100%; }
        .btn-primary:hover { background-color: #000; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .alert-danger { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-success { background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
    <div class="reset-card">
        <h3 class="text-center fw-bold mb-2">New Password</h3>
        <p class="text-center text-muted small mb-4">Please enter your new secure password below.</p>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            <div class="mb-3">
                <label class="form-label small fw-bold text-uppercase">Email Address</label>
                <input type="email" value="{{ $email ?? old('email') }}" class="form-control bg-light" readonly disabled>
                <small class="text-muted d-block mt-1">Password reset requested for this email</small>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-uppercase">New Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password (min. 8 characters)" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-uppercase">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm your new password" required>
            </div>

            <button type="submit" class="btn btn-primary shadow">
                Update Password
            </button>
        </form>
    </div>
</body>
</html>
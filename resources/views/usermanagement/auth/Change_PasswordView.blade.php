<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('css/ktmb.css') }}">
</head>
<body>

    <!-- HEADER -->
    <header class="ktmb-header">
        <div class="ktmb-logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6d/KTMB_Official_Logo.jpg"
                 alt="KTMB Logo" class="ktmb-logo-img">
            <div class="ktmb-system-title">KTM <span>eDOIS</span></div>
        </div>
    </header>

    <!-- SUBHEADER -->
    <div class="ktmb-subheader">
        Electronic Delivery Order &amp; Invoice System
    </div>

    <main>
        <div class="ktmb-card">

            <h2>Forgot Password</h2>
            <p class="subtitle">
                Enter your registered email address and we will send you a password reset link.
            </p>

            <!-- SUCCESS MESSAGE -->
            @if (session('status'))
                <div class="alert-success">
                    ✓ {{ session('status') }}
                </div>
            @endif

            <!-- FORGOT PASSWORD FORM -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                           value="{{ old('email') }}"
                           placeholder="Enter your email address"
                           required
                           autofocus>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-ktmb-primary">
                    Send Password Reset Link
                </button>

                <div style="text-align:center; margin-top:15px;">
                    <a href="{{ route('login') }}" class="ktmb-link">
                        ← Back to Login
                    </a>
                </div>

            </form>
        </div>
    </main>

    <footer class="ktmb-footer">
        &copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad (KTMB). All rights reserved.
    </footer>

</body>
</html>
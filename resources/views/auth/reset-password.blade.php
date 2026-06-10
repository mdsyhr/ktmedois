<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Reset Password</title>
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

            <h2>Reset Password</h2>
            <p class="subtitle">
                Enter your new password below to reset your account password.
            </p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Hidden token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                           value="{{ old('email', $request->email) }}"
                           placeholder="Enter your email address"
                           required
                           autofocus>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="Enter your new password"
                           required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Re-enter your new password"
                           required>
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-ktmb-primary">
                    Reset Password
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
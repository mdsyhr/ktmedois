<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Login</title>
    <link rel="stylesheet" href="{{ asset('css/ktmb.css') }}">
</head>
<body>

    <header class="ktmb-header">
        <div class="ktmb-logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6d/KTMB_Official_Logo.jpg"
                 alt="KTMB Logo" class="ktmb-logo-img">
            <div class="ktmb-system-title">KTM <span>eDOIS</span></div>
        </div>
    </header>

    <div class="ktmb-subheader">
        Electronic Delivery Order &amp; Invoice System
    </div>

    <main>
        <div class="ktmb-card">
            <h2>Welcome Back</h2>
            <p class="subtitle">Please sign in to access the system.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="Role">Login As</label>
                    <select id="Role" name="Role" class="form-control" required>
                        <option value="">-- Select Role --</option>
                        <option value="staff"    {{ old('Role') == 'staff'    ? 'selected' : '' }}>KTMB Staff</option>
                        <option value="customer" {{ old('Role') == 'customer' ? 'selected' : '' }}>KTMB Customer</option>
                        <option value="vendor"   {{ old('Role') == 'vendor'   ? 'selected' : '' }}>Vendor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Username">Username</label>
                    <input type="text"
                           id="Username"
                           name="Username"
                           class="form-control"
                           value="{{ old('Username') }}"
                           required
                           autofocus
                           placeholder="Enter your username">
                    @error('Username')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="Password_Hash">Password</label>
                    <input type="password"
                           id="Password_Hash"
                           name="Password_Hash"
                           class="form-control"
                           required
                           placeholder="Enter your password">
                    @error('Password_Hash')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-ktmb-primary">LOG IN</button>

                @if (Route::has('password.request'))
                    <div style="text-align:center; margin-top:15px;">
                        <a href="{{ route('password.request') }}" class="ktmb-link">
                            Forgot your password?
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </main>

    <footer class="ktmb-footer">
        &copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad (KTMB). All rights reserved.
    </footer>

</body>
</html>
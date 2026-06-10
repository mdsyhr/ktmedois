<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KTM eDOIS')</title>
    <link rel="stylesheet" href="{{ asset('css/ktmb.css') }}">
    @stack('styles')
</head>
<body>

    <!-- HEADER -->
    <header class="ktmb-header">
        <div class="ktmb-logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6d/KTMB_Official_Logo.jpg"
                 alt="KTMB Logo" class="ktmb-logo-img">
            <div class="ktmb-system-title">KTM <span>eDOIS</span></div>
        </div>

        @auth
        <div class="ktmb-user-info">
            <span>{{ auth()->user()->Username }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();">
                    Logout
                </a>
            </form>
        </div>
        @endauth
    </header>

    <!-- DARK SUBHEADER -->
    <div class="ktmb-subheader">
        Electronic Delivery Order &amp; Invoice System
    </div>

    <!-- OPTIONAL TASKBAR (pages can add this) -->
    @yield('taskbar')

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="ktmb-footer">
        &copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad (KTMB). All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
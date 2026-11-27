<nav class="gym-navbar">
    <div class="gym-navbar-container">

        <!-- LOGO -->
        <a href="{{ route('home') }}" class="gym-logo">
            FIT<span>MEMBER</span><b>.</b>
        </a>

        <!-- NAVIGATION -->
        <div class="gym-menu">
            <a href="{{ route('home') }}" class="gym-menu-link">Home</a>
            <a href="{{ route('home') }}#about" class="gym-menu-link">About</a>
            <a href="{{ route('home') }}#packages" class="gym-menu-link">Membership</a>
            <a href="{{ route('home') }}#contact" class="gym-menu-link">Contact</a>
        </div>

        <!-- AUTH BUTTON -->
        <div class="gym-nav-actions">

            @if(auth()->check())

                @php
                    $dashboardUrl = auth()->user()->role === 'admin'
                        ? route('admin.dashboard')
                        : route('member.dashboard');
                @endphp

                <a href="{{ $dashboardUrl }}" class="gym-login-btn">
                    Dashboard
                </a>

                <a href="{{ $dashboardUrl }}" class="gym-join-btn">
                    My Account <i class="bi bi-arrow-right"></i>
                </a>

            @else

                <a href="{{ route('login') }}" class="gym-login-btn">
                    Login
                </a>

                <a href="{{ route('register') }}" class="gym-join-btn">
                    Join Now <i class="bi bi-arrow-right"></i>
                </a>

            @endif

        </div>

    </div>
</nav>

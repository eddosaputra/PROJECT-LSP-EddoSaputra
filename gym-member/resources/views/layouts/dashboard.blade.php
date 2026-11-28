<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') - FITMEMBER</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">

<div class="dashboard-layout">

    @if(auth()->user()->role === 'member' && session('login_announcements'))
        <aside class="login-announcement-toast" data-login-announcements role="alert" aria-live="polite">
            <div class="login-announcement-toast-header">
                <strong>FITMEMBER Announcements</strong>
                <button type="button" data-dismiss-login-announcements aria-label="Close announcement">&times;</button>
            </div>
            @foreach(session('login_announcements') as $announcement)
                <article class="login-announcement-item">
                    <h3>{{ $announcement['title'] }}</h3>
                    <p>{{ $announcement['content'] }}</p>
                </article>
            @endforeach
        </aside>
    @endif

    <!-- SIDEBAR -->
    <aside class="dashboard-sidebar">

        <!-- LOGO (NON-CLICKABLE) -->
        <a href="{{ route('home') }}" class="dashboard-logo">
            FIT<span>MEMBER</span><b>.</b>
            </a>

        <div class="sidebar-label">MAIN MENU</div>

        <nav class="sidebar-menu">

            @if(auth()->user()->role === 'admin')

                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
                </a>

                <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Members</span>
                </a>

                <a href="{{ route('admin.dashboard') }}#pending-payments" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i>
                    <span>Payments</span>
                </a>

                <a href="{{ route('admin.packages.index') }}" class="sidebar-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <i class="bi bi-card-checklist"></i>
                    <span>Membership Plans</span>
                </a>

                <a href="{{ route('admin.announcements.index') }}" class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Announcements</span>
                </a>

            @else

                <a href="{{ route('member.dashboard') }}" class="sidebar-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
                </a>

                <a href="{{ route('member.dashboard') }}#memberships" class="sidebar-link">
                    <i class="bi bi-card-membership"></i>
                    <span>My Membership</span>
                </a>

                <a href="{{ route('member.dashboard') }}#payment-history" class="sidebar-link">
                    <i class="bi bi-credit-card"></i>
                    <span>Payment History</span>
                </a>

                <a href="{{ route('member.dashboard') }}#announcements" class="sidebar-link">
                    <i class="bi bi-megaphone"></i>
                    <span>Announcements</span>
                </a>

            @endif

        </nav>

        <!-- SIDEBAR LOGOUT -->
        <div class="sidebar-bottom">

            <form action="{{ route('logout') }}" method="POST" class="sidebar-bottom-link">
                @csrf

                <button type="submit" class="sidebar-logout">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </button>
            </form>

        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">

        <header class="dashboard-topbar">

            <div>
                <span class="topbar-label">FITMEMBER FITNESS CLUB</span>
                <h2>@yield('page-heading', 'Dashboard')</h2>
            </div>

            <div class="topbar-user">

                @if(auth()->user()->profile_photo_path)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->profile_photo_path) }}"
                        alt="{{ auth()->user()->name }} profile photo"
                        class="topbar-avatar topbar-avatar-image"
                    >
                @else
                    <div class="topbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif

                <div class="topbar-user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ ucfirst(auth()->user()->role) }}</span>
                </div>

            </div>

        </header>

        <div class="dashboard-content">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('dashboard-content')

        </div>

    </main>

</div>

</body>
</html>
```
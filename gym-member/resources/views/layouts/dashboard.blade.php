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

                <a href="{{ route('admin.dashboard') }}#pending-members" class="sidebar-link">
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

                <a href="{{ route('member.dashboard') }}#profile" class="sidebar-link">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
                </a>

                <a href="{{ route('member.dashboard') }}#payment-history" class="sidebar-link">
                    <i class="bi bi-card-membership"></i>
                    <span>My Membership</span>
                </a>

                <a href="{{ route('member.dashboard') }}#memberships" class="sidebar-link">
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

                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

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
@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('page-heading', 'Admin Dashboard')

@section('dashboard-content')

<!-- ADMIN WELCOME -->
<div class="admin-welcome-banner">

    <div>
        <span class="dashboard-eyebrow">ADMIN CONTROL CENTER</span>

        <h1>WELCOME BACK,<br>
            <span>{{ strtoupper(auth()->user()->name) }}</span>
        </h1>

        <p>
            Manage members, membership plans, and gym activities
            from one place.
        </p>
    </div>

    <div class="admin-welcome-icon">
        <i class="bi bi-activity"></i>
    </div>

</div>


<!-- STATISTICS -->
<div class="dashboard-section-heading">
    <h3>Gym Overview</h3>
    <span>Membership statistics</span>
</div>

<div class="admin-stat-grid">

    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Total Members</span>
            <i class="bi bi-people"></i>
        </div>

        <h2>{{ \App\Models\User::where('role', 'member')->count() }}</h2>

        <small>Registered member accounts</small>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Active Members</span>
            <i class="bi bi-person-check"></i>
        </div>

        <h2>0</h2>

        <small>Active memberships</small>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Pending</span>
            <i class="bi bi-clock-history"></i>
        </div>

        <h2>0</h2>

        <small>Awaiting verification</small>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Expired Members</span>
            <i class="bi bi-calendar-x"></i>
        </div>

        <h2>0</h2>

        <small>Expired memberships</small>
    </div>

</div>


<!-- QUICK ACTIONS -->
<div class="dashboard-section-heading">
    <h3>Quick Actions</h3>
    <span>Management shortcuts</span>
</div>

<div class="admin-action-grid">

    <a href="#" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-person-plus"></i>
        </div>

        <div>
            <h4>Manage Members</h4>
            <p>View and manage member accounts</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

    <a href="#" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-credit-card"></i>
        </div>

        <div>
            <h4>Manage Payments</h4>
            <p>Review membership payments</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

    <a href="#" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-card-checklist"></i>
        </div>

        <div>
            <h4>Membership Plans</h4>
            <p>Manage gym membership packages</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

    <a href="#" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-megaphone"></i>
        </div>

        <div>
            <h4>Announcements</h4>
            <p>Create gym announcements</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

</div>


<!-- MEMBER TABLE -->
<div class="dashboard-section-heading">
    <h3>Recently Registered Members</h3>
    <span>Latest accounts</span>
</div>

<div class="dashboard-panel admin-table-panel">

    <div class="admin-table-header">
        <h4>Member Accounts</h4>
        <span class="table-count">
            {{ \App\Models\User::where('role', 'member')->count() }} members
        </span>
    </div>

    <div class="table-responsive">

        <table class="table admin-member-table">

            <thead>
                <tr>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse(\App\Models\User::where('role', 'member')->latest()->take(5)->get() as $member)

                    <tr>
                        <td>
                            <div class="table-member-name">
                                <div class="table-avatar">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>

                                <strong>{{ $member->name }}</strong>
                            </div>
                        </td>

                        <td>{{ $member->email }}</td>

                        <td>{{ $member->phone ?? '-' }}</td>

                        <td>{{ $member->created_at->format('d M Y') }}</td>

                        <td>
                            <span class="account-status">
                                Registered
                            </span>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="empty-table">
                            No members registered yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection

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

        <h2>{{ $activeMemberships }}</h2>

        <small>Active memberships</small>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Pending</span>
            <i class="bi bi-clock-history"></i>
        </div>

        <h2>{{ $pendingMembers->count() + $pendingRegistrations->count() + $pendingPayments->count() }}</h2>

        <small>Accounts, orders, and payments</small>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-top">
            <span>Expired Members</span>
            <i class="bi bi-calendar-x"></i>
        </div>

        <h2>{{ $expiredMemberships }}</h2>

        <small>Expired memberships</small>
    </div>

</div>


<!-- QUICK ACTIONS -->
<div class="dashboard-section-heading">
    <h3>Quick Actions</h3>
    <span>Management shortcuts</span>
</div>

<div class="admin-action-grid">

    <a href="{{ route('admin.members.index') }}" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-person-plus"></i>
        </div>

        <div>
            <h4>Manage Members</h4>
            <p>Edit or remove member accounts</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

    <a href="{{ route('admin.dashboard') }}#pending-payments" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-credit-card"></i>
        </div>

        <div>
            <h4>Manage Payments</h4>
            <p>Review membership payments</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

    <a href="{{ route('admin.packages.index') }}" class="admin-action-card">
        <div class="admin-action-icon">
            <i class="bi bi-card-checklist"></i>
        </div>

        <div>
            <h4>Membership Plans</h4>
            <p>Manage gym membership packages</p>
        </div>

        <i class="bi bi-arrow-up-right action-arrow"></i>
    </a>

    <a href="{{ route('admin.announcements.index') }}" class="admin-action-card">
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


<!-- PENDING MEMBER APPROVALS -->
<div class="dashboard-section-heading" id="pending-members">
    <h3>Pending Member Approvals</h3>
    <span>Review new membership applications</span>
</div>

<div class="dashboard-panel admin-table-panel">

    <div class="admin-table-header">
        <h4>Applications Awaiting Review</h4>
        <span class="table-count">{{ $pendingMembers->count() }} pending</span>
    </div>

    <div class="table-responsive">
        <table class="table admin-member-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingMembers as $member)
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
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.members.approval', $member) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="approval_status" value="approved">
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('admin.members.approval', $member) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="approval_status" value="rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-table">No pending member applications.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- PENDING MEMBERSHIP ORDERS -->
<div class="dashboard-section-heading mt-4" id="pending-registrations">
    <h3>Membership Requests</h3>
    <span>Approve or reject new package orders</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Package</th>
                    <th>Price</th>
                    <th>Requested</th>
                    <th>Decision</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingRegistrations as $registration)
                    <tr>
                        <td>{{ $registration->user->name }}<br><small>{{ $registration->user->email }}</small></td>
                        <td>{{ $registration->package->name }}</td>
                        <td>Rp{{ number_format((float) $registration->package->price, 0, ',', '.') }}</td>
                        <td>{{ $registration->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['approved' => 'Approve', 'rejected' => 'Reject'] as $status => $label)
                                    <form action="{{ route('admin.registrations.status', $registration) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <button type="submit" class="btn btn-{{ $status === 'approved' ? 'success' : 'danger' }} btn-sm">
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-table">No membership requests awaiting review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- PENDING PAYMENT CONFIRMATIONS -->
<div class="dashboard-section-heading mt-4" id="pending-payments">
    <h3>Payment Confirmations</h3>
    <span>Verify or reject member payment proofs</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Method / Proof</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingPayments as $payment)
                    <tr>
                        <td>{{ $payment->registration->user->name }}<br><small>{{ $payment->registration->user->email }}</small></td>
                        <td>{{ $payment->registration->package->name }}</td>
                        <td>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                        <td>
                            {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}<br>
                            @if($payment->proof_image)
                                <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank" rel="noopener">Download proof</a>
                            @else
                                <span class="text-secondary">No proof image provided</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.payments.status', $payment) }}" method="POST" class="d-grid gap-2">
                                @csrf
                                <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional note for member">
                                <div class="d-flex gap-2">
                                    <button type="submit" name="status" value="verified" class="btn btn-success btn-sm">Verify</button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-table">No payment confirmations awaiting review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MEMBER TABLE -->
<div class="dashboard-section-heading">
    <h3>Recently Registered Members</h3>
    <span>Latest accounts</span>
</div>

<div class="dashboard-panel admin-table-panel">

    <div class="admin-table-header">
        <h4>Member Accounts</h4>
        <div class="d-flex align-items-center gap-3">
            <span class="table-count">
                {{ \App\Models\User::where('role', 'member')->count() }} members
            </span>
            <a href="{{ route('admin.members.index') }}" class="btn btn-outline-light btn-sm">Manage all</a>
        </div>
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
                            <span class="account-status">{{ ucfirst($member->approval_status) }}</span>
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

@extends('layouts.dashboard')

@section('title', 'Manage Members')

@section('page-heading', 'Manage Members')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>Member Accounts</h3>
    <span>Edit account details or remove a member and their membership history</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="admin-table-header">
        <h4>All Members</h4>
        <span class="table-count">{{ $members->total() }} members</span>
    </div>

    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>
                            <div class="table-member-name">
                                @if($member->profile_photo_path)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->profile_photo_path) }}"
                                        alt="{{ $member->name }} profile photo"
                                        class="topbar-avatar topbar-avatar-image"
                                    >
                                @else
                                    <div class="table-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                @endif
                                <strong>{{ $member->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->phone ?: '-' }}</td>
                        <td>{{ $member->created_at->format('d M Y') }}</td>
                        <td>{{ ucfirst($member->approval_status) }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-outline-light btn-sm">Edit</a>
                                <form
                                    action="{{ route('admin.members.destroy', $member) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this member account and all associated membership and payment records? This cannot be undone.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-table">No member accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">{{ $members->links() }}</div>
</div>

@endsection

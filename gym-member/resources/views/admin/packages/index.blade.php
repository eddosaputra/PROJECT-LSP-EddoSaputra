@extends('layouts.dashboard')

@section('title', 'Membership Packages')

@section('page-heading', 'Membership Packages')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>Add a membership package</h3>
    <span>Active packages are available for member orders</span>
</div>

<div class="dashboard-panel admin-table-panel p-4 mb-4">
    <form action="{{ route('admin.packages.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Package name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
        </div>
        <div class="col-md-3">
            <label class="form-label">Price (IDR)</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" min="0.01" step="0.01" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Duration (days)</label>
            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days') }}" min="1" max="3650" required>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" maxlength="5000">{{ old('description') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
                Available to members
            </label>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-danger">Add package</button>
        </div>
    </form>
</div>

<div class="dashboard-section-heading">
    <h3>All packages</h3>
    <span>Disable a package to hide it from new orders without removing its history</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Package</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $package)
                    <tr>
                        <td>
                            <strong>{{ $package->name }}</strong>
                            @if($package->description)
                                <br><small>{{ $package->description }}</small>
                            @endif
                        </td>
                        <td>Rp{{ number_format((float) $package->price, 0, ',', '.') }}</td>
                        <td>{{ $package->duration_days }} days</td>
                        <td>{{ $package->is_active ? 'Active' : 'Hidden' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-outline-light btn-sm">Edit</a>
                                <form action="{{ route('admin.packages.status', $package) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-{{ $package->is_active ? 'secondary' : 'success' }} btn-sm">
                                        {{ $package->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-table">No membership packages have been added.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $packages->links() }}</div>
</div>

@endsection

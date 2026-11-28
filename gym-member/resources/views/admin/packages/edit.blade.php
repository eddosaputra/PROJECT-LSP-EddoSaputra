@extends('layouts.dashboard')

@section('title', 'Edit Membership Package')

@section('page-heading', 'Edit Membership Package')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>{{ $package->name }}</h3>
    <span>Update the package details members see when ordering</span>
</div>

<div class="dashboard-panel admin-table-panel p-4">
    <form action="{{ route('admin.packages.update', $package) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')
        <div class="col-md-6">
            <label class="form-label">Package name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $package->name) }}" required maxlength="255">
        </div>
        <div class="col-md-3">
            <label class="form-label">Price (IDR)</label>
            <input type="number" name="price" class="form-control" value="{{ old('price', $package->price) }}" min="0.01" step="0.01" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Duration (days)</label>
            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $package->duration_days) }}" min="1" max="3650" required>
        </div>
        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" maxlength="5000">{{ old('description', $package->description) }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $package->is_active))>
                Available to members
            </label>
        </div>
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-danger">Save changes</button>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-light">Cancel</a>
        </div>
    </form>
</div>

@endsection

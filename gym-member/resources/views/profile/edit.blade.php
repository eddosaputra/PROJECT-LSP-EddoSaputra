@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('page-heading', 'My Profile')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>Profile information</h3>
    <span>Update your display name, phone number, and profile photo</span>
</div>

<div class="dashboard-panel admin-table-panel p-4">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-edit-form">
        @csrf
        @method('PUT')

        <div class="profile-photo-section">
            @if($user->profile_photo_path)
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) }}"
                    alt="{{ $user->name }} profile photo"
                    class="profile-photo-preview"
                >
            @else
                <div class="profile-photo-placeholder">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            <div class="profile-photo-control">
                <label for="profile_photo" class="form-label">Profile photo</label>
                <input
                    type="file"
                    name="profile_photo"
                    id="profile_photo"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                >
                <small class="text-secondary">JPG, PNG, or WEBP. Maximum file size: 5 MB.</small>
                @error('profile_photo')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label for="name" class="form-label">Full name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control"
                    value="{{ old('name', $user->name) }}"
                    maxlength="255"
                    required
                >
                @error('name')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">Phone number</label>
                <input
                    type="tel"
                    name="phone"
                    id="phone"
                    class="form-control"
                    value="{{ old('phone', $user->phone) }}"
                    maxlength="20"
                >
                @error('phone')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-danger">Save profile</button>
            <a href="{{ $user->role === 'admin' ? route('admin.dashboard') : route('member.dashboard') }}" class="btn btn-outline-light">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@extends('layouts.dashboard')

@section('title', 'Edit Announcement')

@section('page-heading', 'Edit Announcement')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>Update announcement</h3>
    <span>Changes are visible to members when published</span>
</div>

<div class="dashboard-panel admin-table-panel p-4">
    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')
        <div class="col-12">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $announcement->title) }}" maxlength="255" required>
        </div>
        <div class="col-12">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="6" maxlength="20000" required>{{ old('content', $announcement->content) }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-check-label">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" @checked(old('is_published', $announcement->is_published))>
                Published
            </label>
        </div>
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-danger">Save changes</button>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-light">Cancel</a>
        </div>
    </form>
</div>

@endsection

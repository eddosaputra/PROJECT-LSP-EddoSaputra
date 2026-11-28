@extends('layouts.dashboard')

@section('title', 'Manage Announcements')

@section('page-heading', 'Manage Announcements')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>Create an announcement</h3>
    <span>Published announcements appear on member dashboards</span>
</div>

<div class="dashboard-panel admin-table-panel p-4 mb-4">
    <form action="{{ route('admin.announcements.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-12">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" maxlength="255" required>
        </div>
        <div class="col-12">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5" maxlength="20000" required>{{ old('content') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-check-label">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" checked>
                Publish immediately
            </label>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-danger">Create announcement</button>
        </div>
    </form>
</div>

<div class="dashboard-section-heading">
    <h3>All announcements</h3>
    <span>{{ $announcements->total() }} total</span>
</div>

<div class="dashboard-panel admin-table-panel">
    <div class="table-responsive">
        <table class="table admin-member-table align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Created</th>
                    <th>Visibility</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                    <tr>
                        <td>
                            <strong>{{ $announcement->title }}</strong><br>
                            <small>{{ \Illuminate\Support\Str::limit($announcement->content, 120) }}</small>
                        </td>
                        <td>{{ $announcement->created_at->format('d M Y') }}</td>
                        <td>{{ $announcement->is_published ? 'Published' : 'Draft' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-outline-light btn-sm">Edit</a>
                                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-table">No announcements have been created.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $announcements->links() }}</div>
</div>

@endsection

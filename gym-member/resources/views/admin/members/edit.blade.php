@extends('layouts.dashboard')

@section('title', 'Edit Member')

@section('page-heading', 'Edit Member')

@section('dashboard-content')

<div class="dashboard-section-heading">
    <h3>{{ $member->name }}</h3>
    <span>Update the member's contact and account details</span>
</div>

<div class="dashboard-panel admin-table-panel p-4">
    <form action="{{ route('admin.members.update', $member) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
            <label for="name" class="form-label">Full name</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $member->name) }}" required maxlength="255">
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $member->email) }}" required maxlength="255">
        </div>

        <div class="col-md-6">
            <label for="phone" class="form-label">Phone number</label>
            <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone) }}" maxlength="20">
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-danger">Save changes</button>
            <a href="{{ route('admin.members.index') }}" class="btn btn-outline-light">Cancel</a>
        </div>
    </form>
</div>

@endsection

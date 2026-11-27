
@extends('layouts.app')

@section('title', 'Member Dashboard | FITMEMBER')

@section('content')

<div style="background:#0b0b0b; min-height:100vh; color:#fff; padding:40px;">

    <div style="max-width:1200px; margin:auto;">

        <h1 style="font-size:32px; font-weight:800;">
            WELCOME, {{ strtoupper(auth()->user()->name) }}!
        </h1>

        <p style="color:#999; margin-top:10px;">
            Welcome to FITMEMBER. Let's build a stronger version of yourself.
        </p>

        <div style="background:#1a1a1a; padding:30px; border-radius:15px; margin-top:30px; border:1px solid #333;">

            <h3 style="color:#e50914;">YOUR MEMBER PROFILE</h3>

            <p style="margin-top:20px;">
                <strong>Name:</strong>
                {{ auth()->user()->name }}
            </p>

            <p>
                <strong>Email:</strong>
                {{ auth()->user()->email }}
            </p>

            <p>
                <strong>Phone:</strong>
                {{ auth()->user()->phone ?? '-' }}
            </p>

            <p>
                <strong>Role:</strong>
                {{ ucfirst(auth()->user()->role) }}
            </p>

        </div>

        <div style="margin-top:30px; display:flex; gap:20px; flex-wrap:wrap;">

            <div style="background:#1a1a1a; padding:25px; border-radius:12px; flex:1; min-width:220px;">
                <h4 style="color:#999;">MEMBERSHIP STATUS</h4>
                <h2 style="color:#e50914;">Pending</h2>
            </div>

            <div style="background:#1a1a1a; padding:25px; border-radius:12px; flex:1; min-width:220px;">
                <h4 style="color:#999;">MEMBERSHIP PLAN</h4>
                <h2>Not Assigned</h2>
            </div>

            <div style="background:#1a1a1a; padding:25px; border-radius:12px; flex:1; min-width:220px;">
                <h4 style="color:#999;">MEMBER SINCE</h4>
                <h2>{{ auth()->user()->created_at->format('d M Y') }}</h2>
            </div>

        </div>

        <form action="{{ route('logout') }}" method="POST" style="margin-top:40px;">
            @csrf

            <button type="submit"
                style="background:#e50914; color:#fff; border:none; padding:12px 25px; border-radius:8px; cursor:pointer;">
                LOGOUT
            </button>
        </form>

    </div>

</div>

@endsection

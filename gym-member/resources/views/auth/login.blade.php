@extends('layouts.app')

@section('title', 'Login | FITMEMBER')

@section('content')

<div class="auth-page">

    <div class="auth-left">

        <a href="{{ route('home') }}" class="auth-logo">
            FIT<span>MEMBER</span><b>.</b>
        </a>

        <div class="auth-form-container">

            <span class="auth-label">WELCOME BACK</span>

            <h1>LOGIN TO<br><span>YOUR ACCOUNT.</span></h1>

            <p class="auth-description">
                Continue your fitness journey and stay committed
                to becoming stronger every day.
            </p>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="auth-input-label">Email Address</label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="auth-input-label">Password</label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            name="password"
                            id="loginPassword"
                            placeholder="Enter your password"
                            required
                        >

                        <button type="button"
                            class="password-toggle"
                            onclick="togglePassword('loginPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="auth-submit">
                    LOGIN TO ACCOUNT
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <p class="auth-switch">
                Don't have an account?
                <a href="{{ route('register') }}">Join Now</a>
            </p>

            <a href="{{ route('home') }}" class="auth-back">
                <i class="bi bi-arrow-left"></i>
                Back to Home
            </a>

        </div>

    </div>

    <div class="auth-right auth-login-background">

        <div class="auth-overlay"></div>

        <div class="auth-visual-content">

            <span class="auth-label">TRAIN. FOCUS. REPEAT.</span>

            <h2>NO EXCUSES.<br>
                <span>JUST RESULTS.</span>
            </h2>

            <p>
                Every rep counts. Every day matters.
                Keep pushing your limits.
            </p>

        </div>

        <div class="auth-visual-bottom">
            <span>FITMEMBER FITNESS CLUB</span>
            <span>01 — 2026</span>
        </div>

    </div>

</div>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

@endsection

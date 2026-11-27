@extends('layouts.app')

@section('title', 'Register | FITMEMBER')

@section('content')

<div class="auth-page">

    <div class="auth-left">

        <a href="{{ route('home') }}" class="auth-logo">
            FIT<span>MEMBER</span><b>.</b>
        </a>

        <div class="auth-form-container auth-register-container">

            <span class="auth-label">START YOUR JOURNEY</span>

            <h1>CREATE YOUR<br><span>MEMBERSHIP.</span></h1>

            <p class="auth-description">
                Join our fitness community and start building
                a stronger, healthier version of yourself.
            </p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.process') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="auth-input-label">Full Name</label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            name="name"
                            placeholder="Enter your full name"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="auth-input-label">Phone Number</label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-telephone"></i>

                        <input
                            type="tel"
                            name="phone"
                            placeholder="Enter your phone number"
                            value="{{ old('phone') }}"
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
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
                            id="registerPassword"
                            placeholder="Minimum 8 characters"
                            required
                        >

                        <button type="button"
                            class="password-toggle"
                            onclick="togglePassword('registerPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="auth-input-label">Confirm Password</label>

                    <div class="auth-input-wrapper">
                        <i class="bi bi-shield-lock"></i>

                        <input
                            type="password"
                            name="password_confirmation"
                            id="confirmPassword"
                            placeholder="Confirm your password"
                            required
                        >

                        <button type="button"
                            class="password-toggle"
                            onclick="togglePassword('confirmPassword', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit">
                    CREATE ACCOUNT
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <p class="auth-switch">
                Already have an account?
                <a href="{{ route('login') }}">Login</a>
            </p>

            <a href="{{ route('home') }}" class="auth-back">
                <i class="bi bi-arrow-left"></i>
                Back to Home
            </a>

        </div>

    </div>

    <div class="auth-right auth-register-background">

        <div class="auth-overlay"></div>

        <div class="auth-visual-content">

            <span class="auth-label">BECOME STRONGER</span>

            <h2>YOUR BODY.<br>
                <span>YOUR RULES.</span>
            </h2>

            <p>
                The hardest step is the first one.
                Start today and let us help you achieve your goals.
            </p>

        </div>

        <div class="auth-visual-bottom">
            <span>FITMEMBER FITNESS CLUB</span>
            <span>02 — 2026</span>
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

@extends('layouts.app')

@section('title', 'FITMEMBER | Membership Gym')

@section('content')

@php
    if (auth()->check()) {
        $dashboardUrl = auth()->user()->role === 'admin'
            ? route('admin.packages.index')
            : route('member.dashboard') . '#memberships';
    } else {
        $dashboardUrl = route('register');
    }
@endphp

@include('partials.navbar')

<!-- HERO SECTION -->
<section class="hero-gym">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">

                <span class="text-red fw-bold">
                    YOUR FITNESS JOURNEY STARTS HERE
                </span>

                <h1 class="hero-title mt-3">
                    BUILD YOUR
                    <span class="text-red">STRENGTH.</span>
                </h1>

                <p class="hero-description mt-4">
                    Train harder. Stay stronger. Become the best version
                    of yourself with the right environment, equipment,
                    and membership plan.
                </p>

                <div class="d-flex gap-3 flex-wrap mt-4">

                    <a href="{{ $dashboardUrl }}" class="gym-btn">
                        JOIN MEMBERSHIP
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                    <a href="#packages" class="gym-btn-outline">
                        EXPLORE PACKAGES
                    </a>

                </div>

            </div>
        </div>
    </div>
</section>


<!-- STATISTIC SECTION -->
<section class="py-4 bg-gym-dark">
    <div class="container">

        <div class="row text-center g-3">

            <div class="col-6 col-md-3">
                <h2 class="text-red">500+</h2>
                <p class="text-secondary mb-0">Active Members</p>
            </div>

            <div class="col-6 col-md-3">
                <h2 class="text-red">20+</h2>
                <p class="text-secondary mb-0">Professional Trainers</p>
            </div>

            <div class="col-6 col-md-3">
                <h2 class="text-red">50+</h2>
                <p class="text-secondary mb-0">Gym Equipment</p>
            </div>

            <div class="col-6 col-md-3">
                <h2 class="text-red">7 DAYS</h2>
                <p class="text-secondary mb-0">Open Every Week</p>
            </div>

        </div>

    </div>
</section>


<!-- ABOUT SECTION -->
<section class="section-padding" id="about">
    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <video
                    class="img-fluid rounded"
                    autoplay
                    muted
                    loop
                    playsinline
                    style="width: 100%; height: 335px; object-fit: cover;"
                >
                    <source src="{{ asset('videos/gym-member.mp4') }}" type="video/mp4">
                    Browser Anda tidak mendukung pemutaran video.
                </video>
            </div>

            <div class="col-lg-6">

                <span class="text-red fw-bold">ABOUT OUR GYM</span>

                <h2 class="display-4 mt-3">
                    MORE THAN JUST
                    <span class="text-red">A WORKOUT.</span>
                </h2>

                <p class="text-secondary mt-3">
                    FITMEMBER is a fitness community designed to help
                    everyone achieve their personal fitness goals.
                    Whether you are a beginner or an experienced athlete,
                    we provide a space to grow, train, and improve.
                </p>

                <ul class="list-unstyled text-secondary mt-4">

                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-red me-2"></i>
                        Professional gym equipment
                    </li>

                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-red me-2"></i>
                        Comfortable training environment
                    </li>

                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-red me-2"></i>
                        Flexible membership packages
                    </li>

                </ul>

            </div>

        </div>

    </div>
</section>


<!-- PACKAGES SECTION -->
<section class="section-padding bg-gym-dark" id="packages">
    <div class="container">

        <div class="text-center mb-5">

            <span class="text-red fw-bold">
                MEMBERSHIP PLAN
            </span>

            <h2 class="display-4 mt-2">
                CHOOSE YOUR <span class="text-red">PLAN</span>
            </h2>

            <p class="text-secondary">
                Find the membership that fits your fitness goals.
            </p>

        </div>

        <div class="row g-4">
            @forelse($packages as $package)
                <div class="col-md-4">
                    <div class="gym-card rounded p-4 h-100">
                        <h3>{{ $package->name }}</h3>

                        @if($package->description)
                            <p class="text-secondary">{{ $package->description }}</p>
                        @endif

                        <h2 class="my-4">
                            Rp{{ number_format((float) $package->price, 0, ',', '.') }}
                            <small class="fs-6 text-secondary">/ {{ $package->duration_days }} days</small>
                        </h2>

                        <a href="{{ $dashboardUrl }}" class="gym-btn w-100 text-center">
                            GET STARTED
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="gym-card rounded p-4 text-center">
                        <p class="text-secondary mb-3">No active membership packages are available yet.</p>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <a href="{{ route('admin.packages.index') }}" class="gym-btn">ADD A PACKAGE</a>
                        @else
                            <a href="{{ $dashboardUrl }}" class="gym-btn">GET STARTED</a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</section>


<!-- CTA SECTION -->
<section class="section-padding text-center">

    <div class="container">

        <span class="text-red fw-bold">
            START TODAY
        </span>

        <h2 class="display-3 mt-3">
            YOUR GOALS.
        </h2>

        <h2 class="display-3 text-red">
            YOUR DISCIPLINE.
        </h2>

        <p class="text-secondary mt-3">
            Take the first step towards a stronger and healthier lifestyle.
        </p>

        <a href="{{ $dashboardUrl }}" class="gym-btn mt-3">
            BECOME A MEMBER
        </a>

    </div>

</section>

@include('partials.footer')

@endsection

@extends('layouts.app')

@section('title', 'FITMEMBER | Membership Gym')

@section('content')

@php
    if (auth()->check()) {
        $dashboardUrl = auth()->user()->role === 'admin'
            ? route('admin.dashboard')
            : route('member.dashboard');
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

                <img
                    src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1000&q=80"
                    class="img-fluid rounded"
                    alt="Gym interior">

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

            <!-- BASIC -->
            <div class="col-md-4">

                <div class="gym-card rounded p-4 h-100">

                    <h3>BASIC</h3>

                    <p class="text-secondary">
                        Perfect for starting your fitness journey.
                    </p>

                    <h2 class="my-4">
                        Rp150.000
                        <small class="fs-6 text-secondary">/ month</small>
                    </h2>

                    <ul class="list-unstyled text-secondary mb-4">
                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Gym access
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Standard equipment
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Locker access
                        </li>
                    </ul>

                    <a href="{{ $dashboardUrl }}" class="gym-btn w-100 text-center">
                        GET STARTED
                    </a>

                </div>

            </div>


            <!-- PREMIUM -->
            <div class="col-md-4">

                <div class="gym-card rounded p-4 h-100 border-danger">

                    <span class="badge bg-danger mb-3">
                        MOST POPULAR
                    </span>

                    <h3>PREMIUM</h3>

                    <p class="text-secondary">
                        More flexibility and better training experience.
                    </p>

                    <h2 class="my-4">
                        Rp350.000
                        <small class="fs-6 text-secondary">/ 3 months</small>
                    </h2>

                    <ul class="list-unstyled text-secondary mb-4">

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Full gym access
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            All standard equipment
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Locker access
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Personal consultation
                        </li>

                    </ul>

                    <a href="{{ $dashboardUrl }}" class="gym-btn w-100 text-center">
                        GET STARTED
                    </a>

                </div>

            </div>


            <!-- ANNUAL -->
            <div class="col-md-4">

                <div class="gym-card rounded p-4 h-100">

                    <h3>ANNUAL</h3>

                    <p class="text-secondary">
                        For those committed to long-term progress.
                    </p>

                    <h2 class="my-4">
                        Rp1.200.000
                        <small class="fs-6 text-secondary">/ year</small>
                    </h2>

                    <ul class="list-unstyled text-secondary mb-4">

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Unlimited gym access
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            All equipment
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Priority membership
                        </li>

                        <li class="mb-3">
                            <i class="bi bi-check text-red"></i>
                            Special member benefits
                        </li>

                    </ul>

                    <a href="{{ $dashboardUrl }}" class="gym-btn w-100 text-center">
                        GET STARTED
                    </a>

                </div>

            </div>

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

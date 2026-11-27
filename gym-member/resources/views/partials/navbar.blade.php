<nav class="gym-navbar">
    <div class="gym-navbar-container">

        <!-- LOGO -->
        <a href="{{ route('home') }}" class="gym-logo">
            FIT<span>MEMBER</span><b>.</b>
        </a>

        <!-- NAVIGATION -->
        <div class="gym-menu">

           <a href="{{ route('home') }}" class="gym-menu-link">Home</a>
            <a href="#about" class="gym-menu-link">About</a>
            <a href="#packages" class="gym-menu-link">Membership</a>
            <a href="#contact" class="gym-menu-link">Contact</a>

        </div>

       
<!-- AUTH BUTTON -->
    <div class="gym-nav-actions">

    <a href="{{ route('login') }}" class="gym-login-btn">
        Login
    </a>

    <a href="{{ route('register') }}" class="gym-join-btn">
        Join Now <i class="bi bi-arrow-right"></i>
    </a>

</div>


    </div>
</nav>

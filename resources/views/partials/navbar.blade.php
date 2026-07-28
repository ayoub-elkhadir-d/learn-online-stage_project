<nav class="site-navbar navbar navbar-expand-lg sticky-top" id="siteNavbar">
    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand" href="{{ route('courses.index') }}">
            <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" style="height:36px;">
        </a>

        {{-- Mobile toggler --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-expanded="false">
            <div class="toggler-icon">
                <span></span><span></span><span></span>
            </div>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">

            {{-- Center links --}}
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}"
                       href="{{ route('courses.index') }}">
                        <i class="fas fa-layer-group me-1" style="font-size:12px;"></i>Courses
                    </a>
                </li>
            </ul>

            {{-- Right side --}}
            <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="btn-my-courses {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap" style="font-size:12px;"></i>
                        My Courses
                    </a>

                    <div class="dropdown">
                        <button class="btn-user" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="d-none d-md-inline" style="max-width:100px;overflow:hidden;text-overflow:ellipsis;">
                                {{ auth()->user()->name }}
                            </span>
                            <i class="fas fa-chevron-down ms-1" style="font-size:10px;opacity:.6;"></i>
                        </button>

                        <ul class="dropdown-menu nav-dropdown dropdown-menu-end">
                            <li class="dropdown-header">
                                <div class="d-name">{{ auth()->user()->name }}</div>
                                <div class="d-email">{{ auth()->user()->email }}</div>
                            </li>

                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <span class="di-icon" style="background:#f0fdf4;color:#16a34a;">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    My Profile
                                </a>
                            </li>

                            @if(auth()->user()->role === 'admin')
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.courses.index') }}">
                                    <span class="di-icon" style="background:#faf5ff;color:#7c3aed;">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    Admin Panel
                                </a>
                            </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger w-100 border-0 bg-transparent">
                                        <span class="di-icon" style="background:#fff1f2;color:#dc2626;">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </span>
                                        Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                @else
                    <a href="{{ route('login') }}" class="btn-nav-login">Login</a>
                    <a href="{{ route('register') }}" class="btn-nav-register">
                        Get Started <i class="fas fa-arrow-right ms-1" style="font-size:11px;"></i>
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>

<script>
    // Scroll shadow effect
    window.addEventListener('scroll', function () {
        const nav = document.getElementById('siteNavbar');
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 10);
    });
</script>

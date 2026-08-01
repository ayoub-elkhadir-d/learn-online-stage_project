<nav
    id="siteNavbar"
    data-navbar
    class="sticky top-0 z-50 border-b border-(--color-border)/70 bg-(--color-bg-light)/80 backdrop-blur-lg transition-[padding,box-shadow] duration-300 dark:border-white/10 dark:bg-(--color-bg-dark)/80"
>
    <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-4 transition-[padding] duration-300 sm:px-6 lg:px-8" data-navbar-inner>

        <a href="{{ route('courses.index') }}" class="flex shrink-0 items-center gap-2">
            <img src="https://artiweb.ma/wp-content/uploads/2023/05/logo-1.png" alt="ArtiWeb" class="h-8 w-auto">
            <span class="text-base font-extrabold tracking-tight text-(--color-text) dark:text-white">{{ __('navbar.brand') }}</span>
        </a>

        <div class="hidden md:flex md:flex-1 md:items-center md:justify-center">
            <a
                href="{{ route('courses.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) {{ request()->routeIs('courses.index') ? 'text-(--color-primary)!' : '' }} dark:hover:text-white"
            >
                <x-icon name="layers" class="h-4 w-4" />
                {{ __('navbar.courses') }}
            </a>
        </div>

        <div class="hidden flex-1 max-w-xs lg:block">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
                <input
                    type="search"
                    placeholder="{{ __('navbar.search_placeholder') }}"
                    aria-label="{{ __('navbar.search_placeholder') }}"
                    class="w-full rounded-lg border border-(--color-border) bg-white/70 py-2 pl-9 pr-3 text-sm text-(--color-text) placeholder:text-(--color-text-secondary) focus:border-(--color-primary) focus:outline focus:outline-2 focus:outline-offset-1 focus:outline-(--color-primary)/30 dark:border-white/10 dark:bg-white/5 dark:text-white"
                >
            </div>
        </div>

        <div class="ml-auto flex items-center gap-1.5 sm:gap-2">

            <x-language-switcher />

            <button type="button" data-theme-toggle aria-label="{{ __('navbar.toggle_theme') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                <x-icon name="sun" class="hidden h-[18px] w-[18px] dark:block" />
                <x-icon name="moon" class="block h-[18px] w-[18px] dark:hidden" />
            </button>

            @auth
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="{{ __('navbar.notifications') }}"
                            class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                        <x-icon name="bell" class="h-[18px] w-[18px]" />
                    </button>
                    <div data-dropdown-panel role="menu"
                         class="absolute right-0 z-50 mt-2 hidden w-72 overflow-hidden rounded-xl border border-(--color-border) bg-(--color-card) shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">
                        <div class="border-b border-(--color-border) px-4 py-3 text-sm font-semibold dark:border-white/10">{{ __('navbar.notifications') }}</div>
                        <div class="flex flex-col items-center gap-2 px-4 py-8 text-center">
                            <x-icon name="bell" class="h-6 w-6 text-(--color-text-secondary)" />
                            <p class="text-sm text-(--color-text-secondary)">{{ __('navbar.no_notifications') }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}"
                   class="hidden items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors sm:inline-flex {{ request()->routeIs('dashboard') ? 'bg-(--color-primary)/10 text-(--color-primary)' : 'text-(--color-text-secondary) hover:text-(--color-text) dark:hover:text-white' }}">
                    <x-icon name="graduation-cap" class="h-4 w-4" />
                    {{ __('navbar.my_courses') }}
                </a>

                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false"
                            class="flex items-center gap-2 rounded-lg py-1.5 pl-1.5 pr-2 transition-colors hover:bg-black/5 dark:hover:bg-white/10">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-(--color-primary) text-xs font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden max-w-24 truncate text-sm font-medium md:inline">{{ auth()->user()->name }}</span>
                        <x-icon name="chevron-down" class="hidden h-3.5 w-3.5 text-(--color-text-secondary) md:inline" />
                    </button>

                    <div data-dropdown-panel role="menu"
                         class="absolute right-0 z-50 mt-2 hidden w-64 overflow-hidden rounded-xl border border-(--color-border) bg-(--color-card) py-1.5 shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">
                        <div class="px-4 py-2.5">
                            <div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div>
                            <div class="truncate text-xs text-(--color-text-secondary)">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="my-1 border-t border-(--color-border) dark:border-white/10"></div>

                        <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                            <x-icon name="user" class="h-4 w-4 text-(--color-text-secondary)" />
                            {{ __('navbar.my_profile') }}
                        </a>

                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                            <x-icon name="shield" class="h-4 w-4 text-(--color-text-secondary)" />
                            {{ __('navbar.admin_panel') }}
                        </a>
                        @endif

                        <div class="my-1 border-t border-(--color-border) dark:border-white/10"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-sm text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                <x-icon name="log-out" class="h-4 w-4" />
                                {{ __('navbar.sign_out') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-(--color-text-secondary) transition-colors hover:text-(--color-text) sm:inline-block dark:hover:text-white">
                    {{ __('navbar.login') }}
                </a>
                <a href="{{ route('register') }}" class="btn-primary !px-4 !py-2 text-sm">
                    {{ __('navbar.get_started') }}
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            @endauth

            <button type="button" data-mobile-toggle aria-label="{{ __('navbar.toggle_menu') }}" aria-expanded="false"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) hover:bg-black/5 md:hidden dark:hover:bg-white/10">
                <x-icon name="menu" class="h-5 w-5" />
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div data-mobile-panel class="hidden border-t border-(--color-border) px-4 pb-4 pt-3 md:hidden dark:border-white/10">
        <div class="relative mb-3">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-(--color-text-secondary)" />
            <input type="search" placeholder="{{ __('navbar.search_placeholder') }}" aria-label="{{ __('navbar.search_placeholder') }}" class="input-field !py-2 pl-9">
        </div>
        <a href="{{ route('courses.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-(--color-text-secondary) hover:bg-black/5 dark:hover:bg-white/5">{{ __('navbar.courses') }}</a>
        @auth
            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-(--color-text-secondary) hover:bg-black/5 dark:hover:bg-white/5">{{ __('navbar.my_courses') }}</a>
        @else
            <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-(--color-text-secondary) hover:bg-black/5 dark:hover:bg-white/5">{{ __('navbar.login') }}</a>
            <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-(--color-primary)">{{ __('navbar.get_started') }}</a>
        @endauth

        <x-language-switcher variant="inline" />
    </div>
</nav>

<script>
(function () {
    const nav = document.getElementById('siteNavbar');
    if (!nav || nav.dataset.bound) return;
    nav.dataset.bound = '1';

    const inner = nav.querySelector('[data-navbar-inner]');
    window.addEventListener('scroll', function () {
        const shrunk = window.scrollY > 12;
        inner.classList.toggle('py-2', shrunk);
        inner.classList.toggle('py-4', !shrunk);
        nav.classList.toggle('shadow-soft', shrunk);
    }, { passive: true });

    nav.querySelectorAll('[data-dropdown]').forEach(function (wrapper) {
        const toggle = wrapper.querySelector('[data-dropdown-toggle]');
        const panel = wrapper.querySelector('[data-dropdown-panel]');
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = !panel.classList.contains('hidden');
            document.querySelectorAll('[data-dropdown-panel]').forEach(p => p.classList.add('hidden'));
            panel.classList.toggle('hidden', isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('[data-dropdown-panel]').forEach(p => p.classList.add('hidden'));
    });

    const mobileToggle = nav.querySelector('[data-mobile-toggle]');
    const mobilePanel = nav.querySelector('[data-mobile-panel]');
    mobileToggle.addEventListener('click', function () {
        const isOpen = !mobilePanel.classList.contains('hidden');
        mobilePanel.classList.toggle('hidden', isOpen);
        mobileToggle.setAttribute('aria-expanded', String(!isOpen));
    });
})();
</script>

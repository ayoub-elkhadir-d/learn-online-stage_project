<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('admin.dashboard_title')) — {{ __('admin.admin_panel') }} — ArtiWeb</title>
    @include('partials.theme-init')
    <script>
        {{-- Synchronous pre-paint sidebar-collapse state, same reasoning as
             partials/theme-init.blade.php: avoids a flash of the wrong
             sidebar width on load. --}}
        (function () {
            if (localStorage.getItem('admin-sidebar-collapsed') === '1') {
                document.documentElement.classList.add('admin-sidebar-collapsed');
            }
        })();
    </script>
    @include('partials.locale-fonts')
    @vite(['resources/css/app.css', 'resources/css/rtl.css', 'resources/css/admin.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-(--color-bg-light) font-sans text-(--color-text) antialiased dark:bg-(--color-bg-dark) dark:text-[#ECECEC]">

@php
    $navItems = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => __('admin.sidebar_dashboard'), 'icon' => 'layout-dashboard'],
        ['route' => 'admin.courses.index', 'match' => 'admin.courses.*', 'label' => __('admin.sidebar_courses'), 'icon' => 'book-open'],
        ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => __('admin.sidebar_categories'), 'icon' => 'tag'],
        ['route' => 'admin.payments.index', 'match' => 'admin.payments.*', 'label' => __('admin.sidebar_payments'), 'icon' => 'credit-card'],
        ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => __('admin.sidebar_users'), 'icon' => 'users'],
    ];
@endphp

<div class="flex min-h-screen" data-admin-shell>

    {{-- Desktop sidebar --}}
    <aside data-admin-sidebar class="sticky top-0 hidden h-screen shrink-0 flex-col overflow-hidden border-r border-(--color-border) bg-(--color-card) lg:flex dark:border-white/10 dark:bg-(--color-card-dark)">
        <a href="{{ route('admin.dashboard') }}" class="flex shrink-0 items-center gap-2.5 border-b border-(--color-border) px-4 py-4 dark:border-white/10">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary) text-sm font-bold text-white">A</span>
            <span data-sidebar-brand-text class="min-w-0 truncate">
                <span class="block text-sm font-bold text-(--color-text) dark:text-white">ArtiWeb</span>
                <span class="block text-[11px] font-medium text-(--color-text-secondary)">{{ __('admin.admin_panel') }}</span>
            </span>
        </a>

        <nav class="scrollbar-thin flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
            @include('admin.partials.sidebar-nav')
        </nav>

        <div class="shrink-0 border-t border-(--color-border) p-3 dark:border-white/10">
            <div class="flex items-center gap-2.5 rounded-xl px-1.5 py-1.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-(--color-primary)/10 text-xs font-bold text-(--color-primary)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <span data-sidebar-user-info class="min-w-0 flex-1">
                    <span class="block truncate text-xs font-semibold text-(--color-text) dark:text-white">{{ auth()->user()->name }}</span>
                    <span class="block truncate text-[11px] text-(--color-text-secondary)">{{ __('admin.administrator') }}</span>
                </span>
            </div>
            <button type="button" data-sidebar-collapse-toggle title="{{ __('admin.collapse') }}"
                    class="mt-1 flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/5 dark:hover:text-white">
                <x-icon name="chevrons-left" data-sidebar-collapse-icon class="h-[18px] w-[18px] shrink-0 transition-transform duration-200" />
                <span data-sidebar-label>{{ __('admin.collapse') }}</span>
            </button>
        </div>
    </aside>

    {{-- Mobile off-canvas sidebar --}}
    <div data-admin-mobile-backdrop class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>
    <aside data-admin-mobile-sidebar class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col overflow-y-auto border-r border-(--color-border) bg-(--color-card) transition-transform duration-200 lg:hidden dark:border-white/10 dark:bg-(--color-card-dark)">
        <div class="flex shrink-0 items-center justify-between gap-2.5 border-b border-(--color-border) px-4 py-4 dark:border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary) text-sm font-bold text-white">A</span>
                <span>
                    <span class="block text-sm font-bold text-(--color-text) dark:text-white">ArtiWeb</span>
                    <span class="block text-[11px] font-medium text-(--color-text-secondary)">{{ __('admin.admin_panel') }}</span>
                </span>
            </a>
            <button type="button" data-admin-mobile-close aria-label="{{ __('admin.close_menu') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) hover:bg-black/5 dark:hover:bg-white/10">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>
        <nav class="flex flex-1 flex-col gap-1 overflow-y-auto px-3 py-4">
            @include('admin.partials.sidebar-nav')
        </nav>
        <div class="shrink-0 border-t border-(--color-border) p-3 dark:border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                    <x-icon name="log-out" class="h-[18px] w-[18px] shrink-0" />
                    {{ __('navbar.sign_out') }}
                </button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-(--color-border) bg-(--color-card)/90 px-4 py-3 backdrop-blur dark:border-white/10 dark:bg-(--color-card-dark)/90 sm:px-6">
            <button type="button" data-admin-mobile-toggle aria-label="{{ __('admin.open_menu') }}"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-(--color-text-secondary) hover:bg-black/5 lg:hidden dark:hover:bg-white/10">
                <x-icon name="menu" class="h-5 w-5" />
            </button>

            <h1 class="min-w-0 truncate text-base font-bold text-(--color-text) dark:text-white sm:text-lg">@yield('title', __('admin.dashboard_title'))</h1>

            <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
                <a href="{{ route('home') }}" target="_blank" class="hidden items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) sm:inline-flex dark:hover:bg-white/10 dark:hover:text-white">
                    <x-icon name="globe" class="h-4 w-4" />
                    {{ __('admin.view_site') }}
                </a>

                <x-language-switcher />

                <button type="button" data-theme-toggle aria-label="{{ __('navbar.toggle_theme') }}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
                    <x-icon name="sun" class="hidden h-[18px] w-[18px] dark:block" />
                    <x-icon name="moon" class="block h-[18px] w-[18px] dark:hidden" />
                </button>

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
                         class="absolute right-0 z-50 mt-2 hidden w-56 overflow-hidden rounded-xl border border-(--color-border) bg-(--color-card) py-1.5 shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">
                        <div class="px-4 py-2.5">
                            <div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div>
                            <div class="truncate text-xs text-(--color-text-secondary)">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="my-1 border-t border-(--color-border) dark:border-white/10"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-sm text-(--color-danger) transition-colors hover:bg-(--color-danger)/10">
                                <x-icon name="log-out" class="h-4 w-4" />
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-5 flex items-center gap-2 rounded-xl border border-(--color-primary)/20 bg-(--color-primary)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)" role="status">
                    <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 flex items-center gap-2 rounded-xl border border-(--color-danger)/20 bg-(--color-danger)/10 px-4 py-3 text-sm text-(--color-danger)" role="alert">
                    <x-icon name="alert-triangle" class="h-4 w-4 shrink-0" />
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-xl border border-(--color-danger)/20 bg-(--color-danger)/10 px-4 py-3 text-sm text-(--color-danger)">
                    <div class="flex items-center gap-2 font-semibold">
                        <x-icon name="alert-triangle" class="h-4 w-4 shrink-0" />
                        {{ __('common.please_fix_following') }}
                    </div>
                    <ul class="mt-1.5 list-disc pl-6 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
(function () {
    // ---- Desktop sidebar collapse ----
    var collapseBtn = document.querySelector('[data-sidebar-collapse-toggle]');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            var collapsed = document.documentElement.classList.toggle('admin-sidebar-collapsed');
            localStorage.setItem('admin-sidebar-collapsed', collapsed ? '1' : '0');
        });
    }

    // ---- Mobile off-canvas sidebar ----
    var mobileSidebar = document.querySelector('[data-admin-mobile-sidebar]');
    var mobileBackdrop = document.querySelector('[data-admin-mobile-backdrop]');
    var mobileToggle = document.querySelector('[data-admin-mobile-toggle]');
    var mobileClose = document.querySelector('[data-admin-mobile-close]');

    function openMobile() {
        mobileSidebar.classList.remove('-translate-x-full');
        mobileBackdrop.classList.remove('hidden');
    }
    function closeMobile() {
        mobileSidebar.classList.add('-translate-x-full');
        mobileBackdrop.classList.add('hidden');
    }

    if (mobileToggle) mobileToggle.addEventListener('click', openMobile);
    if (mobileClose) mobileClose.addEventListener('click', closeMobile);
    if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeMobile);

    // ---- Topbar user dropdown ----
    document.querySelectorAll('[data-dropdown]').forEach(function (wrapper) {
        var toggle = wrapper.querySelector('[data-dropdown-toggle]');
        var panel = wrapper.querySelector('[data-dropdown-panel]');
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = !panel.classList.contains('hidden');
            document.querySelectorAll('[data-dropdown-panel]').forEach(function (p) { p.classList.add('hidden'); });
            panel.classList.toggle('hidden', isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('[data-dropdown-panel]').forEach(function (p) { p.classList.add('hidden'); });
    });
})();
</script>

<x-receipt-modal />

@stack('scripts')
</body>
</html>

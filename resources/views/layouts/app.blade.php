<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ArtiWeb')</title>
    @include('partials.theme-init')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-(--color-bg-light) font-sans text-(--color-text) antialiased dark:bg-(--color-bg-dark) dark:text-[#ECECEC]">

<div class="flex min-h-screen flex-col">
    <x-navbar />

    <main class="auth-shell flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="relative w-full max-w-md">
            <div class="auth-card rounded-2xl border border-(--color-border) p-8 shadow-lift dark:border-white/10">
                <div class="text-center">
                    <h1 class="text-xl font-extrabold tracking-tight text-(--color-text) dark:text-white">@yield('heading', 'Welcome')</h1>
                    <p class="mt-1.5 text-sm text-(--color-text-secondary)">@yield('subtitle', '')</p>
                </div>

                @if(session('success'))
                    <div class="mt-5 flex items-center gap-2 rounded-xl border border-(--color-primary)/20 bg-(--color-primary)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)" role="status">
                        <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('status'))
                    <div class="mt-5 flex items-center gap-2 rounded-xl border border-(--color-primary)/20 bg-(--color-primary)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)" role="status">
                        <x-icon name="alert-circle" class="h-4 w-4 shrink-0" />
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-5 rounded-xl border border-(--color-danger)/20 bg-(--color-danger)/10 px-4 py-3 text-sm text-(--color-danger)">
                        <div class="flex items-center gap-2 font-semibold">
                            <x-icon name="alert-triangle" class="h-4 w-4 shrink-0" />
                            Please fix the following
                        </div>
                        <ul class="mt-1.5 list-disc pl-6 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <x-footer />
</div>

@stack('scripts')
</body>
</html>

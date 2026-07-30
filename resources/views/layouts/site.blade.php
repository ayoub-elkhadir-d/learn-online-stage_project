<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ArtiWeb Learning')</title>
    @include('partials.theme-init')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-(--color-bg-light) font-sans text-(--color-text) antialiased dark:bg-(--color-bg-dark) dark:text-[#ECECEC]">

<x-navbar />

@if(session('status'))
    <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 rounded-xl border border-(--color-primary)/20 bg-(--color-primary)/10 px-4 py-3 text-sm text-(--color-primary-dark) dark:text-(--color-accent)" role="status">
            <x-icon name="alert-circle" class="h-4 w-4 shrink-0" />
            {{ session('status') }}
        </div>
    </div>
@endif

<main>
    @yield('content')
</main>

@stack('scripts')
</body>
</html>

@props(['variant' => 'dropdown'])

@php
    $languages = [
        'en' => ['label' => 'English', 'short' => 'EN'],
        'fr' => ['label' => 'Français', 'short' => 'FR'],
        'ar' => ['label' => 'العربية', 'short' => 'AR'],
    ];
    $current = $languages[app()->getLocale()] ?? $languages['en'];
@endphp

@if($variant === 'inline')
    {{-- Mobile menu section --}}
    <div class="mt-3 border-t border-(--color-border) pt-3 dark:border-white/10">
        <div class="px-3 pb-1.5 text-[11px] font-semibold uppercase tracking-wide text-(--color-text-secondary)">
            {{ __('navbar.language') }}
        </div>
        @foreach($languages as $code => $lang)
            <form method="POST" action="{{ route('locale.switch', $code) }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-start text-sm font-medium transition-colors {{ app()->getLocale() === $code ? 'text-(--color-primary)' : 'text-(--color-text-secondary) hover:bg-black/5 dark:hover:bg-white/5' }}">
                    <span>{{ $lang['label'] }}</span>
                    @if(app()->getLocale() === $code)
                        <x-icon name="check" class="h-4 w-4 text-(--color-primary)" />
                    @endif
                </button>
            </form>
        @endforeach
    </div>
@else
    {{-- Desktop dropdown — reuses the same data-dropdown pattern already
         wired up by the navbar's / admin layout's own script. --}}
    <div class="relative" data-dropdown>
        <button type="button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="{{ __('navbar.language') }}"
                class="inline-flex h-9 items-center gap-1.5 rounded-lg px-2.5 text-sm font-medium text-(--color-text-secondary) transition-colors hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/10 dark:hover:text-white">
            <x-icon name="globe" class="h-4 w-4" />
            <span class="hidden sm:inline">{{ $current['short'] }}</span>
            <x-icon name="chevron-down" class="h-3.5 w-3.5" />
        </button>

        <div data-dropdown-panel role="menu" aria-label="{{ __('navbar.language') }}"
             class="absolute right-0 z-50 mt-2 hidden w-44 overflow-hidden rounded-xl border border-(--color-border) bg-(--color-card) py-1.5 shadow-lift dark:border-white/10 dark:bg-(--color-card-dark)">
            @foreach($languages as $code => $lang)
                <form method="POST" action="{{ route('locale.switch', $code) }}">
                    @csrf
                    <button type="submit" role="menuitem"
                            class="flex w-full items-center justify-between gap-2.5 px-4 py-2 text-start text-sm transition-colors hover:bg-black/5 dark:hover:bg-white/5 {{ app()->getLocale() === $code ? 'font-semibold text-(--color-primary)' : 'text-(--color-text) dark:text-white/90' }}">
                        <span>{{ $lang['label'] }}</span>
                        @if(app()->getLocale() === $code)
                            <x-icon name="check" class="h-3.5 w-3.5 shrink-0 text-(--color-primary)" />
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
@endif

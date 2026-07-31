@foreach($navItems as $item)
    <a href="{{ route($item['route']) }}"
       data-sidebar-link
       title="{{ $item['label'] }}"
       class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($item['match']) ? 'bg-(--color-primary)/10 text-(--color-primary)' : 'text-(--color-text-secondary) hover:bg-black/5 hover:text-(--color-text) dark:hover:bg-white/5 dark:hover:text-white' }}">
        <x-icon name="{{ $item['icon'] }}" class="h-[18px] w-[18px] shrink-0" />
        <span data-sidebar-label class="truncate">{{ $item['label'] }}</span>
    </a>
@endforeach

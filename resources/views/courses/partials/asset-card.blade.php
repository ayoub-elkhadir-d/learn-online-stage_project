<div class="lesson-card p-4 sm:p-5" data-asset-card>
    <div class="flex items-start gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-xs font-semibold text-white">
            {{ strtoupper(substr(optional($asset->user)->name ?? 'A', 0, 1)) }}
        </span>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                <span class="text-sm font-bold text-(--color-text) dark:text-white">{{ optional($asset->user)->name ?? 'Instructor' }}</span>
                <span class="text-xs text-(--color-text-secondary)">{{ $asset->created_at->diffForHumans() }}</span>
            </div>

            @if($asset->message)
                <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-(--color-text-secondary)">{{ $asset->message }}</p>
            @endif

            @if($asset->type === 'image' && $asset->file_path)
                <a href="{{ Storage::url($asset->file_path) }}" target="_blank" rel="noopener"
                   class="mt-3 block overflow-hidden rounded-xl border border-(--color-border)">
                    <img src="{{ Storage::url($asset->file_path) }}" alt="{{ $asset->original_filename ?? 'Shared image' }}" loading="lazy" class="max-h-72 w-full object-cover">
                </a>
            @elseif($asset->type === 'link' && $asset->external_url)
                <a href="{{ $asset->external_url }}" target="_blank" rel="noopener noreferrer"
                   class="mt-3 flex items-center gap-3 rounded-xl border border-(--color-border) p-3 transition-colors hover:bg-(--color-primary)/5">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                        <x-icon name="link" class="h-5 w-5" />
                    </span>
                    <span class="min-w-0 flex-1 truncate text-sm font-semibold text-(--color-primary)">{{ $asset->external_url }}</span>
                    <x-icon name="arrow-right" class="h-4 w-4 shrink-0 text-(--color-text-secondary)" />
                </a>
            @elseif($asset->file_path)
                <a href="{{ Storage::url($asset->file_path) }}" download
                   class="mt-3 flex items-center gap-3 rounded-xl border border-(--color-border) p-3 transition-colors hover:bg-(--color-primary)/5">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-(--color-primary)/10 text-(--color-primary)">
                        <x-icon name="{{ $asset->iconName() }}" class="h-5 w-5" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-(--color-text) dark:text-white">{{ $asset->original_filename ?? 'Download file' }}</span>
                        @if($asset->humanFileSize())
                            <span class="text-xs text-(--color-text-secondary)">{{ $asset->humanFileSize() }}</span>
                        @endif
                    </span>
                    <x-icon name="download" class="h-4 w-4 shrink-0 text-(--color-text-secondary)" />
                </a>
            @endif
        </div>
    </div>
</div>

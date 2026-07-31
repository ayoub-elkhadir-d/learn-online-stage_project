@php
    $isOwn = auth()->check() && $review->user_id === auth()->id();
@endphp
<div class="lesson-card p-4 sm:p-5" data-review-card>
    <div class="flex items-start gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-(--color-primary) text-xs font-semibold text-white">
            {{ strtoupper(substr($review->user->name, 0, 1)) }}
        </span>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                <span class="text-sm font-bold text-(--color-text) dark:text-white">
                    {{ $review->user->name }}
                    @if($isOwn)
                        <span class="ml-1.5 rounded-full bg-(--color-primary)/10 px-2 py-0.5 text-[10px] font-semibold text-(--color-primary)">You</span>
                    @endif
                </span>
                <span class="text-xs text-(--color-text-secondary)">{{ $review->created_at->format('d M Y') }}</span>
            </div>
            <div class="mt-1 flex items-center gap-0.5">
                @for ($i = 1; $i <= 5; $i++)
                    <x-icon name="star" class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'fill-current text-amber-400' : 'text-(--color-border) dark:text-white/15' }}" />
                @endfor
            </div>
            @if($review->comment)
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-(--color-text-secondary)">{{ $review->comment }}</p>
            @endif
        </div>
    </div>
</div>

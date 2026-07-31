{{-- Full "Assets" tab content — an instructor resource timeline, read-only
     for students. Rendered by CourseAssetController for the initial AJAX
     load; there's no write endpoint yet, so this never needs to re-render
     after a mutation the way the reviews panel does. --}}
<div class="space-y-4" data-assets-panel data-course-id="{{ $course->id }}">
    <div class="space-y-4" data-assets-cards>
        @forelse($assets as $asset)
            @include('courses.partials.asset-card', ['asset' => $asset])
        @empty
            <div class="lesson-card flex flex-col items-center px-6 py-16 text-center">
                <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                    <x-icon name="megaphone" class="h-7 w-7" />
                </span>
                <h3 class="text-sm font-bold text-(--color-text) dark:text-white">No learning resources have been shared yet</h3>
                <p class="mt-1 max-w-xs text-xs text-(--color-text-secondary)">When your instructor shares files, links, or announcements for this course, they'll show up here.</p>
            </div>
        @endforelse
    </div>

    <div data-assets-load-more-wrapper>
        @if($assets->hasMorePages())
            <button type="button" data-assets-load-more data-page="{{ $assets->currentPage() + 1 }}" class="btn-secondary w-full text-sm">
                Load more
            </button>
        @endif
    </div>
</div>

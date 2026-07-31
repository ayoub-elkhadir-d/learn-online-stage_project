{{-- Full "Ratings & Reviews" tab content — rendered by CourseReviewController
     for the initial AJAX load and after every store/destroy, so the client
     only ever does one "swap this HTML in" step to reflect the new state. --}}
@php
    $total = (int) $summary->total;
    $average = $total ? round((float) $summary->average, 1) : 0;
@endphp

<div class="space-y-6" data-reviews-panel data-course-id="{{ $course->id }}">
    {{-- Summary --}}
    <div class="lesson-card p-5 sm:p-6">
        <h2 class="mb-4 flex items-center gap-1.5 text-sm font-bold text-(--color-text) dark:text-white">
            <x-icon name="star" class="h-4 w-4 text-(--color-text-secondary)" />
            Ratings &amp; Reviews
        </h2>
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
            <div class="flex shrink-0 flex-col items-center gap-1.5 sm:w-40 sm:border-r sm:border-(--color-border) sm:pr-6">
                <span class="text-4xl font-extrabold leading-none text-(--color-text) dark:text-white">{{ number_format($average, 1) }}</span>
                <div class="flex items-center gap-0.5">
                    @for ($i = 1; $i <= 5; $i++)
                        <x-icon name="star" class="h-5 w-5 {{ $i <= round($average) ? 'fill-current text-amber-400' : 'text-(--color-border) dark:text-white/15' }}" />
                    @endfor
                </div>
                <span class="text-xs text-(--color-text-secondary)">{{ $total }} {{ Str::plural('rating', $total) }}</span>
            </div>
            <div class="flex-1 space-y-1.5">
                @for ($star = 5; $star >= 1; $star--)
                    @php
                        $count = (int) $summary->{"star{$star}"};
                        $pct = $total ? round($count / $total * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-2 text-xs">
                        <span class="flex w-6 shrink-0 items-center gap-1 font-medium text-(--color-text-secondary)">{{ $star }}<x-icon name="star" class="h-3 w-3 fill-current" /></span>
                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-(--color-border)">
                            <div class="h-full rounded-full bg-(--color-primary) transition-all duration-500 ease-out" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-9 shrink-0 text-right font-semibold text-(--color-text-secondary)">{{ $pct }}%</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Write / edit review --}}
    @auth
        @if($canReview)
            <div class="lesson-card p-5 sm:p-6">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-bold text-(--color-text) dark:text-white">{{ $myReview ? 'Your Review' : 'Write a Review' }}</h3>
                    @if($myReview)
                        <button type="button" data-review-delete class="inline-flex items-center gap-1.5 text-xs font-semibold text-(--color-danger) transition-colors hover:opacity-75">
                            <x-icon name="x" class="h-3.5 w-3.5" />
                            Delete
                        </button>
                    @endif
                </div>
                <div class="flex items-center gap-1" data-review-star-input>
                    @for ($star = 1; $star <= 5; $star++)
                        <button type="button" data-star-input="{{ $star }}" aria-label="Rate {{ $star }} star{{ $star > 1 ? 's' : '' }}"
                                class="review-star-btn {{ $myReview && $star <= $myReview->rating ? 'text-amber-400' : '' }}">
                            <x-icon name="star" class="h-7 w-7 {{ $myReview && $star <= $myReview->rating ? 'fill-current' : '' }}" />
                        </button>
                    @endfor
                </div>
                <textarea
                    data-review-text
                    rows="3"
                    maxlength="2000"
                    placeholder="Share your thoughts about this course..."
                    class="input-field mt-3 resize-y text-sm"
                >{{ $myReview->comment ?? '' }}</textarea>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <span class="text-xs text-(--color-danger)" data-review-error></span>
                    <button type="button" data-review-submit data-initial-rating="{{ $myReview->rating ?? 0 }}" class="btn-primary ml-auto shrink-0 text-sm">
                        <x-icon name="check" class="h-4 w-4" />
                        {{ $myReview ? 'Update Review' : 'Submit Review' }}
                    </button>
                </div>
            </div>
        @else
            <div class="lesson-card flex items-center gap-3 p-5 text-sm text-(--color-text-secondary) sm:p-6">
                <x-icon name="lock" class="h-4 w-4 shrink-0 text-(--color-primary)" />
                Purchase this course to leave a review.
            </div>
        @endif
    @endauth

    {{-- List --}}
    <div class="space-y-4" data-reviews-cards>
        @forelse($reviews as $review)
            @include('courses.partials.reviews-review-card', ['review' => $review])
        @empty
            <div class="lesson-card flex flex-col items-center px-6 py-14 text-center">
                <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-(--color-primary)/10 text-(--color-primary)">
                    <x-icon name="star" class="h-7 w-7" />
                </span>
                <h3 class="text-sm font-bold text-(--color-text) dark:text-white">No reviews yet</h3>
                <p class="mt-1 text-xs text-(--color-text-secondary)">Be the first to rate this course.</p>
            </div>
        @endforelse
    </div>

    <div data-reviews-load-more-wrapper>
        @if($reviews->hasMorePages())
            <button type="button" data-reviews-load-more data-page="{{ $reviews->currentPage() + 1 }}" class="btn-secondary w-full text-sm">
                Load more reviews
            </button>
        @endif
    </div>
</div>

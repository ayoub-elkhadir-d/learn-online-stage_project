{{-- Fragment returned by CourseReviewController::loadMore — appended to the
     existing list client-side rather than replacing the whole panel. --}}
@foreach($reviews as $review)
    @include('courses.partials.reviews-review-card', ['review' => $review])
@endforeach

@if($reviews->hasMorePages())
    <button type="button" data-reviews-load-more data-page="{{ $reviews->currentPage() + 1 }}" class="btn-secondary w-full text-sm">
        Load more reviews
    </button>
@endif

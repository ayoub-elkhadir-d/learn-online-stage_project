{{-- Fragment returned by CourseAssetController::loadMore — appended to the
     existing list client-side rather than replacing the whole panel. --}}
@foreach($assets as $asset)
    @include('courses.partials.asset-card', ['asset' => $asset])
@endforeach

@if($assets->hasMorePages())
    <button type="button" data-assets-load-more data-page="{{ $assets->currentPage() + 1 }}" class="btn-secondary w-full text-sm">
        Load more
    </button>
@endif

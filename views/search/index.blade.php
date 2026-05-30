<div class="re-search-wrapper">
    @do_action('property_search_before')
    <form class="re-search-form" method="get" id="formPropertySearch">
        <div class="re-search-inner">
            @include('real-estate::search/location-control')
            @include('real-estate::search/categories-control')
            @include('real-estate::search/price-control', ['prices' => $priceSell])
            @include('real-estate::search/area-control')
            @include('real-estate::search/collection-control')
            @do_action('property_search_controls')
        </div>
        <div class="re-search-more">
            @include('real-estate::search/more/bedroom-control', ['bedrooms' => $bedrooms, 'filters' => $filters ?? []])
            @include('real-estate::search/more/bathroom-control', ['bathrooms' => $bathrooms, 'filters' => $filters ?? []])
            @include('real-estate::search/more/direction-control', ['directions' => $directions, 'filters' => $filters ?? []])
        </div>
        <div class="re-search-actions">
            <button type="submit" class="btn btn-search">
                <i class="fa-light fa-magnifying-glass"></i> Tìm kiếm
            </button>
        </div>
    </form>
    @do_action('property_search_after')
</div>


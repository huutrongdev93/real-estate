<div class="real-estate-detail property-detail">
    @do_action('property_detail_before', $object)
    <div class="container">
        <div class="row">
            <div class="col-md-8 property-main">
                @do_action('property_detail_top', $object)
                @do_action('property_detail_main', $object)
            </div>
            <div class="col-md-4 sidebar">
                <div class="list-widget">
                    @do_action('property_detail_sidebar', $object)
                </div>
            </div>
        </div>
    </div>
    @do_action('property_detail_after', $object)
</div>


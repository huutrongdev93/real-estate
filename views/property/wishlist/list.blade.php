<div class="property-list-item list-item-property-wishlist" id="js_property_list__item">
    @foreach ($objects as $key => $item)
        {!! view('real-estate::property/loop/item', ['item' => $item]) !!}
    @endforeach
</div>


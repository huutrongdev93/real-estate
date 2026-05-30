<div class="property-list-item" id="js_property_list__item">
    @foreach ($objects as $key => $item)
        {!! view('real-estate::property/loop/item', ['item' => $item]) !!}
    @endforeach
</div>
<div class="text-left box-pagination">
    {!! $pagination->frontend() !!}
</div>


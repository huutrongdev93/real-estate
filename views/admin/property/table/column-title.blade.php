<h3 style="max-width:290px; line-height:20px; margin-bottom:5px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-overflow:ellipsis;">
    {!! $item->name !!}
</h3>
<div class="d-flex gap-2">
    <span class="label-3">ID {{ $item->id }}</span>
    <a class="label-3" href="{!! Url::permalink($item->slug) !!}" target="_blank" data-toggle="tooltip" data-placement="top" title="Xem"><i class="fa fa-eye"></i></a>
    <div class="column-collection collection-wrapper">
        @foreach ($collections as $collectionKey => $collection)
            @if(isset($item->{$collectionKey}))
                <span
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-title="{!! $collection['name'] !!}"
                        class="{{ (\SkillDo\Support\Auth::hasCap('property_edit')) ? 'js_property_btn_collection' : '' }} label label-{!! $collectionKey !!} {{ ($item->{$collectionKey} == 1) ? 'active' : '' }}"
                        data-id="{{ $item->id }}"
                        data-collection="{{ $collectionKey }}">{!! $collection['name'] !!}</span>
            @endif
        @endforeach
    </div>
</div>


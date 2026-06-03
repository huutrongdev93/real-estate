<div class="js_property_style_15_data" data-id="{{$id}}" data-tab-id="{{$activeKey}}" data-options="{!! htmlentities(json_encode($options)) !!}">
    <div class="property-s15-header-wrapper">
        <div class="property-s15-header">
            <div class="element-builder-widget" data-container-id="{!! 'property_style_15_heading_'.$id !!}">
                {!! $widget->widgetContainer($widget->options->container['property_style_15_heading_'.$id] ?? []) !!}
            </div>
        </div>
        <div class="property-s15-categories">
            <ul>
                @foreach($tabs as $index => $tab)
                    <li class="item">
                        <a href="#" data-tab="{!! $index !!}" class="tab-button {!! $index === $activeKey ? 'active' : '' !!}">{!! $tab['title'] ?? 'Tab '.($loop->index + 1) !!}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="property-s15-content">
        <div class="box-content property-slider-horizontal" style="position: relative">
            <div class="wg-loading" style="min-height: 350px"></div>
            <div class="properties-wrapper"></div>
        </div>
    </div>
</div>

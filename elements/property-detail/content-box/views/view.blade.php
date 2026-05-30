<div class="section-panel element-box box">
    <p class="heading element-heading">{!! $widget->options->title ?? '' !!}</p>
    <div class="element-box-content element-builder-widget" data-container-id="{!! 'content-'.$widget->id !!}">
        {!! $widget->widgetContainer($widget->options->container['content-'.$widget->id] ?? []) !!}
    </div>
</div>
<div class="item-property item-property-vertical re-card">
    @do_action('property_object_vertical_before', $item)
    <div class="img">
        @do_action('property_object_vertical_before_image', $item)
        <a href="{!! \SkillDo\Cms\Support\Url::permalink($item->slug) !!}" class="img">
            @do_action('property_object_vertical_image', $item)
        </a>
        @do_action('property_object_vertical_after_image', $item)
    </div>
    <div class="info">
        <a href="{!! \SkillDo\Cms\Support\Url::permalink($item->slug) !!}">
            @do_action('property_object_vertical_info', $item)
        </a>
    </div>
    @do_action('property_object_vertical_after', $item)
</div>


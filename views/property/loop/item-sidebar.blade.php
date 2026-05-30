<div class="item-property-sidebar">
    <div class="img">
        <a href="{{ \SkillDo\Cms\Support\Url::permalink($item->slug) }}">
            {!! \SkillDo\Cms\Support\Image::source($item->image, $item->name)->html() !!}
        </a>
    </div>
    <div class="info">
        <p class="heading"><a href="{{ \SkillDo\Cms\Support\Url::permalink($item->slug) }}">{{ $item->name }}</a></p>
        <div class="info-bonus">
            <p class="price">{!! \RealEstate\Supports\PropertyHelper::getPriceFullAuth($item->price, $item->unit) !!}</p>
            <p class="area">{!! (!empty($item->area)) ? \RealEstate\Supports\RealEstateHelper::convertStringAuth($item->area) : '--' !!}m²</p>
        </div>
    </div>
</div>


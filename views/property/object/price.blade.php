<div class="property-price">
    <h3 class="price">{!! PropertyHelper::getPriceFull($item->price, $item->unit) !!}</h3>
    <div class="area">{!! (!empty($item->area)) ? \RealEstate\Supports\RealEstateHelper::convertStringAuth($item->area) : '--' !!}m²</div>
</div>


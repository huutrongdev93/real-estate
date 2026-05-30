<div class="property-price-group">
    <p class="price">{!! PropertyHelper::getPriceFullAuth($item->price, $item->unit) !!}</p>
    <span class="dot"></span>
    <div class="area">{!! (!empty($item->area)) ? \RealEstate\Supports\RealEstateHelper::convertStringAuth($item->area) : '--' !!}m²</div>
    <span class="dot"></span>
    <span>
        {!! (!empty($item->bedroom) && $item->property_type != 3) ? $item->bedroom . '' : '--' !!}
        <i class="fa-light fa-bed"></i>
    </span>
    <span class="dot"></span>
    <span>
        {!! (!empty($item->bathroom) && $item->property_type != 3) ? $item->bathroom . '' : '--' !!}
        <i class="fa-light fa-bath"></i>
    </span>
    <span class="dot"></span>
    <p class="address">{!! $item->address !!}</p>
</div>


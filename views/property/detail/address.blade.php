<div class="d-flex gap-2 align-items-center mb-3">
    <div class="property-address-icon"><i class="fal fa-map-marker-alt"></i></div>
    <div>
        <p class="property-address-long">{!! \RealEstate\Supports\RealEstateHelper::getLocation($object) !!}</p>
        <p class="property-address mb-0">{!! \RealEstate\Supports\RealEstateHelper::getShortLocation($object) !!}</p>
    </div>
</div>




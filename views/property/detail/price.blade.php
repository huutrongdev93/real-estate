<div class="d-flex align-items-center justify-content-between property-price-box">
    <div class="property-price">
        <span class="price">{!! PropertyHelper::getPriceFull($object->price, $object->unit) !!}</span>
        <span class="dot"></span>
        <span class="area">{!! $object->area !!} m²</span>
    </div>
    <div class="property-price-action-button">
        <a class="btn facebook" href="javascript:;" onclick="window.open('http://www.facebook.com/sharer.php?u={!! \SkillDo\Cms\Support\Url::current() !!}', 'Chia sẻ sản phẩm này cho bạn bè', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,width=600,height=455')"><i class="fal fa-share-nodes"></i></a>
        <button class="btn js_handle_property_report" type="button" data-id="{{$object->id}}"><i class="fal fa-triangle-exclamation"></i></button>
        <button class="btn js_handle_property_wishlist {{$likeStatus}}" type="button" data-id="{{$object->id}}"><i class="fa-light fa-heart"></i></button>
    </div>
</div>


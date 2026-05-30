<div class="item-footer">
    <div class="broker-information">
        <div class="avatar">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M24 4C12.954 4 4 12.954 4 24C4 35.046 12.954 44 24 44C35.046 44 44 35.046 44 24C44 12.954 35.046 4 24 4ZM17 19C17 18.0807 17.1811 17.1705 17.5328 16.3212C17.8846 15.4719 18.4002 14.7003 19.0503 14.0503C19.7003 13.4002 20.4719 12.8846 21.3212 12.5328C22.1705 12.1811 23.0807 12 24 12C24.9193 12 25.8295 12.1811 26.6788 12.5328C27.5281 12.8846 28.2997 13.4002 28.9497 14.0503C29.5998 14.7003 30.1154 15.4719 30.4672 16.3212C30.8189 17.1705 31 18.0807 31 19C31 20.8565 30.2625 22.637 28.9497 23.9497C27.637 25.2625 25.8565 26 24 26C22.1435 26 20.363 25.2625 19.0503 23.9497C17.7375 22.637 17 20.8565 17 19ZM36.516 33.968C35.0184 35.8507 33.115 37.371 30.9479 38.4154C28.7807 39.4599 26.4057 40.0015 24 40C21.5943 40.0015 19.2193 39.4599 17.0521 38.4154C14.885 37.371 12.9816 35.8507 11.484 33.968C14.726 31.642 19.15 30 24 30C28.85 30 33.274 31.642 36.516 33.968Z" fill="currentColor"/>
            </svg>
        </div>
        @if(!empty($broker['name']))
        <div class="information">
            <p class="name">{{$broker['name']}}</p>
            <p class="time">{{\RealEstate\Supports\RealEstateHelper::timeElapsed($item->created)}}</p>
        </div>
        @else
        <div class="information">
            <p class="time">{{\RealEstate\Supports\RealEstateHelper::timeElapsed($item->created)}}</p>
        </div>
        @endif
    </div>
    <div class="group-right">
        @if(!empty($broker['phone']))
        <div class="phone-number-button">
            <i class="fa-solid fa-phone"></i>
            <button class="js_phone_display">{{substr($broker['phone'], 0, -3) . '***'}} <a href="#" class="js_show_phone">Hiện số</a></button>
            <a href="tel:{{$broker['phone']}}" class="phone-number js_full_number" style="display: none;">{{$broker['phone']}}</a>
            <button class="js_copy_button" style="display: none;">Sao chép</button>
        </div>
        @endif
        <button class="btn btn-wishlist {{$class}} js_handle_property_wishlist" type="button" data-id="{{$item->id}}"><i class="fa-light fa-heart"></i></button>
    </div>
</div>


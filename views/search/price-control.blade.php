<div class="re-search-column select-control price-control">
    <input class="price-input" type="hidden" name="price" value="">
    <div class="select-control-label">
        <div class="dropbox-label" readonly="readonly">Mức giá <i class="fa-sharp fa-light fa-angle-down"></i></div>
        <div class="custom-value price-label" readonly="readonly">Tất cả</div>
    </div>
    <div class="select-context select-context-left advance-select-options hiding">
        <div class="mobile-control-label">
            <div class="mobile-control-text">Mức giá</div>
            <span class="mobile-control-close js_control_close"><i class="fa-thin fa-xmark"></i></span>
        </div>
        <div id="divPriceOptions" class="custom-scroll">
            <ul class="advance-options">
                <li data-value="" class="advance-options">Tất cả mức giá</li>
                @foreach ($prices as $key => $name)
                    <li data-value="{!! $key !!}" class="advance-options">{!! $name !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
<div class="vertical-line">&nbsp;</div>


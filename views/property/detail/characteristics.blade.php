<div class="section-panel">
    <p class="heading">Đặc điểm bất động sản</p>
    <div class="section-panel-body">
        <div class="property-characteristics">
            @if(hasItems($specifications))
                @foreach($specifications as $item)
                    <div class="specs-content-item">
                        <div class="specs-item-title">
                            @if(!empty($item['icon']))
                            <span class="specs-content-icon"><img src="{!! $item['icon'] !!}" alt="{{ $item['label'] }}"/></span>
                            @endif
                            <span class="specs-content-title">{{ $item['label'] }}</span>
                        </div>
                        <span class="specs-content-value">{{ $item['value'] }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>


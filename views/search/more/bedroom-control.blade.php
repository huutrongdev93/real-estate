<div class="tag-control bedroom-control" id="divBedroomOptions">
    <p class="tag-title">Số phòng ngủ</p>
    <div class="tag-options">
        <div data-value="" class="tag-item">Tất cả</div>
        @foreach ($bedrooms as $key => $name)
            @php
                $name  = str_replace('Phòng ngủ', '', $name);
                $class = (isset($filters['bedroom']) && $filters['bedroom'] == $key) ? 'tag-item--active' : '';
            @endphp
            <div data-value="{!! $key !!}" class="tag-item {!! $class !!}">{!! $name !!}</div>
        @endforeach
    </div>
</div>


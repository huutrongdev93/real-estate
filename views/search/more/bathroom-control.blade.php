<div class="tag-control bathroom-control" id="divBathroomOptions">
    <p class="tag-title">Số phòng tắm</p>
    <div class="tag-options">
        <div data-value="" class="tag-item">Tất cả</div>
        @foreach ($bathrooms as $key => $name)
            @php
                $name  = str_replace('Phòng tắm', '', $name);
                $class = (isset($filters['bathroom']) && $filters['bathroom'] == $key) ? 'tag-item--active' : '';
            @endphp
            <div data-value="{!! $key !!}" class="tag-item {!! $class !!}">{!! $name !!}</div>
        @endforeach
    </div>
</div>


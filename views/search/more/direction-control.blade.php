<div class="tag-control direction-control" id="divDirectionOptions">
    <p class="tag-title">Hướng nhà</p>
    <div class="tag-options">
        <div data-value="" class="tag-item">Tất cả</div>
        @foreach ($directions as $key => $name)
            @php
                $class = (isset($filters['direction']) && $filters['direction'] == $key) ? 'tag-item--active' : '';
            @endphp
            <div data-value="{!! $key !!}" class="tag-item {!! $class !!}">{!! $name !!}</div>
        @endforeach
    </div>
</div>


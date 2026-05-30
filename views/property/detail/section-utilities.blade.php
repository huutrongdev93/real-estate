<div class="section-panel">
    <p class="heading">Tiện ích</p>
    <div class="tab-feature">
        <ul class="cols-3">
            @foreach ($utilities as $feature)
                <li><span>{!! $feature->name !!}</span></li>
            @endforeach
        </ul>
    </div>
</div>


<div class="section-panel">
    <p class="heading">Nội thất</p>
    <div class="tab-feature">
        <ul class="cols-3">
            @foreach ($furniture as $feature)
                <li><span>{!! $feature->name !!}</span></li>
            @endforeach
        </ul>
    </div>
</div>


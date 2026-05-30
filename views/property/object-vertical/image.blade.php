<div class="img-grid">
    <div class="img-item img-main">
        {!! Image::large($item->image, $item->name)->html() !!}
    </div>
    @foreach($galleries as $key => $gallery)
        @break($key == 3)
        <div class="img-item {!! !\SkillDo\Support\Auth::check() ? 'blur' : '' !!}">
            {!! Image::large($gallery->value, $gallery->title)->html() !!}
        </div>
    @endforeach
</div>


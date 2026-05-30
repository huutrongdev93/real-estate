<div class="property-detail-slider-horizontal section-panel">
    <div class="box-image-featured">
        <span class="slide-counter">1 / {{ count($galleries) + 1 }}</span>
        <div id="property-main-slider">
            <div>
                <div class="img">
                    <a href="{!! Image::source($object->image)->link() !!}" data-fancybox="property-thumb">
                        {!! Image::source($object->image, '')->attributes(['class' => 'property-image-feature'])->html() !!}
                    </a>
                </div>
            </div>
            @if(hasItems($galleries))
                @foreach ($galleries as $image)
                    <div>
                        @if($image->type == 'youtube')
                            <iframe style="width:100%; height: 400px" src="https://www.youtube.com/embed/{!! Url::getYoutubeID($image->value) !!}" frameborder="0" allowfullscreen></iframe>
                        @else
                            <div class="img">
                                <a href="{!! Image::source($image->value)->link() !!}" data-fancybox="property-thumb">
                                    {!! Image::source($image->value)->html() !!}
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="box-content" style="margin-left: -10px; margin-right: -10px;">
        <div class="arrow_box" id="property-nav-slider-arrow">
            <div class="prev arrow"><i class="fal fa-chevron-left"></i></div>
            <div class="next arrow"><i class="fal fa-chevron-right"></i></div>
        </div>
        <div id="property-nav-slider" class="property-thumb-horizontal">
            <div>
                <div class="img">{!! \SkillDo\Cms\Support\Image::source($object->image)->html() !!}</div>
            </div>
            @foreach ($galleries as $image)
                <div>
                    <div class="img">{!! \SkillDo\Cms\Support\Image::source($image->value)->html() !!}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .property-detail-slider-horizontal { position: relative; }
    .property-detail-slider-horizontal .property-tag {
        position: absolute; left: -8px; top: 16px; z-index: 2;
        border-radius: 2px 2px 2px 0; padding: 4px 8px;
        font-size: 16px; font-weight: 700; line-height: 18px;
        text-transform: uppercase; background: #BE202E; color: #fff;
    }
    .property-detail-slider-horizontal .property-tag::after {
        content: ''; position: absolute; left: 1px; top: 99%;
        width: 8px; height: 7px; background: #BE202E;
        clip-path: polygon(100% 0, 0 0, 100% 100%);
    }
    .box-image-featured { text-align: center; margin-bottom: 10px; position: relative; }
    .box-image-featured .img { position: relative; height: 0; width: 100%; padding-top: 56.82%; border-radius: 8px; overflow: hidden; }
    .box-image-featured img { position: absolute; width: 100%; height: 100%; top: 0; left: 0; object-fit: cover; }
    .box-image-featured .slide-counter {
        position: absolute; right: 13px; bottom: 23px;
        font-size: 20px; font-weight: 700; padding: 5px 10px;
        border-radius: 4px; background: rgba(37,37,37,0.50); color: #fff; z-index: 2;
    }
    #property-nav-slider { display: flex; }
    #property-nav-slider > div { flex: 1; }
    #property-nav-slider .slick-slide { width: 100%; margin: 2px 10px 10px; }
    #property-nav-slider .slick-slide .img { position: relative; width: 100%; height: 0; padding-top: 80%; }
    #property-nav-slider .img img { position: absolute; left: 0; top: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 8px; cursor: pointer; }
    #property-nav-slider .slick-current img { border: 1px solid var(--theme-color); }
</style>

<script type="text/javascript">
    let totalSlides = {{ count($galleries) + 1 }};
    $(function()
    {
        let mainSlider = $('#property-main-slider');

        mainSlider.slick({
            infinite: false,
            dots: false,
            draggable: true,
            autoplay: true,
            autoplaySpeed: 5000,
            speed: 700,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            asNavFor: '#property-nav-slider'
        });

        let navSlider = $('#property-nav-slider');

        navSlider.slick({
            infinite: false,
            dots: false,
            autoplay: true,
            autoplaySpeed: 5000,
            speed: 700,
            arrows: false,
            slidesToShow: 7,
            slidesToScroll: 1,
            asNavFor: '#property-main-slider',
            centerMode: false,
            focusOnSelect: true,
            draggable: true,
            responsive: [{
                breakpoint: 678,
                settings: {
                    slidesToShow: 3
                }
            }]
        });

        $('#property-nav-slider-arrow .next').click(function() { navSlider.slick('slickNext'); return false; });

        $('#property-nav-slider-arrow .prev').click(function() { navSlider.slick('slickPrev'); return false; });

        mainSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
            $('.box-image-featured .slide-counter').text((nextSlide + 1) + ' / ' + totalSlides);
        });
    });
</script>


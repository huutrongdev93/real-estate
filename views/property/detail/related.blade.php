<div class="section-panel">
    <div class="header-box">
        <p class="heading">Bất động sản dành cho bạn</p>
    </div>
    <div class="box-content" id="js_property_related_box">
        <div class="arrow_box" id="widget_related_property_btn">
            <div class="prev arrow"><i class="fal fa-chevron-left"></i></div>
            <div class="next arrow"><i class="fal fa-chevron-right"></i></div>
        </div>
        <div class="swiper">
            <div class="swiper-wrapper">
                @foreach ($related as $key => $item)
                    <div class="swiper-slide p-2">
                        {!! view('real-estate::property/loop/item', ['item' => $item]) !!}
                    </div>
                @endforeach
            </div>
        </div>
        <script defer>
            $(document).ready(function()
            {
                let productList    = '#js_property_related_box .swiper';

                let productBtnNext = $('#widget_related_property_btn .next');

                let productBtnPrev = $('#widget_related_property_btn .prev');

                function shouldBeEnabled(carousel, numberShow)
                {
                    const slidesCount = carousel.find('.swiper-slide').length;

                    return slidesCount < numberShow ? {loop: false} : {loop: true};
                }
                let config = {
                    ...shouldBeEnabled($(productList), 3), speed: 500, slidesPerView: 3, spaceBetween: 0,
                    breakpoints: {
                        0: {...shouldBeEnabled($(productList), 1), slidesPerView: 1},
                        768: {...shouldBeEnabled($(productList), 3), slidesPerView: 3},
                        1200: {...shouldBeEnabled($(productList), 3), slidesPerView: 3},
                    },
                };

                let swiper = new Swiper(productList, config);

                productBtnNext.click(function() { swiper.slideNext(); });

                productBtnPrev.click(function() { swiper.slidePrev(); });
            });
        </script>
    </div>
</div>


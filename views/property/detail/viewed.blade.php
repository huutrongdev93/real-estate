<div class="section-panel">
    <div class="header-box">
        <p class="heading">Tin đăng đã xem</p>
    </div>
    <div class="box-content" id="js_property_viewed_box">
        <div class="arrow_box" id="widget_viewed_property_btn">
            <div class="prev arrow"><i class="fal fa-chevron-left"></i></div>
            <div class="next arrow"><i class="fal fa-chevron-right"></i></div>
        </div>
        <div class="swiper">
            <div class="swiper-wrapper">
                @foreach ($viewed as $key => $item)
                    <div class="swiper-slide p-2">
                        {!! view('real-estate::property/loop/item', ['item' => $item]) !!}
                    </div>
                @endforeach
            </div>
        </div>
        <script defer>
            $(document).ready(function() {
                let productList    = '#js_property_viewed_box .swiper';
                let productBtnNext = $('#widget_viewed_property_btn .next');
                let productBtnPrev = $('#widget_viewed_property_btn .prev');
                function shouldBeEnabled(carousel, numberShow) {
                    const slidesCount = carousel.find('.swiper-slide').length;
                    // WD-45: PHẢI là <=. Số slide BẰNG số cột thì không có gì để chạy vòng; bật loop khiến
                    // Swiper nhân bản slide rồi đặt khung nhìn vào một bản clone -> thứ tự hiện ra bị xoay.
                    return slidesCount <= numberShow ? {loop: false} : {loop: true};
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


class PropertyElementStyle15
{
    constructor(scope, $)
    {
        this.scope = scope;

        this.container = this.scope.find('.js_property_style_15_data');

        if (!this.container.length) return;

        this.options = this.container.data('options');

        try
        {
            if (typeof this.options === 'string')
            {
                this.options = JSON.parse(this.options);
            }
        }
        catch (e)
        {
            console.error('Error parsing options', e);
            return;
        }

        this.id = this.container.data('id');

        this.tabId = this.container.data('tab-id');

        this.dataItem = [];

        this.swiperInstance = null;

        this.observer = null;

        this.isLoaded = false;

        const self = this;

        this.scope.find('.tab-button').on('click', function ()
        {
            self.scope.find('.tab-button').removeClass('active');

            $(this).addClass('active');

            const tabId = $(this).data('tab');

            self.loadData(tabId);

            return false;
        });

        this.renderBox();

        this.initIntersectionObserver();
    }

    initIntersectionObserver()
    {
        if ('IntersectionObserver' in window)
        {
            const observerOptions = {
                root: null,
                rootMargin: '0px 0px 200px 0px',
                threshold: 0
            };

            this.observer = new IntersectionObserver((entries) =>
            {
                entries.forEach(entry =>
                {
                    if (entry.isIntersecting && !this.isLoaded)
                    {
                        this.isLoaded = true;
                        this.loadData();
                        this.observer.disconnect();
                    }
                });
            }, observerOptions);

            this.observer.observe(this.container[0]);
        }
        else
        {
            this.loadData();
        }
    }

    shouldBeEnabled($carousel, numberShow)
    {
        const slidesCount = $carousel.find('.swiper-slide').length;
        if (slidesCount < numberShow) {
            return { loop: false };
        }
        return { loop: true };
    }

    renderSlider()
    {
        let propertyList = this.scope.find('.swiper');
        let btnNext = this.scope.find('.next');
        let btnPrev = this.scope.find('.prev');

        let gutter = parseInt(getComputedStyle(document.body).getPropertyValue('--bs-gutter-x')) || 30;

        let config = {
            ...this.shouldBeEnabled(propertyList, parseInt(this.options.desktopNumberShow)),
            autoplay: {
                delay: parseInt(this.options.display.time) * 1000,
                disableOnInteraction: false
            },
            speed: 500,
            slidesPerView: parseInt(this.options.desktopNumberShow),
            spaceBetween: gutter,
            breakpoints: {
                0: {
                    ...this.shouldBeEnabled(propertyList, parseInt(this.options.mobileNumberShow)),
                    slidesPerView: parseInt(this.options.mobileNumberShow)
                },
                768: {
                    ...this.shouldBeEnabled(propertyList, parseInt(this.options.tabletNumberShow)),
                    slidesPerView: parseInt(this.options.tabletNumberShow)
                },
                1000: {
                    ...this.shouldBeEnabled(propertyList, parseInt(this.options.desktopNumberShow)),
                    slidesPerView: parseInt(this.options.desktopNumberShow)
                },
            },
        };

        if (propertyList.length)
        {
            this.swiperInstance = new Swiper(propertyList[0], config);
        }

        btnNext.on('click', () =>
        {
            if (this.swiperInstance) this.swiperInstance.slideNext();
        });
        btnPrev.on('click', () =>
        {
            if (this.swiperInstance) this.swiperInstance.slidePrev();
        });
    }

    renderBox()
    {
        let htmlBox = '';

        this.options.display.type = parseInt(this.options.display.type);

        if (this.options.display.type === 0)
        {
            htmlBox = `
                <div class="arrow_box">
                    <div class="prev arrow"><i class="fal fa-chevron-left"></i></div>
                    <div class="next arrow"><i class="fal fa-chevron-right"></i></div>
                </div>
                <div class="swiper"><div class="swiper-wrapper list-property"></div></div>`;
        }

        if (this.options.display.type === 1)
        {
            htmlBox = '<div class="list-property row"></div>';
        }

        this.scope.find('.properties-wrapper').html(htmlBox);

        this.scope.find('.wg-loading').html(this.renderLoading());
    }

    renderLoadingItem()
    {
        return '<div class="product--item-load">\n' +
            '    <div class="picture"></div>\n' +
            '    <div class="row">\n' +
            '        <div class="col-xs-6 col-sm-6 col-md-6 big"></div>\n' +
            '        <div class="col-xs-4 col-sm-4 col-md-4 empty big"></div>\n' +
            '        <div class="col-xs-2 col-sm-2 col-md-2 big"></div>\n' +
            '        <div class="col-xs-4 col-sm-4 col-md-4"></div>\n' +
            '        <div class="col-xs-8 col-sm-8 col-md-8 empty"></div>\n' +
            '        <div class="col-xs-6 col-sm-6 col-md-6"></div>\n' +
            '        <div class="col-xs-6 col-sm-6 col-md-6 empty"></div>\n' +
            '        <div class="col-xs-12 col-sm-12 col-md-12"></div>\n' +
            '    </div>\n' +
            '</div>';
    }

    renderLoading()
    {
        let htmlString = '<div class="row">';

        let loadingItem = this.renderLoadingItem();

        let mobileNumberShow = (this.options.mobileNumberShow === 5) ? 15 : (12 / this.options.mobileNumberShow);

        let tabletNumberShow = (this.options.tabletNumberShow === 5) ? 15 : (12 / this.options.tabletNumberShow);

        let numberShow = (this.options.desktopNumberShow === 5) ? 15 : (12 / this.options.desktopNumberShow);

        let hiddenMobile = 'd-none d-sm-none ';

        let hiddenMobileNumber = 0;

        let hiddenTablet = 'd-md-none ';

        let hiddenTabletNumber = 0;

        for (let i = 1; i <= this.options.desktopNumberShow; i++)
        {
            if ((this.options.desktopNumberShow - this.options.mobileNumberShow) === hiddenMobileNumber)
            {
                hiddenMobile = '';
            }
            else
            {
                hiddenMobileNumber++;
            }

            if ((this.options.desktopNumberShow - this.options.tabletNumberShow) === hiddenTabletNumber)
            {
                hiddenTablet = 'd-md-block ';
            }
            else
            {
                hiddenTabletNumber++;
            }

            htmlString += `<div class="${hiddenMobile}${hiddenTablet}d-lg-block col-xs-${mobileNumberShow} col-sm-${tabletNumberShow} col-md-${tabletNumberShow} col-lg-${numberShow}">${loadingItem}</div>`;
        }

        htmlString += '</div>';

        return htmlString;
    }

    renderItem(items)
    {
        let htmlString = '';

        let itemCount = items.length;

        if (this.options.display.type === 1)
        {
            let mobileNumberShow = (this.options.mobileNumberShow === 5) ? 15 : (12 / this.options.mobileNumberShow);

            let tabletNumberShow = (this.options.tabletNumberShow === 5) ? 15 : (12 / this.options.tabletNumberShow);

            let desktopNumberShow = (this.options.desktopNumberShow === 5) ? 15 : (12 / this.options.desktopNumberShow);

            let classColumn = `<div class="col-xs-${mobileNumberShow} col-sm-${tabletNumberShow} col-md-${desktopNumberShow} col-lg-${desktopNumberShow}">`;

            for (let i = 0; i < itemCount; i++)
            {
                htmlString += classColumn + items[i] + '</div>';
            }
        }

        if (this.options.display.type === 0)
        {
            let displayRows = this.options.display.rows;

            let classColumn = `<div class="swiper-slide">`;

            for (let i = 0; i < itemCount; i++)
            {
                if (i % displayRows === 0) htmlString += classColumn;

                htmlString += items[i];

                if ((i + 1) % displayRows === 0 || i === itemCount - 1) htmlString += '</div>';
            }
        }

        return htmlString;
    }

    loadData(tabId)
    {
        if (typeof tabId === 'undefined')
        {
            tabId = this.tabId;
        }

        let self = this;

        let propertyList = this.scope.find('.list-property');

        let propertyLink = this.scope.find('a.more-link');

        let loading = this.scope.find('.wg-loading');

        this.options.display.type = parseInt(this.options.display.type);

        propertyList.html('');

        loading.show();

        if (tabId in this.dataItem)
        {
            propertyList.html(this.renderItem(this.dataItem[tabId].items));

            propertyLink.attr('href', this.dataItem[tabId].url);

            loading.hide();

            if (this.options.display.type === 0)
            {
                this.renderSlider();
            }

            return false;
        }

        let data = {
            action: 'PropertyElementStyle15::loadProperty',
            id: this.id,
            tabId: tabId,
            options: this.options,
        };

        request.post(ajax, data).then(function (response)
        {
            loading.hide();

            if (response.status === 'success')
            {
                propertyList.html(self.renderItem(response.data.items));

                propertyLink.attr('href', response.data.slug);

                self.dataItem[tabId] = {
                    items: response.data.items,
                    url: response.data.slug,
                };

                if (self.options.display.type === 0)
                {
                    self.renderSlider();
                }
            }
        }).catch(err => {
            console.error('Lỗi Ajax Widget:', err);
            loading.hide();
        });
    }

    destroy()
    {
        if (this.swiperInstance)
        {
            this.swiperInstance.destroy(true, true);
            this.swiperInstance = null;
        }

        if (this.observer)
        {
            this.observer.disconnect();
            this.observer = null;
        }

        this.scope.find('.next').off('click');
        this.scope.find('.prev').off('click');
        this.scope.find('.tab-button').off('click');
    }
}

$(window).on('elementor/frontend/init', function ()
{
    elementorFrontend.hooks.addAction(
        'frontend/ready/PropertyElementStyle15.default',
        function (scope, $)
        {
            const instance = new PropertyElementStyle15(scope, $);

            scope.data('onDestroy', function () {
                instance.destroy();
            });
        }
    );
});

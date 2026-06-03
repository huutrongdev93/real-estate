<div class="re-search-bar property-search-bar" data-search-filter="{!! htmlentities(json_encode($filters)) !!}">
    <div class="re-search-bar-inside re-search-keyword">
        <div class="re-search-column tab-control type-control">
            <a href="{{ config('real-estate::routes.sell') }}" class="tab-item {!! ($filters['type'] == 'sell') ? 'tab-item--active' : '' !!}">Bán</a>
            <a href="{{ config('real-estate::routes.rent') }}" class="tab-item {!! ($filters['type'] == 'rent') ? 'tab-item--active' : '' !!}" data->Cho thuê</a>
        </div>
        <div class="vertical-line">&nbsp;</div>
        <div class="re-search-column text-control keyword-control">
            <input class="form-control search-text" type="text" value="{!! $filters['keyword'] !!}" id="jsReSearchKeyWord" name="keyword" autocomplete="off" placeholder="Tìm kiếm bất động sản">
        </div>
        <div class="vertical-line">&nbsp;</div>
    </div>
    <div class="re-search-bar-inside re-search-filter">
        {!! view('real-estate::search/location-control') !!}
        {!! view('real-estate::search/categories-control') !!}
        {!! view('real-estate::search/price-control', ['prices' => $prices]) !!}
        {!! view('real-estate::search/area-control') !!}
        <div class="re-search-column select-control more-control">
            <div class="select-control-label">
                <div class="custom-value process-label" readonly="readonly"><i class="fa-light fa-filter-list"></i> Lọc thêm</div>
            </div>
            <div class="select-context select-context-right advance-select-options hiding">
                <div class="mobile-control-label">
                    <div class="mobile-control-text">Lọc thêm</div>
                    <span class="mobile-control-close js_control_close"><i class="fa-thin fa-xmark"></i></span>
                </div>
                <div id="divMoreOptions">
                    {!! view('real-estate::search/more/bedroom-control', compact('bedrooms', 'filters')) !!}
                    {!! view('real-estate::search/more/bathroom-control', compact('bathrooms', 'filters')) !!}
                    {!! view('real-estate::search/more/direction-control', compact('directions', 'filters')) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="re__bg-filter-dropdown hiding"></div>
</div>

<script>
$(function() {
    class RealEstatePropertySearch {
        constructor() {
            this.filtersData = $('.property-search-bar').data('search-filter');
            this.options = {
                locationData: {
                    cities: SkilldoUtil.storage.get('cityList'),
                    wards: SkilldoUtil.storage.get('wardList'),
                },
                location: {
                    city: { value: '', label: '' },
                    ward: { value: '', label: '' },
                    wardCurrent: []
                },
                elements: {
                    inputKeyword: $('#jsReSearchKeyWord'),
                    divCityOptions: $('#divCityOptions'),
                    divWardOptions: $('#divWardOptions'),
                    divCategoryOptions: $('#divCategoryOptions'),
                    divPriceOptions: $('#divPriceOptions'),
                    divAreaOptions: $('#divAreaOptions'),
                },
                url: '',
                categories: [],
                category: { id: '', name: '', slug: '' },
                price: { value: '', label: '' },
                area: { value: '', label: '' },
                bedroom: { value: '', label: '' },
                bathroom: { value: '', label: '' },
                direction: { value: '', label: '' },
            };

            this.loadLocation();

            this.loadCategories();

            if (this.filters.price !== '')
            {
                let control = this.elements.divPriceOptions.closest('.price-control');

                this.price.value = this.filters.price;

                this.price.label = this.elements.divPriceOptions.find('li[data-value="' + this.filters.price + '"]').html();

                control.find('.price-label').html(this.price.label);

                control.find('.select-context').hide();
            }

            if (this.filters.area !== '')
            {
                let control = this.elements.divAreaOptions.closest('.area-control');
                this.area.value = this.filters.area;
                this.area.label = this.elements.divAreaOptions.find('li[data-value="' + this.filters.area + '"]').html();
                control.find('.area-label').html(this.area.label);
                control.find('.area-input').val(this.area.value);
                control.find('.select-context').hide();
            }

            if (this.filters.status !== '')
            {
                let control = this.elements.divStatusOptions.closest('.status-control');
                this.status.value = this.filters.status;
                this.status.label = this.elements.divStatusOptions.find('li[data-value="' + this.filters.status + '"]').html();
                control.find('.status-label').html(this.status.label);
                control.find('.select-context').hide();
            }
        }

        get filters() { return this.filtersData; }
        get type() { return this.filters.type; }
        get locationData() { return this.options.locationData; }
        get location() { return this.options.location; }
        get elements() { return this.options.elements; }
        get url() { return this.options.url; }
        set url(value) { this.options.url = value; }
        get categories() { return this.options.categories; }
        set categories(value) { return this.options.categories = value; }
        get category() { return this.options.category; }
        set category(value) { return this.options.category = value; }
        get price() { return this.options.price; }
        get bedroom() { return this.options.bedroom; }
        get bathroom() { return this.options.bathroom; }
        get direction() { return this.options.direction; }
        get area() { return this.options.area; }

        loadLocation()
        {
            let self = this;

            this.locationData.cities = SkilldoUtil.storage.get('cityList');

            if (typeof this.locationData.cities == 'undefined' || this.locationData.cities == null || this.locationData.cities === 'undefined' || this.locationData.cities === undefined)
            {
                let data = { action: 'RealEstate\\Ajax\\Web\\PropertyAjax::getLocations' };

                request.post(ajax, data).then(function(response)
                {
                    if (response.status === 'success')
                    {
                        SkilldoUtil.storage.set('cityList', response.data.cities);
                        SkilldoUtil.storage.set('wardList', response.data.wards);
                        self.locationData.cities = response.data.cities;
                        self.locationData.wards  = response.data.wards;
                        self.renderLocation();
                    }
                });
            }
            else
            {
                this.renderLocation();
            }
        }

        renderLocation()
        {
            let citiesOptions = '<li data-value="0" class="advance-options">Toàn quốc</li>';

            if (Object.keys(this.locationData.cities).length !== 0)
            {
                for (const [index, city] of Object.entries(this.locationData.cities))
                {
                    if (city.active === true)
                    {
                        citiesOptions += `<li data-value="${city.id}" class="advance-options">${city?.fullname}</li>`;
                    }
                }

                this.elements.divCityOptions.find('ul').html(citiesOptions);
            }

            if (this.filters.city !== '' && this.filters.city !== 0)
            {
                let control = this.elements.divCityOptions.closest('.city-control');

                this.location.city.value = this.filters.city;

                for (const [index, city] of Object.entries(this.locationData.cities))
                {
                    if (city.id === this.filters.city)
                    {
                        this.location.city.label = city.fullname;
                        break;
                    }
                }

                let wardOptionsHtml = '';
                
                this.location.wardCurrent = [];

                if (Object.keys(this.locationData.wards).length !== 0) 
                {
                    for (const [index, cities] of Object.entries(this.locationData.wards)) 
                    {
                        if (cities.id == this.filters.city) 
                        {
                            this.location.wardCurrent[''] = 'Tất cả quận huyện';
                            
                            for (const [index, ward] of Object.entries(cities.wards)) 
                            {
                                this.location.wardCurrent[ward.id] = ward.fullname;
                                
                                wardOptionsHtml += `<li data-value="${ward.id}" class="advance-options">${ward.fullname}</li>`;
                            }
                            break;
                        }
                    }
                    control.find('.city-label').html(this.location.city.label);
                    control.find('.city-root').show();
                    control.find('.city-root a.level').html(this.location.city.label);
                    this.elements.divCityOptions.hide();
                    this.elements.divWardOptions.show().find('ul').html(wardOptionsHtml);
                }

                if (this.filters.ward !== '') 
                {
                    this.location.ward.value = this.filters.ward;
                    this.location.ward.label = this.location.wardCurrent[this.filters.ward];
                    control.find('.city-label').html(this.location.ward.label);
                    control.find('.select-context').hide();
                    $('.re__bg-filter-dropdown').hide();
                }
            }
        }

        searchLocation(input) 
        {
            let control = input.closest('.city-control');
            let keyword = input.val();
            control.find('.advance-options li').hide();
            control.find('.advance-options li').each(function() {
                if ($(this).text().toLowerCase().indexOf("" + keyword + "") !== -1) {
                    $(this).show();
                }
            });
            return false;
        }

        onClickCity(element) 
        {
            let control = element.closest('.city-control');
            
            let cityValue = element.attr('data-value');

            if (cityValue === '' || cityValue === 0 || cityValue === undefined) 
            {
                this.location.city.value = '';
                this.location.city.label = 'Tất cả';
            } 
            else 
            {
                this.location.city.value = cityValue;
                
                for (const [index, city] of Object.entries(this.locationData.cities))
                {
                    if (city.id === cityValue) 
                    {
                        this.location.city.label = city.fullname;
                        break;
                    }
                }
            }

            control.find('.city-label').html(this.location.city.label);
            control.find('.select-text-content').val('');
            
            let wardOptionsHtml = '';
            
            this.location.wardCurrent = [];

            if (Object.keys(this.locationData.wards).length !== 0 && cityValue !== '' && cityValue !== 0  && cityValue !== undefined) 
            {
                for (const [index, cities] of Object.entries(this.locationData.wards)) 
                {
                    if (cities.id == cityValue) 
                    {
                        this.location.wardCurrent[''] = 'Tất cả quận huyện';
                        
                        for (const [index, ward] of Object.entries(cities.wards)) 
                        {
                            this.location.wardCurrent[ward.id] = ward.fullname;
                            wardOptionsHtml += `<li data-value="${ward.id}" class="advance-options">${ward.fullname}</li>`;
                        }
                        break;
                    }
                }
                control.find('.city-root').show();
                control.find('.city-root a.level').html(this.location.city.label);
                this.elements.divCityOptions.hide();
                this.elements.divWardOptions.show().find('ul').html(wardOptionsHtml);
            }
            this.createUrl();
        }

        onClickWard(element) 
        {
            let control = element.closest('.city-control');
            let wardValue = element.attr('data-value');
            if (wardValue === '') 
            {
                this.location.ward.value = '';
                this.location.ward.label = this.location.wardCurrent[''];
            } 
            else 
            {
                this.location.ward.value = wardValue;
                this.location.ward.label = this.location.wardCurrent[wardValue];
            }
            control.find('.city-label').html(this.location.ward.label);
            control.find('.select-text-content').val('');
            control.find('.select-context').hide();
            $('.re__bg-filter-dropdown').hide();
            this.createUrl();
        }

        onClickBackToCity(element) 
        {
            let control = element.closest('.city-control');
            control.find('.city-label').html(this.location.city.label);
            control.find('.select-text-content').val('');
            control.find('.city-root').hide();
            this.location.wardCurrent = [];
            this.location.ward.value = '';
            this.location.ward.label = '';
            this.elements.divCityOptions.show();
            this.elements.divWardOptions.hide();
            this.createUrl();
        }

        loadCategories()
        {
            let self = this;
            let data = {
                action: 'RealEstate\\Ajax\\Web\\PropertyAjax::getCategories',
                object: 'property',
                type: this.type
            };
            request.post(ajax, data).then(function(response)
            {
                if (response.status === 'success')
                {
                    self.categories = response.data.categories;

                    let categoryOptions = '<li data-value="0" class="advance-options">Tất cả loại hình</li>';

                    if (Object.keys(response.data.categories).length !== 0)
                    {
                        for (const [index, cate] of Object.entries(response.data.categories))
                        {
                            categoryOptions += `<li data-value="${cate.id}" class="advance-options">${cate.name}</li>`;
                        }
                        self.elements.divCategoryOptions.find('ul').html(categoryOptions);
                    }
                    if (typeof self.filters.category != 'undefined' && self.filters.category !== '')
                    {
                        let control = self.elements.divCategoryOptions.closest('.category-control');

                        self.category = response.data.categories.find((item) => item.id == self.filters.category);

                        if (self.category?.name)
                        {
                            control.find('.cate-label').html(self.category.name);
                        }
                        control.find('.select-context').hide();

                        $('.re__bg-filter-dropdown').hide();
                    }
                    $(".advance-options").mCustomScrollbar();
                }
            });
        }

        onClickCategory(element)
        {
            let control = element.closest('.category-control');
            let value = element.attr('data-value');
            if (value === 0 || value === '' || value === undefined || value === '0')
            {
                this.category = { 'id': 0, 'slug': '', 'name': 'Tất cả' };
            }
            else
            {
                this.category = this.categories.find((item) => item.id == value);
            }

            control.find('.cate-label').html(this.category.name);

            control.find('.select-context').hide();

            $('.re__bg-filter-dropdown').hide();

            this.createUrl();
        }

        onClickPrice(element)
        {
            let control = element.closest('.price-control');
            this.price.value = element.attr('data-value');
            this.price.label = element.html();
            control.find('.price-label').html(this.price.label);
            control.find('.select-context').hide();
            $('.re__bg-filter-dropdown').hide();
            this.createUrl();
        }

        onClickArea(element)
        {
            let control = element.closest('.area-control');
            this.area.value = element.attr('data-value');
            this.area.label = element.html();
            control.find('.area-label').html(this.area.label);
            control.find('.area-input').val(this.area.value);
            control.find('.select-context').hide();
            $('.re__bg-filter-dropdown').hide();
            this.createUrl();
        }

        onClickBedroom(element) {

            let control = element.closest('.tag-options');

            this.bedroom.value = element.attr('data-value');

            this.bedroom.label = element.html();

            control.find('.tag-item').removeClass('tag-item--active');

            element.addClass('tag-item--active')

            $('.re__bg-filter-dropdown').hide();

            this.createUrl();
        }

        onClickBathroom(element) {

            let control = element.closest('.tag-options');

            this.bathroom.value = element.attr('data-value');

            this.bathroom.label = element.html();

            control.find('.tag-item').removeClass('tag-item--active');

            element.addClass('tag-item--active')

            $('.re__bg-filter-dropdown').hide();

            this.createUrl();
        }

        onClickDirection(element)
        {
            let control = element.closest('.tag-options');

            this.direction.value = element.attr('data-value');

            this.direction.label = element.html();

            control.find('.tag-item').removeClass('tag-item--active');

            element.addClass('tag-item--active')

            $('.re__bg-filter-dropdown').hide();

            this.createUrl();
        }

        clickClose(element)
        {
            let control = element.closest('.select-context');
            control.hide();
            $('.re__bg-filter-dropdown').hide();
        }

        createUrl()
        {
            this.url = '';

            if (!this.category?.id || this.category.id === '')
            {
                if (this.type === 'sell') this.url = real_estate_route_sell ?? 'bat-dong-san-ban';
                if (this.type === 'rent') this.url = real_estate_route_rent ?? 'bat-dong-san-cho-thue';
            }
            else
            {
                this.url = this.category.slug;
            }

            if (this.location.city.value !== '')
            {
                this.url += '/' + (this.location.city.value + "").toLowerCase();
            }

            if (this.location.ward.value !== '')
            {
                this.url += '--' + (this.location.ward.value + "").toLowerCase();
            }

            let params = false;

            let keyword = this.elements.inputKeyword.val();

            if (keyword != null && keyword.length > 0)
            {
                this.url += ((params) ? '&' : '?') + 'keyword=' + keyword;
                params = true;
            }

            if (this.price.value !== '')
            {
                this.url += '?price=' + this.price.value;
                params = true;
            }
            if (this.area.value !== '')
            {
                this.url += ((params) ? '&' : '?') + 'area=' + this.area.value;
                params = true;
            }

            if(this.bedroom.value !== '')
            {
                this.url += ((params) ? '&' : '?') + 'bedroom='+ this.bedroom.value
                params = true;
            }

            if(this.bathroom.value !== '')
            {
                this.url += ((params) ? '&' : '?') + 'bathroom='+ this.bathroom.value
                params = true;
            }

            if(this.direction.value !== '')
            {
                this.url += ((params) ? '&' : '?') + 'direction='+ this.direction.value
            }

            $('#js_property_index_form__load').attr('action', this.url);

            window.history.pushState("data", '', this.url);

            this.loadProperty();
        }

        loadProperty()
        {
            let self = this;

            let data = {
                action: 'RealEstate\\Ajax\\Web\\PropertyAjax::property',
                type: this.type,
                url: window.location.href,
                categoryId: this.category?.id,
                city: this.location.city.value,
                ward: this.location.ward.value,
                price: this.price.value,
                keyword: this.elements.inputKeyword.val(),
                area: this.area.value,
                bedroom: this.bedroom.value,
                bathroom: this.bathroom.value,
                direction: this.direction.value,
            };

            request.post(ajax, data).then(function(response)
            {
                if (response.status === 'success')
                {
                    $('.js_show_total').text(response.data.total);

                    if (response.data.total === 0)
                    {
                        $('#js_property_list__item').html('');

                        $('.box-pagination').html('');
                    }
                    else
                    {
                        $('#js_property_list__item').html(decodeURIComponent(atob(response.data.html).split('').map(function(c) {
                            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                        }).join('')));

                        $('.box-pagination').html(atob(response.data.pagination));
                    }
                }
                else
                {
                    window.location.href = self.url;
                }
            });
        }
    }

    let searchProperty = new RealEstatePropertySearch();

    $(document)
        .on('keyup', '#jsReSearchKeyWord', SkilldoUtil.debounce(function() {
            searchProperty.createUrl();
        }, 500))
        .on('keyup', '.city-control .select-text-content', function() {
            searchProperty.searchLocation($(this));
        })
        .on('click', '#divCityOptions li.advance-options', function() {
            searchProperty.onClickCity($(this));
        })
        .on('click', '#divWardOptions li.advance-options', function() {
            searchProperty.onClickWard($(this));
        })
        .on('click', '.city-root a.level', function() {
            searchProperty.onClickBackToCity($(this));
        })
        .on('click', '#divCategoryOptions li.advance-options', function() {
            searchProperty.onClickCategory($(this));
        })
        .on('click', '#divPriceOptions li.advance-options', function() {
            searchProperty.onClickPrice($(this));
        })
        .on('click', '#divAreaOptions li.advance-options', function() {
            searchProperty.onClickArea($(this));
        })
        .on('click', '#divBedroomOptions .tag-item', function() {
            searchProperty.onClickBedroom($(this))
        })
        .on('click', '#divBathroomOptions .tag-item', function() {
            searchProperty.onClickBathroom($(this))
        })
        .on('click', '#divDirectionOptions .tag-item', function() {
            searchProperty.onClickDirection($(this))
        })
        .on('click', '.js_control_close', function() {
            searchProperty.clickClose($(this));
        })
        .on('click', '.select-control .select-control-label', function(e) {
            $(this).closest('.select-control').find('.select-context').toggle();
            $('.re__bg-filter-dropdown').toggle();
            return false;
        })
        .mouseup(function(e) {
            let container = $('.select-context');
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
                $('.re__bg-filter-dropdown').hide();
            }
        });
});
</script>


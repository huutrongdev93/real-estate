$(function () {
    // Cache selectors
    let lastId, topMenu = $(".real-estate-tab-heading"), topMenuHeight = topMenu.outerHeight() + 100, menuItems = topMenu.find("a"),

    scrollItems = menuItems.map(function(){
        let item = $($(this).attr("href"));
        if (item.length) { return item; }
    });

    menuItems.click(function(e){
        let href = $(this).attr("href"),
            offsetTop = href === "#" ? 0 : $(href).offset().top - topMenuHeight + 50;
        $('html, body').stop().animate({
            scrollTop: offsetTop
        }, 0);
        e.preventDefault();
    });

    $(window).scroll(function(){
        // Get container scroll position
        let fromTop = $(this).scrollTop()+topMenuHeight;

        // Get id of current scroll item
        let cur = scrollItems.map(function(){
            if ($(this).offset().top < fromTop)
                return this;
        });
        // Get the id of the current element
        cur = cur[cur.length-1];
        let id = cur && cur.length ? cur[0].id : "";

        if (lastId !== id) {
            lastId = id;
            // Set/remove active class
            menuItems.removeClass("active").filter("[href='#"+id+"']").addClass("active");
        }
    });

    //Booking
    $(document).on('submit', '#js_real_estate_form__booking', function () {

        let loading = $(this).find('.loading');

        loading.show();

        let data = $(this).serializeJSON();

        data.action = 'RealEstate\\Ajax\\Web\\PropertyAjax::booking'

        request.post(ajax, data).then(function(response) {
            SkilldoMessage.response(response);
            loading.hide();
            if (response.status === 'success') {
                $('#js_real_estate_form__booking').trigger('reset');
            }
        });

        return false;
    });

    // wishlist
    $(document).on('click', '.js_handle_property_wishlist', function () {
        let id = $(this).data('id');
        let self = $(this);
        let data = {'action': 'RealEstate\\Ajax\\Web\\PropertyAjax::wishlist', 'id': id};
        request.post(ajax, data).then(function(response) {
            SkilldoMessage.response(response);
            if (response.status === 'success') {
                self.toggleClass('like');
                if(response.data.status === 'remove') {
                    self.closest('.list-item-property-wishlist').find(`.item-property:has(.btn-wishlist[data-id="${id}"])`).remove();
                }
            }
        });

        return false;
    })
    // show - phonenumber
    $(document).on('click', '.phone-number-button .js_show_phone', function(e) {
        e.preventDefault();
        $(this).closest('.phone-number-button').find('.js_full_number').show();
        $(this).closest('.phone-number-button').find('.js_phone_display').hide();
    })

    // report
    $(document).on('click', '.js_handle_property_report', function()
    {
        $('#modalSubmitFeedback').modal('show');
    })

    $(document).on('click', '#modalSubmitFeedback .btn-close', function()
    {
        $('#modalSubmitFeedback').modal('hide');
    })
});
class RealEstateLocation {
    constructor() {
        this.elements = {
            city: $('#city'),
            ward: $('#ward')
        }
    }

    loadWards()
    {
        let self = this;

        let data = {
            provinceId: this.elements.city.val(),
            action: 'RealEstate\\Ajax\\Web\\PropertyAjax::wards'
        };

        request.post(ajax, data).then(function (response)
        {
            if (response.status === 'success')
            {
                self.elements.ward.html(response.data);
            }
        });
    }
}

$(function ()
{
    const location = new RealEstateLocation();
    //Billing
    $(document).on('change', '#city', function () {
        location.loadWards()
    });

    $(document).on('click', '.js_property_btn_collection', function()
    {
        if ($(this).hasClass("active"))
        {
            $(this).removeClass("active");
        }
        else
        {
            $(this).addClass("active");
        }

        let propertyId = $(this).attr('data-id');

        let collection = $(this).attr('data-collection');

        let data = {
            'action'        : 'RealEstate\\Ajax\\Admin\\PropertyAjax::saveCollection',
            'propertyId'    : propertyId,
            'collection'    : collection,
        };

        request.post(ajax, data).then(function(response)
        {
            SkilldoMessage.response(response);

            if(response.status === 'error')
            {
                let button = $('.js_property_btn_collection[data-id="'+propertyId+'"]');

                if (button.hasClass("active"))
                {
                    button.removeClass("active");
                }
                else
                {
                    button.addClass("active");
                }
            }
        });
    });
})
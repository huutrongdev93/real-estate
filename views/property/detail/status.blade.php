<div class="property-status label mb-3" style="padding:0; position: relative;">
    @if(!empty($object->type))
        <div class="label-1">{!! PropertyHelper::getType($object->type) !!}</div>
    @endif
    @if(!empty($object->status) && !empty(PropertyHelper::getStatus($object->status)))
        <div class="label-2 property-label property-label-{!! $object->status !!}" style="position: relative; cursor: pointer;">
            {!! PropertyHelper::getStatus($object->status) !!}
            <i class="fa-solid fa-circle-info"></i>
            <div class="modal-tooltip-appraised" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 10px; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 20px; min-width: 350px; z-index: 1000;">
                <div class="title" style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    {!! Image::source(asset('real-estate::images/verified-shield.svg'), 'Tin xác thực')->html() !!}
                    <p style="margin: 0; font-weight: 600; font-size: 16px;">Tin xác thực là gì?</p>
                </div>
                <div class="item-information" style="display: flex; gap: 12px; margin-bottom: 15px;">
                    <div class="icon" style="flex-shrink: 0;">
                        {!! Image::source(asset('real-estate::images/document-check.svg'), 'sổ đỏ/sổ hồng')->html() !!}
                    </div>
                    <div class="information">
                        <p class="heading" style="font-weight: 600; margin: 0 0 5px 0; font-size: 14px;">Có sổ đỏ/sổ hồng</p>
                        <p style="margin: 0; font-size: 13px; color: #666;">Người bán đã cung cấp bản chụp sổ đỏ/sổ hồng</p>
                    </div>
                </div>
                <div class="item-information" style="display: flex; gap: 12px; margin-bottom: 15px;">
                    <div class="icon" style="flex-shrink: 0;">
                        {!! Image::source(asset('real-estate::images/pin-check.svg'), 'địa chỉ')->html() !!}
                    </div>
                    <div class="information">
                        <p class="heading" style="font-weight: 600; margin: 0 0 5px 0; font-size: 14px;">Đúng địa chỉ</p>
                        <p style="margin: 0; font-size: 13px; color: #666;">Có bản đồ nhà thật đúng địa chỉ trên tin đăng và trên sổ đỏ/sổ hồng</p>
                    </div>
                </div>
                <div class="item-information" style="display: flex; gap: 12px;">
                    <div class="icon" style="flex-shrink: 0;">
                        {!! Image::source(asset('real-estate::images/image-check.svg'), 'hình ảnh và đặc điểm')->html() !!}
                    </div>
                    <div class="information">
                        <p class="heading" style="font-weight: 600; margin: 0 0 5px 0; font-size: 14px;">Đúng hình ảnh và đặc điểm</p>
                        <p style="margin: 0; font-size: 13px; color: #666;">Hình ảnh và đặc điểm bất động sản đã được đối chiếu với sổ đỏ</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(!empty($locationLabel))
    <div class="label-3">{!! $locationLabel !!}</div>
    @endif
    <div class="label-3">ID {!! $object->id !!}</div>
</div>

<script>
    $(document).ready(function()
    {
        $(document).on("click", ".property-label", function(e) {
            e.stopPropagation();
            let $tooltip = $(this).find(".modal-tooltip-appraised");
            $(".modal-tooltip-appraised").not($tooltip).hide();  // ẩn các tooltip khác

            if ($tooltip.is(':visible')) {
                $tooltip.hide();
            } else {
                $tooltip.show();

                // Điều chỉnh vị trí nếu modal bị tràn ra ngoài màn hình
                let tooltipOffset = $tooltip.offset();
                let tooltipWidth = $tooltip.outerWidth();
                let windowWidth = $(window).width();

                // Nếu tooltip tràn sang phải màn hình, căn về bên trái
                if (tooltipOffset.left + tooltipWidth > windowWidth) {
                    $tooltip.css({
                        'left': 'auto',
                        'right': '0'
                    });
                }
            }
        });

        $(document).on("click", function() {
            $(".modal-tooltip-appraised").hide();
        });

        // Ngăn chặn đóng modal khi click vào nó
        $(document).on("click", ".modal-tooltip-appraised", function(e) {
            e.stopPropagation();
        });
    });
</script>


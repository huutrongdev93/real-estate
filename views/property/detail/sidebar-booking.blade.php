<div class="widget property-booking" style="top: 60px;">
    <h6 class="widget-title">Liên hệ tư vấn miễn phí</h6>
    <p class="widget-des">Hãy để lại thông tin của bạn để nhận tư vấn</p>
    <form class="widget-content" id="js_real_estate_form__booking">
        {!! \SkillDo\Cms\Support\Theme::loading() !!}
        <input name="object_id" type="hidden" value="{!! $object->id !!}"/>
        <input name="object_type" type="hidden" value="property"/>
        <input name="url" type="hidden" value="{!! \SkillDo\Cms\Support\Url::current() !!}"/>
        <div class="form-register form-info bv-form">
            <label class="form-group has-feedback">
                <i class="fa-thin fa-user"></i>
                <input name="name" id="customer-name" type="text" class="input" placeholder="Nhập họ tên (*)" required/>
            </label>
            <label class="form-group has-feedback">
                <i class="fa-thin fa-envelope"></i>
                <input name="email" id="customer-email" type="text" class="input" placeholder="Nhập email" required/>
            </label>
            <label class="form-group has-feedback">
                <i class="fa-thin fa-phone"></i>
                <input name="phone" id="customer-phone" type="text" class="input" placeholder="Nhập số điện thoại (*)" required/>
            </label>
            <button id="btnSendInfo" class="btn btn-effect-default btn-theme btn-block"><i class="fal fa-user-headset"></i> Yêu cầu gọi lại</button>
        </div>
    </form>
</div>
<script defer>
    $(function() {
        let winW = $(window).width();
        if (winW >= 1024) {
            let heightMain = $('.property-main').height();
            $('.sidebar').css('height', heightMain + 'px');
        }
    });
</script>



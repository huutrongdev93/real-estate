<div class="report-modal modal fade" id="modalSubmitFeedback" tabindex="-1" aria-labelledby="modalSubmitFeedbackLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <p class="header">Báo cáo tin rao có thông tin không đúng</p>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! Admin::loading() !!}
                {!! $form->open() !!}
                    <div class="input">
                        {!! $form->html() !!}
                    </div>
                    <div class="button">
                        <button type="submit" class="btn btn-theme btn-block">{{ trans('general.send') }}</button>
                    </div>
                {!! $form->close() !!}
            </div>
        </div>
    </div>
</div>

<style>
    #modalSubmitFeedback .form-control { margin-bottom: 14px; }
    #modalSubmitFeedback .btn { width: 100%; }
</style>

<script>
    function formPropertyFeedbackHandler(form) {
        let data = form.serializeJSON();
        data.action = 'RealEstate\\Ajax\\Web\\PropertyAjax::feedback';
        let button = document.querySelector('#modalSubmitFeedback .btn[type="submit"]');
        let loading = SkilldoUtil.buttonLoading(button);
        loading.start();
        request.post(ajax, data).then(function (response) {
            SkilldoMessage.response(response);
            loading.stop();
            if (response.status === 'success') {
                form.trigger('reset');
                $('#modalSubmitFeedback').modal('hide');
            }
        });
        return false;
    }
</script>


<form method="get" class="mb-5" id="js_property_index_form__load" action="{!! \SkillDo\Cms\Support\Url::current() !!}" accept-charset="utf-8">
    <input type="hidden" name="page" value="1">
    <div class="js_search_filter_input"></div>
    @do_action('page_property_index_search')
</form>
<div class="row">
    <div class="col-md-12">
        <div class="page-property-index">
            {!! \SkillDo\Cms\Support\Admin::loading() !!}
            @do_action('page_property_index_view')
        </div>
    </div>
</div>


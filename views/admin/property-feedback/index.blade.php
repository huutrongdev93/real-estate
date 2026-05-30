{!! Admin::partial('resources/page-default/page-index', [
    'module'  => 're_feedback',
    'name'    => trans('real-estate::property-feedback.title_list'),
    'table'   => $table,
    'tableId' => 'admin_re_feedback_table_list',
    'limitKey'=> 'admin_re_feedback_limit',
]) !!}

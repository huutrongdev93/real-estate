{!! Admin::partial('resources/page-default/page-index', [
    'module'  => 're_features',
    'name'    => trans('real-estate::features.title_list'),
    'table'   => $table,
    'tableId' => 'admin_re_features_table_list',
    'limitKey'=> 'admin_re_features_limit',
]) !!}

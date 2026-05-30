{!! Admin::partial('resources/page-default/page-index', [
    'module'    => 'property',
    'name'      => trans('real-estate::property.title_list'),
    'table'     => $table,
    'tableId'   => 'admin_property_table_list',
    'limitKey'  => 'admin_property_limit',
    'trash'     => true,
    'public'    => true,
]) !!}

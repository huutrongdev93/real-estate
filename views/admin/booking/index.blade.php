{!! Admin::partial('resources/page-default/page-index', [
    'module'  => 're_booking',
    'name'    => trans('real-estate::booking.title_list'),
    'table'   => $table,
    'tableId' => 'admin_re_booking_table_list',
    'limitKey'=> 'admin_re_booking_limit',
]) !!}

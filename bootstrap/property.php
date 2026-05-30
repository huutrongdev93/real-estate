<?php

use RealEstate\Modules\Admin\Property\Form;
use RealEstate\Modules\Admin\Property\Metaboxs;

/*
|--------------------------------------------------------------------------
| Hook Form - Property
|--------------------------------------------------------------------------
| Form::fields   - đăng ký fields trong form add/edit
| Form::buttons  - đăng ký nút hành động trong form
*/
add_filter('manage_property_input', [Form::class, 'fields']);
add_filter('manage_property_input', [Form::class, 'buttons']);
add_filter('insert_data_property_before_save', [Form::class, 'dataSave'], 10, 2);
/*
|--------------------------------------------------------------------------
| Hook MetaBox - Property
|--------------------------------------------------------------------------
| Metabox::register - đăng ký các metabox cho property
*/
add_action('add_meta_box', [Metaboxs::class, 'register']);

/*
|--------------------------------------------------------------------------
| Hook Save - Property
|--------------------------------------------------------------------------
| Metabox::save      - lưu dữ liệu metabox sau khi save
*/
add_action('save_property_object', [Metaboxs::class, 'save'], 10, 2);


<?php

use RealEstate\Modules\Admin\PropertyCategory\Form;

/*
|--------------------------------------------------------------------------
| Hook Form - Property Categories
|--------------------------------------------------------------------------
*/
add_filter('manage_property_categories_sell_input', [Form::class, 'fieldsSell']);
add_filter('manage_property_categories_rent_input', [Form::class, 'fieldsRent']);

/*
|--------------------------------------------------------------------------
| Hook Save - Property Categories
|--------------------------------------------------------------------------
*/
add_filter('insert_data_property_categories_sell_before_save', [Form::class, 'dataSell']);
add_filter('insert_data_property_categories_rent_before_save', [Form::class, 'dataRent']);
add_filter('submit_category_args', [Form::class, 'submitCategoryArgs'], 10, 2);

/*
|--------------------------------------------------------------------------
| Whitelist model cho CategoryAjax (core chỉ thao tác model đã đăng ký ở đây)
|--------------------------------------------------------------------------
*/
add_filter('admin_category_models', function (array $models) {
    $models['property_categories_sell'] = \RealEstate\Models\PropertyCategory::class;
    $models['property_categories_rent'] = \RealEstate\Models\PropertyCategory::class;
    return $models;
});

<?php

use RealEstate\Modules\Admin\Features\Form;

/*
|--------------------------------------------------------------------------
| Hook Form - Features (Tiện ích / Nội thất)
|--------------------------------------------------------------------------
*/
add_filter('manage_re_features_input', [Form::class, 'fields']);
add_filter('manage_re_features_input', [Form::class, 'buttons']);


<?php

use RealEstate\Services\AdminService;
use RealEstate\Services\RoleService;

/*
|--------------------------------------------------------------------------
| Admin Navigation & Assets
|--------------------------------------------------------------------------
*/
add_action('admin_navigation', [AdminService::class, 'navigation']);
add_action('admin_assets', [AdminService::class, 'assets']);
add_filter('admin_breadcrumb', [AdminService::class, 'breadcrumb']);

/*
|--------------------------------------------------------------------------
| Phân quyền
|--------------------------------------------------------------------------
*/
add_filter('user_role_editor_group', [RoleService::class, 'roles'], 10);
add_filter('user_role_editor_label', [RoleService::class, 'label'], 10);

/*
|--------------------------------------------------------------------------
| Menu
|--------------------------------------------------------------------------
*/
add_filter('admin_menu_list_object', [AdminService::class, 'menuListObject'], 10);
add_filter('admin_menu_item_data', [AdminService::class, 'menuItemData'], 10, 2);

/*
|--------------------------------------------------------------------------
| Booking
|--------------------------------------------------------------------------
*/
add_action('admin_table_re_booking_before_render', [\RealEstate\Modules\Admin\Booking\Table::class, 'beforeRender']);
add_action('admin_table_re_feedback_before_render', [\RealEstate\Modules\Admin\PropertyFeedback\Table::class, 'beforeRender']);


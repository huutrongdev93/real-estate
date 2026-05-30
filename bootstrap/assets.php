<?php
use RealEstate\Services\AssetService;
use RealEstate\Template\Layout;

add_action('theme_custom_assets',[AssetService::class, 'assets'], 30, 2);
add_action('theme_head_script_variable',[AssetService::class, 'scriptVariable']);

/**
 * Layout
 * Đăng ký layout cho builder
 */
add_filter('builder_layout_types', [Layout::class, 'builderLayoutType']);
add_action('builder_review_layout', [Layout::class, 'builderLayoutData']);

/**
 * Template
 * Đăng ký layout và view cho template
 */
add_action('template_layout_property_all', [Layout::class, 'layoutIndex']);
add_action('template_view_property_all', [Layout::class, 'viewIndex']);
add_action('template_layout_property_index', [Layout::class, 'layoutIndex']);
add_action('template_view_property_index', [Layout::class, 'viewIndex']);
add_action('template_layout_property_detail', [Layout::class, 'layoutDetail']);
add_action('template_view_property_detail', [Layout::class, 'viewDetail']);
<?php

use RealEstate\Services\AssetService;
use RealEstate\Services\SeoService;
use RealEstate\Services\SitemapPropertyCategory;
use RealEstate\Services\SitemapProperty;
use RealEstate\Template\Layout;
use RealEstate\Template\Breadcrumb;

add_action('theme_custom_assets',[AssetService::class, 'assets'], 30, 2);
add_action('theme_head_script_variable',[AssetService::class, 'scriptVariable']);

/**
 * Seo
 * Đăng ký thông tin cho plygin SEO
 */
add_filter('seo_head_base', [SeoService::class, 'headBase'], 10, 2);
add_filter('schema_render', [SeoService::class, 'schema'], 10, 2);

add_filter('seo_sitemap_list', [SitemapPropertyCategory::class, 'register']);
add_filter('seo_sitemap_property_category_xml', [SitemapPropertyCategory::class, 'sitemap'], 10, 3);

add_filter('seo_sitemap_list', [SitemapProperty::class, 'register']);
add_filter('seo_sitemap_property_xml', [SitemapProperty::class, 'sitemap'], 10, 3);

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


/*
|--------------------------------------------------------------------------
|  Breadcrumb
|--------------------------------------------------------------------------
| productsIndex - trang danh sách sản phẩm
| productsDetail - trang chi tiết sản phẩm
*/
add_action('theme_breadcrumb_property_index_data', [Breadcrumb::class, 'propertyIndex'], 20, 2);
add_action('theme_breadcrumb_property_all_data', [Breadcrumb::class, 'propertyAll'], 20, 2);
add_action('theme_breadcrumb_property_detail_data', [Breadcrumb::class, 'propertyDetail'], 20, 2);

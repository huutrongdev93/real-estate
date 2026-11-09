<?php
namespace RealEstate\Services;

use RealEstate\Models\PropertyCategory;
use SkdSeo\Services\SitemapService;

/**
 * Sitemap danh mục bất động sản. `lastmod` là ngày sửa thật của từng danh mục
 * (trước đây truyền hằng `DATE_ATOM` nên luôn bằng giờ hiện tại).
 */
class SitemapPropertyCategory
{
    static function register($listSiteMap)
    {
        if(!class_exists(SitemapService::class)) return $listSiteMap;

        $listSiteMap['property-category'] = ['date' => SitemapService::maxDate(PropertyCategory::query())];

        return $listSiteMap;
    }

    static function sitemap($sitemap)
    {
        $object = PropertyCategory::all();

        $sitemap->openUrlset();

        foreach ($object as $item)
        {
            $sitemap->itemUrl($item->slug, SitemapService::itemDate($item), 'weekly', 0.5, SitemapService::itemImages($item));
        }

        $sitemap->closeUrlset();

        return $sitemap;
    }
}

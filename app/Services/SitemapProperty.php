<?php
namespace RealEstate\Services;

use RealEstate\Models\Property;
use SkdSeo\Services\SitemapService;

/**
 * Sitemap tin bất động sản (đăng ký qua filter `seo_sitemap_list` của skd-seo).
 *
 * Theo hợp đồng skd-seo >= 6.0.0:
 *   - Sitemap chỉ 2 cấp: `register()` trả khoá `pages` để index gốc khai thẳng
 *     `?p=property-1`, `?p=property-2`… Trước đây `?p=property` lại dựng một
 *     `<sitemapindex>` nữa (3 cấp) — chuẩn sitemap không cho lồng index nên
 *     Google dừng ở tầng hai, không đọc tới tin nào.
 *   - `lastmod` là ngày sửa thật (cột `updated`, lùi về `created`). Trước đây
 *     truyền hằng `DATE_ATOM` nên mọi tin luôn khai "vừa sửa".
 */
class SitemapProperty
{
    const LIMIT = 200;

    /**
     * Truy vấn dùng chung cho register() lẫn sitemap(): cùng điều kiện, cùng thứ
     * tự, thì số trang và ngày của từng trang mới khớp với nội dung thật.
     */
    protected static function query()
    {
        return Property::where('public', 1)->where('trash', 0)->orderBy('id');
    }

    static function register($listSiteMap)
    {
        if(!class_exists(SitemapService::class)) return $listSiteMap;

        $listSiteMap['property'] = [
            'date'  => SitemapService::maxDate(self::query()),
            'pages' => SitemapService::pageDates(self::query(), self::LIMIT),
        ];

        return $listSiteMap;
    }

    //`?p=property` không kèm số trang trả về trang 1, để đường dẫn cũ không thành 404.
    static function sitemap($sitemap, $type, $paging)
    {
        if (empty($paging)) $paging = 1;

        $object = self::query()
            ->offset(($paging - 1) * self::LIMIT)
            ->limit(self::LIMIT)
            ->get();

        $sitemap->openUrlset();

        foreach ($object as $item)
        {
            $sitemap->itemUrl($item->slug, SitemapService::itemDate($item), 'weekly', 1.0, SitemapService::itemImages($item));
        }

        $sitemap->closeUrlset();

        return $sitemap;
    }
}

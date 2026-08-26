<?php
/*
|--------------------------------------------------------------------------
| Slug rieng cho tung ngon ngu (CMS 8.2.0)
|--------------------------------------------------------------------------
|
| Khai bao cac loai doi tuong cua plugin cho cong cu "Sinh duong dan" trong
| Cau hinh he thong -> Duong dan. Khong khai thi cong cu chi quet page/post/
| post_categories cua loi, va noi dung cua plugin da dich tu truoc se khong bao
| gio duoc sinh slug rieng.
|
| Chi khai model dung CA ModelRoute lan ModelLanguage — thieu mot trong hai thi
| khong co gi de sinh. Xem SkillDo\Cms\Support\SlugBackfill.
*/

add_filter('slug_backfill_modules', function (array $modules) {

    $modules['property'] = \RealEstate\Models\Property::class;

    $modules['property_categories'] = \RealEstate\Models\PropertyCategory::class;


    return $modules;
});

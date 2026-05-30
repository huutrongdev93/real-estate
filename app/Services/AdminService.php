<?php
namespace RealEstate\Services;

use RealEstate\Models\Booking;
use RealEstate\Models\PropertyCategory;
use RealEstate\Models\PropertyFeedback;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Menu\AdminMenu;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Url;
use SkillDo\Support\Auth;

class AdminService
{
    static function navigation(): void
    {
        if (Auth::hasCap('property_category_list') || Auth::hasCap('property_list') || Auth::hasCap('property_booking') || Auth::hasCap('property_feedback'))
        {
            AdminMenu::add('property', trans('real-estate::admin.nav.property'), 'property', [
                'position' => 'home',
                'icon'     => '<i class="fa-solid fa-building"></i>',
                'count'    => 0,
            ]);

            if (Auth::hasCap('property_category_list'))
            {
                AdminMenu::addSub('property', 'property-sell-categories', trans('real-estate::admin.nav.sell_categories'), 'property-sell-categories');

                AdminMenu::addSub('property', 'property-rent-categories', trans('real-estate::admin.nav.rent_categories'), 'property-rent-categories');
            }

            if (Auth::hasCap('property_list'))
            {
                AdminMenu::addSub('property', 'property', trans('real-estate::admin.nav.property_list'), 'property');

                AdminMenu::addSub('property', 're-features', trans('real-estate::admin.nav.features'), 're-features');
            }

            if (Auth::hasCap('property_booking'))
            {
                $numberNew = Cache::get('re_booking_new_count');

                AdminMenu::addSub('property', 're-booking-property', trans('real-estate::admin.nav.booking_property'), 're-booking/property', [
                    'count' => (int)$numberNew,
                ]);
            }

            if (Auth::hasCap('property_feedback'))
            {
                $numberNew = Cache::get('re_feedback_new_count');

                AdminMenu::addSub('property', 're-feedback', trans('real-estate::admin.nav.feedback'), 're-feedback', [
                    'count' => (int)$numberNew,
                ]);
            }
        }
    }

    static function assets(): void
    {
        Admin::asset()->location('header')
            ->add('real-estate-admin-css', asset('real-estate::css/real-estate-admin-style.css'));

        Admin::asset()->location('footer')
            ->add('real-estate-admin-js', asset('real-estate::js/real-estate-admin-script.js'));

        $pages = ['property_add', 'property_edit'];

        $base = \SkillDo\Cms\Support\Url::base('plugins/real-estate/assets/add-on/leaflet/');

        Admin::asset()->location('header')->add('leaflet-css', $base . 'leaflet.css', ['page' => $pages])->minify(false);

        Admin::asset()->location('footer')->add('leaflet-js',  $base . 'leaflet.js',  ['page' => $pages])->minify(false);
    }

    static function breadcrumb(): void
    {
        app('breadcrumb.admin')->add('admin.property.index', [
            ['label' => trans('real-estate::admin.nav.property_list')]
        ]);

        app('breadcrumb.admin')->add('admin.property.add', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('admin::general.add')],
        ]);

        app('breadcrumb.admin')->add('admin.property.edit', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('admin::general.update')],
        ]);

        //Danh mục
        app('breadcrumb.admin')->add('admin.property.category.sell', [
            ['label' => trans('real-estate::admin.nav.sell_categories')]
        ]);
        app('breadcrumb.admin')->add('admin.property.category.rent', [
            ['label' => trans('real-estate::admin.nav.rent_categories')]
        ]);

        //Features (Tiện ích & Nội thất)
        app('breadcrumb.admin')->add('admin.re-features.index', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('real-estate::admin.nav.features')],
        ]);

        app('breadcrumb.admin')->add('admin.re-features.add', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('real-estate::admin.nav.features'), 'url' => route('admin.re-features.index')],
            ['label' => trans('admin::general.add')],
        ]);

        app('breadcrumb.admin')->add('admin.re-features.edit', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('real-estate::admin.nav.features'), 'url' => route('admin.re-features.index')],
            ['label' => trans('admin::general.update')],
        ]);

        //Booking
        app('breadcrumb.admin')->add('admin.re-booking.property', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('real-estate::admin.nav.booking_property')],
        ]);

        //Property Feedback
        app('breadcrumb.admin')->add('admin.re-feedback.index', [
            ['label' => trans('real-estate::admin.nav.property_list'), 'url' => route('admin.property.index')],
            ['label' => trans('real-estate::admin.nav.feedback')],
        ]);
    }

    static function menuListObject(array $listObject): array
    {
        $listObject['property_sell_categories'] = [
            'label' => trans('real-estate::admin.menu.sell_categories'),
            'type'  => 'property_sell_categories',
            'data'  => [],
        ];

        $listObject['property_sell_categories']['data'][0] = (object)['id' => 0, 'name' => '<b>' . trans('real-estate::admin.menu.property_sell') . '</b>'];

        $categories = PropertyCategory::where('type', 'sell')->multilevel();

        if (have_posts($categories))
        {
            foreach ($categories as $key => $datum)
            {
                if (is_string($datum) && is_numeric($key))
                {
                    if ($key == 0) continue;

                    $listObject['property_sell_categories']['data'][$key] = (object)['id' => $key, 'name' => $datum];
                }
                else
                {
                    if (!isset($datum->id)) continue;

                    $listObject['property_sell_categories']['data'][$datum->id] = (object)['id' => $datum->id, 'name' => $datum->name];
                }
            }
        }

        $listObject['property_rent_categories'] = [
            'label' => trans('real-estate::admin.menu.rent_categories'),
            'type'  => 'property_rent_categories',
            'data'  => [],
        ];

        $listObject['property_rent_categories']['data'][0] = (object)['id' => 0, 'name' => '<b>' . trans('real-estate::admin.menu.property_rent') . '</b>'];

        $categories = PropertyCategory::where('type', 'rent')->multilevel();

        if (have_posts($categories))
        {
            foreach ($categories as $key => $datum)
            {
                if (is_string($datum) && is_numeric($key))
                {
                    if ($key == 0) continue;
                    $listObject['property_rent_categories']['data'][$key] = (object)['id' => $key, 'name' => $datum];
                }
                else
                {
                    if (!isset($datum->id)) continue;
                    $listObject['property_rent_categories']['data'][$datum->id] = (object)['id' => $datum->id, 'name' => $datum->name];
                }
            }
        }

        return $listObject;
    }

    static function menuItemData(array $data, int $id): array
    {
        if ($data['object_type'] == 'property_sell_categories')
        {
            if ($id == 0)
            {
                $data['name']      = trans('real-estate::admin.menu.property_sell');
                $data['slug']      = config('real-estate::routes.sell');
                $data['object_id'] = 0;
            }
            else
            {
                $object = PropertyCategory::where('type', 'sell')
                    ->where('id', $id)
                    ->select('id', 'name', 'slug')
                    ->first();

                if (hasItems($object))
                {
                    $data['name']      = $object->name;
                    $data['slug']      = $object->slug;
                    $data['object_id'] = $id;
                }
            }
        }

        if ($data['object_type'] == 'property_rent_categories')
        {
            if ($id == 0)
            {
                $data['name']      = trans('real-estate::admin.menu.property_rent');
                $data['slug']      = config('real-estate::routes.rent');
                $data['object_id'] = 0;
            }
            else
            {
                $object = PropertyCategory::where('type', 'rent')->where('id', $id)->select('id', 'name', 'slug')->first();

                if (hasItems($object))
                {
                    $data['name']      = $object->name;
                    $data['slug']      = $object->slug;
                    $data['object_id'] = $id;
                }
            }
        }

        return $data;
    }
}


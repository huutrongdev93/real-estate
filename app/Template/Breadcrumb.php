<?php
namespace RealEstate\Template;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RealEstate\Models\PropertyCategory;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Language;
use Illuminate\Support\Arr;

Class Breadcrumb
{
    static function propertyIndex($breadcrumb, $languageCurrent): array
    {
        $category = Cms::getData('category');

        if(noItems($category))
        {
            return $breadcrumb;
        }

        $cacheId = 'breadcrumb_property_category_'.$category->id.'_'.$languageCurrent;

        if(Cache::has($cacheId))
        {
            return Cache::get($cacheId);
        }

        $query = PropertyCategory::where('public', 1)
            ->select('id', 'name', 'slug')
            ->where('lft', '<=', $category->lft)
            ->where('rgt', '>=', $category->rgt)
            ->orderBy('level');

        $breadcrumbData = $query->get();

        if(hasItems($breadcrumbData))
        {
            $breadcrumb = $breadcrumbData->map(function($item) {
                return (object)[
                    'slug' => $item->slug,
                    'name' => $item->name
                ];
            })->toArray();
        }

        Cache::save($cacheId, $breadcrumb);

        return $breadcrumb;
    }

    static function propertyAll($breadcrumb, $languageCurrent): array
    {
        $type = Route::current()->parameter('type');

        $breadcrumbMain = null;

        if($type == 'rent')
        {
            $breadcrumbMain = (object)[
                'slug' => config('real-estate::routes.rent'),
                'name' => 'Bất động sản cho thuê'
            ];
        }

        if($type == 'sell')
        {
            $breadcrumbMain = (object)[
                'slug' => config('real-estate::routes.sell'),
                'name' => 'Bất động sản bán'
            ];
        }

        if(!empty($breadcrumbMain))
        {
            $breadcrumb[] = $breadcrumbMain;

            $location = Route::current()->parameter('segment1');

            if(!empty($location))
            {
                $location = explode('--', $location);

                if (!empty($location[0]))
                {
                    $cityId = Location2::provincesIdBySlug($location[0]);

                    if(!empty($cityId))
                    {
                        $breadcrumb[] = (object)[
                            'slug' => $breadcrumbMain->slug.'/'.$location[0],
                            'name' => Location2::provinceName($cityId)
                        ];

                        if(!empty($location[1]))
                        {
                            $wardId = Location2::wardIdBySlug($location[1], $cityId);

                            if(!empty($wardId))
                            {
                                $breadcrumb[] = (object)[
                                    'slug' => $breadcrumbMain->slug.'/'.$location[0].'--'.$location[1],
                                    'name' => Location2::wardName($cityId, $wardId)
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $breadcrumb;
    }

    static function propertyDetail($breadcrumb, $languageCurrent): array
    {
        $object = Cms::getData('object');

        $cacheId = 'breadcrumb_property_detail_'.$object->id.'_'.$languageCurrent;

        if(Cache::has($cacheId))
        {
            return Cache::get($cacheId);
        }

        $category = Cms::getData('category');

        if(hasItems($category))
        {
            $query = PropertyCategory::where('public', 1)
                ->select('id', 'name', 'slug')
                ->where('lft', '<=', $category->lft)
                ->where('rgt', '>=', $category->rgt)
                ->orderBy('level');

            $breadcrumbData = $query->get();

            if(hasItems($breadcrumbData))
            {
                $breadcrumb = $breadcrumbData->map(function($item) {
                    return (object)[
                        'slug' => $item->slug,
                        'name' => $item->name
                    ];
                })->toArray();;
            }
        }
        else
        {
            $breadcrumb[] = (object)[
                'name' => trans('sicommerce::product.title'),
                'slug' => 'san-pham'
            ];
        }

        $breadcrumb[] = (object)[
            'slug' => $object->slug,
            'name' => $object->title
        ];

        Cache::save($cacheId, $breadcrumb);

        return $breadcrumb;
    }

    static function deleteCache($module, $id): void
    {
        $languages = Language::listKey();

        if( $module == 'property_categories_sell' || $module == 'property_categories_rent')
        {
            $id = Arr::wrap($id);

            foreach ($id as $item)
            {
                foreach ($languages as $language)
                {
                    Cache::delete( 'breadcrumb_property_index_'.$item.'_'.$language);
                }
            }

            Cache::delete('breadcrumb_property_detail_', true);
        }

        if( $module == 'property')
        {
            $id = Arr::wrap($id);

            foreach ($id as $item)
            {
                foreach ($languages as $language)
                {
                    Cache::delete( 'breadcrumb_property_detail_'.$item.'_'.$language);
                }
            }
        }
    }
}
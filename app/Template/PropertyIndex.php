<?php
namespace RealEstate\Template;

use SkillDo\Cms\Sidebar\Sidebar;
use SkillDo\Cms\Support\Cms;

class PropertyIndex
{
    static function layout(): void
    {
        echo view('real-estate::property/index/layout');
    }

    static function search(): void
    {
        $filters = Cms::getData('filters');
        $prices  = [];

        if ($filters['type'] == 'sell') {
            $prices['duoi-500-trieu']       = trans('real-estate::property.price.sell.below_500m');
            $prices['500-den-800-trieu']    = trans('real-estate::property.price.sell.500_800');
            $prices['800-den-1000-trieu']   = trans('real-estate::property.price.sell.800_1b');
            $prices['1000-den-2000-trieu']  = trans('real-estate::property.price.sell.1_2b');
            $prices['2000-den-3000-trieu']  = trans('real-estate::property.price.sell.2_3b');
            $prices['3000-den-5000-trieu']  = trans('real-estate::property.price.sell.3_5b');
            $prices['5000-den-10000-trieu'] = trans('real-estate::property.price.sell.5_10b');
            $prices['tren-10000-trieu']     = trans('real-estate::property.price.sell.above_10b');
        }

        if ($filters['type'] == 'rent') {
            $prices['duoi-5-trieu']    = trans('real-estate::property.price.rent.below_5m');
            $prices['5-den-10-trieu']  = trans('real-estate::property.price.rent.5_10');
            $prices['10-den-20-trieu'] = trans('real-estate::property.price.rent.10_20');
            $prices['20-den-35-trieu'] = trans('real-estate::property.price.rent.20_35');
            $prices['35-den-50-trieu'] = trans('real-estate::property.price.rent.35_50');
            $prices['50-den-80-trieu'] = trans('real-estate::property.price.rent.50_80');
            $prices['tren-80-trieu']   = trans('real-estate::property.price.rent.above_80');
        }

        $data = [
            'bedrooms'   => \PropertyHelper::getBedroom(),
            'bathrooms'  => \PropertyHelper::getBathroom(),
            'directions' => \PropertyHelper::getDirection(),
            'prices'     => $prices,
            'filters'    => $filters,
        ];

        echo view('real-estate::property/index/search', $data);
    }

    static function heading(): void
    {
        echo view('real-estate::property/index/heading');
    }

    static function sort(): void
    {
        $filters = Cms::getData('filters');
        echo view('real-estate::property/index/sort', ['total' => $filters['total'] ?? 0]);
    }

    static function list(): void
    {
        $pagination = Cms::getData('pagination') ?? [];

        echo view('real-estate::property/index/list', [
            'objects'    => Cms::getData('objects') ?? [],
            'pagination' => $pagination,
        ]);
    }

    static public function sidebarWidget(): void
    {
        Sidebar::render('property_index_sidebar');
    }
}

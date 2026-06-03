<?php
namespace RealEstate\Template;

use RealEstate\Supports\PropertyHelper;
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

        if ($filters['type'] == 'sell') 
        {
            $prices = PropertyHelper::getSearchPriceSell();
        }

        if ($filters['type'] == 'rent') 
        {
            $prices = PropertyHelper::getSearchPriceRent();
        }

        $data = [
            'bedrooms'   => PropertyHelper::getBedroom(),
            'bathrooms'  => PropertyHelper::getBathroom(),
            'directions' => PropertyHelper::getDirection(),
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

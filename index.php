<?php

use RealEstate\Services\Activator;
use RealEstate\Services\Deactivator;

class RealEstate
{
    public function active(): void
    {
        Activator::activate();
    }

    public function uninstall(): void
    {
        Deactivator::uninstall();
    }

    /**
     * Widget tìm kiếm cho trang chủ (dùng từ theme)
     */
    static function searchHome(): void
    {
        $data = [
            'bedrooms'   => \RealEstate\Supports\PropertyHelper::getBedroom(),
            'bathrooms'  => \RealEstate\Supports\PropertyHelper::getBathroom(),
            'directions' => \RealEstate\Supports\PropertyHelper::getDirection(),
            'priceSell'  => [],
            'priceRent'  => []
        ];

        $data['priceSell']['duoi-500-trieu']    = 'Dưới 500 triệu';
        $data['priceSell']['500-den-800-trieu'] = '500 đến 800 triệu';
        $data['priceSell']['800-den-1000-trieu']= '800 đến 1 tỷ';
        $data['priceSell']['1000-den-2000-trieu']= '1 - 2 tỷ';
        $data['priceSell']['2000-den-3000-trieu']= '2 - 3 tỷ';
        $data['priceSell']['3000-den-5000-trieu']= '3 - 5 tỷ';
        $data['priceSell']['5000-den-10000-trieu']= '5 - 10 tỷ';
        $data['priceSell']['tren-10000-trieu']  = 'trên 10 tỷ';

        $data['priceRent']['duoi-5-trieu']      = 'Dưới 5 triệu';
        $data['priceRent']['5-den-10-trieu']    = '5 đến 10 triệu';
        $data['priceRent']['10-den-20-trieu']   = '10 đến 20 triệu';
        $data['priceRent']['20-den-35-trieu']   = '20 đến 35 triệu';
        $data['priceRent']['35-den-50-trieu']   = '35 đến 50 triệu';
        $data['priceRent']['50-den-80-trieu']   = '50 đến 80 triệu';
        $data['priceRent']['tren-80-trieu']     = 'trên 80 triệu';

        echo view('real-estate::search/index', $data);
    }
}


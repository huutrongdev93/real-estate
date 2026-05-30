<?php
namespace RealEstate\Services;

use Ecommerce\Supports\Config;
use Ecommerce\Supports\Prd;

class AssetService
{
    static function assets($header, $footer): void
    {
        $header->add('real-estate-mCustomScrollbar', asset('real-estate::add-on/mCustomScrollbar/jquery.mCustomScrollbar.min.css'))->minify(false);
        $footer->add('real-estate-mCustomScrollbar', asset('real-estate::add-on/mCustomScrollbar/jquery.mCustomScrollbar.min.js'))->minify(false);
        $header->add('real-estate', asset('real-estate::css/real-estate-style.css'))->minify(false);
        $footer->add('real-estate', asset('real-estate::js/real-estate-script.js'))->minify(false);
    }

    static function scriptVariable($variables)
    {
        $variables['real_estate_route_sell']  = "'".config('real-estate::routes.sell')."'";

        $variables['real_estate_route_rent']  = "'".config('real-estate::routes.rent')."'";

        return $variables;
    }
}
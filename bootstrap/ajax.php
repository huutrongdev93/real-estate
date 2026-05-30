<?php

use SkillDo\Cms\Support\Ajax;

/*
|--------------------------------------------------------------------------
| Ajax Backend (Admin) - Property
|--------------------------------------------------------------------------
*/
Ajax::admin('RealEstate\Ajax\Admin\PropertyAjax::saveCollection');
Ajax::admin('RealEstate\Ajax\Admin\PropertyAjax::searchAddress');
/*
|--------------------------------------------------------------------------
| Ajax Frontend (Client) - Property
|--------------------------------------------------------------------------
*/
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::getLocations');
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::wards');
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::getCategories');
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::property');
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::booking');
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::wishlist');
Ajax::client('RealEstate\Ajax\Web\PropertyAjax::feedback');


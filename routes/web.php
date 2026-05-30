<?php

use SkillDo\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Property (Frontend)
|--------------------------------------------------------------------------
*/
Route::localized(function () {

    $controller = \RealEstate\Controllers\Web\PropertyController::class;

    // Danh sách BĐS bán
    Route::match(['get', 'post'], config('real-estate::routes.sell').'/{segment1?}/{segment2?}', $controller . '@all')
        ->defaults('type', 'sell')
        ->name('property.index.sell');

    // Danh sách BĐS cho thuê
    Route::match(['get', 'post'], config('real-estate::routes.rent').'/{segment1?}/{segment2?}', $controller . '@all')
        ->defaults('type', 'rent')
        ->name('property.index.rent');

    // Bộ sưu tập BĐS
    Route::match(['get', 'post'], config('real-estate::routes.collection'), $controller . '@collection')
        ->name('property.index.collection');

    // BĐS yêu thích
    Route::match(['get', 'post'], config('real-estate::routes.wish-list'), $controller . '@wishlist')
        ->name('property.index.wishlist');
});



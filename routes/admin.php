<?php

use SkillDo\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes - Property
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin/property')->group(function () {
    $controller = \RealEstate\Controllers\Admin\PropertyController::class;
    Route::match(['get', 'post'], '/', $controller . '@index')->name('admin.property.index');
    Route::match(['get', 'post'], '/add', $controller . '@add')->name('admin.property.add');
    Route::match(['get', 'post'], '/edit/{id}', $controller . '@edit')->where('id', '[0-9]+')->name('admin.property.edit');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Property Categories
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->group(function () {
    $controller = \RealEstate\Controllers\Admin\PropertyCategoryController::class;
    Route::match(['get', 'post'], 'admin/property-sell-categories/{param1?}/{param2?}', $controller . '@sell')->name('admin.property.category.sell');
    Route::match(['get', 'post'], 'admin/property-rent-categories/{param1?}/{param2?}', $controller . '@rent')->name('admin.property.category.rent');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Features (Tiện ích & Nội thất)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin/re-features')->group(function () {
    $controller = \RealEstate\Controllers\Admin\FeaturesController::class;
    Route::match(['get', 'post'], '/', $controller . '@index')->name('admin.re-features.index');
    Route::match(['get', 'post'], '/add', $controller . '@add')->name('admin.re-features.add');
    Route::match(['get', 'post'], '/edit/{id}', $controller . '@edit')->where('id', '[0-9]+')->name('admin.re-features.edit');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Booking
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin/re-booking')->group(function () {
    $controller = \RealEstate\Controllers\Admin\BookingController::class;
    Route::match(['get', 'post'], '/property', $controller . '@property')->name('admin.re-booking.property');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Property Feedback
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->prefix('admin/re-feedback')->group(function () {
    $controller = \RealEstate\Controllers\Admin\PropertyFeedbackController::class;
    Route::match(['get', 'post'], '/', $controller . '@index')->name('admin.re-feedback.index');
});



<?php
namespace RealEstate\Controllers\Admin;

use Admin\Supports\FormAdminHelper;
use RealEstate\Models\PropertyCategory;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class PropertyCategoryController extends Controller
{
    public function sell(Request $request)
    {
        Cms::setData('module', 'property_categories_sell');

        $keyword = $request->input('keyword');

        $model   = PropertyCategory::where('type', 'sell')
                                    ->orderBy('order')
                                    ->orderBy('created', 'desc');

        if (!empty($keyword)) $model->where('name', 'like', '%' . $keyword . '%');

        Cms::setData('objects', $model->multilevel());

        Cms::setData('form', FormAdminHelper::getForm('property_categories_sell'));

        return Cms::view('real-estate::admin/property-categories/index');
    }

    public function rent(Request $request)
    {
        Cms::setData('module', 'property_categories_rent');

        $keyword = $request->input('keyword');

        $model   = PropertyCategory::where('type', 'rent')->orderBy('order')->orderBy('created', 'desc');

        if (!empty($keyword)) $model->where('name', 'like', '%' . $keyword . '%');

        Cms::setData('objects', $model->multilevel());

        Cms::setData('form', FormAdminHelper::getForm('property_categories_rent'));

        return Cms::view('real-estate::admin/property-categories/index');
    }
}


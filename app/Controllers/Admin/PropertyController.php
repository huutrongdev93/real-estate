<?php
namespace RealEstate\Controllers\Admin;

use Admin\Supports\FormAdminHelper;
use RealEstate\Models\Property;
use RealEstate\Modules\Admin\Property\Table;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Language;
use SkillDo\Http\Request;

class PropertyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Cms::setData('module', 'property');
    }

    public function index(Request $request)
    {
        Cms::setData('table', new Table());
        return Cms::view('real-estate::admin/property/index');
    }

    public function add(Request $request)
    {
        Cms::setData('form', FormAdminHelper::getForm('property'));
        return Cms::view('real-estate::admin/property/save');
    }

    public function edit(Request $request, int $id)
    {
        $object = Cache::remember('property_detail_' . md5($id) . '_' . Language::current(), config('cms.cache_time.default', 60), function () use ($id) {
            return Property::whereKey($id)->first();
        });

        if (noItems($object))
        {
            return Admin::pageNotFound();
        }

        if (!empty($object->type))
        {
            if ($object->type == 'sell')
            {
                $object->category_sell_id = $object->category_id;
            }

            if ($object->type == 'rent')
            {
                $object->category_rent_id = $object->category_id;
            }
        }

        Cms::setData('object', $object);

        Cms::setData('form', FormAdminHelper::getForm('property', $object));

        return Cms::view('real-estate::admin/property/save');
    }
}


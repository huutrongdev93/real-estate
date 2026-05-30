<?php
namespace RealEstate\Controllers\Admin;

use Admin\Supports\FormAdminHelper;
use RealEstate\Models\Features;
use RealEstate\Modules\Admin\Features\Table;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class FeaturesController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        Cms::setData('module', 're_features');
    }

    public function index(Request $request)
    {
        Cms::setData('table', new Table());

        return Cms::view('real-estate::admin/features/index');
    }

    public function add(Request $request)
    {
        Cms::setData('form', FormAdminHelper::getForm('re_features'));

        return Cms::view('real-estate::admin/features/save');
    }

    public function edit(Request $request, int $id)
    {
        $object = Features::find($id);

        if (noItems($object)) {
            return Admin::pageNotFound();
        }

        Cms::setData('object', $object);

        Cms::setData('form', FormAdminHelper::getForm('re_features', $object));

        return Cms::view('real-estate::admin/features/save');
    }
}


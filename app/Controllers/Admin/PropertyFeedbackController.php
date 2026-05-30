<?php
namespace RealEstate\Controllers\Admin;

use RealEstate\Modules\Admin\PropertyFeedback\Table;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class PropertyFeedbackController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Cms::setData('module', 're_feedback');
    }

    public function index(Request $request)
    {
        Cms::setData('table', new Table());
        return Cms::view('real-estate::admin/property-feedback/index');
    }
}


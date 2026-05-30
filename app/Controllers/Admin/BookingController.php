<?php
namespace RealEstate\Controllers\Admin;

use RealEstate\Modules\Admin\Booking\Table;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Cms::setData('module', 're_booking');
    }

    public function property(Request $request)
    {
        Cms::setData('booking_type', 'property');
        Cms::setData('table', new Table());
        return Cms::view('real-estate::admin/booking/index');
    }
}


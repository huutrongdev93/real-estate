<?php
namespace RealEstate\Ajax\Web;

use Illuminate\Support\Str;
use RealEstate\Models\Booking;
use RealEstate\Models\Property;
use RealEstate\Models\PropertyCategory;
use RealEstate\Models\PropertyFeedback;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Models\User;
use SkillDo\Http\Request;
use SkillDo\Support\Auth;
use SkillDo\Support\Cookie;
use SkillDo\Validate\Rule;

class PropertyAjax
{
    static function getLocations(Request $request): void
    {
        $wards  = \SkillDo\Cms\Location\Location2::wards();

        $citiesData    = [];

        $wardsData = [];

        if (hasItems($wards))
        {
            foreach ($wards as $city)
            {
                $citiesData[] = [
                    'id'       => $city->slugId,
                    'fullname' => $city->fullname,
                    'active'   => true,
                ];

                $wardsData[$city->slugId] = [
                    'id'        => $city->slugId,
                    'wards' => [],
                ];

                foreach ($city->wards as $ward)
                {
                    $wardsData[$city->slugId]['wards'][] = [
                        'id'       => $ward->slugId,
                        'fullname' => $ward->fullname,
                    ];
                }
            }
        }

        response()->success(trans('ajax.load.success'), [
            'cities'    => $citiesData,
            'wards' => $wardsData,
        ]);
    }

    static function wards(Request $request): void
    {
        $options = '<option value="">' . trans('real-estate::property.ward.select') . '</option>';

        if ($request->has('provinceId'))
        {
            $provinceId = (int) $request->input('provinceId');

            $wardId     = (int) $request->input('wardId');

            $wards      = Location2::wardsOptions($provinceId);

            if (hasItems($wards))
            {
                foreach ($wards as $key => $name)
                {
                    $options .= '<option value="' . $key . '" ' . (($wardId == $key) ? 'selected' : '') . '>' . $name . '</option>';
                }
            }
        }

        response()->success(trans('ajax.load.success'), $options);
    }

    static function getCategories(Request $request): void
    {
        $object = $request->input('object');

        $data   = [];

        if (!empty($object))
        {
            $categories = [];

            if ($object == 'property')
            {
                $type       = $request->input('type');

                $categories = PropertyCategory::where('type', $type)->get();
            }

            if (have_posts($categories)) {
                $categories = $categories->map(fn($item) => ['id' => $item->id, 'name' => $item->name, 'slug' => $item->slug])->toArray();
            }
            $data['categories'] = $categories;
        } else {
            $sell = PropertyCategory::where('type', 'sell')->get();
            $rent = PropertyCategory::where('type', 'rent')->get();

            $data['propertySellCategories'] = have_posts($sell)
                ? $sell->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'slug' => $i->slug])->toArray() : [];
            $data['propertyRentCategories'] = have_posts($rent)
                ? $rent->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'slug' => $i->slug])->toArray() : [];
        }

        response()->success(trans('ajax.load.success'), $data);
    }

    static function property(Request $request): void
    {
        $type  = $request->input('type');

        $url   = $request->input('url');

        $query = PropertyHelper::getControllerQuery($request, Property::where('public', 1)->where('type', $type));

        $query->orderBy('created', 'desc');

        $total = (clone $query)->count();

        $urlData = explode('?', $url);

        if (count($urlData) == 2)
        {
            $params = preg_replace('/page=\d+/', '', $urlData[1]);
            $params = trim($params, '&');
            $url    = $urlData[0] . '?page={page}&' . $params;
        }
        else
        {
            $url = $url . '?page={page}';
        }

        $pagination = apply_filters('property_controllers_index_paging', pagination($total, $url, 20));

        $query->limit(20)
            ->offset($pagination->offset())
            ->orderBy('property.order')
            ->orderBy('property.created', 'desc');

        $objects = $query->get();

        $html    = '';

        foreach ($objects as $project)
        {
            $html .= view('real-estate::property/loop/item', ['item' => $project]);
        }

        response()->success(trans('ajax.load.success'), [
            'html'       => base64_encode($html),
            'total'      => $total,
            'pagination' => base64_encode($pagination->frontend()),
        ]);
    }

    static function booking(Request $request): void
    {
        $validate = $request->validate([
            'object_id'   => Rule::make('object_id')->notEmpty(),
            'object_type' => Rule::make('object_type')->notEmpty(),
            'name'        => Rule::make(trans('real-estate::booking.table.name'))->notEmpty(),
            'phone'       => Rule::make(trans('real-estate::booking.table.phone'))->notEmpty(),
            'email'       => Rule::make(trans('real-estate::booking.table.email'))->notEmpty()->email(),
        ]);

        if ($validate->fails()) response()->error($validate->errors());

        $objectId   = $request->input('object_id');

        $objectType = $request->input('object_type');

        $url        = $request->input('url');

        if (!empty($url))
        {
            $url = trim(trim(str_replace(\Url::base(), '', $url)), '/');
        }

        $object = null;

        if ($objectType == 'property')
        {
            $object = Property::find($objectId);
        }

        if (!hasItems($object))
        {
            response()->error(trans('real-estate::ajax.booking.not_found'));
        }

        $error = Booking::insert([
            'name'        => trim($request->input('name')),
            'phone'       => trim($request->input('phone')),
            'email'       => trim($request->input('email')),
            'url'         => $url,
            'object_type' => $objectType,
            'object_id'   => $object->id,
            'object_name' => $object->name,
        ]);

        if (is_skd_error($error)) response()->error($error);

        $count = Booking::where('object_type', 'property')->where('read', 0)->count();

        Cache::save('re_booking_new_count', $count);

        response()->success(trans('ajax.save.success'));
    }

    static function wishlist(Request $request): void
    {
        $propertyId = $request->input('id');

        if (empty($propertyId)) response()->error(trans('real-estate::ajax.wishlist.not_found'));

        $isLoggedIn = Auth::check();

        if ($isLoggedIn)
        {
            $userID       = Auth::id();
            $userWishlist = User::getMeta($userID, 'property_wish_list', true) ?: [];
        }
        else
        {
            $userWishlist = Cookie::has('property_wish_list')
                ? (json_decode(Cookie::get('property_wish_list'), true) ?: [])
                : [];
        }

        if (!empty($userWishlist[$propertyId]))
        {
            unset($userWishlist[$propertyId]);
            $message = trans('real-estate::ajax.wishlist.remove');
            $status  = 'remove';
        }
        else
        {
            $userWishlist[$propertyId] = $propertyId;
            $message = trans('real-estate::ajax.wishlist.add');
            $status  = 'add';
        }

        if ($isLoggedIn)
        {
            User::updateMeta($userID, 'property_wish_list', $userWishlist);
        }
        else
        {
            Cookie::queue('property_wish_list', json_encode($userWishlist), 30 * 24 * 60);
        }

        response()->success($message, ['status' => $status]);
    }

    static function feedback(Request $request): void
    {
        if (!$request->isMethod('post')) return;

        $validate = $request->validate([
            'feedback_topic' => Rule::make(trans('real-estate::property-feedback.table.topic'))->notEmpty(),
            'phone'          => Rule::make(trans('real-estate::booking.table.phone'))->notEmpty()->phone(['vi']),
            'email'          => Rule::make(trans('real-estate::booking.table.email'))->notEmpty()->email(),
        ]);

        if ($validate->fails()) response()->error($validate->errors());

        $data = $request->input();

        PropertyFeedback::insert([
            'name'        => trim($data['name'] ?? ''),
            'phone'       => trim($data['phone']),
            'email'       => trim($data['email']),
            'topic'       => is_array($data['feedback_topic']) ? implode(', ', $data['feedback_topic']) : $data['feedback_topic'],
            'feedback'    => trim($data['feedback'] ?? ''),
            'url'         => trim($data['url'] ?? ''),
            'object_id'   => trim($data['object_id'] ?? ''),
            'object_name' => trim($data['object_name'] ?? ''),
        ]);

        $count = PropertyFeedback::where('read', 0)->count();

        Cache::save('re_feedback_new_count', $count);

        response()->success(trans('real-estate::ajax.feedback.success'));
    }
}


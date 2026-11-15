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

    /**
     * Giới hạn tần suất theo IP: 5 lần / 10 phút cho mỗi loại form (tính cả lần lỗi).
     * Lõi cũ chưa có RateLimiter thì bỏ qua.
     */
    protected static function throttle(Request $request, string $name): void
    {
        if (!class_exists(\SkillDo\Support\RateLimiter::class)) return;

        $key = \SkillDo\Support\RateLimiter::key('real-estate-' . $name, (string) $request->ip());

        if (\SkillDo\Support\RateLimiter::tooManyAttempts($key, 5))
        {
            response()->error(trans('real-estate::ajax.too_many'));
        }

        \SkillDo\Support\RateLimiter::hit($key, 600);
    }

    /**
     * Chỉ nhận URL cùng host với site (trả về dạng đường dẫn tương đối); khác host thì bỏ.
     */
    protected static function localUrl($url): string
    {
        $url = is_string($url) ? trim($url) : '';

        if ($url === '') return '';

        $parts = parse_url($url);

        if ($parts === false) return '';

        if (!empty($parts['scheme']) || !empty($parts['host']))
        {
            $siteHost = parse_url(\Url::base(), PHP_URL_HOST);

            if (empty($parts['host']) || strcasecmp((string) $parts['host'], (string) $siteHost) !== 0) return '';

            if (!in_array(strtolower($parts['scheme'] ?? 'http'), ['http', 'https'], true)) return '';
        }

        $path = trim((string) ($parts['path'] ?? ''), '/');

        if (!empty($parts['query'])) $path .= '?' . $parts['query'];

        return mb_substr($path, 0, 255);
    }

    static function booking(Request $request): void
    {
        static::throttle($request, 'booking');

        $validate = $request->validate([
            'object_id'   => Rule::make('object_id')->notEmpty(),
            'object_type' => Rule::make('object_type')->notEmpty(),
            'name'        => Rule::make(trans('real-estate::booking.table.name'))->notEmpty(),
            'phone'       => Rule::make(trans('real-estate::booking.table.phone'))->notEmpty(),
            'email'       => Rule::make(trans('real-estate::booking.table.email'))->notEmpty()->email(),
        ]);

        if ($validate->fails()) response()->error($validate->errors());

        $objectId   = (int) $request->input('object_id');

        $objectType = $request->input('object_type');

        $url        = static::localUrl($request->input('url'));

        $object = null;

        if ($objectType == 'property' && $objectId > 0)
        {
            $object = Property::find($objectId);
        }

        if (!hasItems($object))
        {
            response()->error(trans('real-estate::ajax.booking.not_found'));
        }

        $error = Booking::insert([
            'name'        => mb_substr(trim((string) $request->input('name')), 0, 100),
            'phone'       => mb_substr(trim((string) $request->input('phone')), 0, 20),
            'email'       => mb_substr(trim((string) $request->input('email')), 0, 100),
            'url'         => $url,
            'object_type' => $objectType,
            'object_id'   => $object->id,
            'object_name' => $object->name,
        ]);

        if (is_skd_error($error)) response()->error($error);

        $count = Booking::where('object_type', 'property')->where('read', 0)->count();

        Cache::save('re_booking_new_count', $count);

        response()->success(trans('real-estate::ajax.booking.success'));
    }

    static function wishlist(Request $request): void
    {
        // Ép số nguyên: id này được dùng làm khoá mảng và ghi vào cookie / user meta.
        $propertyId = (int)$request->input('id');

        if ($propertyId <= 0) response()->error(trans('real-estate::ajax.wishlist.not_found'));

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
            // Giới hạn kích thước danh sách để cookie / meta không phình vô hạn.
            if (count($userWishlist) >= 200) response()->error(trans('real-estate::ajax.wishlist.not_found'));

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

        static::throttle($request, 'feedback');

        $validate = $request->validate([
            'feedback_topic' => Rule::make(trans('real-estate::property-feedback.table.topic'))->notEmpty(),
            'phone'          => Rule::make(trans('real-estate::booking.table.phone'))->notEmpty()->phone(['vi']),
            'email'          => Rule::make(trans('real-estate::booking.table.email'))->notEmpty()->email(),
        ]);

        if ($validate->fails()) response()->error($validate->errors());

        $data = $request->input();

        // object_id phải là bất động sản có thật; tên lấy từ DB, không tin object_name do client gửi
        $objectId = (int) ($data['object_id'] ?? 0);

        $property = ($objectId > 0) ? Property::find($objectId) : null;

        if (!hasItems($property))
        {
            response()->error(trans('real-estate::ajax.booking.not_found'));
        }

        $topic = is_array($data['feedback_topic']) ? implode(', ', array_map('strval', $data['feedback_topic'])) : (string) $data['feedback_topic'];

        PropertyFeedback::insert([
            'name'        => mb_substr(trim((string) ($data['name'] ?? '')), 0, 100),
            'phone'       => mb_substr(trim((string) $data['phone']), 0, 20),
            'email'       => mb_substr(trim((string) $data['email']), 0, 100),
            'topic'       => mb_substr($topic, 0, 255),
            'feedback'    => mb_substr(trim((string) ($data['feedback'] ?? '')), 0, 2000),
            'url'         => static::localUrl($data['url'] ?? ''),
            'object_id'   => $property->id,
            'object_name' => $property->name,
        ]);

        $count = PropertyFeedback::where('read', 0)->count();

        Cache::save('re_feedback_new_count', $count);

        response()->success(trans('real-estate::ajax.feedback.success'));
    }
}


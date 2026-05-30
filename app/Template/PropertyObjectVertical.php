<?php
namespace RealEstate\Template;

use RealEstate\Models\Property;
use SkillDo\Cms\Models\GalleryItem;
use SkillDo\Cms\Models\User;
use SkillDo\Support\Auth;
use SkillDo\Support\Cookie;

class PropertyObjectVertical
{
    static public function image($item): void
    {
        $galleries = GalleryItem::where('object_id', $item->id)
            ->where('object_type', 'property')
            ->orderBy('order')
            ->get();

        echo view('real-estate::property/object-vertical/image', [
            'item'      => $item,
            'galleries' => $galleries,
        ]);
    }

    static public function image_gallery($item): void
    {
        $galleries = GalleryItem::where('object_id', $item->id)
            ->where('object_type', 'property')
            ->orderBy('order')
            ->get();

        $count = $galleries->count() + (empty($item->image) ? 0 : 1);

        echo view('real-estate::property/object-vertical/image-gallery', [
            'item'      => $item,
            'galleries' => $galleries,
            'count'     => $count,
        ]);
    }

    static public function status($item): void
    {
        echo view('real-estate::property/object-vertical/status', ['item' => $item]);
    }

    static public function name($item): void
    {
        echo view('real-estate::property/object-vertical/name', ['item' => $item]);
    }

    static public function information($item): void
    {
        $location = '';

        if (!empty($item->ward))
        {
            $location .= \SkillDo\Cms\Location\Location2::wardName($item->city, $item->ward) . ', ';
        }

        if (!empty($item->city))
        {
            $location .= \SkillDo\Cms\Location\Location2::provinceName($item->city);
        }

        $item->address = trim($location, ', ');

        echo view('real-estate::property/object-vertical/information', ['item' => $item]);
    }

    static public function description($item): void
    {
        echo view('real-estate::property/object-vertical/description', ['item' => $item]);
    }

    static public function footer($item): void
    {
        $status = 'unlike';

        if (Auth::check())
        {
            $userID     = Auth::id();
            $wishListId = User::getMeta($userID, 'property_wish_list', true);
        }
        else
        {
            $wishListId = Cookie::has('property_wish_list')
                ? (json_decode(Cookie::get('property_wish_list'), true) ?: [])
                : [];
        }

        if (isset($wishListId[$item->id]))
        {
            $status = 'like';
        }

        $broker     = [];

        $userBroker = User::find($item->user_created == 1 ? 2 : $item->user_created);

        if (have_posts($userBroker))
        {
            $broker = [
                'title' => trans('real-estate::property.detail.broker_contact'),
                'name'  => $userBroker->firstname . ' ' . $userBroker->lastname,
                'phone' => $userBroker->phone ?? '',
                'email' => $userBroker->email ?? '',
            ];
        }

        echo view('real-estate::property/object-vertical/footer', [
            'item'   => $item,
            'class'  => $status,
            'broker' => $broker,
        ]);
    }
}

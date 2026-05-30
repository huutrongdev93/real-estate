<?php
namespace RealEstate\Template;

use RealEstate\Models\Property;
use SkillDo\Cms\Models\GalleryItem;
use SkillDo\Cms\Models\User;
use SkillDo\Support\Auth;
use SkillDo\Support\Cookie;

class PropertyObject
{
    static public function image($item): void
    {
        $galleries = GalleryItem::where('object_id', $item->id)
            ->where('object_type', 'property')
            ->orderBy('order')
            ->get();

        echo view('real-estate::property/object/image', ['item' => $item, 'galleries' => $galleries]);
    }

    static public function image_gallery($item): void
    {
        $galleries = GalleryItem::where('object_id', $item->id)
            ->where('object_type', 'property')
            ->orderBy('order')
            ->get();

        $count = $galleries->count() + (empty($item->image) ? 0 : 1);

        echo view('real-estate::property/object/image-gallery', [
            'item'      => $item,
            'galleries' => $galleries,
            'count'     => $count,
        ]);
    }

    static public function status($item): void
    {
        echo view('real-estate::property/object/status', ['item' => $item]);
    }

    static public function price($item): void
    {
        echo view('real-estate::property/object/price', ['item' => $item]);
    }

    static public function name($item): void
    {
        echo view('real-estate::property/object/name', ['item' => $item]);
    }

    static public function address($item): void
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

        echo view('real-estate::property/object/address', ['item' => $item]);
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

        echo view('real-estate::property/object/footer', ['item' => $item, 'class' => $status]);
    }
}

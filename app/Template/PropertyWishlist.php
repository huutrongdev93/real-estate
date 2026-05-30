<?php
namespace RealEstate\Template;

use SkillDo\Cms\Support\Cms;

class PropertyWishlist
{
    static function layout(): void
    {
        echo view('real-estate::property/wishlist/layout');
    }

    static function heading(): void
    {
        echo view('real-estate::property/wishlist/heading');
    }

    static function list(): void
    {
        echo view('real-estate::property/wishlist/list', [
            'objects' => Cms::getData('objects') ?? [],
        ]);
    }
}

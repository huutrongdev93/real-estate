<?php
namespace RealEstate\Supports;

use Illuminate\Support\Arr;
use SkillDo\Cms\Location\Location2;
use SkillDo\Support\Auth;

class RealEstateHelper
{
    static function convertStringAuth(string $str = ''): string
    {
        if (!Auth::check() && strlen($str) != 0) {
            $str = str_replace([',', '.'], '', $str);
            return str_repeat('x', strlen($str));
        }
        return $str;
    }

    static function getPrice($price = 0): string
    {
        $unit = 'vnđ';
        if ($price >= 1000000000)
        {
            $price = $price / 1000000000;
            $unit  = ' tỷ';
        }
        elseif ($price >= 1000000)
        {
            $price = $price / 1000000;
            $unit  = ' triệu';
        }
        return $price . $unit;
    }

    static function getPriceAuth($price = 0): string
    {
        $unit = 'vnđ';
        if ($price >= 1000000000) {
            $price = $price / 1000000000;
            $unit  = ' tỷ';
        } elseif ($price >= 1000000) {
            $price = $price / 1000000;
            $unit  = ' triệu';
        }

        if (!Auth::check()) {
            return self::convertStringAuth($price) . $unit;
        }
        return $price . $unit;
    }

    static function getUnit(string $key = '')
    {
        $units = [
            'month' => trans('real-estate::property.unit.month'),
            'year'  => trans('real-estate::property.unit.year'),
            'm2'    => 'm2',
            'base'  => trans('real-estate::property.unit.base'),
        ];

        if (!empty($key))
        {
            if ($key == 'option') return array_merge(['' => trans('real-estate::property.unit.select')], $units);

            return Arr::get($units, $key);
        }
        return $units;
    }

    static function getLocation($property): string
    {
        $location = $property->address . ',';
        if (!empty($property->ward)) $location .= Location2::wardName($property->city, $property->ward) . ',';
        if (!empty($property->city)) $location .= Location2::provinceName($property->city);
        return trim($location, ',');
    }

    static function getShortLocation($property): string
    {
        $location = '';
        if (!empty($property->ward)) $location .= Location2::wardName($property->city, $property->ward) . ',';
        if (!empty($property->city)) $location .= Location2::provinceName($property->city);
        return trim($location, ',');
    }

    static function timeElapsed(string $datetime, bool $full = false): string
    {
        $now  = new \DateTime;
        $ago  = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff  = (object) [
            'y' => $diff->y, 'm' => $diff->m, 'd' => $diff->d,
            'h' => $diff->h, 'i' => $diff->i, 's' => $diff->s,
            'w' => floor($diff->d / 7),
        ];
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => trans('real-estate::property.time.year'),
            'm' => trans('real-estate::property.time.month'),
            'w' => trans('real-estate::property.time.week'),
            'd' => trans('real-estate::property.time.day'),
            'h' => trans('real-estate::property.time.hour'),
            'i' => trans('real-estate::property.time.minute'),
            's' => trans('real-estate::property.time.second'),
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) : '';
    }

    static function collections(?string $key = null): mixed
    {
        $collections = [
            'status1' => [
                'name'         => apply_filters('properties_collections_status1_name', trans('real-estate::property.collection.potential')),
                'searchKey'    => apply_filters('properties_collections_status1_search_key', 'potential'),
                'searchActive' => apply_filters('properties_collections_status1_search_active', false),
            ],
            'status2' => [
                'name'         => apply_filters('properties_collections_status2_name', trans('real-estate::property.collection.love')),
                'searchKey'    => apply_filters('properties_collections_status2_search_key', 'love'),
                'searchActive' => apply_filters('properties_collections_status2_search_active', true),
            ],
            'status3' => [
                'name'         => apply_filters('properties_collections_status3_name', trans('real-estate::property.collection.hot')),
                'searchKey'    => apply_filters('properties_collections_status3_search_key', 'hot'),
                'searchActive' => apply_filters('properties_collections_status3_search_active', true),
            ],
        ];

        $collections = apply_filters('properties_collections', $collections);

        if ($key !== null) return Arr::get($collections, $key);

        return $collections;
    }
}


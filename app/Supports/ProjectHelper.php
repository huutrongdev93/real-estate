<?php
namespace RealEstate\Supports;

use Illuminate\Support\Arr;

class ProjectHelper
{
    static public function getProcess(string $key = '')
    {
        $data = [
            1 => trans('real-estate::project.process.coming_soon'),
            2 => trans('real-estate::project.process.selling'),
            3 => trans('real-estate::project.process.completed'),
        ];
        if (!empty($key)) {
            if ($key == 'option') return array_merge([0 => trans('real-estate::project.process.select')], $data);
            return Arr::get($data, $key);
        }
        return $data;
    }

    static public function getYear(string $key = '')
    {
        $current = date('Y') + 1;
        $data    = [];
        for ($i = $current; $i >= ($current - 30); $i--) {
            $data[$i] = $i;
        }
        if (!empty($key)) {
            if ($key == 'option') return array_merge(['' => trans('real-estate::project.year.select')], $data);
            return Arr::get($data, $key);
        }
        return $data;
    }

    static public function getPriceFull($price_min = 0, $price_max = 0, $unit = 0): string
    {
        $price = '';
        if (empty($price_min) && empty($price_max)) return trans('real-estate::project.price.updating');

        if (($price_min == 0 && $price_max != 0) || ($price_min != 0 && $price_max != 0)) {
            $price = trans('real-estate::project.price.from', [
                'min' => RealEstateHelper::getPrice($price_min),
                'max' => RealEstateHelper::getPrice($price_max),
            ]);
        } elseif ($price_min != 0) {
            $price = RealEstateHelper::getPrice($price_min);
        }

        if (!empty($unit)) $price .= '/' . RealEstateHelper::getUnit($unit);
        return $price;
    }

    static public function getPriceFullAuth($price_min = 0, $price_max = 0, $unit = 0): string
    {
        $price = '';
        if (empty($price_min) && empty($price_max)) return trans('real-estate::project.price.updating');

        if (($price_min == 0 && $price_max != 0) || ($price_min != 0 && $price_max != 0)) {
            $price = trans('real-estate::project.price.from', [
                'min' => RealEstateHelper::getPriceAuth($price_min),
                'max' => RealEstateHelper::getPriceAuth($price_max),
            ]);
        } elseif ($price_min != 0) {
            $price = RealEstateHelper::getPriceAuth($price_min);
        }

        if (!empty($unit)) $price .= '/' . RealEstateHelper::getUnit($unit);
        return $price;
    }
}


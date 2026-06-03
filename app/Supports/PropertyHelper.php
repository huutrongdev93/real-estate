<?php
namespace RealEstate\Supports;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Url;
use SkillDo\Http\Request;

class PropertyHelper
{
    static public function getBedroom(string $key = '')
    {
        $data = [
            '0'   => '0 ' . trans('real-estate::property.bedroom'),
            '1'   => '1 ' . trans('real-estate::property.bedroom'),
            '2'   => '2 ' . trans('real-estate::property.bedroom'),
            '3'   => '3 ' . trans('real-estate::property.bedroom'),
            '4'   => '4 ' . trans('real-estate::property.bedroom'),
            '5'   => '5 ' . trans('real-estate::property.bedroom'),
            '6'   => '6 ' . trans('real-estate::property.bedroom'),
            '100' => trans('real-estate::property.bedroom.more'),
        ];

        if (!empty($key)) return Arr::get($data, $key);

        return $data;
    }

    static public function getBathroom(string $key = '')
    {
        $data = [
            '0'   => '0 ' . trans('real-estate::property.bathroom'),
            '1'   => '1 ' . trans('real-estate::property.bathroom'),
            '2'   => '2 ' . trans('real-estate::property.bathroom'),
            '3'   => '3 ' . trans('real-estate::property.bathroom'),
            '4'   => '4 ' . trans('real-estate::property.bathroom'),
            '5'   => '5 ' . trans('real-estate::property.bathroom'),
            '6'   => '6 ' . trans('real-estate::property.bathroom'),
            '100' => trans('real-estate::property.bathroom.more'),
        ];

        if (!empty($key)) return Arr::get($data, $key);

        return $data;
    }

    static public function getType(string $key = '')
    {
        $data = [
            'sell' => trans('real-estate::property.type.sell'),
            'rent' => trans('real-estate::property.type.rent'),
        ];

        if (!empty($key))
        {
            if ($key == 'option') return array_merge([0 => trans('real-estate::property.type.select')], $data);

            return Arr::get($data, $key);
        }
        return $data;
    }

    static public function getStatus(string $key = '')
    {
        $data = [
            'not-appraised' => trans('real-estate::property.status.not_appraised'),
            'appraised'     => trans('real-estate::property.status.appraised'),
            'sold'          => trans('real-estate::property.status.sold'),
        ];
        if (!empty($key)) {
            if ($key == 'option') return array_merge([0 => trans('real-estate::property.status.select')], $data);
            return Arr::get($data, $key);
        }
        return $data;
    }

    static public function getPropertyType(string $key = '')
    {
        $data = [
            1 => trans('real-estate::property.property_type.apartment'),
            2 => trans('real-estate::property.property_type.house'),
            3 => trans('real-estate::property.property_type.land'),
        ];
        if (!empty($key)) return Arr::get($data, $key);
        return $data;
    }

    static public function getDirection(string $key = '')
    {
        $data = [
            1 => trans('real-estate::property.direction.east'),
            2 => trans('real-estate::property.direction.west'),
            3 => trans('real-estate::property.direction.south'),
            4 => trans('real-estate::property.direction.north'),
            5 => trans('real-estate::property.direction.northeast'),
            6 => trans('real-estate::property.direction.northwest'),
            7 => trans('real-estate::property.direction.southeast'),
            8 => trans('real-estate::property.direction.southwest'),
        ];
        if (!empty($key)) {
            if ($key == 'option') return array_merge([0 => trans('real-estate::property.direction.select')], $data);
            return Arr::get($data, $key);
        }
        return $data;
    }

    static public function getLocation(string $key = '')
    {
        $data = [
            0        => trans('real-estate::property.location.not_updated'),
            'facade' => trans('real-estate::property.location.facade'),
            'alley'  => trans('real-estate::property.location.alley'),
        ];
        if (!empty($key)) return Arr::get($data, $key);

        return $data;
    }

    static public function getPriceFull($price = 0, $unit = 0): string
    {
        $priceStr = RealEstateHelper::getPrice($price);

        if (!empty($unit))
        {
            $priceStr .= '/' . RealEstateHelper::getUnit($unit);
        }

        return $priceStr;
    }

    static public function getPriceFullAuth($price = 0, $unit = 0): string
    {
        $priceStr = RealEstateHelper::getPriceAuth($price);

        if (!empty($unit))
        {
            $priceStr .= '/' . RealEstateHelper::getUnit($unit);
        }

        return $priceStr;
    }

    static public function getFeaturesType(string $key = '')
    {
        $data = [
            'utilities' => trans('real-estate::property.features_type.utilities'),
            'furniture' => trans('real-estate::property.features_type.furniture'),
        ];

        if (!empty($key)) return Arr::get($data, $key);

        return $data;
    }

    static public function getControllerQuery(Request $request, $query, array &$filters = [])
    {
        $keyword = $request->input('keyword');

        if (!empty($keyword))
        {
            $filters['keyword'] = $keyword;

            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $categoryId = $request->input('categoryId');

        if (!empty($categoryId)) $query->where('category_id', $categoryId);

        //Giá
        $price    = trim((string) $request->input('price'));

        $priceMin = 0;

        $priceMax = 0;

        if (!empty($price))
        {
            $filters['price'] = $price;

            if (Str::startsWith($price, 'duoi-'))
            {
                $priceMax = (int) str_replace('-trieu', '', str_replace('duoi-', '', $price)) * 1000000;
            }
            elseif (Str::startsWith($price, 'tren-'))
            {
                $priceMin = (int) str_replace('-trieu', '', str_replace('tren-', '', $price)) * 1000000;
            }
            else
            {
                $parts = explode('-den-', str_replace('-trieu', '', $price));

                if (count($parts) == 2)
                {
                    $priceMin = (int) $parts[0] * 1000000;
                    $priceMax = (int) $parts[1] * 1000000;
                }
            }

            if ($priceMin == 0 && !empty($priceMax))
            {
                $query->where('price', '<=', $priceMax);
            }
            elseif ($priceMax == 0 && !empty($priceMin))
            {
                $query->where('price', '>=', $priceMin);
            }
            elseif (!empty($priceMin) && !empty($priceMax))
            {
                $query->where('price', '>=', $priceMin)->where('price', '<=', $priceMax);
            }
        }

        //Diện tích
        $area    = trim((string) $request->input('area'));

        $areaMin = 0;

        $areaMax = 0;

        if (!empty($area))
        {
            $filters['area'] = $area;
            if (Str::startsWith($area, 'duoi-'))
            {
                $areaMax = (int) str_replace('m2', '', str_replace('duoi-', '', $area));
            }
            elseif (Str::startsWith($area, 'tren-'))
            {
                $areaMin = (int) str_replace('m2', '', str_replace('tren-', '', $area));
            }
            else
            {
                $parts = explode('-den-', str_replace('m2', '', $area));

                if (count($parts) == 2)
                {
                    $areaMin = (int) $parts[0];
                    $areaMax = (int) $parts[1];
                }
            }

            if ($areaMin == 0 && !empty($areaMax))
            {
                $query->where('area', '<=', $areaMax);
            }
            elseif ($areaMax == 0 && !empty($areaMin))
            {
                $query->where('area', '>=', $areaMin);
            }
            elseif (!empty($areaMin) && !empty($areaMax))
            {
                $query->where('area', '>=', $areaMin)->where('area', '<=', $areaMax);
            }
        }

        //Trạng thái
        $status = trim((string) $request->input('status'));
        if (!empty($status))
        {
            $filters['status'] = $status;
            $query->where($status, 1);
        }

        //Phòng ngủ
        $bedroom = (int) trim((string) $request->input('bedroom'));
        if (!empty($bedroom))
        {
            $filters['bedroom'] = $bedroom;
            $query->where('bedroom', $bedroom);
        }

        //Phòng tắm
        $bathroom = (int) trim((string) $request->input('bathroom'));
        if (!empty($bathroom))
        {
            $filters['bathroom'] = $bathroom;
            $query->where('bathroom', $bathroom);
        }

        //Hướng
        $direction = (int) trim((string) $request->input('direction'));
        if (!empty($direction))
        {
            $filters['direction'] = $direction;
            $query->where('direction', $direction);
        }

        //Địa điểm
        $city = trim((string) $request->input('city'));

        if (!empty($city))
        {
            $cityId = Location2::provincesIdBySlug($city);

            if(!empty($cityId))
            {
                $query->where('city', $cityId);

                $ward = trim((string) $request->input('ward'));

                if (!empty($ward))
                {
                    $wardId = Location2::wardIdBySlug($ward, $cityId);

                    if(!empty($wardId))
                    {
                        $query->where('ward', $wardId);
                    }
                }
            }
        }
        else
        {
            $locations = $request->segment(3);

            if (!empty($locations))
            {
                $locations = Str::clear($locations);

                $locations = explode('--', $locations);

                if (!empty($locations[0]))
                {
                    $filters['city'] = Str::upper($locations[0]);

                    $cityId = Location2::provincesIdBySlug($city);

                    if(!empty($cityId))
                    {
                        $query->where('city', $cityId);

                        if(!empty($locations[1]))
                        {
                            $filters['ward'] = Str::upper($locations[1]);

                            $wardId = Location2::wardIdBySlug($locations[1], $cityId);

                            if(!empty($wardId))
                            {
                                $query->where('ward', $wardId);
                            }
                        }
                    }
                }
            }
        }

        return $query;
    }

    static public function getControllerData($slug, $query, array &$filters = []): array
    {
        $query = apply_filters('property_controllers_index_args', $query);

        $total = apply_filters('property_controllers_index_count', (clone($query))->count());

        $filters['total'] = $total;

        $url = Url::base() . Url::permalink($slug) . '?page={page}';

        $pagination = apply_filters('property_controllers_index_paging', pagination($total, $url, Cms::config('client_post_number')));

        $query->limit(Cms::config('client_post_number'))
            ->offset($pagination->offset())
            ->orderBy('property.order')
            ->orderBy('property.created', 'desc');

        $query = apply_filters('property_controllers_index_params', $query);

        return [
            'filters' => apply_filters('property_controllers_index_filters', $filters),
            'objects' => apply_filters('property_controllers_index_objects', $query->get()),
            'pagination' => $pagination,
        ];
    }

    static public function getSearchPriceSell(): array
    {
        return [
            'duoi-500-trieu'       => trans('real-estate::property.price.sell.below_500m'),
            '500-den-800-trieu'    => trans('real-estate::property.price.sell.500_800'),
            '800-den-1000-trieu'   => trans('real-estate::property.price.sell.800_1b'),
            '1000-den-2000-trieu'  => trans('real-estate::property.price.sell.1_2b'),
            '2000-den-3000-trieu'  => trans('real-estate::property.price.sell.2_3b'),
            '3000-den-5000-trieu'  => trans('real-estate::property.price.sell.3_5b'),
            '5000-den-10000-trieu' => trans('real-estate::property.price.sell.5_10b'),
            'tren-10000-trieu'     => trans('real-estate::property.price.sell.above_10b'),
        ];
    }

    static public function getSearchPriceRent(): array
    {
        return [
            'duoi-5-trieu'    => trans('real-estate::property.price.rent.below_5m'),
            '5-den-10-trieu'  => trans('real-estate::property.price.rent.5_10'),
            '10-den-20-trieu' => trans('real-estate::property.price.rent.10_20'),
            '20-den-35-trieu' => trans('real-estate::property.price.rent.20_35'),
            '35-den-50-trieu' => trans('real-estate::property.price.rent.35_50'),
            '50-den-80-trieu' => trans('real-estate::property.price.rent.50_80'),
            'tren-80-trieu'   => trans('real-estate::property.price.rent.above_80'),
        ];
    }
}


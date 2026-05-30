<?php
namespace RealEstate\Ajax\Admin;

use RealEstate\Models\Property;
use SkillDo\Http\Http;
use SkillDo\Http\Request;
use SkillDo\Validate\Rule;

class PropertyAjax
{
    static function saveCollection(Request $request): void
    {
        $validate = $request->validate([
            'propertyId' => Rule::make(trans('real-estate::property.title'))->notEmpty()->integer()->min(1),
            'collection' => Rule::make(trans('real-estate::property.collection.potential'))->notEmpty(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $collection = $request->input('collection');

        $id      = (int)$request->input('propertyId');

        $property = Property::find($id);

        if(!hasItems($property))
        {
            response()->error(trans('real-estate::ajax.property.not_found'));
        }

        if(!isset($property->{$collection}))
        {
            response()->error(trans('real-estate::ajax.property.not_found'));
        }


        $property->{$collection} = ($property->{$collection} == 0) ? 1 : 0;

        $property->save();

        response()->success(trans('ajax.save.success'));
    }

    public static function searchAddress(Request $request): void
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2)
        {
            response()->success('', []);
        }

        try
        {
            $response = Http::withHeaders([
                'Accept-Language' => 'vi',
                'User-Agent'      => 'LocatorStore/1.0',
            ])
                ->timeout(8)
                ->withOptions(['verify' => false])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format'       => 'json',
                    'q'            => $q,
                    'limit'        => 6,
                    'countrycodes' => 'vn',
                ]);

            if (!$response->successful())
            {
                response()->error('Không thể kết nối đến dịch vụ tìm kiếm.');
            }

            $places = $response->json() ?? [];

            $results = array_map(fn($p) => [
                'display_name' => $p['display_name'] ?? '',
                'lat'          => (float) ($p['lat'] ?? 0),
                'lng'          => (float) ($p['lon'] ?? 0),
            ], $places);

            response()->success('', $results);
        }
        catch (\Exception $e)
        {
            response()->error('Lỗi tìm kiếm: ' . $e->getMessage());
        }
    }
}

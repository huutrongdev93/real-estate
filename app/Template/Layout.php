<?php
namespace RealEstate\Template;

use RealEstate\Models\Property;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;

class Layout
{
    static function builderLayoutType($layoutTypes): array
    {
        $layoutTypes['property_all'] = trans('real-estate::property.layout.all');

        $layoutTypes['property_index'] = trans('real-estate::property.layout.index');

        $layoutTypes['property_detail'] = trans('real-estate::property.layout.detail');

        return $layoutTypes;
    }

    static function builderLayoutData($layout): void
    {
        if($layout->element == 'property_all' || $layout->element == 'property_index')
        {
            add_filter('template_get_page', function () use ($layout)
            {
                return $layout->element;
            });

            $filters = [
                'type' => 'sell',
                'keyword'   => '',
                'price'     => '',
                'area'      => '',
                'status'    => '',
                'city'      => '',
                'district'  => '',
                'category'  => '',
                'bedroom'   => '',
                'bathroom'  => '',
                'direction' => '',
            ];

            $query = PropertyHelper::getControllerQuery(request(), Property::where('type', $filters['type']), $filters);

            $data = PropertyHelper::getControllerData(config('real-estate::routes.sell'), $query, $filters);

            Cms::setData('filters', $data['filters']);

            Cms::setData('objects', $data['objects']);

            Cms::setData('category', []);

            Cms::setData('pagination', $data['pagination']);
        }

        if($layout->element == 'property_detail')
        {
            add_filter('template_get_page', function () {
                return 'property_detail';
            });

            $data = Cache::get('builder_layout_property_detail_data');

            if(hasItems($data))
            {
                $object = $data['object'] ?? null;
            }
            else
            {
                $object = Property::first();

                if (hasItems($object) && ($object->public != 1 || $object->trash == 1))
                {
                    $object = [];
                }

                if (noItems($object))
                {
                    Theme::page404();
                }

                $data = [
                    'object' => $object
                ];

                Cache::save('builder_layout_property_detail_data', $data, 24 * 60);
            }

            /**
             * @since 4.0.0
             */

            do_action('controllers_property_detail', $object);

            Cms::setData('object', $object);
        }
    }

    static function layoutIndex(): string
    {
        return 'template-full-width';
    }

    static function viewIndex(): string
    {
        return 'real-estate::property/index';
    }

    static function layoutDetail(): string
    {
        return 'template-full-width';
    }

    static function viewDetail(): string
    {
        return 'real-estate::property/detail';
    }
}

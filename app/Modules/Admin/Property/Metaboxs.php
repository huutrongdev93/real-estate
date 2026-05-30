<?php
namespace RealEstate\Modules\Admin\Property;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use RealEstate\Models\Features;
use RealEstate\Models\Property;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Support\Metabox;
use SkillDo\Http\Request;

class Metaboxs
{
    /**
     * Đăng ký các metabox cho module property
     * Hook: add_meta_box
     */
    static public function register(): void
    {
        Metabox::add('admin_property_specification', trans('real-estate::property.specification'), [self::class, 'specifications'], ['module' => 'property', 'content' => 'tabs']);

        Metabox::add('admin_property_location', trans('real-estate::property.specification.location'), [self::class, 'location'], ['module' => 'property']);

        Metabox::add('admin_property_features', trans('real-estate::property.specification.features'), [self::class, 'features'], ['module' => 'property']);
    }

    static public function location($object): void
    {
        $lat           = '';
        $lng           = '';
        $googleMapLink = '';

        if (hasItems($object))
        {
            $lat           = Property::getMeta($object->id, 'lat', true);
            $lng           = Property::getMeta($object->id, 'lng', true);
            $googleMapLink = Property::getMeta($object->id, 'google_map_link', true);
        }

        $form   = form();

        $states = Location2::provincesOptions();

        $states = Arr::prepend($states, trans('real-estate::property.city.select'), '');

        $form->text('address', ['label' => trans('real-estate::property.address'), 'start' => 4], (hasItems($object) ? $object->address : ''));

        $form->select2('city', ['label' => trans('real-estate::property.city'), 'start' => 4], $object->city ?? '')->options($states);

        $wardOptions = [];

        if (!empty($object->city))
        {
            $wardOptions = Location2::wardsOptions($object->city);
        }

        $form->select2('ward', ['label' => trans('real-estate::property.ward'), 'start' => 4], $object->ward ?? '')->options($wardOptions);

        $form->number('lat', ['label' => 'Vĩ độ (Latitude)', 'start' => 4, 'step' => 'any'], $lat);

        $form->number('lng', ['label' => 'Kinh độ (Longitude)', 'start' => 4, 'step' => 'any'], $lng);

        $form->text('google_map_link', ['label' => 'Link Google Maps', 'start' => 4], $googleMapLink);

        $form->html(false);
    }

    static public function features($object): void
    {
        $featuresType = PropertyHelper::getFeaturesType();
        $form         = form();

        foreach ($featuresType as $type => $item) {
            $features = Features::where('type', $type)->get();
            if (have_posts($features)) {
                $data = [];
                foreach ($features as $value) {
                    $data[$value->id] = $value->name;
                }

                $form->none('<div class="col-md-6">');
                $form->none('<div class="col-md-12"><h5 style="font-size:18px;margin-bottom:20px;">' . $item . '</h5></div>');
                $form->checkbox($type, $data, ['label' => ''], (have_posts($object)) ? Property::getMeta($object->id, $type, true) : []);
                $form->none('</div>');
            }
        }

        $form->html(false);
    }

    static public function specifications($object): void
    {
        if(hasItems($object))
        {
            $moreSpecifications = Property::getMeta($object->id, 'more_specifications');

            $location = Property::getMeta($object->id, 'location');

            $locationDetail = Property::getMeta($object->id, 'location_detail');

            $juridical = Property::getMeta($object->id, 'juridical');
        }

        $form                = form();

        $form->text('area', ['label' => trans('real-estate::property.area')], $object->area ?? '');

        $form->select('bedroom', PropertyHelper::getBedroom(), ['label' => trans('real-estate::property.bedroom'), 'start' => 4], $object->bedroom ?? 1);

        $form->select('bathroom', PropertyHelper::getBathroom(), ['label' => trans('real-estate::property.bathroom'), 'start' => 4], $object->bathroom ?? 4);

        $form->select('direction', PropertyHelper::getDirection('option'), ['label' => trans('real-estate::property.direction'), 'start' => 4], $object->direction ?? '');

        $form->text('juridical', ['label' => trans('real-estate::property.juridical'), 'start' => 4], $juridical ?? '');

        $form->tab('location', ['label' => trans('real-estate::property.location'), 'start' => 4], $location ?? '')->options(PropertyHelper::getLocation());

        $form->number('location_detail', ['label' => trans('real-estate::property.specification.location_detail'), 'start' => 4], $locationDetail ?? '');

        $form->repeater('more_specifications', [
            'label'  => trans('real-estate::property.specification.more'),
            'fields' => [
                ['name' => 'icon',    'type' => 'image', 'label' => trans('real-estate::property.specification.icon'),    'start' => 4],
                ['name' => 'title',   'type' => 'text',  'label' => trans('real-estate::property.specification.title'),   'start' => 4],
                ['name' => 'content', 'type' => 'text',  'label' => trans('real-estate::property.specification.content'), 'start' => 4],
            ],
        ], $moreSpecifications ?? []);

        $form->html(false);
    }

    static public function save(int $id, Request $request): void
    {
        $address  = $request->input('address');

        $city     = (int) $request->input('city');

        $ward     = (int) $request->input('ward');

        $lat           = $request->input('lat');
        $lng           = $request->input('lng');
        $googleMapLink = $request->input('google_map_link');

        Property::insert(['id' => $id, 'address' => $address, 'city' => $city, 'ward' => $ward]);

        foreach (PropertyHelper::getFeaturesType() as $type => $item)
        {
            $features = $request->input($type);

            if (!is_array($features)) $features = [];

            Property::updateMeta($id, $type, $features);
        }

        $more_specifications = $request->input('more_specifications');

        if (have_posts($more_specifications))
        {
            foreach ($more_specifications as &$specification)
            {
                if (isset($specification['icon']))
                {
                    $specification['icon'] = File::clear($specification['icon']);
                }
            }
        }

        Property::updateMeta($id, 'location', $request->input('location'));
        Property::updateMeta($id, 'location_detail', (int) $request->input('location_detail'));
        Property::updateMeta($id, 'juridical', $request->input('juridical'));
        Property::updateMeta($id, 'lat', $lat);
        Property::updateMeta($id, 'lng', $lng);
        Property::updateMeta($id, 'google_map_link', $googleMapLink);
        Property::updateMeta($id, 'more_specifications', $more_specifications);
    }
}


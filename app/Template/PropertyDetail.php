<?php
namespace RealEstate\Template;

use RealEstate\Models\Features;
use RealEstate\Models\Property;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cms\Models\User;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Url;
use SkillDo\Cms\Form\Form;
use SkillDo\Support\Auth;
use SkillDo\Support\Cookie;
use SkillDo\Validate\Rule;

class PropertyDetail
{
    static public function layout($object): void
    {
        $watchedList   = session()->get('viewed_property');

        $watchedList   = empty($watchedList) ? [] : $watchedList;

        $watchedList[] = $id ?? 0;

        $watchedList   = array_unique($watchedList);

        session()->set('viewed_property', $watchedList);

        $object->location        = Property::getMeta($object->id, 'location', true);

        $object->location_detail = Property::getMeta($object->id, 'location_detail', true);

        $object->juridical       = Property::getMeta($object->id, 'juridical', true);

        $object->width       = (int)Property::getMeta($object->id, 'width', true);

        $object->length       = (int)Property::getMeta($object->id, 'length', true);

        echo view('real-estate::property/detail/layout', compact('object'));
    }

    static public function breadcrumb($object): void
    {
        echo view('real-estate::property/detail/breadcrumb', compact('object'));
    }

    static public function slider($object): void
    {
        $galleries = \SkillDo\Cms\Models\GalleryItem::where('object_id', $object->id)
            ->where('object_type', 'property')
            ->orderBy('order')
            ->get();

        echo view('real-estate::property/detail/slider', [
            'galleries' => $galleries,
            'object'    => $object,
        ]);
    }

    static public function info($object): void
    {
        echo view('real-estate::property/detail/info', [
            'object'    => $object,
        ]);
    }

    static public function name($object): void
    {
        echo view('real-estate::property/detail/name', compact('object'));
    }

    static public function address($object): void
    {
        echo view('real-estate::property/detail/address', compact('object'));
    }

    static public function price($object): void
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

        if (isset($wishListId[$object->id]))
        {
            $status = 'like';
        }

        echo view('real-estate::property/detail/price', [
            'object'              => $object,
            'likeStatus'          => $status,
        ]);
    }

    static public function status($object): void
    {
        $locationLabel = '';

        if ($object->property_type == 2 || $object->property_type == 3)
        {
            $locationLabel = trans('real-estate::property.location.not_updated_detail');

            $location      = Property::getMeta($object->id, 'location', true);

            if ($location == 'alley')
            {
                $locationLabel = trans('real-estate::property.location.alley_detail', ['width' => Property::getMeta($object->id, 'location_detail', true)]);
            }

            if ($location == 'facade')
            {
                $locationLabel = trans('real-estate::property.location.facade_house');
            }
        }

        echo view('real-estate::property/detail/status', compact('object', 'locationLabel'));
    }

    /**
     * Đặc điểm bất động sản
     */
    static public function characteristics($object): void
    {
        $specifications = [
            'area' => [
                'label' => trans('real-estate::property.detail.area_label'),
                'value' => $object->area ? $object->area . ' m²' : trans('real-estate::property.detail.not_available'),
                'icon'  => asset('real-estate::images/icon-area.png'),
            ],
            'width' => [
                'label' => trans('real-estate::property.width'),
                'value' => (!empty($object->width)) ? $object->width . ' m' : 0,
                'icon'  => asset('real-estate::images/icon-width.png'),
            ],
            'length' => [
                'label' => trans('real-estate::property.length'),
                'value' => (!empty($object->length)) ? $object->length . ' m' : 0,
                'icon'  => asset('real-estate::images/icon-length.png'),
            ],
            'price' => [
                'label' => trans('real-estate::property.detail.price_label'),
                'value' => PropertyHelper::getPriceFull($object->price, $object->unit),
                'icon'  => asset('real-estate::images/icon-price.png'),
            ],
            'location' => [
                'label' => (!empty($object->location)) ? PropertyHelper::getLocation($object->location): trans('real-estate::property.location'),
                'value' => (!empty($object->location_detail)) ? $object->location_detail.' m²' : '--',
                'icon'  => asset('real-estate::images/icon-highway.png'),
            ],
            'direction' => [
                'label' => trans('real-estate::property.direction'),
                'value' => PropertyHelper::getDirection($object->direction),
                'icon'  => asset('real-estate::images/icon-direction.png'),
            ],
            'bedroom' => [
                'label' => trans('real-estate::property.bedroom'),
                'value' => $object->bedroom ? PropertyHelper::getBedroom($object->bedroom) : trans('real-estate::property.detail.not_available'),
                'icon'  => asset('real-estate::images/icon-bedroom.png'),
            ],
            'bathroom' => [
                'label' => trans('real-estate::property.bathroom'),
                'value' => $object->bathroom ? PropertyHelper::getBathroom($object->bathroom) : trans('real-estate::property.detail.not_available'),
                'icon'  => asset('real-estate::images/icon-bathroom.png'),
            ],
            'juridical' => [
                'label' => trans('real-estate::property.juridical'),
                'value' => $object->juridical,
                'icon'  => asset('real-estate::images/icon-document.png'),
            ],
        ];

        $moreSpecifications = Property::getMeta($object->id, 'more_specifications', true);

        if(hasItems($moreSpecifications))
        {
            foreach($moreSpecifications as $key => $spec)
            {
                $specifications[$key] = [
                    'label' => $spec['title'] ?? '',
                    'value' => $spec['content'] ?? '',
                    'icon'  => asset('source/'.$spec['icon']),
                ];
            }
        }

        foreach ($specifications as $key => $spec)
        {
            if(!is_string($spec['value']))
            {
                unset($specifications[$key]);
                continue;
            }

            if(empty($spec['value']))
            {
                unset($specifications[$key]);
            }

            if($object->property_type == 3 && ($key == 'bedroom' || $key == 'bathroom'))
            {
                unset($specifications[$key]);
            }
        }

        echo view('real-estate::property/detail/characteristics', compact('object', 'specifications'));
    }

    /**
     * Phần nội dung chi tiết của bất động sản
     */
    static public function sectionContent($object): void
    {
        echo view('real-estate::property/detail/section-content', compact('object'));
    }

    static public function sectionUtilities($object): void
    {
        $utilitiesData = Property::getMeta($object->id, 'utilities', true);

        if (hasItems($utilitiesData))
        {
            $utilities = Features::whereIn('id', $utilitiesData)->get();

            echo view('real-estate::property/detail/section-utilities', ['utilities' => $utilities, 'object' => $object]);
        }
    }

    static public function sectionFurniture($object): void
    {
        $furnitureData = Property::getMeta($object->id, 'furniture', true);

        if (hasItems($furnitureData))
        {
            $furniture = Features::whereIn('id', $furnitureData)->get();

            echo view('real-estate::property/detail/section-features', ['furniture' => $furniture, 'object' => $object]);
        }
    }

    static public function sectionMap($object): void
    {
        $lat           = Property::getMeta($object->id, 'lat', true);
        $lng           = Property::getMeta($object->id, 'lng', true);
        $googleMapLink = Property::getMeta($object->id, 'google_map_link', true);

        if (!empty($lat) && !empty($lng))
        {
            echo view('real-estate::property/detail/section-map', compact('object', 'lat', 'lng', 'googleMapLink'));
        }
    }

    static public function sidebarBroker($object): void
    {
        $userData = [];

        $user     = User::find($object->user_created == 1 ? 2 : $object->user_created);

        if (hasItems($user))
        {
            $userData = [
                'title' => trans('real-estate::property.detail.broker_contact'),
                'name'  => $user->firstname . ' ' . $user->lastname,
                'phone' => $user->phone ?? '',
                'email' => $user->email ?? '',
            ];
        }

        if (hasItems($userData) && !empty($userData['phone']))
        {
            echo view('real-estate::property/detail/sidebar-contact', compact('object', 'userData'));
        }
    }

    static public function sidebarBooking($object): void
    {
        echo view('real-estate::property/detail/sidebar-booking', compact('object'));
    }

    static public function related($object): void
    {
        $category = Cms::getData('category');

        $args = Property::where('id', '!=', $object->id)->limit(20)->inRandomOrder();

        if (have_posts($category))
        {
            $args->where('category_id', $category->id);
        }

        $args    = apply_filters('property_related_args', $args);

        $related = apply_filters('property_related', $args->get());

        echo view('real-estate::property/detail/related', ['related' => $related, 'object' => $object]);
    }

    static public function viewed($object): void
    {
        $watchedList = session()->get('viewed_property');

        if (!empty($watchedList))
        {
            $viewed = apply_filters('property_viewed',
                Property::where('id', '!=', $object->id)->whereIn('id', $watchedList)->limit(20)->get()
            );

            echo view('real-estate::property/detail/viewed', ['viewed' => $viewed, 'object' => $object]);
        }
    }

    static function modalFeedback($object): void
    {
        $fbOptions = [
            trans('real-estate::property.feedback.topic.address')    => trans('real-estate::property.feedback.topic.address'),
            trans('real-estate::property.feedback.topic.info')       => trans('real-estate::property.feedback.topic.info'),
            trans('real-estate::property.feedback.topic.photo')      => trans('real-estate::property.feedback.topic.photo'),
            trans('real-estate::property.feedback.topic.duplicate')  => trans('real-estate::property.feedback.topic.duplicate'),
            trans('real-estate::property.feedback.topic.no_contact') => trans('real-estate::property.feedback.topic.no_contact'),
            trans('real-estate::property.feedback.topic.fake')       => trans('real-estate::property.feedback.topic.fake'),
            trans('real-estate::property.feedback.topic.sold')       => trans('real-estate::property.feedback.topic.sold'),
            trans('real-estate::property.feedback.topic.other')      => trans('real-estate::property.feedback.topic.other'),
        ];

        $user = Auth::user();

        $form = new Form;

        $form->setFormId('formPropertyFeedback');

        $form->setIsValid(true);

        $form->setCallbackValidJs('formPropertyFeedbackHandler');

        $form->checkbox('feedback_topic', [
            'label'       => trans('real-estate::property.feedback.form.topic'),
            'validations' => Rule::make(trans('real-estate::property.feedback.form.topic'))->notEmpty(),
        ])->options($fbOptions);

        $form->textarea('feedback', [
            'label'       => trans('real-estate::property.feedback.form.other'),
            'placeholder' => trans('real-estate::property.feedback.form.enter_content'),
        ]);

        $form->hidden('url', [], Url::current());

        $form->hidden('object_id', [], $object->id);

        $form->hidden('object_name', [], $object->name);

        $form->text('name', [
            'placeholder' => trans('real-estate::property.feedback.form.full_name'),
        ], hasItems($user) ? $user->firstname . ' ' . $user->lastname : '');

        $form->text('phone', [
            'placeholder' => trans('real-estate::property.feedback.form.phone'),
            'validations' => Rule::make(trans('real-estate::property.feedback.form.phone'))->notEmpty()->phone(['vi']),
        ], hasItems($user) ? $user->phone : '');

        $form->text('email', [
            'placeholder' => 'Email',
            'validations' => Rule::make('email')->notEmpty()->email(),
        ], hasItems($user) ? $user->email : '');

        echo view('real-estate::property/modal-report', ['form' => $form]);
    }
}


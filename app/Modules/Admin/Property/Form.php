<?php
namespace RealEstate\Modules\Admin\Property;

use RealEstate\Models\Property;
use RealEstate\Supports\PropertyHelper;
use RealEstate\Supports\RealEstateHelper;
use SkillDo\Cms\FormAdmin\FormAdmin;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Support\Url;
use SkillDo\Validate\Rule;

class Form
{
    static function fields(FormAdmin $form): FormAdmin
    {
        $form->setModel(Property::class);

        $categoriesSell = \RealEstate\Models\PropertyCategory::where('type', 'sell')->options();

        $categoriesRent = \RealEstate\Models\PropertyCategory::where('type', 'rent')->options();

        $form->lang()
            ->addGroup('info', trans('form.group.info'))
            ->text('name', [
                'label'       => trans('real-estate::property.name'),
                'note'        => trans('form.info.title.note'),
                'validations' => [
                    Language::default() => Rule::make()->notEmpty(),
                ],
            ])
            ->wysiwygShort('excerpt', ['label' => trans('form.info.excerpt')])
            ->wysiwyg('content', ['label' => trans('form.info.content')]);

        $form->right()
            ->addGroup('media', trans('form.group.media'))
            ->image('image', ['label' => trans('form.media.image')]);

        $form->right()
            ->addGroup('moreInfo', trans('real-estate::property.form.info'))
            ->radio('type', PropertyHelper::getType(), ['label' => trans('real-estate::property.form.type_label'), 'value' => 'sell'])
            ->select2('category_sell_id', $categoriesSell->toArray(), ['label' => trans('real-estate::property.form.category.sell')])
            ->select2('category_rent_id', $categoriesRent->toArray(), ['label' => trans('real-estate::property.form.category.rent')])
            ->select('property_type', PropertyHelper::getPropertyType(), ['label' => trans('real-estate::property.property_type')])
            ->price('price', ['label' => trans('real-estate::property.price')])
            ->select('unit', RealEstateHelper::getUnit('option'), ['label' => trans('real-estate::property.unit')])
            ->select('status', PropertyHelper::getStatus('option'), ['label' => trans('real-estate::property.status')]);

        /*
        $formTabDetail = new \SkillDo\Cms\FormAdmin\FormAdminLocation($form);

        $formTabDetail
            ->addGroup('detail', 'Đặc điểm')
            ->text('area', ['label' => 'Diện tích (m²)'])
            ->select('bedroom', PropertyHelper::getBedroom(), ['label' => 'Số phòng ngủ', 'value' => 1, 'start' => 4])
            ->select('bathroom', PropertyHelper::getBathroom(), ['label' => 'Số phòng tắm', 'value' => 1, 'start' => 4])
            ->select('direction', PropertyHelper::getDirection('option'), ['label' => 'Hướng', 'start' => 4])
            ->text('juridical', ['label' => 'Pháp lý', 'start' => 4])
            ->tab('location', PropertyHelper::getLocation(), ['label' => 'Vị trí', 'start' => 4])
            ->number('location_detail', ['label' => 'Hẻm (mặt tiền) rộng (m²)', 'start' => 4]);

        $form->addTab('detail', 'Đặc điểm', 10, $formTabDetail);
        */

        $form->right()
            ->addGroup('seo', trans('form.group.seo'))
            ->text('slug', ['label' => 'Slug'])
            ->text('seo_title', ['label' => 'Meta title'])
            ->text('seo_keywords', ['label' => 'Meta Keyword'])
            ->textarea('seo_description', ['label' => 'Meta Description']);

        $form->setUseGallery(true);

        return $form;
    }

    static function buttons(FormAdmin $form): FormAdmin
    {
        $buttons = [];

        if (Admin::isPage('property_add')) {
            $buttons[] = Admin::button('save', ['data-redirect' => 'table_id_property_list']);
            $buttons[] = Admin::button('back', [
                'href'          => Url::admin('property'),
                'class'         => ['btn-back-to-redirect'],
                'data-redirect' => 'table_id_property_list',
            ]);
        }

        if (Admin::isPage('property_edit')) {
            $buttons[] = Admin::button('save', ['data-redirect' => 'table_id_property_list']);
            $buttons[] = Admin::button('add', [
                'href'    => Url::admin('property/add'),
                'text'    => '',
                'tooltip' => trans('button.add'),
            ]);
            $buttons[] = Admin::button('back', [
                'href'          => Url::admin('property'),
                'text'          => '',
                'tooltip'       => trans('button.back'),
                'class'         => ['btn-back-to-redirect'],
                'data-redirect' => 'table_id_property_list',
            ]);
        }

        $form->setButtons($buttons);
        return $form;
    }

    static function dataSave(array $insertData): array
    {
        if (!empty($insertData['type']))
        {
            if ($insertData['type'] == 'sell')
            {
                $insertData['category_id'] = !empty($insertData['category_sell_id']) ? (int) $insertData['category_sell_id'] : 0;
            }

            if ($insertData['type'] == 'rent')
            {
                $insertData['category_id'] = !empty($insertData['category_rent_id']) ? (int) $insertData['category_rent_id'] : 0;
            }
        }

        return $insertData;
    }
}


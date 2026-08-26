<?php
namespace RealEstate\Modules\Admin\PropertyCategory;

use RealEstate\Models\PropertyCategory;
use SkillDo\Cms\FormAdmin\FormAdmin;
use SkillDo\Cms\Support\Language;
use SkillDo\Validate\Rule;

class Form
{
    static function fieldsSell(FormAdmin $form): FormAdmin
    {
        return self::fields($form, 'sell');
    }

    static function fieldsRent(FormAdmin $form): FormAdmin
    {
        return self::fields($form, 'rent');
    }

    static function fields(FormAdmin $form, string $type): FormAdmin
    {
        $form->setModel(PropertyCategory::class);

        $form->lang()
            ->addGroup('info', trans('form.group.info'))
            ->text('name', [
                'label'       => trans('form.info.title'),
                'note'        => trans('form.info.title.note'),
                'validations' => [
                    Language::default() => Rule::make()->notEmpty(),
                ],
            ]);

        $form->right()
            ->addGroup('category', trans('admin::category.title'))
            ->select2('parent_id', PropertyCategory::where('type', $type)->options()->toArray(), [
                'label' => trans('admin::category.form.multiple.parent'),
            ]);

        $form->right()
            ->addGroup('media', 'Media')
            ->image('image', ['label' => trans('form.media.image')])
            ->hidden('type', ['value' => $type]);

        $formSeo = new \SkillDo\Cms\FormAdmin\FormAdminLocation($form);

        //O "Duong dan" tu ve dung cho theo co `language_slug_per_language`:
        //tat thi dung dau nhom SEO nhu cu, bat thi tach thanh mot o cho moi ngon ngu.
        $form->slugField(['label' => trans('admin::form.seo.slug')], ['location' => $formSeo, 'groupName' => trans('admin::form.group.seo')]);

        $formSeo
            ->addGroup('seo', trans('admin::form.group.seo'))
            ->text('seo_title', ['label' => trans('admin::form.seo.title')])
            ->text('seo_keywords', ['label' => trans('admin::form.seo.keywords')])
            ->textarea('seo_description', ['label' => trans('admin::form.seo.description')]);

        $form->addTab('seo', trans('admin::form.group.seo'), 10, $formSeo);

        return $form;
    }

    static function dataSell(array $insertData): array
    {
        $insertData['type'] = 'sell';

        return $insertData;
    }

    static function dataRent(array $insertData): array
    {
        $insertData['type'] = 'rent';

        return $insertData;
    }

    static function submitCategoryArgs($args, string $module)
    {
        if ($module == 'property_categories_sell') $args->where('type', 'sell');

        if ($module == 'property_categories_rent') $args->where('type', 'rent');

        return $args;
    }
}


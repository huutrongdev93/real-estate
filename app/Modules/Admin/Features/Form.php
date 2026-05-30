<?php
namespace RealEstate\Modules\Admin\Features;

use RealEstate\Models\Features;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cms\FormAdmin\FormAdmin;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Support\Url;
use SkillDo\Validate\Rule;

class Form
{
    static function fields(FormAdmin $form): FormAdmin
    {
        $form->setModel(Features::class);

        $form->lang()
            ->addGroup('info', trans('form.group.info'))
            ->text('name', [
                'label'       => trans('real-estate::features.form.name'),
                'validations' => [
                    Language::default() => Rule::make()->notEmpty(),
                ],
            ]);

        $form->right()
            ->addGroup('media', trans('form.group.media'))
            ->select('type', PropertyHelper::getFeaturesType(), ['label' => trans('real-estate::features.form.type')])
            ->image('image', ['label' => trans('form.media.image')]);

        return $form;
    }

    static function buttons(FormAdmin $form): FormAdmin
    {
        $buttons = [];

        if (Admin::isPage('re_features_add'))
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('back', ['href' => Url::admin('re-features')]);
        }

        if (Admin::isPage('re_features_edit'))
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('add', ['href' => Url::admin('re-features/add'), 'text' => '', 'tooltip' => trans('button.add')]);
            $buttons[] = Admin::button('back', ['href' => Url::admin('re-features'), 'text' => '', 'tooltip' => trans('button.back')]);
        }

        $form->setButtons($buttons);

        return $form;
    }
}

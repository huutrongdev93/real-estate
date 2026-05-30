<?php
namespace RealEstate\Services;

use RealEstate\Models\Booking;
use RealEstate\Models\PropertyCategory;
use RealEstate\Models\PropertyFeedback;
use SkillDo\Cms\Menu\AdminMenu;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Url;
use SkillDo\Support\Auth;

class RoleService
{
    static function roles(array $group): array
    {
        $group['real_estate_property_category'] = [
            'label'        => trans('real-estate::admin.role.property_category'),
            'capabilities' => array_keys(self::capabilitiesPropertyCategory()),
        ];
        $group['real_estate_property'] = [
            'label'        => trans('real-estate::admin.role.property'),
            'capabilities' => array_keys(self::capabilitiesProperty()),
        ];
        return $group;
    }

    static function label(array $label = []): array
    {
        return array_merge($label, self::capabilitiesPropertyCategory(), self::capabilitiesProperty());
    }

    static function capabilitiesPropertyCategory(): array
    {
        return [
            'property_category_list'   => trans('real-estate::admin.role.manage'),
            'property_category_edit'   => trans('real-estate::admin.role.edit'),
            'property_category_delete' => trans('real-estate::admin.role.delete'),
        ];
    }

    static function capabilitiesProperty(): array
    {
        return [
            'property_list'     => trans('real-estate::admin.role.manage'),
            'property_edit'     => trans('real-estate::admin.role.edit'),
            'property_delete'   => trans('real-estate::admin.role.delete'),
            'property_booking'  => trans('real-estate::admin.role.booking'),
            'property_feedback' => trans('real-estate::admin.role.feedback'),
        ];
    }
}
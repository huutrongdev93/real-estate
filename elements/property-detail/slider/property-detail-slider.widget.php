<?php

use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Cms;

class PropertyDetailSliderElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyDetailSliderElement', 'Slider');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-sliders"></i>';
    }

    public function category(): string
    {
        return 'property_detail';
    }

    public function widget(): void
    {
        do_action('property_detail_top', Cms::getData('object') ?? null);
    }
}
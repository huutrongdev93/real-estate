<?php

use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;

class PropertyDetailContentElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyDetailContentElement', 'Nội Dung BĐS');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-sliders"></i>';
    }

    public function category(): string
    {
        return 'property_detail';
    }

    public function form(): void
    {
        $this->tabs('generate')->adds(function (\SkillDo\Cms\Form\Form $form)
        {
        });

        parent::form();
    }

    public function widget(): void
    {
        do_action('property_detail_main', Cms::getData('object') ?? null);
    }
}
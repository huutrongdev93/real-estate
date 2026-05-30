<?php

use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;

class PropertyDetailSidebarContactElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyDetailSidebarContactElement', 'Liên hệ');
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
        \RealEstate\Template\PropertyDetail::sidebarBroker(Cms::getData('object'));
    }
}
<?php

use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;

class PropertyIndexDescriptionElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyIndexDescriptionElement', 'Mô tả danh mục');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-sliders"></i>';
    }

    public function category(): string
    {
        return 'property_index';
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
        $category = Cms::getData('category');

        if(empty($category) || empty($category->excerpt))
        {
            echo $this->empty();
            return;
        }

        echo $category->excerpt;
    }
}
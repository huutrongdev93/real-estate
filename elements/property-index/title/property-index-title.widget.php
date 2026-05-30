<?php

use RealEstate\Template\PropertyIndex;
use SkillDo\Cms\Element\Element;

class PropertyIndexTitleElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyIndexTitleElement', 'Tiêu đề');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-newspaper"></i>';
    }

    public function category(): string
    {
        return 'property_index';
    }

    public function form(): void
    {
        $this->tabs('generate')->adds(function (\SkillDo\Cms\Form\Form $form)
        {
            $form->textBuilding('titleStyle', ['label' => 'Tiêu đề'])->popup(false);
        });

        parent::form();
    }

    public function widget(): void
    {
        PropertyIndex::heading();
    }

    public function cssBuilder(): string
    {
        $this->cssSelector('h1', [
            'data'=> $this->options->titleStyle ?? [],
            'style' => 'text',
        ]);

        return $this->cssBuild();
    }
}
<?php
use RealEstate\Template\PropertyIndex;
use SkillDo\Cms\Element\Element;

class PropertyIndexDataElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyIndexDataElement', 'Bất động sản');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-newspaper"></i>';
    }

    public function category(): string
    {
        return 'property_index';
    }

    public function widget(): void
    {
        PropertyIndex::list();
    }
}
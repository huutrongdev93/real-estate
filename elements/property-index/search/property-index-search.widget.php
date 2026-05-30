<?php

use Ecommerce\Supports\Prd;
use RealEstate\Template\PropertyIndex;
use SkillDo\Cms\Element\Element;

class PropertyIndexSearchElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyIndexSearchElement', 'Thanh tìm kiếm');
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
        PropertyIndex::search();
    }
}
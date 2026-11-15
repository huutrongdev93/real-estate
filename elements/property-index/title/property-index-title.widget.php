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

    /** Thành phần con để bấm chọn trong trình dựng (tên danh mục lấy từ DB nên chỉ khai field kiểu dáng) */
    public function parts(): array
    {
        return [
            'title' => ['selector' => 'h1', 'label' => 'Tiêu đề trang', 'field' => 'titleStyle'],
        ];
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
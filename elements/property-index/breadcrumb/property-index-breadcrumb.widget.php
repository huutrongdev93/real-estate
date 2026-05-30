<?php

use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Theme;

class PropertyIndexBreadcrumbElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyIndexBreadcrumbElement', 'Breadcrumb');
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
            $form->tab('align', ['label' => 'Căn chỉnh', 'cssStyle' => true])->options([
                'start' => '<i class="fa-thin fa-align-left"></i>&nbsp;Trái',
                'center' => '<i class="fa-thin fa-align-center"></i>&nbsp;Giửa',
                'end' => 'Phải&nbsp; <i class="fa-thin fa-align-right"></i>'
            ])->display('inline');

            $form->addGroup(function (\SkillDo\Cms\Form\Form $form)
            {
                $form->textBuilding('titleStyle')->popup(false);

            }, $this->groupFormBox('Thiết lập kiểu chữ', 'titleStyleGroup'));

            $form->addGroup(function (\SkillDo\Cms\Form\Form $form)
            {
                $form->textBuilding('boxStyle')->popup(false);

            }, $this->groupFormBox('Khung Breadcrumb', 'boxStyleGroup'));

        });

        parent::form();
    }

    public function widget(): void
    {
        echo Theme::breadcrumb()->render();
    }

    public function cssBuilder(): string
    {
        if(!empty($this->options->align))
        {
            $this->cssStyle('', [
                'style' => 'display:flex; justify-content:'.$this->options->align.';',
            ]);
        }

        $this->cssSelector('', [
            'data'=> $this->options->boxStyle ?? [],
            'style' => 'box',
        ]);

        $this->cssSelector('.breadcrumb ol a', [
            'data'=> $this->options->titleStyle ?? [],
            'style' => 'text',
        ]);

        $this->cssSelector('.breadcrumb ol span', [
            'data'=> $this->options->titleStyle ?? [],
            'style' => 'text',
        ]);

        return $this->cssBuild();
    }
}
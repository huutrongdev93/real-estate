<?php

use SkillDo\Cms\Element\Element;

class PropertyDetailContentBoxElement extends Element
{
    function __construct()
    {
        parent::__construct('PropertyDetailContentBoxElement', 'Content Box');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-box"></i>';
    }

    public function category(): string
    {
        return 'property_detail';
    }

    public function form(): void
    {
        parent::form();

        $this->tabs('generate')->adds(function (\SkillDo\Cms\Form\Form $form)
        {
            $form->text('title', ['label' => 'Tiêu đề', 'language' => true]);
        });

        $this->tabs('style')->adds(function (\SkillDo\Cms\Form\Form $form)
        {
            $form->addGroup(function (\SkillDo\Cms\Form\Form $form)
            {
                $form->boxBuilding('boxStyle')->popup(false);

            }, $this->groupFormBox('Khung', 'boxStyleGroup'));

            $form->addGroup(function (\SkillDo\Cms\Form\Form $form)
            {
                $form->typography('titleStyle', ['label'=> 'Kiểu chữ tiêu đề'])->display('inline');

                $form->colorBuilding('titleColor', ['label'=> 'Màu chữ'])->display('inline');

            }, $this->groupFormBox('Tiêu đề', 'titleStyleGroup', true));
        });
    }

    public function widget(): void
    {
        Theme::view($this->getDir().'views/view', [
            'widget' => $this,
        ]);
    }

    public function cssBuilder(): string
    {
        $this->cssSelector('.element-box',
            [
                'data' => $this->options->boxStyle ?? [],
                'style' => 'box',
            ]
        );

        $this->cssSelector('.element-box .element-heading',
            [
                'data' => $this->options->titleStyle ?? [],
                'style' => 'typography',
            ],
            [
                'data' => $this->options->titleColor ?? [],
                'style' => 'textColor',
            ]
        );

        return $this->cssBuild();
    }

    public function default(): void
    {
        $defaults = [
            'titleColor' => [
                'active' => 'color',
                'color' => '#000000',
            ],
            'boxStyle' => [
                'radius' => [
                    'top' => 16,
                    'left' => 16,
                    'right' => 16,
                    'bottom' => 16,
                    'link' => true
                ],
                'shadow' => [
                    'h_shadow' => 0,
                    'v_shadow' => 0,
                    'blur' => 0,
                    'spread' => 0,
                    'color' => '#000000',
                ],
                'margin' => [
                    'desktop' => [
                        'top' => 0,
                        'left' => 0,
                        'right' => 0,
                        'bottom' => 10,
                    ],
                ]
            ]
        ];

        foreach ($defaults as $key => $value)
        {
            $this->options->{$key} = $this->options->{$key} ?? $value;
        }
    }
}
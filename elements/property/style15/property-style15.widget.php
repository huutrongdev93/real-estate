<?php

use RealEstate\Models\Property;
use RealEstate\Models\PropertyCategory;
use RealEstate\Supports\RealEstateHelper;
use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Theme;
use SkillDo\Cms\Support\Url;

class PropertyElementStyle15 extends Element
{
    public function __construct()
    {
        parent::__construct('PropertyElementStyle15', 'Bất động sản (Style 15)');

        $this->assets('assets/property-style-15.less');

        $this->assets('assets/property-style-15.js');
    }

    public function icon(): string
    {
        return '<i class="fa-duotone fa-solid fa-building"></i>';
    }

    public function category(): string
    {
        return 'general';
    }

    public function form(): void
    {
        $this->tabs('generate')->adds(function (\SkillDo\Cms\Form\Form $form)
        {
            $form->repeater('tabs', [
                'label'  => 'Danh sách Tab',
                'fields' => function (\SkillDo\Cms\Form\Form $form)
                {
                    $form->text('title', ['label' => 'Tiêu đề tab']);

                    $form->select2('categoryId', ['label' => 'Danh mục BĐS'])
                        ->options(PropertyCategory::options()->toArray());

                    $form->select2('status', ['label' => 'Loại BĐS'])->options($this->statusOption());
                }
            ]);

            $form->addResponsive('tabsWidth', [
                'label' => 'Chiều rộng tab',
                'type'  => \SkillDo\Cms\Form\Field\Range::class,
                'min'   => 10,
                'max'   => 100,
                'step'  => 1,
            ]);

            $form->addGroup(function (\SkillDo\Cms\Form\Form $form)
            {
                $form->tab('display[type]', ['label' => 'Kiểu hiển thị'])
                    ->options([
                        0 => 'Chạy slider',
                        1 => 'Dạng danh sách',
                    ]);

                $form->number('display[time]', ['label' => 'Thời gian tự động chạy'])
                    ->step(0.01)
                    ->start(6)
                    ->condition('display[type]', [0]);

                $form->tab('display[rows]', ['label' => 'Số hàng'])
                    ->options([1 => '1 hàng', 2 => '2 hàng'])
                    ->start(6)
                    ->condition('display[type]', [0]);

                $form->addResponsive('numberShow', [
                    'label' => 'Số BĐS hiển thị trên 1 hàng',
                    'type'  => \SkillDo\Cms\Form\Field\NumericSelector::class,
                    'min'   => 1,
                    'max'   => 6,
                ]);

            }, $this->groupFormBox('BĐS', 'property_content_box'));
        });

        $this->tabs('style')->adds(function (\SkillDo\Cms\Form\Form $form)
        {
            $form->addGroup(function (\SkillDo\Cms\Form\Form $form) {

                $form->addResponsive('tabTitleBetween', [
                    'label' => 'Khoảng cách giữa các tab',
                    'type'  => \SkillDo\Cms\Form\Field\Range::class,
                    'min'   => 0,
                    'max'   => 100,
                    'step'  => 1,
                ]);

                $form->addResponsive('tabTitleSpacing', [
                    'label' => 'Khoảng cách từ tab đến nội dung',
                    'type'  => \SkillDo\Cms\Form\Field\Range::class,
                    'min'   => 0,
                    'max'   => 100,
                    'step'  => 1,
                ]);

                $form->addTab(function (\SkillDo\Cms\Form\Form $form)
                {
                    $form->background('tabButtonBg', ['label' => 'Màu nền tab'])->display('inline');
                    $form->border('tabButtonBorder', ['label' => 'Đường viền']);
                    $form->boxShadow('tabButtonShadow', ['label' => 'Đổ bóng'])->display('inline');
                }, 'tab_style_normal', ['label' => 'Bình thường', 'active' => true]);

                $form->addTab(function (\SkillDo\Cms\Form\Form $form) {
                    $form->background('tabButtonBgHover', ['label' => 'Màu nền tab'])->display('inline');
                    $form->border('tabButtonBorderHover', ['label' => 'Đường viền']);
                    $form->boxShadow('tabButtonShadowHover', ['label' => 'Đổ bóng'])->display('inline');
                }, 'tab_style_hover', ['label' => 'Di chuột']);

                $form->addTab(function (\SkillDo\Cms\Form\Form $form) {
                    $form->background('tabButtonBgActive', ['label' => 'Màu nền tab'])->display('inline');
                    $form->border('tabButtonBorderActive', ['label' => 'Đường viền']);
                    $form->boxShadow('tabButtonShadowActive', ['label' => 'Đổ bóng'])->display('inline');
                }, 'tab_style_active', ['label' => 'Kích hoạt']);

                $form->spacing('tabSpacing')->customInput(['margin' => false]);

            }, $this->groupFormBox('Tab', 'property_tab_style_box', true));

            $form->addGroup(function (\SkillDo\Cms\Form\Form $form)
            {
                $form->textBuilding('tabButtonTxt')
                    ->customInput([
                        'tabHover'    => true,
                        'tabActive'   => true,
                        'tabAdvanced' => false,
                    ])->popup(false);

            }, $this->groupFormBox('Tiêu đề', 'property_tab_title_style_box'));
        });
    }

    public function widget(): void
    {
        if (empty($this->options->tabs))
        {
            echo $this->empty();
            return;
        }

        $activeKey = array_key_first($this->options->tabs ?? []);

        Theme::view($this->getDir().'views/view', [
            'id'        => $this->id,
            'widget'    => $this,
            'tabs'      => $this->options->tabs,
            'activeKey' => $activeKey,
            'options'   => [
                'display'           => $this->options->display,
                'limit'             => $this->options->limit,
                'desktopNumberShow' => $this->options->desktopNumberShow,
                'tabletNumberShow'  => $this->options->tabletNumberShow,
                'mobileNumberShow'  => $this->options->mobileNumberShow,
                'tabs'              => $this->options->tabs,
            ],
        ]);
    }

    public function default(): void
    {
        $defaults = [
            'display' => [
                'type' => '0',
                'time' => 3,
                'rows' => '1',
            ],
            'limit'             => 10,
            'desktopNumberShow' => 3,
            'tabletNumberShow'  => 2,
            'mobileNumberShow'  => 1,
            'desktopTabsWidth'  => 50,
            'tabletTabsWidth'   => 100,
            'mobileTabsWidth'   => 100,
            'tabButtonBorder'   => [
                'style' => 'solid',
                'color' => [
                    'active' => 'color',
                    'color'  => '#000',
                ],
                'width' => [
                    'top'    => 1,
                    'right'  => 1,
                    'bottom' => 1,
                    'left'   => 1,
                ],
                'radius' => [
                    'top'    => 10,
                    'left'   => 10,
                    'right'  => 10,
                    'bottom' => 10,
                ],
            ],
            'tabButtonBorderActive' => [
                'style' => 'solid',
                'color' => [
                    'active' => 'global',
                    'global' => 'primary',
                ],
                'width' => [
                    'top'    => 1,
                    'right'  => 1,
                    'bottom' => 1,
                    'left'   => 1,
                ],
            ],
            'tabButtonTxt' => [
                'color' => [
                    'active' => 'color',
                    'color'  => '#fff',
                ],
                'hover' => [
                    'active' => 'color',
                    'color'  => '#000',
                ],
                'active' => [
                    'color' => [
                        'active' => 'global',
                        'global' => 'primary',
                    ],
                ],
            ],
        ];

        foreach ($defaults as $key => $value)
        {
            $this->options->{$key} = $this->options->{$key} ?? $value;
        }

        $this->options->tabs = $this->options->tabs ?? [
            '6968734b56b65' => [
                'title'      => 'Bất động sản nổi bật',
                'categoryId' => '0',
                'status'     => '',
            ],
            '656734bcg6b66' => [
                'title'      => 'Tiềm năng',
                'categoryId' => '',
                'status'     => 'status1',
            ],
            '65x4bdf5gxb67' => [
                'title'      => 'Hot',
                'categoryId' => '',
                'status'     => 'status3',
            ],
        ];
    }

    public function cssBuilder(): string
    {
        if (!empty($this->options->desktopTabsWidth))
        {
            $this->cssVariables('--tabs-heading-width', $this->options->desktopTabsWidth.'%');
        }
        else
        {
            $this->cssVariables('--tabs-heading-width', '240px');
        }

        if (!empty($this->options->tabletTabsWidth))
        {
            $this->cssVariables('--tabs-heading-width-tablet', $this->options->tabletTabsWidth.'%');
        }
        if (!empty($this->options->mobileTabsWidth))
        {
            $this->cssVariables('--tabs-heading-width-mobile', $this->options->mobileTabsWidth.'%');
        }

        if (!empty($this->options->desktopTabTitleBetween))
        {
            $this->cssVariables('--tabs-nav-gap', $this->options->desktopTabTitleBetween.'px');
        }
        if (!empty($this->options->tabletTabTitleBetween))
        {
            $this->cssVariables('--tabs-nav-gap-tablet', $this->options->tabletTabTitleBetween.'px');
        }
        if (!empty($this->options->mobileTabTitleBetween))
        {
            $this->cssVariables('--tabs-nav-gap-mobile', $this->options->mobileTabTitleBetween.'px');
        }

        if (!empty($this->options->desktopTabTitleSpacing))
        {
            $this->cssVariables('--tabs-gap', $this->options->desktopTabTitleSpacing.'px');
        }
        if (!empty($this->options->tabletTabTitleSpacing))
        {
            $this->cssVariables('--tabs-gap-tablet', $this->options->tabletTabTitleSpacing.'px');
        }
        if (!empty($this->options->mobileTabTitleSpacing))
        {
            $this->cssVariables('--tabs-gap-mobile', $this->options->mobileTabTitleSpacing.'px');
        }

        $this->cssSelector('.property-s15-categories li a',
            ['data' => $this->options->tabButtonBg ?? [],     'style' => 'background'],
            ['data' => $this->options->tabButtonBorder ?? [], 'style' => 'border'],
            ['data' => $this->options->tabButtonShadow ?? [], 'style' => 'boxShadow'],
            ['data' => $this->options->tabButtonTxt ?? [],    'style' => 'text'],
            ['data' => $this->options->tabSpacing ?? [],      'style' => 'spacing']
        );

        $this->cssSelector('.property-s15-categories li a:hover',
            ['data' => $this->options->tabButtonBgHover ?? [],     'style' => 'background'],
            ['data' => $this->options->tabButtonBorderHover ?? [], 'style' => 'border'],
            ['data' => $this->options->tabButtonShadowHover ?? [], 'style' => 'boxShadow']
        );

        $this->cssSelector('.property-s15-categories li a.active',
            ['data' => $this->options->tabButtonBorderActive ?? [], 'style' => 'border'],
            ['data' => $this->options->tabButtonBgActive ?? [],     'style' => 'background'],
            ['data' => $this->options->tabButtonShadowActive ?? [], 'style' => 'boxShadow']
        );

        return $this->cssBuild();
    }

    public function statusOption(): array
    {
        $collections = RealEstateHelper::collections();

        $options = ['' => 'Tất cả'];

        foreach ($collections as $key => $collection)
        {
            $options[$key] = $collection['name'];
        }

        return $options;
    }

    static function loadProperty(\SkillDo\Http\Request $request): void
    {
        $options = $request->input('options');

        $id     = $request->input('id');

        $tabId  = $request->input('tabId');

        if (hasItems($options))
        {
            $slug = '';

            $widget = new PropertyElementStyle15();

            $widget->setId($id);

            $widget->setOption($options);

            $widget->default();

            if (!isset($widget->options->tabs[$tabId]))
            {
                response()->error(trans('ajax.load.error'));
            }

            $tab = $widget->options->tabs[$tabId];

            $query = Property::where('public', 1)
                ->orderBy('order')
                ->orderBy('created', 'desc')
                ->limit((!empty($widget->options->limit)) ? $widget->options->limit : 20);

            if (!empty($tab['status']))
            {
                $query->where($tab['status'], 1);
            }

            if (!empty($tab['categoryId']))
            {
                $category = PropertyCategory::find($tab['categoryId']);

                if (hasItems($category))
                {
                    $query->whereByCategory($category);

                    $slug = Url::permalink($category->slug);
                }
            }

            $properties = $query->get();

            $result = [
                'items' => [],
                'slug'  => $slug,
            ];

            foreach ($properties as $property)
            {
                $result['items'][] = (string) view('real-estate::property/loop/item', ['item' => $property]);
            }

            response()->success(trans('ajax.load.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }
}

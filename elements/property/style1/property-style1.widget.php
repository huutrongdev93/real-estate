<?php

use RealEstate\Models\Property;
use RealEstate\Models\PropertyCategory;
use RealEstate\Supports\RealEstateHelper;
use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Theme;
use SkillDo\Cms\Support\Url;
use SkillDo\Cms\Form\Form;

class PropertyElementStyle1 extends Element
{
    function __construct()
    {
        parent::__construct('PropertyElementStyle1', 'Bất động sản (Style 1)');
        $this->configClass('property-slider-horizontal');
        $this->assets('assets/property-style-1.less');
        $this->assets('assets/property-script-1.js');
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
        $this->tabs('generate')->adds(function (Form $form)
        {
            $form->select2('type', ['label' => 'Loại bất động sản'])
                ->options([
                    'sell' => 'Bán',
                    'rent' => 'Cho thuê',
                ]);

            $form->select2('categorySellId', ['label' => 'Danh mục BĐS (Bán)'])
                ->options(PropertyCategory::where('type', 'sell')->options()->toArray())
                ->condition('type', ['sell']);

            $form->select2('categoryRentId', ['label' => 'Danh mục BĐS (Cho thuê)'])
                ->options(PropertyCategory::where('type', 'rent')->options()->toArray())
                ->condition('type', ['rent']);

            $form->select2('status', ['label' => 'Loại BĐS'])->options($this->statusOption());

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
        });

        $this->tabs('style')->adds(function (Form $form)
        {
            $form->buttonBuilding('arrowStyle', [
                'label' => 'Kiểu button điều hướng',
            ])->popup(false);
        });

        parent::form();
    }

    public function widget(): void
    {
        $categoryId = ($this->options->type == 'sell') ? $this->options->categorySellId : $this->options->categoryRentId;

        Theme::view($this->getDir().'views/view', [
            'name'    => $this->name,
            'options' => (object)[
                'categoryId'        => $categoryId,
                'type'              => $this->options->type,
                'status'            => $this->options->status,
                'display'           => $this->options->display,
                'desktopNumberShow' => $this->options->desktopNumberShow,
                'tabletNumberShow'  => $this->options->tabletNumberShow,
                'mobileNumberShow'  => $this->options->mobileNumberShow,
            ],
        ]);
    }

    public function cssBuilder(): string
    {
        $this->cssSelector('.box-content .arrow', [
            'data'  => $this->options->arrowStyle ?? [],
            'style' => 'button',
        ]);

        return $this->cssBuild();
    }

    public function default(): void
    {
        $defaults = [
            'type'           => 'sell',
            'categorySellId' => 0,
            'categoryRentId' => 0,
            'status'        => 0,
            'display'    => [
                'type' => '0',
                'time' => 3,
                'rows' => '1',
            ],
            'limit'             => 10,
            'desktopNumberShow' => 3,
            'tabletNumberShow'  => 2,
            'mobileNumberShow'  => 1,
        ];

        foreach ($defaults as $key => $value)
        {
            $this->options->{$key} = $this->options->{$key} ?? $value;
        }
    }

    public function statusOption(): array
    {
        $collections = RealEstateHelper::collections();

        $options = [0 => 'Tất cả'];

        foreach ($collections as $key => $collection)
        {
            $options[$key] = $collection['name'];
        }

        return $options;
    }

    static function loadProperty(\SkillDo\Http\Request $request): void
    {
        $options = $request->input('options');

        if (hasItems($options))
        {
            $widget = new PropertyElementStyle1();

            $widget->setOption($options);

            $widget->default();

            $query = Property::where('public', 1)
                ->where('type', $widget->options->type)
                ->orderBy('order')
                ->orderBy('created', 'desc')
                ->limit((!empty($widget->options->limit)) ? $widget->options->limit : 20);

            if (!empty($widget->options->status))
            {
                $query->where($widget->options->status, 1);
            }

            $slug = '';

            $categoryId = ($widget->options->type == 'sell') ? $widget->options->categorySellId : $widget->options->categoryRentId;

            if (!empty($categoryId))
            {
                $query->where('category_id', $categoryId);
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

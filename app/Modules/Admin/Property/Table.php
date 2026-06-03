<?php
namespace RealEstate\Modules\Admin\Property;

use RealEstate\Models\Property;
use RealEstate\Models\PropertyCategory;
use RealEstate\Supports\PropertyHelper;
use RealEstate\Supports\RealEstateHelper;
use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Url;
use SkillDo\Cms\Table\Columns\ColumnImage;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\Columns\ColumnView;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;
use SkillDo\Support\Auth;

class Table extends SKDObjectTable
{
    protected string $module = 'property';

    protected mixed $model = Property::class;

    protected bool $trash = true;

    public function __construct($args = [])
    {
        parent::__construct($args);

        $this->tableChild = new TableChild();
    }

    public function getColumns(): array
    {
        $this->_column_headers = [
            'cb'    => 'cb',
            'image' => [
                'label'  => trans('real-estate::property.table.image'),
                'column' => fn($item, $args) => ColumnImage::make('image', $item, $args)->size(50),
            ],
            'name' => [
                'label'  => trans('real-estate::property.table.name'),
                'column' => fn($item, $args) => ColumnView::make('name', $item, $args)->html(function ($column) {
                    echo view('real-estate::admin/property/table/column-title', [
                        'item'        => $column->item,
                        'collections' => RealEstateHelper::collections(),
                    ]);
                }),
            ],
            'detail' => [
                'label'  => trans('real-estate::property.area'),
                'column' => fn($item, $args) => ColumnView::make('info', $item, $args)->html(function ($column)
                {
                    echo $column->item->area . ' m<sup>2</sup>';
                }),
            ],
            'city' => [
                'label'  => trans('real-estate::property.table.city'),
                'column' => fn($item, $args) => ColumnView::make('city', $item, $args)->html(function ($column)
                {
                    echo (!empty($column->item->city)) ? Location2::provinceName($column->item->city) : '--';
                }),
            ],
            'categories' => [
                'label'  => trans('real-estate::property.table.category'),
                'column' => fn($item, $args) => ColumnView::make('categories', $item, $args)->html(function ($column) {
                    if (have_posts($column->item->category)) {
                        echo sprintf('<span>%s</span>', $column->item->category->name);
                    }
                }),
            ],
            'type' => [
                'label'  => trans('real-estate::property.table.type'),
                'column' => fn($item, $args) => ColumnView::make('type', $item, $args)->html(function ($column)
                {
                    echo '<span class="label-'.$column->item->type.'">'.\RealEstate\Supports\PropertyHelper::getType($column->item->type).'</span>';
                }),
            ],
            'status' => [
                'label'  => trans('real-estate::property.table.status'),
                'column' => fn($item, $args) => ColumnText::make('status', $item, $args)->value(fn($item) => (!empty($item->status)) ? PropertyHelper::getStatus($item->status) : ''),
            ],
            'price' => [
                'label'  => trans('real-estate::property.table.price'),
                'column' => fn($item, $args) => ColumnText::make('price', $item, $args)->value(fn($item) => PropertyHelper::getPriceFull($item->price, $item->unit)),
            ]
        ];

        $this->_column_headers = apply_filters('manage_property_columns', $this->_column_headers);

        $this->_column_headers['action'] = trans('real-estate::property.table.action');

        $this->_column_headers = apply_filters('manage_property_columns_full', $this->_column_headers);

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $request    = request();

        $listButton = [];

        $trash      = (int) $request->input('trash');

        $url        = Url::admin('property/edit/' . $item->id) . ((!empty($_GET)) ? '?' . $request->getQueryString() : '');

        if ($trash == 1)
        {
            $listButton['restore'] = Admin::btnRestore([
                'id'          => $item->id,
                'model'       => Property::class,
                'des'         => trans('message.page.confirmRestore', ['title' => html_escape($item->name)]),
            ]);
        }

        if ($trash == 0)
        {
            $listButton['public'] = Admin::button(($item->public == 1) ? 'green' : 'white', [
                'class'   => 'js_property_btn_public',
                'data-id' => $item->id,
                'tooltip' => ($item->public == 1) ? trans('real-estate::property.public.show') : trans('real-estate::property.public.hidden'),
                'icon'    => ($item->public == 1) ? '<i class="fa-light fa-eye"></i>' : '<i class="fa-light fa-eye-slash"></i>',
            ]);

            $listButton['edit'] = Admin::button('blue', [
                'href'    => $url,
                'tooltip' => trans('real-estate::property.btn.update'),
                'icon'    => Admin::icon('edit'),
            ]);
        }

        $listButton = apply_filters('admin_property_table_columns_action', $listButton, $item, $request);

        if (Auth::hasCap('property_delete'))
        {
            $listButton['delete'] = Admin::btnDelete([
                'trash'       => $trash == 0,
                'id'          => $item->id,
                'model'       => Property::class,
                'description' => trans('message.page.confirmDelete', ['title' => html_escape($item->name)]),
            ]);
        }

        return $listButton;
    }

    function headerButton(): array
    {
        $buttons = [];

        if (Auth::hasCap('property_edit'))
        {
            $buttons[] = Admin::button('add', ['href' => Url::admin('property/add')]);
        }

        $buttons[] = Admin::button('reload');

        return $buttons;
    }

    function headerFilter(Form $form, Request $request)
    {
        $options    = PropertyCategory::where('public', 1)->options();

        $options[0] = trans('real-estate::property.table.all');

        $form->select2('category', $options->toArray(), [
            'label' => trans('admin::post.filter.category'),
            'start' => 3,
        ], $request->input('category'));

        $form->select2('public', [
            ''  => trans('admin::post.filter.status.select'),
            '1' => trans('admin::post.filter.status.public'),
            '0' => trans('admin::post.filter.status.hidden'),
        ], ['label' => trans('admin::post.filter.status'), 'start' => 3], $request->input('public'));

        $form->date('created', ['label' => trans('admin::post.filter.created'), 'start' => 3]);

        return apply_filters('admin_property_table_form_filter', $form);
    }

    function headerSearch(Form $form, Request $request): Form
    {
        $form->text('keyword', ['placeholder' => trans('table.search.keyword') . '...'], $request->input('keyword'));

        return apply_filters('admin_property_table_form_search', $form);
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        $keyword    = $request->input('keyword');

        $categoryID = (int) $request->input('category');

        $created    = $request->input('created');

        $public     = $request->input('public');

        if (!empty($categoryID)) $query->where('category_id', $categoryID);

        if (!empty($keyword)) $query->where('name', 'like', '%' . $keyword . '%');

        if (!empty($created))
        {
            $created = date('Y-m-d', strtotime(str_replace('/', '-', $created)));

            $query->where('created', '>=', $created . ' 00:00:00')
                ->where('created', '<=', $created . ' 23:59:59');
        }

        if (is_numeric($public)) $query->where('public', $public);

        return apply_filters('admin_property_controllers_index_args', $query);
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        $query = parent::queryDisplay($query, $request, $data);

        return $query->orderBy('order')->orderBy('created', 'desc');
    }

    public function dataDisplay($objects)
    {
        if (hasItems($objects))
        {
            $categoryListId = array_unique($objects->pluck('category_id')->toArray());

            $categories     = PropertyCategory::whereIn('id', $categoryListId)->select('id', 'name', 'slug')->get();

            foreach ($objects as $object)
            {
                $object->category = null;

                if (hasItems($categories))
                {
                    foreach ($categories as $category)
                    {
                        if ($category->id == $object->category_id)
                        {
                            $object->category = $category;
                            break;
                        }
                    }
                }
            }
        }

        return $objects;
    }
}


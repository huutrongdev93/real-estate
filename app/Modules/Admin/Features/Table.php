<?php
namespace RealEstate\Modules\Admin\Features;

use RealEstate\Models\Features;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Url;
use SkillDo\Cms\Table\Columns\ColumnImage;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;

class Table extends SKDObjectTable
{
    protected string $module = 're_features';

    protected mixed $model = Features::class;

    public function getColumns(): array
    {
        $this->_column_headers = [
            'cb'    => 'cb',
            'image' => [
                'label'  => trans('real-estate::features.table.image'),
                'column' => fn($item, $args) => ColumnImage::make('image', $item, $args)->size(50),
            ],
            'name' => [
                'label'  => trans('real-estate::features.table.name'),
                'column' => fn($item, $args) => ColumnText::make('name', $item, $args),
            ],
            'type' => [
                'label'  => trans('real-estate::features.table.type'),
                'column' => fn($item, $args) => ColumnText::make('type', $item, $args)->value(fn($item) => PropertyHelper::getFeaturesType($item->type)),
            ],
            'action' => trans('real-estate::features.table.action'),
        ];

        $this->_column_headers = apply_filters('manage_re_features_columns', $this->_column_headers);

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $listButton = [];
        $listButton['edit'] = Admin::button('blue', [
            'href'    => Url::admin('re-features/edit/' . $item->id),
            'tooltip' => trans('real-estate::features.btn.update'),
            'icon'    => Admin::icon('edit'),
        ]);

        $listButton = apply_filters('admin_re_features_table_columns_action', $listButton, $item, request());

        $listButton['delete'] = Admin::btnDelete([
            'id'          => $item->id,
            'model'       => Features::class,
            'description' => trans('message.page.confirmDelete', ['title' => html_escape($item->name)]),
        ]);

        return $listButton;
    }

    function headerButton(): array
    {
        return [
            Admin::button('add', ['href' => Url::admin('re-features/add')]),
            Admin::button('reload'),
        ];
    }

    function headerSearch(Form $form, Request $request): Form
    {
        $form->text('keyword', ['placeholder' => trans('table.search.keyword') . '...'], $request->input('keyword'));

        return apply_filters('admin_re_features_table_form_search', $form);
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        $keyword = $request->input('keyword');

        if (!empty($keyword)) $query->where('name', 'like', '%' . $keyword . '%');

        return $query;
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        parent::queryDisplay($query, $request, $data);

        return $query->orderBy('order')->orderBy('created', 'desc');
    }
}


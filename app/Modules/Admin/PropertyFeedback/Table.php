<?php
namespace RealEstate\Modules\Admin\PropertyFeedback;

use RealEstate\Models\PropertyFeedback;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Table\Columns\ColumnBadge;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\Columns\ColumnView;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;

class Table extends SKDObjectTable
{
    protected string $module = 're_feedback';

    protected mixed $model = PropertyFeedback::class;

    public function getColumns(): array
    {
        $this->_column_headers = [
            'cb'    => 'cb',
            'name'  => [
                'label'  => trans('real-estate::property-feedback.table.name'),
                'column' => fn($item, $args) => ColumnText::make('name', $item, $args),
            ],
            'email' => [
                'label'  => trans('real-estate::property-feedback.table.email'),
                'column' => fn($item, $args) => ColumnText::make('email', $item, $args),
            ],
            'phone' => [
                'label'  => trans('real-estate::property-feedback.table.phone'),
                'column' => fn($item, $args) => ColumnText::make('phone', $item, $args),
            ],
            'url' => [
                'label'  => trans('real-estate::property-feedback.table.property'),
                'column' => fn($item, $args) => ColumnView::make('url', $item, $args)->html(function ($column) {
                    echo view('real-estate::admin/property-feedback/table/column-name', ['item' => $column->item]);
                }),
            ],
            'topic' => [
                'label'  => trans('real-estate::property-feedback.table.topic'),
                'column' => fn($item, $args) => ColumnText::make('topic', $item, $args),
            ],
            'feedback' => [
                'label'  => trans('real-estate::property-feedback.table.feedback'),
                'column' => fn($item, $args) => ColumnText::make('feedback', $item, $args),
            ],
            'created' => [
                'label'  => trans('real-estate::property-feedback.table.created'),
                'column' => fn($item, $args) => ColumnText::make('created', $item, $args)->value(fn($item) => date('d/m/Y H:i', strtotime($item->created))),
            ],
            'read' => [
                'label'  => 'Trạng thái',
                'column' => fn($item, $args) => ColumnBadge::make('read', $item, $args)
                    ->color(fn($status) => $status == 1 ? 'success' : 'danger')
                    ->label(fn($status) => $status == 1 ? 'Đã xem' : 'Mới')
            ],
            'action' => trans('real-estate::property-feedback.table.action'),
        ];

        $this->_column_headers = apply_filters('manage_re_feedback_columns', $this->_column_headers);
        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $listButton = apply_filters('admin_re_feedback_table_columns_action', [], $item, request());

        $listButton['delete'] = Admin::btnDelete([
            'id'          => $item->id,
            'model'       => PropertyFeedback::class,
            'description' => trans('message.page.confirmDelete', ['title' => html_escape($item->name)]),
        ]);

        return $listButton;
    }

    function headerSearch(Form $form, Request $request): Form
    {
        $form->text('keyword', ['placeholder' => trans('table.search.keyword') . '...'], $request->input('keyword'));
        return apply_filters('admin_re_feedback_table_form_search', $form);
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        $keyword = $request->input('keyword');
        if (!empty($keyword)) $query->where('name', 'like', '%' . $keyword . '%');
        return $query;
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        return $query->orderBy('created', 'desc');
    }

    static function beforeRender(): void
    {
        $numberNew = Cache::get('re_feedback_new_count');

        if(!empty($numberNew) || $numberNew == null)
        {
            PropertyFeedback::where('read', 0)->update(['read' => 1]);

            Cache::save('re_feedback_new_count', 0);
        }
    }
}


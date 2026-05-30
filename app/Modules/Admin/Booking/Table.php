<?php
namespace RealEstate\Modules\Admin\Booking;

use RealEstate\Models\Booking;
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
    protected string $module = 're_booking';

    protected mixed $model = Booking::class;

    public function getColumns(): array
    {
        $this->_column_headers = [
            'cb'        => 'cb',
            'object_id' => [
                'label'  => trans('real-estate::booking.table.object_id'),
                'column' => fn($item, $args) => ColumnText::make('object_id', $item, $args),
            ],
            'name' => [
                'label'  => trans('real-estate::booking.table.name'),
                'column' => fn($item, $args) => ColumnView::make('name', $item, $args)->html(function ($column) {
                    echo view('real-estate::admin/booking/table/column-name', ['item' => $column->item]);
                }),
            ],
            'email' => [
                'label'  => trans('real-estate::booking.table.email'),
                'column' => fn($item, $args) => ColumnText::make('email', $item, $args),
            ],
            'phone' => [
                'label'  => trans('real-estate::booking.table.phone'),
                'column' => fn($item, $args) => ColumnText::make('phone', $item, $args),
            ],
            'created' => [
                'label'  => trans('real-estate::booking.table.created'),
                'column' => fn($item, $args) => ColumnText::make('created', $item, $args)->datetime(),
            ],
            'read' => [
                'label'  => 'Trạng thái',
                'column' => fn($item, $args) => ColumnBadge::make('read', $item, $args)
                    ->color(fn($status) => $status == 1 ? 'success' : 'danger')
                    ->label(fn($status) => $status == 1 ? 'Đã xem' : 'Mới')
            ],
            'action' => trans('real-estate::booking.table.action'),
        ];

        $this->_column_headers = apply_filters('manage_re_booking_columns', $this->_column_headers);

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $listButton = apply_filters('admin_re_booking_table_columns_action', [], $item, request());

        $listButton['delete'] = Admin::btnDelete([
            'id'          => $item->id,
            'model'       => Booking::class,
            'description' => trans('message.page.confirmDelete', ['title' => html_escape($item->name)]),
        ]);

        return $listButton;
    }

    function headerSearch(Form $form, Request $request): Form
    {
        $form->text('keyword', ['placeholder' => trans('table.search.keyword') . '...'], $request->input('keyword'));
        return apply_filters('admin_re_booking_table_form_search', $form);
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        $keyword  = $request->input('keyword');
        $created  = $request->input('created');
        $type     = $request->input('booking_type', 'property');

        $query->where('object_type', $type);

        if (!empty($keyword)) $query->where('name', 'like', '%' . $keyword . '%');

        if (!empty($created)) {
            $created = date('Y-m-d', strtotime(str_replace('/', '-', $created)));
            $query->where('created', '>=', $created . ' 00:00:00')->where('created', '<=', $created . ' 23:59:59');
        }

        return $query;
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        return $query->orderBy('created', 'desc');
    }

    static function beforeRender(): void
    {
        $numberNew = Cache::get('re_booking_new_count');

        if(!empty($numberNew) || $numberNew == null)
        {
            Booking::where('read', 0)->update(['read' => 1]);

            Cache::save('re_booking_new_count', 0);
        }
    }
}

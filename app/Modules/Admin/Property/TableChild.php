<?php
namespace RealEstate\Modules\Admin\Property;

use RealEstate\Models\Property;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Table\Columns\ColumnView;
use SkillDo\Cms\Table\SKDObjectTable;

class TableChild extends SKDObjectTable
{
    function getColumns()
    {
        $this->_column_headers = [];

        $this->_column_headers['property_name'] = [
            'label'  => trans('real-estate::property.table.name'),
            'column' => fn($item, $args) => ColumnView::make('property_child', $item, $args)
                ->attributesColumn(['colspan' => $this->colspan])
                ->html(function ($column) {
                    $item = $column->item;
                    $mapCode          = Property::getMeta($item->id, 'map_code', true);
                    $location         = Property::getMeta($item->id, 'location', true);
                    $locationDetail   = Property::getMeta($item->id, 'location_detail', true);
                    $juridical        = Property::getMeta($item->id, 'juridical', true);
                    $moreSpecs        = Property::getMeta($item->id, 'more_specifications', true);
                    $utilities        = Property::getMeta($item->id, 'utilities', true);
                    $furniture        = Property::getMeta($item->id, 'furniture', true);

                    $cityName  = (!empty($item->city)) ? Location2::provinceName($item->city) : '';
                    $wardName  = (!empty($item->city) && !empty($item->ward)) ? Location2::wardName($item->city, $item->ward) : '';

                    $typeLabel = PropertyHelper::getType($item->type ?? '');
                    $statusLabel = !empty($item->status) ? PropertyHelper::getStatus($item->status) : '';
                    $directionLabel = !empty($item->direction) ? PropertyHelper::getDirection($item->direction) : '';
                    $locationLabel  = !empty($location) ? PropertyHelper::getLocation($location) : '';
                    $propertyTypeLabel = !empty($item->property_type) ? PropertyHelper::getPropertyType($item->property_type) : '';

                    $statusColor = match($item->status ?? '') {
                        'appraised'     => '#16a34a',
                        'sold'          => '#dc2626',
                        'not-appraised' => '#d97706',
                        default         => '#6b7280',
                    };
                    $typeColor = ($item->type ?? '') === 'sell' ? '#ef7733' : '#155aa9';

                    echo view('real-estate::admin/property/table/child', [
                        'item' => $column->item,
                        'mapCode' => $mapCode,
                        'location' => $location,
                        'locationDetail' => $locationDetail,
                        'juridical' => $juridical,
                        'moreSpecs' => $moreSpecs,
                        'utilities' => $utilities,
                        'furniture' => $furniture,
                        'cityName' => $cityName,
                        'wardName' => $wardName,
                        'typeLabel' => $typeLabel,
                        'statusLabel' => $statusLabel,
                        'directionLabel' => $directionLabel,
                        'locationLabel' => $locationLabel,
                        'propertyTypeLabel' => $propertyTypeLabel,
                        'statusColor' => $statusColor,
                        'typeColor' => $typeColor,
                    ]);
                })
        ];

        return $this->_column_headers;
    }
}
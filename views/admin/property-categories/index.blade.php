{!! Admin::partial('resources/page-default/category-default', [
    'module'  => $module,
    'model'   => \RealEstate\Models\PropertyCategory::class,
    'objects' => $objects,
]) !!}


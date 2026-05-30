<?php
namespace RealEstate\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Models\GalleryItem;
use SkillDo\Cms\Models\ThemeMenuItem;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Database\Eloquent\Model;
use SkillDo\Traits\Eloquent\ModelLanguage;
use SkillDo\Traits\Eloquent\ModelRoute;
use SkillDo\Traits\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes, ModelLanguage, ModelRoute;

    protected string $table = 'property';

    protected string $primaryKey = 'id';

    protected array $columns = [
        'price' => ['price', 0],
        'image' => ['image'],
    ];

    protected array $rules = [
        'add' => [
            'takeIt' => [
                'seo_title'       => 'name',
                'seo_description' => 'excerpt',
            ],
            'require' => [
                'name' => 'message.post.insert.title',
            ],
        ],
        'update' => [
            'require' => [
                'name' => 'message.post.insert.title',
            ],
        ],
    ];

    public function __construct($attributes = [])
    {
        $this->route = [
            'type'       => 'property',
            'controller' => \RealEstate\Controllers\Web\PropertyController::class,
            'method'     => 'detail',
            'dependent'  => 'name',
        ];

        $this->language = 'property';

        parent::__construct($attributes);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('setDefaultPublicColumn', function(Builder $query)
        {
            if(!\SkillDo\Cms\Support\Admin::is())
            {
                if(!$query->isWhere($query->qualifyColumn('public')) && !$query->isWhere('public'))
                {
                    $query->where($query->qualifyColumn('public'), 1);
                }
            }
        });

        static::saving(function (Property $product)
        {
            if (empty($product->id))
            {
                if (empty($product->seo_title) && !empty($product->name))
                {
                    $product->seo_title = Str::clear($product->name);
                }
                if (empty($product->seo_description) && !empty($product->excerpt))
                {
                    $product->seo_description = Str::clear($product->excerpt);
                }
            }
        });

        static::saved(function (Property $product, $action)
        {
            Cache::delete('property_detail_' . md5($product->id), true);
            Cache::delete('breadcrumb_property_detail_' . $product->id, true);
        });

        static::trashed(function (Property $product, $params)
        {
            if (is_array($params))
            {
                foreach ($params as $id)
                {
                    Cache::delete('property_detail_' . md5($id), true);
                }
                ThemeMenuItem::whereIn('object_id', $params)->where('object_type', 'property')->delete();
            }
            elseif (is_numeric($params))
            {
                Cache::delete('property_detail_' . md5($params), true);
                ThemeMenuItem::where('object_id', $params)->where('object_type', 'property')->delete();
            }
            else
            {
                Cache::delete('property_detail_', true);
            }
        });

        static::deleted(function (Property $product, $listIdRemove, $objects)
        {
            foreach ($listIdRemove as $id)
            {
                Cache::delete('property_detail_' . md5($id), true);
                do_action('delete_property_success', $id);
            }

            GalleryItem::whereIn('object_id', $listIdRemove)->where('object_type', 'property')->delete();

            ThemeMenuItem::whereIn('object_id', $listIdRemove)->where('object_type', 'property')->delete();

            DB::table('relationships')
                ->where('object_type', 'property')
                ->whereIn('category_id', $listIdRemove)
                ->delete();

            $listProductId = [];

            foreach ($objects as $prod)
            {
                $listProductId[] = $prod->id;
            }

            if (have_posts($listProductId))
            {
                foreach ($listProductId as $id)
                {
                    Cache::delete('breadcrumb_property_detail_' . $id, true);
                }
            }
        });
    }
}


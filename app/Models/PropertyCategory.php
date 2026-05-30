<?php
namespace RealEstate\Models;

use SkillDo\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Models\GalleryItem;
use SkillDo\Cms\Models\Language;
use SkillDo\Cms\Models\Router;
use SkillDo\Cms\Models\ThemeMenuItem;
use SkillDo\Database\Eloquent\Model;
use SkillDo\Traits\Eloquent\ModelLanguage;
use SkillDo\Traits\Eloquent\ModelRoute;

class PropertyCategory extends Model
{
    use ModelLanguage, ModelRoute;

    protected string $table = 'property_categories';

    protected string $primaryKey = 'id';

    protected array $rules = [
        'nestedSet' => true,
        'add'       => [
            'takeIt' => [
                'seo_title'       => 'name',
                'seo_description' => 'excerpt',
            ],
            'require' => [
                'name' => 'real-estate::property-category.error.add_empty',
            ],
        ],
        'update' => [
            'require' => [
                'name' => 'real-estate::property-category.error.update_empty',
            ],
        ],
    ];

    public function __construct($attributes = [])
    {
        $this->route = [
            'type'       => 'property_categories',
            'controller' => \RealEstate\Controllers\Web\PropertyController::class,
            'method'     => 'index',
            'dependent'  => 'name',
        ];

        $this->language = 'property_categories';

        parent::__construct($attributes);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (PropertyCategory $category)
        {
            if (empty($category->id))
            {
                if (empty($category->seo_title) && !empty($category->name))
                {
                    $category->seo_title = Str::clear($category->name);
                }
                if (empty($category->seo_description) && !empty($category->excerpt))
                {
                    $category->seo_description = Str::clear($category->excerpt);
                }
            }
        });

        static::deleted(function (PropertyCategory $category, $listIdRemove, $categories) {
            $listId = [];

            foreach ($categories as $cat)
            {
                $listChildId = static::children(['andParent' => true, 'category' => $cat]);
                $listId      = array_merge($listId, $listChildId);
            }

            $listId = array_unique(array_merge($listId, $listIdRemove));

            if (!empty($listId))
            {
                Router::where('object_type', 'property_categories')
                    ->whereIn('object_id', $listId)->delete();

                Language::where('object_type', 'property_categories')
                    ->whereIn('object_id', $listId)->delete();

                GalleryItem::whereIn('object_id', $listId)
                    ->where('object_type', 'property_categories')->delete();

                DB::table('metabox')
                    ->where('object_type', 'property_categories')
                    ->whereIn('object_id', $listId)->delete();

                ThemeMenuItem::whereIn('object_id', $listId)
                    ->where('object_type', 'property_categories')->delete();

                DB::table('relationships')
                    ->where('object_type', 'property_categories')
                    ->whereIn('category_id', $listId)->delete();

                foreach ($listId as $id)
                {
                    Cache::delete('breadcrumb_property_index_' . $id, true);
                }

                Cache::delete('breadcrumb_property_detail_', true);
            }

            Cache::delete('menu-', true);

            Cache::delete('property_categories_', true);
        });

        static::saved(function (PropertyCategory $category, $action)
        {
            Cache::delete('property_categories_', true);
        });
    }

    public function scopeTree(Builder $query, $parentId = 0)
    {
        $object = [];

        return $query->getModel()::getsTree($object, $parentId, $query, 'tree');
    }

    public function scopeMultilevel(Builder $query, $parentId = 0)
    {
        $object = [];

        return $query->getModel()::getsTree($object, $parentId, $query, 'multilevel');
    }

    public function scopeOptions(Builder $query, $parentId = 0): Collection
    {
        $object = [];

        $query->select($this->table.'.id', $this->table.'.name', $this->table.'.level', $this->table.'.lft', $this->table.'.rgt');

        $temp = static::getsTree([], $parentId, $query, 'tree');

        if(hasItems($temp))
        {
            $object[0] = trans('real-estate::property-category.select');

            foreach($temp as $item)
            {
                $object[$item->id] = str_repeat('|-----', (($item->level > 0) ? ($item->level - 1) : 0)).$item->name;
            }
        }

        return Collection::make($object);
    }

    static function getsTree($trees, $parentId, $args, $type)
    {
        $argsClone = clone $args;

        $argsClone->orderBy('order');

        $argsClone->where('parent_id', (int)$parentId);

        $root = $argsClone->get();

        if(hasItems($root))
        {
            if($type == 'multilevel')
            {
                if(empty($trees))
                {
                    $trees = $root;

                    foreach ($trees as &$item)
                    {
                        $item->child = Collection::empty();

                        $item = static::getsTree($item, $item->id, $args, $type);
                    }
                }
                else
                {
                    $trees->child = $root;
                }

                if(!empty($trees->child))
                {
                    foreach ($trees->child as &$item)
                    {
                        $item->child = Collection::empty();

                        $item = (object)static::getsTree($item, $item->id,$args, $type);
                    }
                }
            }
            if($type == 'tree')
            {
                foreach ($root as $item)
                {
                    $trees[] = $item;

                    $trees   = static::getsTree($trees, $item->id, $args, $type);
                }
            }
        }

        return $trees;
    }

    static function children($params = []): ?array
    {
        $catalogues = NULL;

        $params['andParent'] = (isset($params['andParent']) && $params['andParent']) ? '=' : '';

        if(isset($params['lft']) && isset($params['rgt']))
        {
            $catalogues['lft'] = $params['lft'];
            $catalogues['rgt'] = $params['rgt'];
            $catalogues = (object)$catalogues;
        }
        else if(isset($params['id']))
        {
            $catalogues = PropertyCategory::select('id', 'lft', 'rgt')->where('id', $params['id'])->first();
        }
        else if(isset($params['category']))
        {
            $catalogues = $params['category'];
        }

        if(!hasItems($catalogues)) return [];

        $temp = [];

        $children = PropertyCategory::select('id')
            ->where('lft', '>'.$params['andParent'], $catalogues->lft)
            ->where('rgt', '<'.$params['andParent'], $catalogues->rgt)
            ->get();

        if(hasItems($children))
        {
            foreach($children as $val) $temp[] = $val->id;
        }

        return $temp;
    }
}


<?php
namespace RealEstate\Controllers\Web;

use RealEstate\Models\Property;
use RealEstate\Models\PropertyCategory;
use RealEstate\Supports\PropertyHelper;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Models\User;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Language;
use SkillDo\Cms\Support\Theme;
use SkillDo\Cms\Support\ThemeLayoutView;
use SkillDo\Cms\Support\Url;
use SkillDo\Http\Request;
use SkillDo\Support\Auth;
use SkillDo\Support\Cookie;

class PropertyController extends Controller
{
    /**
     * Danh sách BĐS theo danh mục (route từ slug danh mục)
     */
    public function index(Request $request, $id)
    {
        $filters  = $this->defaultFilters();

        $category = Cache::remember('property_category_' . md5($id) . '_' . Language::current(), 30 * 24 * 60 * 60, function () use ($id) {
            return PropertyCategory::whereKey($id)->first();
        });

        if (!hasItems($category))
        {
            Theme::page404();
        }

        $filters['category'] = $category->id;

        $filters['type']     = $category->type;

        $query = Property::where('category_id', $category->id)->where('type', $filters['type']);

        $query = PropertyHelper::getControllerQuery($request, $query, $filters);

        $data = PropertyHelper::getControllerData($category->slug, $query, $filters);

        Cms::setData('filters', $data['filters']);

        Cms::setData('objects', $data['objects']);

        Cms::setData('category', apply_filters('property_controllers_index_category', $category->toObject()));

        Cms::setData('pagination', $data['pagination']);

        do_action('property_controllers_index', $request);

        $layout = new ThemeLayoutView('property_index', $category);

        return Cms::view(
            apply_filters('theme_property_index_view', $layout->view(), $category),
            apply_filters('theme_property_index_layout', $layout->layout(), $category)
        );
    }

    /**
     * Tất cả BĐS theo loại (sell/rent)
     */
    public function all(Request $request, string $type)
    {
        $filters   = $this->defaultFilters();

        $filters['type'] = $type;

        $slugPage = ($type == 'sell') ? config('real-estate::routes.sell') : config('real-estate::routes.rent');

        $query = PropertyHelper::getControllerQuery($request, Property::where('type', $filters['type']), $filters);

        $data = PropertyHelper::getControllerData($slugPage, $query, $filters);

        Cms::setData('filters', $data['filters']);

        Cms::setData('objects', $data['objects']);

        Cms::setData('category', []);

        Cms::setData('pagination', $data['pagination']);

        do_action('property_controllers_index', $request);

        $layout = new ThemeLayoutView('property_all', []);

        if(empty($layout) || empty($layout->getBuilderKey()))
        {
            $layout = new ThemeLayoutView('property_index', []);
        }

        return Cms::view(
            apply_filters('theme_property_index_view', $layout->view(), []),
            apply_filters('theme_property_index_layout', $layout->layout(), [])
        );
    }

    /**
     * Danh sách BĐS yêu thích của user
     */
    public function wishlist(Request $request)
    {
        if (Auth::check())
        {
            $userID     = Auth::id();
            $wishListId = User::getMeta($userID, 'property_wish_list', true) ?: [];
        }
        else
        {
            $wishListId = Cookie::has('property_wish_list')
                ? (json_decode(Cookie::get('property_wish_list'), true) ?: [])
                : [];
        }

        $objects = [];

        $total = 0;

        $pagination = null;

        if (hasItems($wishListId))
        {
            $query = Property::where('public', 1);

            $query->whereIn('id', array_values($wishListId));

            $query  = apply_filters('property_controllers_index_wishlist_args', $query);

            $total = apply_filters('property_controllers_index_wishlist_count', (clone($query))->count());

            $url        = Url::base() . Url::permalink('bat-dong-san-yeu-thich') . '?page={page}';

            $pagination = apply_filters('property_controllers_index_wishlist_paging', pagination($total, $url, Cms::config('client_post_number')));

            $query->limit(Cms::config('client_post_number'))
                ->offset($pagination->offset())
                ->orderBy('property.order')
                ->orderBy('property.created', 'desc');

            $query = apply_filters('property_controllers_index_wishlist_params', $query);

            $objects = $query->get();
        }

        Cms::setData('objects', apply_filters('property_controllers_index_wishlist_objects', $objects));

        Cms::setData('filters', apply_filters('property_controllers_index_wishlist_filters', ['total' => $total]));

        Cms::setData('category', []);

        Cms::setData('pagination', $pagination);

        do_action('property_controllers_index_wishlist', $request);

        return Cms::view('real-estate::property/wishlist', 'template-full-width');
    }

    /**
     * Trang chi tiết BĐS
     */
    public function detail(Request $request, $id)
    {
        $object = Cache::remember('property_detail_' . md5($id) . '_' . Language::current(), config('cms.cache_time.default', 60), function () use ($id) {
            return Property::whereKey($id)->first();
        });

        if (!hasItems($object))
        {
            Theme::page404();
        }

        do_action('controllers_property_detail', $object);

        Cms::setData('object', $object);

        Cms::setData('module', 'property');

        $layout = new ThemeLayoutView('property_detail', $object);

        return Cms::view(apply_filters('theme_property_detail_view', $layout->view(), $object), apply_filters('theme_property_detail_layout', $layout->layout(), $object));
    }

    private function defaultFilters(): array
    {
        return [
            'type'      => '',
            'keyword'   => '',
            'price'     => '',
            'area'      => '',
            'status'    => '',
            'city'      => '',
            'district'  => '',
            'category'  => '',
            'bedroom'   => '',
            'bathroom'  => '',
            'direction' => '',
        ];
    }
}


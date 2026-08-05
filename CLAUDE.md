# CLAUDE.md — Plugin real-estate

File này giúp agent hiểu ngay cấu trúc plugin mà không cần scan lại source. Đọc TRƯỚC khi sửa bất kỳ file nào trong plugin.

## Plugin này là gì

**Sàn Bất Động Sản** — quản lý BĐS cho SkillDo CMS v8: danh mục BĐS **bán/thuê** (nested-set, 2 trang admin riêng theo `type`), tin đăng property, tiện ích/nội thất (features), **booking** (đặt lịch xem), **feedback** (báo cáo tin), trang danh sách/chi tiết frontend, **wishlist** (user meta hoặc cookie 30 ngày), sitemap + SEO schema `RealEstateListing`. Version 2.0.0.

- **Namespace PHP: `RealEstate\*`**. Main class `RealEstate` trong `index.php` (có thêm static `searchHome()` render widget tìm kiếm trang chủ cho theme gọi). plugin.json chỉ khai PSR-4 cho `RealEstate\Template`; các namespace khác nạp theo quy ước autoload chung của core.
- Provider `PluginServiceProvider` đăng ký 3 alias global: **`RealEstateHelper`**, **`PropertyHelper`**, **`ProjectHelper`**.
- plugin.json khai alias controller: `property`, `property_category`, `re_features`, `re_booking`, `re_feedback`.

## Database & config

**7 bảng** (tạo trong `Activator::createTables()`): `property_categories` (nested-set, có theme_layout/theme_view), `property` (địa chỉ/ward/city, hướng, giá, phòng ngủ/tắm, loại, trạng thái, cờ `status1/2/3` cho collections), `property_metadata`, `features` (type: utilities/furniture, có bản dịch), `property_price_history`, `property_feedback`, `real_estate_booking`.

**Config** `config/routes.php` → `real-estate::routes.*`: `sell` = `bat-dong-san-ban`, `rent` = `bat-dong-san-cho-thue`, `wish-list`, `collection`.

**Cache keys**: `property_detail_{md5(id)}_{lang}`, `breadcrumb_property_{category|detail}_{id}_{lang}`, `property_categories_`, `re_booking_new_count`, `re_feedback_new_count` (badge menu admin).

**Capabilities**: `property_category_list/edit/delete`, `property_list/edit/delete`, `property_booking`, `property_feedback` — cấp cho root/administrator khi activate.

## Routing đặc thù

- `routes/web.php` (trong `Route::localized()`): chỉ có danh sách bán/thuê (`{slug sell|rent}/{segment1?}/{segment2?}` → `PropertyController@all`), collection, wishlist. **KHÔNG có route tường minh cho trang chi tiết** — chi tiết resolve qua router động (`ModelRoute` trong `Property`, controller `Web\PropertyController@detail`). Debug 404 trang chi tiết → xem bảng `routes`, không phải file route.
- `routes/api.php` là placeholder rỗng.
- `segment1` của trang danh sách chứa slug city/ward tách bằng `--` (Breadcrumb::propertyAll parse).

## Map file

### bootstrap/ (mỗi file wire một mảng)

| File | Wire gì |
|---|---|
| `ajax.php` | Registry ajax: admin `saveCollection`, `searchAddress`; client `getLocations`, `wards`, `getCategories`, `property`, `booking`, `wishlist`, `feedback` |
| `assets.php` | `theme_custom_assets`/`theme_head_script_variable` → AssetService; SEO (`seo_head_base`, `schema_render`) → SeoService; sitemap (`seo_sitemap_*`) → SitemapProperty/SitemapPropertyCategory; Page Builder (`builder_layout_types`, `builder_review_layout`) + `template_layout_property_*`/`template_view_property_*` → Template\Layout; breadcrumb `theme_breadcrumb_property_*_data` → Template\Breadcrumb |
| `config.php` | Admin: navigation/assets/breadcrumb → AdminService; role → RoleService; menu object → AdminService; `admin_table_re_booking|re_feedback_before_render` → Table::beforeRender (auto mark-as-read) |
| `features.php` | `manage_re_features_input` → Features\Form |
| `property-categories.php` | `manage_property_categories_sell|rent_input` → Form::fieldsSell/fieldsRent; `insert_data_..._before_save` → dataSell/dataRent; `submit_category_args` |
| `property.php` | `manage_property_input` → Property\Form; `insert_data_property_before_save` → Form::dataSave; `add_meta_box` → Metaboxs::register; `save_property_object` → Metaboxs::save |
| `template-detail.php` / `template-index.php` / `template-object.php` / `template-wishlist.php` | Wire các renderer `Template\PropertyDetail|PropertyIndex|PropertyObject(Vertical)|PropertyWishlist` vào hook nội dung từng trang |

### app/Models/

| Model | Bảng | Ghi chú |
|---|---|---|
| `Property` | `property` | SoftDeletes + ModelLanguage + ModelRoute (`type=property`, `@detail`, dependent=`name`). Global scope ẩn bản ghi không public ngoài admin. Boot: tự sinh seo, xóa cache detail/breadcrumb, cascade dọn menu/gallery/relationships |
| `PropertyCategory` | `property_categories` | ModelLanguage + ModelRoute (`@index`), nestedSet. Scope tree/multilevel/options + static getsTree/children. Boot deleted dọn cả cây |
| `Features` | `features` | ModelLanguage |
| `Booking` | `real_estate_booking` | Model đơn giản |
| `PropertyFeedback` | `property_feedback` | Model đơn giản |

### app/Services/ & app/Supports/

- `Activator`/`Deactivator` — tạo bảng + quyền / drop bảng (⚠️ Deactivator drop nhầm `booking` — xem Gotcha #1).
- `AdminService` — menu admin (badge từ 2 cache count), assets (Leaflet map chỉ nạp ở property add/edit), breadcrumb admin, dữ liệu category cho menu builder theme.
- `AssetService` — assets frontend + biến JS `real_estate_route_sell/rent`.
- `RoleService` / `SeoService` (title/desc + schema RealEstateListing) / `SitemapProperty` (200 item/segment) / `SitemapPropertyCategory`.
- `Supports\RealEstateHelper` — `convertStringAuth()` (**ẩn số bằng `x` khi chưa đăng nhập** — tính năng ẩn giá/SĐT), `getPrice/getPriceAuth`, `getLocation/getShortLocation` (Location2), `timeElapsed()`, `collections()` (3 nhóm `status1/2/3`, có filter override).
- `Supports\PropertyHelper` — **lõi tra cứu + filter**: enum (bedroom/bathroom/type/status/direction...), `getControllerQuery()` (parse filter từ request: keyword, khoảng giá/diện tích dạng slug, city/ward theo Location2 → áp vào query), `getControllerData()` (build filters/objects/pagination, nhiều filter hook `property_controllers_index_*`).
- `Supports\ProjectHelper` — helper phần "dự án" (tiến độ, năm, giá full) — tính năng dự án chưa triển khai đầy đủ ở admin/web (language `project.php` cũng chỉ được file này dùng).

### app/Ajax/, Controllers/, Modules/

| File | Chức năng |
|---|---|
| `Ajax/Admin/PropertyAjax.php` | `saveCollection` (toggle cờ `status1/2/3` qua tên field động), `searchAddress` (proxy Nominatim/OSM cho map picker) |
| `Ajax/Web/PropertyAjax.php` | `getLocations` (toàn bộ tỉnh/phường, FE cache), `wards` (HTML options), `getCategories`, `property` (list AJAX, trả html/pagination **base64**), `booking` (insert + cache count), `wishlist` (toggle: user meta hoặc cookie), `feedback` (insert + cache count) |
| `Controllers/Admin/PropertyController.php` | index/add/edit — edit cache `property_detail_{md5(id)}_{lang}`, map `category_id` → `category_sell_id`/`category_rent_id` theo type để đổ form |
| `Controllers/Admin/PropertyCategoryController.php` | 2 trang `sell()`/`rent()` — module `property_categories_sell` / `property_categories_rent` dùng chung Form class |
| `Controllers/Admin/FeaturesController.php` / `BookingController.php` / `PropertyFeedbackController.php` | CRUD `re_features` / danh sách booking / danh sách feedback |
| `Controllers/Web/PropertyController.php` | `index($id)` (theo danh mục), `all($type)` (sell/rent, fallback layout property_all → property_index), `wishlist()`, `detail($id)` (cache + `do_action('controllers_property_detail')`). Theme override qua filter `theme_property_index|detail_view/layout` |
| `Modules/Admin/Property/Form.php` | Form chính: type sell/rent (2 select2 category hiện/ẩn theo type), property_type, price/unit/status, seo; `dataSave()` map category_sell/rent_id → `category_id` |
| `Modules/Admin/Property/Metaboxs.php` | 3 metabox: `location` (address/city/ward/lat/lng), `features` (checkbox theo từng type của Features), `specifications` (area/bedroom/bathroom/direction/juridical + repeater); `save()` chạy trên hook `save_property_object` |
| `Modules/Admin/Property/Table.php` | Table 256 dòng: trash, TableChild (expand row đọc nhiều meta), collection badges, filter category/public/created; `dataDisplay()` gom category 1 lần (tránh N+1) |
| `Modules/Admin/PropertyCategory/Form.php` | Form chung sell/rent: `fieldsSell/fieldsRent` → `fields($form,$type)`; `submitCategoryArgs()` thêm `where('type')` |
| `Modules/Admin/Booking/Table.php` / `PropertyFeedback/Table.php` | Bảng có cột `read` (badge Mới/Đã xem); `beforeRender()` **auto đánh dấu tất cả đã đọc khi mở trang** + reset cache count |
| `Modules/Admin/Features/Form.php` / `Table.php` | CRUD tiện ích: name (lang), type, image |

### app/Template/ — renderer frontend

- `PropertyDetail.php` (376 dòng) — mỗi method echo 1 view con của trang chi tiết: layout, breadcrumb, slider, info/name/address/price/status, characteristics, sectionContent/Utilities/Furniture/Map (Leaflet + Google tile), sidebarBroker/sidebarBooking, related/viewed (Swiper), modalFeedback. Đọc nhiều `Property::getMeta()` rời rạc.
- `PropertyIndex.php` — layout/search/heading/sort/list/sidebarWidget. `PropertyWishlist.php` — layout/heading/list.
- `PropertyObject.php` / `PropertyObjectVertical.php` — block card property ngang/dọc (hook `property_object_*` / `property_object_vertical_*`); footer tính trạng thái wishlist, bản vertical kèm broker.
- `Breadcrumb.php` — propertyIndex/propertyAll (parse slug city--ward từ segment)/propertyDetail, cache + `deleteCache()`.
- `Layout.php` — đăng ký 3 loại layout Page Builder (`property_all/index/detail`) + dữ liệu preview + default layout/view.

### views/ & assets/

- `views/admin/` — mỏng, gọi partial `page-index`/`page-save`; NGOẠI LỆ: `property/save.blade.php` (225 dòng — trộn page-save + modal Leaflet map-picker + JS toggle category theo type + ajax searchAddress) và `property/table/child.blade.php` (expand row ~220 dòng, ⚠️ N+1 `Features::find()` trong vòng lặp).
- `views/property/` — kiến trúc "shell + action hook": `index|detail|wishlist.blade.php` chỉ gọi `@do_action`, nội dung do Template\* + theme hook vào. `loop/item(.vertical)` = card qua hook; `loop/item-sidebar` render trực tiếp không hook. `detail/` 17 file — slider/status/section-map/related/viewed nhúng JS nặng (related + viewed trùng code Swiper gần y hệt).
- `views/search/` — bộ filter control (location/categories/price/area/bedroom/bathroom/direction) cho form GET độc lập (`search/index.blade.php` — widget/element gọi qua `RealEstate::searchHome()`); **song song** với search AJAX của trang danh sách (`property/index/search.blade.php`, class JS `RealEstatePropertySearch`) — 2 UI không share code, cái AJAX gắn với PropertyController, cái GET dùng cho widget trang chủ.
- `assets/js/real-estate-admin-script.js` — `RealEstateLocation` (ward theo city) + toggle collection. `real-estate-script.js` — scrollspy, submit booking, toggle wishlist, hiện SĐT, modal báo cáo. CSS viết bằng **LESS** (sửa `.less`).

### language/

3 ngôn ngữ **en/vi/zh**, namespaces: `admin`, `ajax`, `booking`, `features`, `lang-js`, `project` (chỉ ProjectHelper dùng), `property-category`, `property-feedback`, `property` (lớn nhất). Gọi `trans('real-estate::property.x')`.

## Hook conventions

Module CRUD chuẩn với `{module}` ∈ `property`, `re_features`, `re_booking`, `re_feedback`, `property_categories_sell|rent`: `manage_{module}_input`, `manage_{module}_columns(_full)`, `admin_{module}_table_columns_action`, `admin_{module}_table_form_search|filter`. Property thêm: `insert_data_property_before_save`, `save_property_object` (metabox save), `admin_property_controllers_index_args`.

Frontend: nội dung trang qua action `content_property_index|detail|index_wishlist`; card qua `property_object_*` / `property_object_vertical_*`; layout/view override qua filter `theme_property_index|detail_view/layout` + `template_layout_property_*`; filter danh sách qua `property_controllers_index_*`; chi tiết bắn `controllers_property_detail`.

## Quy tắc khi sửa

1. **Trang chi tiết không có route file** — resolve qua ModelRoute/bảng `routes`. Trang danh sách phân biệt sell/rent bằng tham số `type` từ route config.
2. Property có 1 cột `category_id` duy nhất nhưng form admin dùng 2 field `category_sell_id`/`category_rent_id` — mapping 2 chiều nằm ở `Form::dataSave()` (lưu) và `PropertyController::edit()` (đổ form). Sửa một phía phải sửa phía kia.
3. Booking/Feedback dùng cache đếm (`re_booking_new_count`, `re_feedback_new_count`) cho badge menu; `Table::beforeRender` auto mark-read + reset cache — thêm bản ghi mới phải cập nhật cache count.
4. Wishlist có 2 đường lưu: user login → `User::updateMeta`, khách → cookie 30 ngày. Sửa phải giữ cả 2.
5. Tính năng ẩn thông tin với khách chưa đăng nhập đi qua `RealEstateHelper::convertStringAuth()/getPriceAuth()` — đừng render giá/SĐT trực tiếp.
6. Xóa cache đúng key khi đụng property/category (xem mục Cache keys). CSS sửa file `.less`. Chuỗi dùng `trans('real-estate::...')`.
7. Có 2 UI search song song (AJAX trong trang danh sách vs form GET widget) — xác định đúng cái đang sửa trước khi đụng.

## Gotcha / nghi vấn bug (đã phát hiện khi scan, CHƯA sửa)

1. `app/Services/Deactivator.php:14` — uninstall drop bảng `booking` nhưng bảng thật tên `real_estate_booking` → gỡ plugin để rác bảng booking lại DB.
2. `app/Template/PropertyDetail.php:23` — `$watchedList[] = $id ?? 0;` nhưng `$id` chưa từng được gán (phải là `$object->id`) → session `viewed_property` toàn số 0, tính năng "tin đã xem" không hoạt động.
3. `app/Services/AssetService.php:4-5` — import chết `Ecommerce\Supports\Config`/`Prd` (copy-paste từ sicommerce, không dùng trong file).
4. `views/property/index/search.blade.php:5` — attribute HTML dở dang `data->Cho thuê` (nghi thiếu `data-type="rent"`).
5. `views/admin/property/table/child.blade.php` — N+1: `Features::find()` trong 2 vòng `@foreach` (utilities + furniture); nên `whereIn` 1 lần như `PropertyDetail::sectionUtilities` đã làm đúng.
6. `Modules/Admin/Property/Metaboxs.php:155` — `Property::insert(['id' => $id, ...])` với id đã tồn tại — chỉ đúng nếu `Model::insert` của framework hỗ trợ upsert theo PK (cần xác nhận trước khi bắt chước pattern này).
7. Tính năng "dự án" (ProjectHelper + language `project.php`) chưa triển khai đầy đủ ở admin/web — đừng coi là tính năng hoạt động.

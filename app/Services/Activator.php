<?php
namespace RealEstate\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use SkillDo\Cms\Support\Role;

class Activator
{
    public static function activate(): void
    {
        static::createTables();

        static::role();
    }

    public static function createTables(): void
    {
        if (!schema()->hasTable('property_categories'))
        {
            schema()->create('property_categories', function (Blueprint $table)
            {
                $table->increments('id');
                $table->string('name', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('excerpt')->nullable();
                $table->longtext('content')->nullable();
                $table->string('type', 100)->default('sell');
                $table->string('image', 255)->nullable();
                $table->string('seo_title', 255)->nullable();
                $table->text('seo_description')->nullable();
                $table->text('seo_keywords')->nullable();
                $table->integer('order')->default(0);
                $table->tinyInteger('public')->default(1);
                $table->integer('status')->default(0);
                $table->integer('parent_id')->default(0);
                $table->integer('level')->default(0);
                $table->integer('lft')->default(0);
                $table->integer('rgt')->default(0);
                $table->string('key', 255)->nullable();
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->string('theme_layout', 100)->nullable();
                $table->string('theme_view', 100)->nullable();
            });
        }

        if (!schema()->hasTable('property'))
        {
            schema()->create('property', function (Blueprint $table)
            {
                $table->increments('id');
                $table->string('name', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('excerpt')->nullable();
                $table->longtext('content')->nullable();
                $table->string('image', 255)->nullable();
                $table->tinyInteger('public')->default(1);
                $table->tinyInteger('trash')->default(0);
                $table->string('status', 100)->default('public');
                $table->tinyInteger('status1')->default(0);
                $table->tinyInteger('status2')->default(0);
                $table->tinyInteger('status3')->default(0);
                $table->string('address', 255)->nullable();
                $table->integer('ward')->default(0);
                $table->integer('city')->default(0);
                $table->integer('direction')->default(0);
                $table->integer('project_id')->default(0);
                $table->string('area', 100)->nullable();
                $table->integer('category_id')->default(0);
                $table->integer('bedroom')->default(0);
                $table->integer('bathroom')->default(0);
                $table->bigInteger('price')->default(0);
                $table->string('unit', 100)->nullable();
                $table->integer('property_type')->default(0);
                $table->string('type', 100)->nullable();
                $table->string('seo_title', 255)->nullable();
                $table->text('seo_description')->nullable();
                $table->text('seo_keywords')->nullable();
                $table->integer('order')->default(0);
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->string('theme_layout', 100)->nullable();
                $table->string('theme_view', 100)->nullable();
            });
        }

        if (!schema()->hasTable('property_metadata'))
        {
            schema()->create('property_metadata', function (Blueprint $table)
            {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->string('meta_key', 100)->nullable();
                $table->text('meta_value')->collation('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->index('object_id');
                $table->index(['meta_key', 'object_id']);
            });
        }

        if (!schema()->hasTable('features'))
        {
            schema()->create('features', function (Blueprint $table)
            {
                $table->increments('id');
                $table->string('name', 255)->nullable();
                $table->string('image', 255)->nullable();
                $table->string('type', 100)->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
        }

        if (!schema()->hasTable('property_price_history'))
        {
            schema()->create('property_price_history', function (Blueprint $table)
            {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->bigInteger('price_old')->default(0);
                $table->string('unit_old', 100)->nullable();
                $table->bigInteger('price')->default(0);
                $table->string('unit', 100)->nullable();
                $table->string('description', 255)->nullable();
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
        }

        if (!schema()->hasTable('property_feedback'))
        {
            schema()->create('property_feedback', function (Blueprint $table)
            {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->string('object_name', 255)->nullable();
                $table->longtext('url')->nullable();
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->longtext('topic')->nullable();
                $table->longtext('feedback')->nullable();
                $table->integer('read')->default(0);
                $table->integer('user_created')->default(0);
                $table->integer('user_updated')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
        }

        if (!schema()->hasTable('real_estate_booking'))
        {
            schema()->create('real_estate_booking', function (Blueprint $table)
            {
                $table->increments('id');
                $table->integer('user_id')->default(0);
                $table->integer('read')->default(0);
                $table->string('name', 255)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('url', 255)->nullable();
                $table->integer('time')->default(0);
                $table->integer('object_id')->default(0);
                $table->string('object_type', 100)->nullable();
                $table->string('object_name', 255)->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            });
        }
    }

    public static function role(): void
    {
        $roles = ['root', 'administrator'];

        $permissions = array_keys(RoleService::label());

        foreach ($roles as $role)
        {
            $role = Role::get($role);

            foreach ($permissions as $permission)
            {
                $role->add($permission);
            }
        }
    }
}


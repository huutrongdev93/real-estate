<?php
namespace RealEstate\Providers;

use RealEstate\Supports\PropertyHelper;
use RealEstate\Supports\ProjectHelper;
use RealEstate\Supports\RealEstateHelper;
use SkillDo\AliasLoader;
use SkillDo\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('RealEstateHelper', RealEstateHelper::class);
        $loader->alias('PropertyHelper', PropertyHelper::class);
        $loader->alias('ProjectHelper', ProjectHelper::class);
    }
}


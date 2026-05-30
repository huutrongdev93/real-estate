<?php
namespace RealEstate\Services;

class Deactivator
{
    public static function uninstall(): void
    {
        schema()->dropIfExists('property_categories');
        schema()->dropIfExists('property');
        schema()->dropIfExists('property_metadata');
        schema()->dropIfExists('property_price_history');
        schema()->dropIfExists('property_feedback');
        schema()->dropIfExists('features');
        schema()->dropIfExists('booking');
    }
}


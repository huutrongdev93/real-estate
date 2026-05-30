<?php

use RealEstate\Template\PropertyDetail;

add_action('content_property_detail',  [PropertyDetail::class, 'layout'], 10);

add_action('property_detail_top',      [PropertyDetail::class, 'slider'], 10);
add_action('property_detail_top',      [PropertyDetail::class, 'breadcrumb'], 20);

add_action('property_detail_main',     [PropertyDetail::class, 'info'], 30);
add_action('property_detail_info',     [PropertyDetail::class, 'status'], 10);
add_action('property_detail_info',     [PropertyDetail::class, 'name'], 20);
add_action('property_detail_info',     [PropertyDetail::class, 'address'], 30);
add_action('property_detail_info',     [PropertyDetail::class, 'price'], 40);

add_action('property_detail_main',     [PropertyDetail::class, 'characteristics'], 40);
add_action('property_detail_main',     [PropertyDetail::class, 'sectionContent'], 50);
add_action('property_detail_main',     [PropertyDetail::class, 'sectionUtilities'], 60);
add_action('property_detail_main',     [PropertyDetail::class, 'sectionFurniture'], 70);
add_action('property_detail_main',     [PropertyDetail::class, 'sectionMap'], 80);
add_action('property_detail_main',     [PropertyDetail::class, 'related'], 90);
add_action('property_detail_main',     [PropertyDetail::class, 'viewed'], 100);

add_action('property_detail_after',    [PropertyDetail::class, 'modalFeedback'], 100);

add_action('property_detail_sidebar',  [PropertyDetail::class, 'sidebarBroker'], 10);
add_action('property_detail_sidebar',  [PropertyDetail::class, 'sidebarBooking'], 20);


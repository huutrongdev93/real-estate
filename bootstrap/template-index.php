<?php

use RealEstate\Template\PropertyIndex;

add_action('content_property_index',    [PropertyIndex::class, 'layout'], 10);
add_action('page_property_index_search',[PropertyIndex::class, 'search'], 10);
add_action('page_property_index_view',  [PropertyIndex::class, 'heading'], 20);
add_action('page_property_index_view',  [PropertyIndex::class, 'sort'], 30);
add_action('page_property_index_view',  [PropertyIndex::class, 'list'], 40);

add_action('property_index_sidebar',    [PropertyIndex::class, 'sidebarWidget'], 10);


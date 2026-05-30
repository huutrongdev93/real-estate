<?php

use RealEstate\Template\PropertyWishlist;

add_action('content_property_index_wishlist',       [PropertyWishlist::class, 'layout'], 10);
add_action('page_property_index_wishlist_view',     [PropertyWishlist::class, 'heading'], 20);
add_action('page_property_index_wishlist_view',     [PropertyWishlist::class, 'list'], 40);


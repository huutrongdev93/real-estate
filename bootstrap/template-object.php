<?php

use RealEstate\Template\PropertyObject;
use RealEstate\Template\PropertyObjectVertical;

add_action('property_object_image',        [PropertyObject::class, 'image'], 20);
add_action('property_object_after_image',  [PropertyObject::class, 'image_gallery'], 20);
add_action('property_object_info',         [PropertyObject::class, 'status'], 20);
add_action('property_object_info',         [PropertyObject::class, 'name'], 30);
add_action('property_object_info',         [PropertyObject::class, 'price'], 40);
add_action('property_object_info',         [PropertyObject::class, 'address'], 50);
add_action('property_object_info',         [PropertyObject::class, 'footer'], 70);

add_action('property_object_vertical_image',       [PropertyObjectVertical::class, 'image'], 20);
add_action('property_object_vertical_after_image', [PropertyObjectVertical::class, 'image_gallery'], 20);
add_action('property_object_vertical_info',        [PropertyObjectVertical::class, 'status'], 20);
add_action('property_object_vertical_info',        [PropertyObjectVertical::class, 'name'], 30);
add_action('property_object_vertical_info',        [PropertyObjectVertical::class, 'information'], 40);
add_action('property_object_vertical_info',        [PropertyObjectVertical::class, 'description'], 50);
add_action('property_object_vertical_after',       [PropertyObjectVertical::class, 'footer'], 70);


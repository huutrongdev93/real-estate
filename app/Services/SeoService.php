<?php
namespace RealEstate\Services;

use Illuminate\Support\Str;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Url;

class SeoService
{
    static public function headBase($headService, $page)
    {
        if($page == 'property_detail')
        {
            $object = Cms::getData('object');
        }

        if($page == 'property_index')
        {
            $object = Cms::getData('category');
        }

        if(!empty($object))
        {
            $title 		= (!empty($object->seo_title)) ? $object->seo_title : $object->name;
            $description= (!empty($object->seo_description)) ? $object->seo_description : Str::clear($object->excerpt);
            $keyword 	= (!empty($object->seo_keywords)) ? $object->seo_keywords : '';
            $image 		= (!empty($object->image)) ? $object->image : '';

            $headService
                ->setTitle($title)
                ->setDescription($description)
                ->setKeyword($keyword);

            if(!empty($image))
            {
                $headService->setImage($image);
            }
        }

        return $headService;
    }

    static public function schema($schemas, $page)
    {
        if($page == 'property_detail')
        {
            $object = Cms::getData('object');

            if(!empty($object))
            {
                $schemas[] = [
                    "@context"      => 'http://schema.org',
                    "@type"         => "RealEstateListing",
                    "name"          => $object->name,
                    "image"         => asset('source/'.$object->image),
                    "description" 	=> (!empty($object->seo_description)) ? Str::clear($object->seo_description) : Str::clear($object->excerpt),
                    "url"           => Url::base().'/'.$object->slug,
                    "price"      => $object->price,
                    "priceCurrency" => "VND",
                ];
            }
        }

        return $schemas;
    }
}
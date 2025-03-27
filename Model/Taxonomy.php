<?php

namespace Mindmycat\Model;

class Taxonomy 
{
    
    /**
     * 
     *  $pet_type->count
     *  $pet_type->term_id
     *  $pet_type->slug
     *  $pet_type->description
     * 
     */
    public static function getAllPetTypesAndPluck()
    {

        $pet_types = self::getAllTermsFromTaxoBySlug('pet-type');

        $ret = [];

        foreach( $pet_types as $pet_type ) {

            $ret[$pet_type->term_id] = [
                'name' => $pet_type->name,
                'slug' => $pet_type->slug,
            ];
        }

        return $ret;
    }

    public static function getAllLocationAndPluck()
    {

        $terms = self::getAllTermsFromTaxoBySlug('service_location');

        $ret = [];

        foreach( $terms as $item ) {

            $ret[$item->term_id] = [
                'name' => $item->name,
                'slug' => $item->slug,
            ];
        }

        return $ret;
    }


    public static function getAllTermsFromTaxoBySlug( $slug, $hide_empty = false )
    {

        $terms = get_terms(array(
            'taxonomy' => $slug,
            'hide_empty' => $hide_empty
        ));

        if ( is_wp_error($terms) ) {

            return [];
        }
        
        return $terms;
    }
}

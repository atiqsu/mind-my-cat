<?php

namespace Mindmycat\Model;

use Mindmycat\Handler\Installer;


/**
 * ACF docs - 
 * 
 * $post_id = false; // current post
 * $post_id = 1; // post ID = 1
 * $post_id = "user_2"; // user ID = 2
 * $post_id = "category_3"; // category term ID = 3
 * $post_id = "event_4"; // event (custom taxonomy) term ID = 4
 * $post_id = "option"; // options page
 * $post_id = "options"; // same as above
 * 
 * $value = get_field( 'my_field', $post_id );
 * 
 * 
 * $user_fields = get_fields( 'user_2' );
 * 
 */
class ACF
{

    public static function getSitterPreferencesFields()
    {
        return self::getFieldGroupsByFieldId('group_67d1708505a80');
    }

    public static function getSitterAvailabilityFields()
    {
        return self::getFieldGroupsByFieldId('group_67de78f110079');
    }

    public static function getServicePricingFields()
    {
        return self::getFieldGroupsByFieldId('group_67d04a08a9af4');
    }

    public static function getServiceDetailsFields()
    {
        return self::getFieldGroupsByFieldId('group_67d04c2ca4b3d');
    }
    


    public static function get_service_pricing($post_id)
    {
        return self::getServiceMetaFields($post_id, 'group_67d04a08a9af4');
    }

    public static function getSitterServiceChoiceField()
    {
        
        return self::getServiceMetaFields( 'option', 'group_67d1708505a80' );
    }

    
    public static function getServiceMetaFields($service_id, $group_id) 
    {
        $ret = [];
        $group_fileds = acf_get_fields($group_id);


        foreach ( $group_fileds as $field ) {
            $value = get_field( $field['name'], $service_id );

            $ret[$field['name']] = $value;
        }

        return $ret;
    }

    public static function getUserMetaField($group_fileds, $user_id)
    {

        $meta_key = 'user_'. $user_id;

        $ret = [];


        foreach ( $group_fileds as $field ) {
            $value = get_field( $field['name'], $meta_key );

            $ret[$field['name']] = $value;
        }

        return $ret;
    }


    public static function getSitterAvailabilityInfo($user_id)
    {
        $meta_key = 'user_'. $user_id;

        return self::getServiceMetaFields($meta_key, 'group_67de78f110079');
    }

    public static function getSitterPreferences($user_id)
    {
        $meta_key = 'user_'. $user_id;

        return self::getServiceMetaFields( $meta_key, 'group_67d1708505a80' );
    }

    protected static function getFieldGroupsByFieldId($groupId)
    {
        return acf_get_fields($groupId);
    }
}


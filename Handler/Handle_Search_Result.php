<?php

namespace Mindmycat\Handler;

use Mindmycat\Helper;
use Mindmycat\Model\ACF;
use Mindmycat\Model\Users;

class Handle_Search_Result
{
    public function __construct()
    {
        //add_action('wp_ajax_bpc_search_result', [$this, 'search_result']);
        //add_action('wp_ajax_nopriv_bpc_search_result', [$this, 'search_result']);
    }
    

    public static function search_pet_sitter(array $userFilter)
    {

        $result = [];
        $service_wanted = (array) $userFilter['services'];
        $service_location = $userFilter['service_area'];
        $start_date = $userFilter['start_date'];
        $end_date = $userFilter['end_date'];

        $sitters = Users::getAllPetSitterByRole();
        $pref_fields = ACF::getSitterPreferencesFields();


        foreach ($sitters as $user) {

            $service_meta = ACF::getUserMetaField($pref_fields, $user->ID);

            // todo - check if sitter profile is accepted or not yet


            if ( ! self::searchMatchByIdForServiceWanted($service_meta, $service_wanted) ) {

                continue;
            }

            if ( ! self::searchMatchForServiceLocation($service_location, $user->ID, $service_meta) ) {

                continue;
            }

            if ( ! self::searchMatchAvailableDate($user->ID, $start_date, $end_date) ) {

                continue;
            }


            $result[] = [
                'user_id' => $user->ID,
                'name' => $user->user_nicename,
                'img' => Helper::get_asset_url('avatar.jpg')
            ];
        
        }

        return $result;
    }

    protected static function searchMatchByIdForServiceWanted($service_meta, $seraching_service_ids): bool
    {

        if(empty($service_meta['preferred_services'])) {

            return true;
        }

        foreach( $service_meta['preferred_services'] as $item ) {

            if (in_array($item->ID, $seraching_service_ids)) {

                return true;
            }
        }

        return false;
    }

    protected static function searchMatchForServiceLocation($name, $userId, $service_meta) 
    {
        // todo - cached location array while saving sitter preference
        $cachedLoc = Users::getSitterPreferredLocationFromCache($userId, $service_meta);

        return true; // todo -
        return in_array($name, $cachedLoc);
    }

    protected static function searchMatchAvailableDate($userId, $start_date, $end_date)
    {
        // need working with schedule
        // get all sitter calender in between start and end date

        return true;
    }
}

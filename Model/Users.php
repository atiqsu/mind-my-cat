<?php

namespace Mindmycat\Model;

class Users 
{

    protected string $table = 'types';

    public static function hasPetSitterRole(\WP_User $user)
    {
        return in_array('pet_sitter', $user->roles);
    }

    public static function hasPetOwnerRole(\WP_User $user)
    {
        return in_array('pet_owner', $user->roles);
    }

    public static function hasAdminRole(\WP_User $user)
    {
        return in_array('administrator', $user->roles);
    }

    public static function isAPetOwner($user_id)
    {
        $user = get_user_by('id', $user_id);

        return in_array('pet_owner', $user->roles);
    }

    public static function isAPetSitter($user_id)
    {
        $user = get_user_by('id', $user_id);

        return in_array('pet_sitter', $user->roles);
    }

    public static function isAnAdmin($user_id)
    {
        $user = get_user_by('id', $user_id);

        return in_array('administrator', $user->roles);
    }


    public static function getAllPerSitter()
    {
        return get_users([
            'role' => 'pet_sitter',
            'orderby' => 'display_name',
        ]);
    }

    public static function getAllPetOwners()
    {
        return get_users(['role' => 'pet_owner']);
    }

    public static function getStatusListForPetSitter()  : array
    {
        return array( 
            'pending' => 'Pending', 
            'reviewing' => 'Reviewing', 
            'accepted' => 'Accepted', 
            'banned' => 'Banned',
        );
    }

    public static function isAValidSitterStatus($status)
    {
        $list = self::getStatusListForPetSitter();

        return isset($list[$status]);
        #return in_array($status, self::getStatusListForPetSitter());
    }

    public static function getPetSitterStatusMeta( $idd )
    {

        $status = get_user_meta( $idd, 'pet_sitter_status', true );

        if ( empty( $status ) ) {
            self::updateSitterStatusMeta($idd, $status);
        }

        return $status;
    }

    public static function updateSitterStatusMeta( $user_id, $status )
    {
        return update_user_meta($user_id, 'pet_sitter_status', sanitize_text_field($status));
    }
    
    /**
     * todo - need more working here
     * 
     */
    public static function getSitterPreferredLocationFromCache( $userId, $service_meta ): array 
    {

        $cachedLoc = false;
        $loc = [];

        if ($cachedLoc === false) {

            // todo - this is very temporary, bare minimum func

            if(empty($service_meta['preferred_location'])) {

                return [];
            }

            foreach( $service_meta['preferred_location'] as $item ) {

                $loc[] = $item->name;
            }
        } else {
            //.... todo later
        }

        return $loc;
    }

    public static function getAllPetSitterByRole()
    {

        $args = [
            'role' => 'pet_sitter',
            'orderby' => 'display_name',
        ];

        return get_users($args);
    }
}


<?php

namespace Mindmycat\Model;

class Role 
{

    public static function add_default()
    {

        add_role(
            'pet_owner',
            __( 'Pet Owner', 'petcare-service' ),
            array(
                'read' => true,  // todo - adjust caps later
            )
        );
    
    
        add_role(
            'pet_sitter',
            __( 'Pet Sitter', 'petcare-service' ),
            array(
                'read' => true,  // todo - adjust caps later
            )
        );
    
  
        add_role(
            'pet_staff',
            __( 'Pet Staff', 'petcare-service' ),
            array(
                'read'        => true,         // todo - adjust caps later
                'edit_posts'  => true,        
                'upload_files' => true,        
                'moderate_comments' => true,  
            )
        );
    }

    public static function remove_default()
    {
        remove_role( 'pet_owner' );
        remove_role( 'pet_sitter' );
        remove_role( 'pet_staff' );
    }

    public static function isASitter($roles)
    {
        return in_array( 'pet_sitter', $roles);
    }

    public static function isPetOwner($roles)
    {
        return in_array( 'pet_owner', $roles);
    }
    
}


<?php

namespace Mindmycat\Cpt;


/**
 * Some examples: Dog walking, Vaccinations, Kennel Attendants, Drying Washing
 */
class Services extends Cpt
{

    protected string $cpt_slug = 'service';

    public function init()
    {
        add_action( 'init', [$this, 'register'] );
        
    }

    public function register()
    {

        $labels_services = array(
            'name'               => _x( 'Services', 'Post Type General Name', 'petcare-service' ),
            'singular_name'      => _x( 'Service', 'Post Type Singular Name', 'petcare-service' ),
            'menu_name'          => esc_html__( 'Pet Services', 'petcare-service' ),
            'name_admin_bar'     => esc_html__( 'Service', 'petcare-service' ),
            'parent_item_colon'  => esc_html__( 'Parent Service:', 'petcare-service' ),
            'all_items'          => esc_html__( 'All Services', 'petcare-service' ),
            'add_new_item'       => esc_html__( 'Add New Service', 'petcare-service' ),
            'add_new'            => esc_html__( 'Add New', 'petcare-service' ),
            'new_item'           => esc_html__( 'New Service', 'petcare-service' ),
            'edit_item'          => esc_html__( 'Edit Service', 'petcare-service' ),
            'update_item'        => esc_html__( 'Update Service', 'petcare-service' ),
            'view_item'          => esc_html__( 'View Service', 'petcare-service' ),
            'search_items'       => esc_html__( 'Search Services', 'petcare-service' ),
            'not_found'          => esc_html__( 'Not found', 'petcare-service' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'petcare-service' ),
        );

        $args_services   = array(
            'label'               => __( 'Service', 'petcare-service' ),
            'description'         => __( 'Services offered by Pet Sitters', 'petcare-service' ),
            'labels'              => $labels_services,
            'supports'            => array( 'title' ),
            'taxonomies'          => array( 'pet-type' ),
            'hierarchical'        => false,
            'public'              => false, 
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 6,
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
            'show_in_rest'        => true, 
        );
        
        register_post_type( $this->cpt_slug, $args_services );
    }

    protected function hasPermission($post_id)
    {

        // todo - user permission - check later 
        #return current_user_can( 'edit_post', $post_id );
        return true;
    }
}

<?php

namespace Mindmycat\Pages;

use Mindmycat\Model\Users;

class Sitter_Profile
{

    public $parent;

    public function __construct($parent_slug)
    {
        $this->parent = $parent_slug;
    }

    public function init()
    {
        add_submenu_page(
            $this->parent,
            __( 'Profile', 'mind-my-cat' ), 
            __( 'Profile', 'mind-my-cat' ), 
            'manage_options', 
            'sitter-profile-view',
            [$this, 'render']
        );
    }


    public function render()
    {
        
        $user = wp_get_current_user();

        if( !Users::hasPetSitterRole($user) && !Users::hasAdminRole($user) ) {
            
            echo '<div class="notice notice-error"><p>' . esc_html__( 'You are not a Pet Sitter.', 'mind-my-cat' ) . '</p></div>';
            
            return;
        }

        $user_id = intval($_GET['profile_id'] ?? 0);

        if( $user_id == 0 ) {

            echo '<h1>Pet Sitter Profile:</h1>';
            echo '<div class="notice notice-error"><p>' . esc_html__( 'No sitter id provided.', 'mind-my-cat' ) . '</p></div>';
            
            return;
        }

        $user = get_user_by('id', $user_id);
    
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Pet Sitter Profile:', 'mind-my-cat' ); ?></h1>
    
            <table class="wp-list-table widefat fixed striped">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Username', 'mind-my-cat' ); ?></th>
                        <td><?php echo esc_html( $user->user_login ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Email', 'mind-my-cat' ); ?></th>
                        <td><?php echo esc_html( $user->user_email ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Name', 'mind-my-cat' ); ?></th>
                        <td><?php echo esc_html( $user->first_name . ' ' . $user->last_name ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Phone', 'mind-my-cat' ); ?></th>
                        <td><?php echo esc_html( get_user_meta( $user_id, 'phone', true ) ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Address', 'mind-my-cat' ); ?></th>
                        <td><?php echo esc_html( get_user_meta( $user_id, 'address', true ) ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Status', 'mind-my-cat' ); ?></th>
                        <td><?php echo esc_html( ucfirst( get_user_meta( $user_id, 'pet_sitter_status', true ) ) ); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Services Providing', 'mind-my-cat' ); ?></th>
                        <td>
                            <?php
                            $service_providing_ids = get_user_meta( $user_id, 'service_providing', true );

                            if ( ! empty( $service_providing_ids ) && is_array( $service_providing_ids ) ) {
                                $service_titles = array();
                                foreach ( $service_providing_ids as $service_id ) {
                                    $service_titles[] = get_the_title( $service_id );
                                }
                                echo esc_html( implode( ', ', $service_titles ) );
                            } else {
                                esc_html_e( 'Not set', 'mind-my-cat' );
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Preferred Locations', 'mind-my-cat' ); ?></th>
                        <td>
                            <?php
                            $preferred_locations = get_user_meta( $user_id, 'preferred_location', true );
                            if ( ! empty( $preferred_locations ) && is_array( $preferred_locations ) ) {
                                echo esc_html( implode( ', ', $preferred_locations ) );
                            } else {
                                esc_html_e( 'Not set', 'mind-my-cat' );
                            }
                            ?>
                        </td>
                    </tr>   
                </tbody>
            </table>
        </div>

        <?php

    }
}

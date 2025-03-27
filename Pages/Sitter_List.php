<?php

namespace Mindmycat\Pages;

use Mindmycat\Helper;
use Mindmycat\Model\Users;

class Sitter_List
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
            __( 'Pet Sitters', 'mind-my-cat' ), 
            __( 'Pet Sitters', 'mind-my-cat' ), 
            'manage_options',
            'pet_sitter_list',
            [$this, 'render']
        );
    }


    /**
     * todo - translate all the strings 
     */
    public function render()
    {
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_POST['sitter_status'])) {
            foreach ($_POST['sitter_status'] as $user_id => $status) {
                Users::updateSitterStatusMeta( $user_id, $status );
            }

            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Statuses updated successfully!', 'mind-my-cat' ) . '</p></div>';
        }

        $statusList = Users::getStatusListForPetSitter();
        $petSitters = Users::getAllPerSitter();
    
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Pet Sitter Management', 'mind-my-cat' ); ?></h1>
    
            <form method="post">
                <table class="wp-list-table widefat fixed striped users">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column"><?php esc_html_e( 'Name', 'mind-my-cat' ); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e( 'Email', 'mind-my-cat' ); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e( 'Status', 'mind-my-cat' ); ?></th>
						    <th scope="col" class="manage-column"><?php esc_html_e( 'Profile', 'mind-my-cat' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
    
                        if ( ! empty( $petSitters ) ) {
                            foreach ( $petSitters as $user ) {
                                $cur_status = Users::getPetSitterStatusMeta( $user->ID );
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $user->display_name ); ?></td>
                                    <td><?php echo esc_html( $user->user_email ); ?></td>
                                    <td>
                                        
                                        <select name="sitter_status[<?php echo esc_attr( $user->ID ); ?>]"> <?php 
                                            
                                            foreach($statusList as $status => $aText) {
                                                
                                                echo '<option value="'.$status.'" '. selected( $cur_status, $status ) . ' >'. $aText .'</option>';
                                            } ?>

                                        </select>
                                    </td>

                                    <td>
                                        <a href="<?php echo esc_url( Helper::get_sitter_profile_url($user->ID) ); ?>"> View </a>
								    </td>

                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="3"><?php esc_html_e( 'No Pet Sitter users found.', 'mind-my-cat' ); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="col" class="manage-column"><?php esc_html_e( 'Username', 'mind-my-cat' ); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e( 'Email', 'mind-my-cat' ); ?></th>
                            <th scope="col" class="manage-column"><?php esc_html_e( 'Status', 'mind-my-cat' ); ?></th>
                        </tr>
                    </tfoot>
                </table>
                <?php if ( ! empty( $petSitters ) ) : ?>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Update Statuses', 'mind-my-cat' ); ?>">
                    </p>
                <?php endif; ?>
            </form>
        </div>
        <?php
        
    }
}

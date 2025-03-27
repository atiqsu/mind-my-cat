<?php

namespace Mindmycat\Pages;

use Mindmycat\Helper;
use Mindmycat\Model\Users;

class Owner_List
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
            __( 'Pet Owners', 'mind-my-cat' ), 
            __( 'Pet Owners', 'mind-my-cat' ), 
            'manage_options',
            'pet_owner_list',
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

        $petOwners = Users::getAllPetOwners();
    
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Pet Owner Management', 'mind-my-cat' ); ?></h1>

            <table class="wp-list-table widefat fixed striped users">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column"><?php esc_html_e( 'Name', 'mind-my-cat' ); ?></th>
                        <th scope="col" class="manage-column"><?php esc_html_e( 'Email', 'mind-my-cat' ); ?></th>
                        <th scope="col" class="manage-column"><?php esc_html_e( 'Link', 'mind-my-cat' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    if ( ! empty( $petOwners ) ) {
                        foreach ( $petOwners as $user ) { ?>
                            <tr>
                                <td><?php echo esc_html( $user->display_name ); ?></td>
                                <td><?php echo esc_html( $user->user_email ); ?></td>
                                <td><a href="<?php echo esc_url( Helper::getDashboardUrl($user->ID) ); ?>"> View </a></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="3"><?php esc_html_e( 'No Pet owners users found.', 'mind-my-cat' ); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column"><?php esc_html_e( 'Username', 'mi' ); ?></th>
                        <th scope="col" class="manage-column"><?php esc_html_e( 'Email', 'mind-my-cat' ); ?></th>
                        <th scope="col" class="manage-column"><?php esc_html_e( 'Link', 'mind-my-cat' ); ?></th>
                    </tr>
                </tfoot>
            </table>

        </div>
        <?php
        
    }
}

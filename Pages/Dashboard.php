<?php

namespace Mindmycat\Pages;

use Mindmycat\Model\Role;

class Dashboard
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
            __( 'Dashboard', 'mind-my-cat' ), 
            __( 'Dashboard', 'mind-my-cat' ), 
            'read',
            'mindmycat_dashboard',
            [$this, 'render']
        );
    }


    /**
     * todo - translate all the strings 
     */
    public function render()
    {
        
        if ( isset( $_GET['profile_id'] ) && intval( $_GET['profile_id'] ) ) {

            if ( ! current_user_can( 'manage_options' ) ) {

                
                echo '<div class="notice notice-error"><p>' . esc_html__( 'Insufficient permission.', 'mind-my-cat' ) . '</p></div>';

                return;
            }

            $this->dashboardForAdmin();
            
            return;
        }

        $current_user = wp_get_current_user();
        $roles = $current_user->roles;

        if ( empty( $roles ) ) {

            ?> <h2> No assigned role found to render this page, ask for a better role.... </h2> <?php

            return;
        }

        if( Role::isPetOwner($roles)) {

            $this->dashboardForOwner();

            return;
        }

        if( Role::isASitter($roles)) {

            $this->dashboardForSitter();

            return;
        }


        if ( current_user_can( 'manage_options' ) ) {

                
            echo '<div class="notice notice-error"><p>' . esc_html__( 'For now nothing is in this page for admin. ', 'mind-my-cat' ) . '</p></div>';

            return;
        }


        ?> <h2> Insufficient role permission! Ask for a better role.... </h2> <?php
    }

    public function dashboardForAdmin()
    {
        $userId = intval($_GET['profile_id']);

        $profile  = get_user_by( 'id', $userId );

        ?> 
        
        <h3>Hello Admin...</h3> 

        <?php
        
        if( Role::isPetOwner($profile->roles)) {

            $this->owner_view($profile);

            return;
        }

        if( Role::isASitter($profile->roles)) {

            $this->sitter_view($profile);

            return;
        }

        ?>
        
        <div>
            <h2>Pending orders payment link</h2>
        </div>
        
        <div>
            <h2>Current contract ongoing ....</h2>
        </div>

        <pre>
            <?php 

            print_r($profile);


            ?>
        </pre>
        
        <?php
    }

    public function dashboardForOwner()
    {
        ?> 
        
        <h3>Hello Owner...</h3> 

        how ot get all the woocommerce pending order of a specific customer by customer_id


        <div>
            <h2>Pending orders payment link</h2>
        </div>
        
        <div>
            <h2>Current contract ongoing ....</h2>
        </div>

        <div>
            <h2>Last 5 contract ....</h2>
        </div>
        
        <?php

    }

    public function dashboardForSitter()
    {
        ?> 
        
        <h3>Hello Sitter...</h3> 



        <div>
            <h2>Current contract ongoing ....</h2>
        </div>

        <div>
            <h2>Schedule list .....</h2>
        </div>
        
        <div>
            <h2>Upcoming contract </h2>
        </div>
        
        <div>
            <h2>Last 5 contract ....</h2>
        </div>
        
        <?php
    }


    protected function sitter_view($profile)
    {

        ?> 
        
        <h3>Pet sitter profile [NAME]</h3>

        <div>
            <h2>Current contract ongoing ....</h2>
        </div>

        <div>
            <h2>Schedule list .....</h2>
        </div>
        
        <div>
            <h2>Upcoming contract </h2>
        </div>
        
        <div>
            <h2>Last 5 contract ....</h2>
        </div>
        
        <pre>
            <?php 

            print_r($profile);


            ?>
        </pre>

        <?php
    }

    protected function owner_view($profile)
    {


        $args = array(
            'status' => 'pending',
            'customer_id' => $profile->ID, // Replace with the actual customer ID
        );

        $pending_orders = wc_get_orders( $args );

        ?>

        <h3>Pet owner profile [NAME]</h3>



        <div>
            <h2>Pending orders payment link</h2>
        </div>
        
        <div>
            <h2>Current contract ongoing ....</h2>
        </div>

        <div>
            <h2>Last 5 contract ....</h2>
        </div>

        <pre>
            <?php 

            print_r($pending_orders);
            //print_r($profile);


            ?>
        </pre>

        
        <?php

    }


}
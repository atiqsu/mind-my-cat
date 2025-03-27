<?php

namespace Mindmycat\Handler;

use Mindmycat\Pages\Dashboard;
use Mindmycat\Pages\Sitter_List;
use Mindmycat\Pages\Sitter_Profile;
use Mindmycat\Pages\Owner_List;

class Admin_Menu
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'pet_sitter_list']);
    }

    public function pet_sitter_list()
    {

        add_menu_page(
            'Pet care',
            'Pet care',
            'read',
            'pet_care_dashboard',
            [$this, 'dashboard'],
        );

        (new Dashboard('pet_care_dashboard'))->init();
        new Sitter_List('pet_care_dashboard')->init();
        new Sitter_Profile('pet_care_dashboard')->init();
        new Owner_List('pet_care_dashboard')->init();
    }


    public function dashboard()
    {
        ?>
        
        <div class="wrap">
            <h2>Dashboard</h2>
        </div>

        <?php
    }
}

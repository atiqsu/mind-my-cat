<?php

namespace Mindmycat\Handler;

use Mindmycat\Db\Migrator;
use Mindmycat\Pages\Front_Page;

class Installer
{
    
    const PAGE_OPTION_KEY = 'mmc_find_sitter_page_id';

    public static function activate()
    {

        User_Role::add_default();

        Migrator::run();

        (new Front_Page())->create_or_update_page();

        //Seeder::seed();
        //flush_rewrite_rules(); 
    }

    public static function deactivate()
    {
        //todo - need clarification.....
       //User_Role::remove_default();

       (new Front_Page())->change_status_to_draft(); 

    }
}

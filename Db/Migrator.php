<?php

namespace Mindmycat\Db;

class Migrator
{

    public static function run($network_wide = false) {

        self::dependency();

        Appointment::migrate();
        Contract::migrate();
        Calendar::migrate();

    }

    public static function dependency(): void {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    }
}

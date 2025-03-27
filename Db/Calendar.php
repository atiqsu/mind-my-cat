<?php

namespace Mindmycat\Db;

class Calendar
{

    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . 'bpc_calendar';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {

            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `sitter_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `date` DATE NOT NULL,
                `start_time` TIME NOT NULL,
                `end_time` TIME NOT NULL,
                `appointment_id` BIGINT UNSIGNED NOT NULL,

                INDEX `appointment_id_idx` (`appointment_id` ASC),
                INDEX `sitter_id_idx` (`sitter_id` ASC)
            ) $charsetCollate;";

            dbDelta($sql);
        }
    }
}

 
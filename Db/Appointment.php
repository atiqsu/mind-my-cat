<?php

namespace Mindmycat\Db;

class Appointment
{

    /**
     * Sitter_id redundant field
     * 
     */
    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . 'bpc_appointments';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {

            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `title` VARCHAR(255) NOT NULL DEFAULT 'new appointment',
                `owner_id` BIGINT UNSIGNED NOT NULL,
                `sitter_id` BIGINT UNSIGNED NULL DEFAULT 0,  
                `status` VARCHAR(60) NOT NULL DEFAULT 'pending',
                `notes` longtext NULL,

                INDEX `owner_id_idx` (`owner_id` ASC)
            ) $charsetCollate;";

            dbDelta($sql);
        }
    }
}

 
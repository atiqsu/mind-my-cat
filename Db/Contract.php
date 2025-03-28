<?php

namespace Mindmycat\Db;

class Contract
{

    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $table = $wpdb->prefix . 'bpc_contracts';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {

            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `owner_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `sitter_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `order_id` BIGINT UNSIGNED NULL DEFAULT 0,
                `order_id2` BIGINT UNSIGNED NULL DEFAULT 0,
                `status` VARCHAR(60) NOT NULL DEFAULT 'pending',
                `start_date` DATE NULL,
                `end_date` DATE NULL,
                `previsit_date` DATE NULL,
                `schedule` longtext NULL,
                `service_info` longtext NULL,
                `metadata` longtext NULL,

                INDEX `owner_id_idx` (`owner_id` ASC),
                INDEX `sitter_id_idx` (`sitter_id` ASC),
                INDEX `order_id_idx` (`order_id` ASC)
            ) $charsetCollate;";

            dbDelta($sql);
        }
    }
}

 
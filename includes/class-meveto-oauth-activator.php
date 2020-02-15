<?php

class Meveto_OAuth_Activator
{

    public static function activate()
    {
        add_rewrite_endpoint('meveto', EP_ROOT);

        global $wpdb;
        $dbname = $wpdb->dbname;

        // Create 'meveto_users' table if it does not exist
        $tablename = $wpdb->prefix.'meveto_users';
        $create_table_query = "CREATE TABLE IF NOT EXISTS `".$dbname."`.`".$tablename."` ( `id` BIGINT UNSIGNED NOT NULL , `last_logged_in` BIGINT NULL DEFAULT NULL , `last_logged_out` BIGINT NULL DEFAULT NULL , UNIQUE `wp_meveto_users_id_unique` (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
        $wpdb->query($create_table_query);

        // Add 'meveto_id' column to the 'users' table if it doesn't exist
        $tablename = $wpdb->prefix.'users';
        $check_column_query = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = `".$tablename."` AND COLUMN_NAME = `meveto_id`";
        $result = $wpdb->query($check_column_query);
        if(!$result)
        {
            $wpdb->query("ALTER TABLE `".$dbname."`.`".$tablename."` ADD `meveto_id` BIGINT NULL DEFAULT NULL AFTER `ID`");
            $wpdb->query("ALTER TABLE `".$dbname."`.`".$tablename."` ADD UNIQUE `users_meveto_id_unique` (`meveto_id`)");
        }
    }
}

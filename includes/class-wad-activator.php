<?php

class WAD_Activator {

	public static function activate() {
		self::create_table();
		if ( ! get_option( 'wad_storage_type' ) ) {
			add_option( 'wad_storage_type', 'database' );
		}
	}

	private static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wad_analytics_data';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			date date NOT NULL,
			path varchar(255) NOT NULL,
			hash_id varchar(64) NOT NULL,
			device varchar(50) NOT NULL,
			browser varchar(50) NOT NULL,
			referrer varchar(255) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY date (date),
			KEY hash_id (hash_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}

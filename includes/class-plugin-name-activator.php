<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$wpdb->query( "CREATE TABLE {$wpdb->prefix}mjaads_time (
			ID bigint(20) UNSIGNED NOT NULL,
			post_id int(11) DEFAULT NULL,
			started_from datetime DEFAULT NULL,
			ended_to datetime DEFAULT NULL,
			seen int(11) NOT NULL DEFAULT 0
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='This tables is created by MJA Ads for buddypress Ads listing';" );
		$wpdb->query( "CREATE TABLE {$wpdb->prefix}mjaads_like (
			ID bigint(20) UNSIGNED NOT NULL,
			post_id int(11) DEFAULT NULL,
			visitor_id int(11) NOT NULL,
			timed timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
			expression text NOT NULL DEFAULT '0'
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='This tables is created by MJA Ads for buddypress Ads Like';" );
		// dbDelta( $queries:array|string, $execute:boolean )
	}

}

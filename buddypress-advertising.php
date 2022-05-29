<?php

/**
 * Special advertising plugin
 *
 * Buddypress feeds advertising plugin with client tickting and payment support made for listing Ads to showing BuddyPress news feed and profile feed. Various purpose and quality ADS displaing supports with Youtube player and hosted video player controls. Framework supports so, it is more increadible to see.
 *
 * @link              https://www.fiverr.com/mahmud_remal/
 * @since             1.0.0
 * @package           Buddypress-ads
 *
 * @wordpress-plugin
 * Plugin Name:       Buddypress Advertising
 * Plugin URI:        https://www.fiverr.com/mahmud_remal/
 * Description:       This plugin helps you monetize your buddypress community website by integrating ads in the activity feed.
 * Version:           1.0.0
 * Author:            Remal Mahmud
 * Author URI:        https://www.fiverr.com/mahmud_remal/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-advertising
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );
defined( 'MJA_ADS_PREFIX' ) || define( 'MJA_ADS_PREFIX', '[ads id=Ad' );
defined( 'MJA_ADS_NAME' ) || define( 'MJA_ADS_NAME', 'visual-ads' );
defined( 'MJA_PREFIX' ) || define( 'MJA_PREFIX', 'visual_ads' );
defined( 'MJA_ADS_DURATION' ) || define( 'MJA_ADS_DURATION', [
	'lifetime' => __( 'Lifetime', "buddypress-advertising" ),
	'one_week' => __( 'One Week', "buddypress-advertising" ),
	'two_weeks' => __( 'Two Weeks', "buddypress-advertising" ),
	'one_month' => __( 'One Month', "buddypress-advertising" )
] );
defined( 'MJA_ADS_CATEGORIES' ) || define( 'MJA_ADS_CATEGORIES', [
	'tech' => __( 'Tech Services', "buddypress-advertising" ),
	'job' => __( 'Job vacancy', "buddypress-advertising" ),
	'house' => __( 'Real Estate', "buddypress-advertising" ),
	'car' => __( 'Automobile', "buddypress-advertising" ),
	'food' => __( 'Food/Restaurant', "buddypress-advertising" ),
	'school' => __( 'School/Education', "buddypress-advertising" ),
	'shop' => __( 'Clothes & Shoes', "buddypress-advertising" ),
	'beauty' => __( 'Beauty & Saloon', "buddypress-advertising" ),
	'other' => __( 'Other', "buddypress-advertising" ),
] );
defined( 'MJA_ADS_CURRENCY' ) || define( 'MJA_ADS_CURRENCY', [
	'dollars' => __( 'USD', "buddypress-advertising" ),
	'pounds' => __( 'GBP', "buddypress-advertising" ),
	'euro' => __( 'EUR', "buddypress-advertising" ),
	'cfa' => __( 'CFA', "buddypress-advertising" ),
	'xaf' => __( 'XAF', "buddypress-advertising" ),
	'ngn' => __( 'NGN', "buddypress-advertising" ),
] );
defined( 'MJA_ADS_STATUS' ) || define( 'MJA_ADS_STATUS', [
	'active' => [
		'title' => _x( 'Active', 'Active', "buddypress-advertising" ),
		'params' => [
			'paused' => _x( 'Pause', 'Pause', "buddypress-advertising" ),
			'expired' => _x( 'Deactivate', 'Deactivate', "buddypress-advertising" )
		]
	],
	'pending' => [
		'title' => _x( 'Pending', 'Pending', "buddypress-advertising" ),
		'params' => [
			'active' => _x( 'Accept', 'Accept', "buddypress-advertising" ),
			'rejected' => _x( 'Decline', 'Decline', "buddypress-advertising" )
		]
	],
	'paused' => [
		'title' => _x( 'Paused', 'Paused', "buddypress-advertising" ),
		'params' => [
			'active' => _x( 'Resume', 'Resume', "buddypress-advertising" ),
			'delete' => _x( 'Delete', 'Delete', "buddypress-advertising" )
		]
	],
	'expired' => [
		'title' => _x( 'Expired', 'Expired', "buddypress-advertising" ),
		'params' => [
			'delete' => _x( 'Delete', 'Delete', "buddypress-advertising" ),
			'active' => _x( 'Reactivate', 'Reactivate', "buddypress-advertising" )
		]
	],
	'rejected' => [
		'title' => _x( 'Rejected', 'Rejected', "buddypress-advertising" ),
		'params' => [
			'active' => _x( 'activate', 'activate', "buddypress-advertising" ),
			'delete' => _x( 'Delete', 'Delete', "buddypress-advertising" )
		]
	]
] );
defined('MJA_OPTIONS') || define('MJA_OPTIONS', get_option( MJA_PREFIX . 'plugin_options',
[
	'prices' => [],
	'ads' => [
			'loop' => 3,
			'avater' => false
		]
] ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-activator.php';
	Plugin_Name_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name-deactivator.php';
	Plugin_Name_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );
add_action('after_setup_theme', function() {
	load_theme_textdomain( "buddypress-advertising", '/languages' );
});



if( ! class_exists( 'CSF' ) ) {
	if( is_admin() ) {
		require_once plugin_dir_path( __FILE__ ) .'/admin/framework/framework.php';
	}
}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name.php';
require plugin_dir_path( __FILE__ ) . 'includes/index.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-admin.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-shortcode.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new Plugin_Name();
	$plugin->run();

}
run_plugin_name();
function mja_get_wp_query( $args ) {
	return new WP_Query( $args );
}
add_action( 'pre_get_posts', function( $query ) {
	global $post;
	register_post_status( 'accepted', [
		'label'                     => __( 'Accepted', "buddypress-advertising" ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Accepted <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ),
	]);
});
! is_admin() || wp_enqueue_script( 'ads-js', untrailingslashit( plugin_dir_url( __FILE__ ). 'admin/js/admin.js' ), ['jquery'] , filemtime( plugin_dir_path( __FILE__ ) . 'admin/js/admin.js' ), true );
is_admin() || wp_enqueue_script( 'ads-js', untrailingslashit( plugin_dir_url( __FILE__ ). 'public/js/public.js' ), ['jquery'] , filemtime( plugin_dir_path( __FILE__ ) . 'public/js/public.js' ), true );
wp_enqueue_script( 'notify-js', untrailingslashit( plugin_dir_url( __FILE__ ) . 'admin/js/bootstrap-notify.min.js' ), ['jquery'], true );
include 'includes/class-metabox.php';
include 'includes/class-reactions.php';
include 'includes/class-adsform.php';
include 'includes/class-embed.php';
// include 'includes/class-video.php';
include 'includes/class-ads.php';
add_action('admin_init', function( $args = [] ) {
	if( current_user_can( 'manage_options' ) ) {
			register_setting( 'buddy_ads_setting', 'buddy_ads_setting', $args );
	}
});


! is_admin() || wp_enqueue_style( 'ads-style', untrailingslashit( plugin_dir_url( __FILE__ ). 'admin/css/new-ads.css' ), [] ,filemtime( plugin_dir_path( __FILE__ ) . 'admin/css/new-ads.css' ), 'all' );


add_action( 'init', function( $args ) {
	wp_localize_script( 'ads-js', 'siteConfig', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce( 'ads_post_nonce' ),
		'ads_list_action_nonce' => wp_create_nonce( 'ads_list_action_nonce' ),
		'mja_ads_like_nonce' => wp_create_nonce( 'mja_ads_like_nonce' ),
		'mja_ads_click_nonce' => wp_create_nonce( 'mja_ads_click_nonce' ),
		'mja_ads_video_thumb_nonce' => wp_create_nonce( 'mja_ads_video_thumb_nonce' )
	] );
} );









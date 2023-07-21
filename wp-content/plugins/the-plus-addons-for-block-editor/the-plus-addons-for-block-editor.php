<?php
/*
* Plugin Name: The Plus Addons for Block Editor
* Plugin URI: https://theplusblocks.com/
* Description: Highly Customizable 85+ Advanced WordPress Blocks for Performance-Driven Website.
* Version: 2.0.4
* Author: POSIMYTH
* Author URI: https://posimyth.com
* Text Domain: tpgb
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'TPGB_VERSION' ) or define( 'TPGB_VERSION', '2.0.4' );
define( 'TPGB_FILE__', __FILE__ );

define( 'TPGB_PATH', plugin_dir_path( __FILE__ ) );
define( 'TPGB_BASENAME', plugin_basename(__FILE__) );
define( 'TPGB_BDNAME', basename( dirname(__FILE__)) );
define( 'TPGB_URL', plugins_url( '/', __FILE__ ) );
define( 'TPGB_ASSETS_URL', TPGB_URL );
define( 'TPGB_INCLUDES_URL', TPGB_PATH.'includes/' );
define( 'TPGB_CATEGORY', 'tpgb' );

if ( ! version_compare( PHP_VERSION, '5.6.40', '>=' ) ) {
	add_action( 'admin_notices', 'tpgb_check_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7.1', '>=' ) ) {
	add_action( 'admin_notices', 'tpgb_check_wp_version' );
} else {
	require_once 'plus-block-loader.php';
}

/**
 * The Plus Addons for Gutenberg check minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function tpgb_check_php_version() {	
	$check_message      = sprintf( esc_html__( 'The Plus Addons for Block Editor requires PHP version %s+, plugin is currently NOT RUNNING.', 'tpgb' ), '5.6.40' );
	$display_message = sprintf( '<div class="error">%s</div>', wpautop( $check_message ) );
	echo wp_kses_post( $display_message );
}

/**
 * The Plus Addons for Gutenberg check minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function tpgb_check_wp_version() {	
	$check_message      = sprintf( esc_html__( 'The Plus Addons for Block Editor requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'tpgb' ), '4.7.1' );
	$display_message = sprintf( '<div class="error">%s</div>', wpautop( $check_message ) );
	echo wp_kses_post( $display_message );
}

/* 
 * The Plus Addons for Gutenberg Plugin Update Message
 * @since 1.1.3
 */
add_action('in_plugin_update_message-the-plus-addons-for-block-editor/the-plus-addons-for-block-editor.php','tpgb_plugin_update_message',10,2);
function tpgb_plugin_update_message( $data, $response ){			
	if( isset( $data['upgrade_notice'] ) && !empty($data['upgrade_notice']) ) {
		printf(
			'<div class="update-message">%s</div>',
			wpautop( $data['upgrade_notice'] )
		);
	}
}
?>
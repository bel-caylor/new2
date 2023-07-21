<?php

// @formatter:off
/**
 * Plugin Name: dflip
 * Description: Realistic 3D Flip-books for WordPress
 * Version: 1.7.35
 * Update URI: https://api.freemius.com
 *
 * Text Domain: DFLIP
 * Author: DearHive
 * Author URI: http://dearhive.com/
 *
 */
// @formatter:on
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'dflip_fs' ) ) {
    // Create a helper function for easy SDK access.
    function dflip_fs()
    {
        global  $dflip_fs ;
        
        if ( !isset( $dflip_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_6572_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_6572_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $dflip_fs = fs_dynamic_init( array(
                'id'              => '6572',
                'slug'            => 'dearflip',
                'premium_slug'    => 'dflip',
                'type'            => 'plugin',
                'public_key'      => 'pk_bee20a49903909e5695a20199c9b1',
                'is_premium'      => true,
                'is_premium_only' => true,
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'menu'            => array(
                'slug'    => 'edit.php?post_type=dflip',
                'contact' => false,
                'support' => false,
            ),
                'is_live'         => true,
            ) );
        }
        
        return $dflip_fs;
    }
    
    // Init Freemius.
    dflip_fs();
    // Signal that SDK was initiated.
    do_action( 'dflip_fs_loaded' );
}

require_once dirname( __FILE__ ) . '/dflip-core.php';
function dflip_plugin_activated()
{
    deactivate_plugins( '3d-flipbook-dflip-lite/3d-flipbook-dflip-lite.php' );
}

function dflip_load_plugin_textdomain()
{
    load_plugin_textdomain( 'DFLIP', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'dflip_load_plugin_textdomain' );
register_activation_hook( __FILE__, 'dflip_plugin_activated' );
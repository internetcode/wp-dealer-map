<?php
/**
 * Dealer Map Install
 */

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Run single site / network-wide activation of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being activated network-wide.
 */
function dealer_map_activate( $networkwide = false ) {
    if ( ! is_multisite() || ! $networkwide ) {
        func_dealer_map_activate();
    }
    else {
        /* Multi-site network activation - activate the plugin for all blogs. */
        dealer_map_network_activate( true );
    }
}

/**
 * Run network-wide activation of the plugin.
 *
 * @param bool $activate True for plugin activation, false for de-activation.
 */
function dealer_map_network_activate( $activate = true ) {
    global $wpdb;

    $network_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d", $wpdb->siteid ) );

    if ( is_array( $network_blogs ) && $network_blogs !== array() ) {
        foreach ( $network_blogs as $blog_id ) {
            switch_to_blog( $blog_id );

            if ( $activate === true ) {
                func_dealer_map_activate();
            }

            restore_current_blog();
        }
    }
}

/**
 * Install the required data.
 *
 * @since 1.0.0
 * @return void
 */
function func_dealer_map_activate() {

    global $dealer_map;
    
    // Set the correct version.
    update_option( 'dealer_map_version', DEALER_VERSION_NUM );

    // Add user roles.
    dealer_map_add_roles();
    
    // Add user capabilities.
    dealer_map_add_caps();
    
}




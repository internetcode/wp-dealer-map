<?php
/**
 * Grim Install
 */

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Run single site / network-wide activation of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being activated network-wide.
 */
function grim_activate( $networkwide = false ) {
    if ( ! is_multisite() || ! $networkwide ) {
        func_grim_activate();
    }
    else {
        /* Multi-site network activation - activate the plugin for all blogs. */
        grim_network_activate_uninstall( true );
    }
}

/**
 * Run single site / network-wide uninstall of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being uninstall network-wide.
 */
function grim_uninstall( $networkwide = false ) {
    if ( ! is_multisite() || ! $networkwide ) {
        func_grim_uninstall();
    }
    else {
        /* Multi-site network activation - de-activate the plugin for all blogs. */
        grim_network_activate_uninstall( false );
    }
}

/**
 * Run network-wide (de-)activation of the plugin.
 *
 * @param bool $activate True for plugin activation, false for de-activation.
 */
function grim_network_activate_uninstall( $activate = true ) {
    global $wpdb;

    $network_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d", $wpdb->siteid ) );

    if ( is_array( $network_blogs ) && $network_blogs !== array() ) {
        foreach ( $network_blogs as $blog_id ) {
            switch_to_blog( $blog_id );

            if ( $activate === true ) {
                func_grim_activate();
            }
            else {
                func_grim_uninstall();
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
function func_grim_activate() {

    global $grim;
    
    // Set the correct version.
    update_option( 'grim_version', GRIM_VERSION_NUM );

    // Add user roles.
    grim_add_roles();
    
    // Add user capabilities.
    grim_add_caps();
    
}

/**
 * Remove data on deleting / uninstalling of plugin
 *
 * @since 1.0.0
 * @return void
 */
function func_grim_uninstall() {
    global $grim_settings, $wpdb;

    // if uninstall is not called by WordPress, return
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        return;
    }
    
    if(isset($grim_settings['remove_all']) && $grim_settings['remove_all'] != 'keep') {

        //delete all options from database table
        $option_name = 'grim_settings';
        delete_option($option_name);
     
        // for site options in Multisite
        delete_site_option($option_name);
     
        //remove a grim_stores database table, by default is to remove
        if(isset($grim_settings['keep_table']) && $grim_settings['keep_table'] != 'keep') {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}grim_stores");
        }
        //remove capabilities and roles
        grim_remove_caps_and_roles();

    } else {
        grim_remove_alldata();
    }

}

/**
 * Remove all data if called
 *
 * @since 1.0.0
 * @return void
 */

function grim_remove_alldata() {

    //delete all options from database table
    $option_name = 'grim_settings';
    delete_option($option_name);
 
    // for site options in Multisite
    delete_site_option($option_name);
 
    //remove a grim_stores database table
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}grim_stores");
  
    //remove capabilities and roles
    grim_remove_caps_and_roles();

}


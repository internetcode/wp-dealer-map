<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 *
 * @since 1.0.0
 *
 */

if ( ! current_user_can( 'activate_plugins' ) ) {
  return;
}

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/**
 * Remove the dealer_map caps and roles on uninstall plugin
 * 
 *@since 1.0.0
 *@return void 
 */
function remove_dealer_map_all_caps_and_roles() {
	global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }
    
    if ( is_object( $wp_roles ) ) {
        $wp_roles->remove_cap( 'administrator', 'manage_dealer_map_settings' );
        
        $capabilities = array(
        'edit_store',
        'read_store',
        'delete_store',
        'edit_stores',
        'edit_others_stores',
        'publish_stores',
        'read_private_stores',
        'delete_stores',
        'delete_private_stores',
        'delete_published_stores',
        'delete_others_stores',
        'edit_private_stores',
        'edit_published_stores');
        
        foreach ( $capabilities as $cap ) {
            $wp_roles->remove_cap( 'dealer_map_store_locator_manager', $cap );
            $wp_roles->remove_cap( 'administrator',              $cap );
        }
    } 
    
    remove_role( 'dealer_map_store_locator_manager' ); 
}

global $wpdb;
$rem_all = get_option('dealer_map_remove_all');
$keep_tab = get_option('dealer_map_keep_table');

if ( $rem_all != 'keep') {

    //delete all options from database table
    $option_name = 'dealer_map_settings';
    delete_option($option_name);
 
    // for site options in Multisite
    delete_site_option($option_name);
 
    //remove a dealer_map_stores database table, by default is to remove
    if( $keep_tab  != 'keep' ) {
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dealer_map_stores");
    }
    //remove capabilities and roles
    remove_dealer_map_all_caps_and_roles();

} else {

    //delete all options from database table
    $option_name = 'dealer_map_settings';
    delete_option($option_name);
 
    // for site options in Multisite
    delete_site_option($option_name);
 
    //remove a dealer_map_stores database table
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}dealer_map_stores");
  
    //remove capabilities and roles
    remove_dealer_map_all_caps_and_roles();
}

//Delete the rest of options registered by plugin
delete_option('dealer_db_version');
delete_option('dealer_map_remove_all');
delete_option('dealer_map_keep_table');
<?php

/**
 * Add Dealer Map Roles.
 *@since 1.0.0 (description)
 *@return void 
 */
function dealer_map_add_roles() {
    
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }
    //Check $wp_roles is OBJECT
	if ( is_object( $wp_roles ) ) {
		add_role( 'dealer_map_store_locator_manager', __( 'Dealer Map Manager', 'wp-dealer-map' ), array(
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'delete_others_posts'    => true,
			'delete_private_posts'   => true,
			'edit_others_posts'      => true,
	    	'edit_posts'             => true,
            'edit_private_posts'     => true,
			'read_private_posts'     => true,
            'manage_dealer_map_settings'   => true
		) );
    }
}

/**
 * Add dealer_map user capabilities.
 *
 * @since 1.0.0
 * @return void
 */
function dealer_map_add_caps() {
    
    global $wp_roles;

    if ( class_exists( 'WP_Roles' ) ) {
        if ( !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }
    }

    if ( is_object( $wp_roles ) ) {
        $wp_roles->add_cap( 'administrator', 'manage_dealer_map_settings' );
        
        $capabilities = dealer_map_get_post_caps();
        
        foreach ( $capabilities as $cap ) {
            $wp_roles->add_cap( 'dealer_map_store_locator_manager', $cap );
            $wp_roles->add_cap( 'administrator',              $cap );
        }
    } 
}

/** 
 * Get the dealer_map post type capabilities.
 * 
 * @since 1.0.0
 * @return array $capabilities The post type capabilities
 */
function dealer_map_get_post_caps() {

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
        'edit_published_stores'
    );
    
    return $capabilities;
}

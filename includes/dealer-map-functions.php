<?php
/**
 * Get the current plugin settings.
 *
 * 
 * @since 1.0
 * @return array $setting The current plugin settings
 */
function dealer_map_get_settings() {

    $settings = get_option( 'dealer_map_settings' );
    return $settings;
}

/**
 * Get the available map types.
 *
 * @since 1.0
 * @return array $map_types The available map types
 */
function dealer_map_get_map_types() {

    $map_types = array(
        'roadmap'   => __( 'Roadmap', 'wp-dealer-map' ),
        'satellite' => __( 'Satellite', 'wp-dealer-map' ),
        'hybrid'    => __( 'Hybrid', 'wp-dealer-map' ),
        'terrain'   => __( 'Terrain', 'wp-dealer-map' )
    );

    return $map_types;
}


/**
 * @since 1.0
 * @param string $address  The address to geocode
 * @return array $response response of Google Geocode API service or WP_error
 */
function dealer_map_call_geocode_api( $address ) {
    $response = '';
    
    if( dealer_map_api_status() === 'OK' ) { 
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . dealer_map_get_api();
        $response = wp_remote_get( $url );
    }

    return $response;
}

/**
 * @since 1.0
 * @return string $key Google API key if is setted and if is valid, otherwise return empty string  
 */
function dealer_map_get_api() {
    global $dealer_map_settings;
    $key = '';
    $api_key = (isset($dealer_map_settings['api_server_key']) && dealer_map_api_status() === 'OK') ? $dealer_map_settings['api_server_key'] : false;

    if( $api_key ) {
       $key = '&key='. $dealer_map_settings['api_server_key']; 
    }
    
    return $key;
}

/**
 * Get the latitude/longitude coordinates for the provided address in shortcode attribute
 *
 * @since 1.0
 * @param string      $address The address to geocode
 * @return array|void $latlong  The returned coordinates or empty array if there was an error
 */
function dealer_map_get_address_coordinates( $address ) {

    $latlong   = array();
    $response = dealer_map_call_geocode_api( $address );

    if ( !is_wp_error( $response ) ) {
        $response = json_decode( $response['body'], true );
        
        if ( $response['status'] == 'OK' ) {

            $latlong[] = $response['results'][0]['geometry']['location'];
        }
    }

    return $latlong[0];
}

/**
 * Check if there's a transient that holds
 * the coordinates already for requested address
 *
 * @since 1.0
 * @param  string $address The location to geocode
 * @return string $latlng  The latitude/longitude of requested address
 */
function dealer_map_check_coordinates_transient( $address ) {

    $nameit   = explode( ',', $address );
    $trans_name = 'dealer_map_' . trim( strtolower( $nameit[0] ) ) . '_loc';   

    if ( false === ( $location = get_transient( $trans_name ) ) ) {
        $location = dealer_map_get_address_coordinates( $address );

        if ( $location ) {
            set_transient( $trans_name, $location, 0 );
        }
    }

    return $location;
}

/**
 * Deregister other Google Maps lodaed scripts
 *
 * @since 1.0
 * @return void
 */
function dealer_map_deregister_other_gmaps() {

    global $wp_scripts;

    foreach ( $wp_scripts->registered as $index => $script ) {
        if ( ( strpos( $script->src, 'maps.google.com' ) !== false ) || ( strpos( $script->src, 'maps.googleapis.com' ) !== false ) && ( $script->handle !== 'dealer_map_gmap' ) ) {
            wp_deregister_script( $script->handle );
        }
    }
}

/**
 * Return the used distance unit.
 *
 * @since 1.0
 * @return string km or mi
 */
function dealer_map_get_distance_unit() {
    
    global $dealer_map_settings;
    $unit = isset($dealer_map_settings['distance_unit']) ? $dealer_map_settings['distance_unit'] : 'mi';

    return $unit;
}

/**
 * Return Geocoder status
 *
 * @since 1.0
 * @return boolen Geocoder status if OK false else true
 */
function dealer_map_check_api($map_api) {
    $addr = 'New York, US';
    $err_res = true;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($addr) . '&libraries=places&key=' . $map_api .'';
    $e = wp_remote_get( $url );

        if ( !is_wp_error( $e ) ) {
            $e = json_decode( $e['body'], true );
        }
        if ( $e['status'] == 'OK' ) {
            dealer_map_check_api_valid_transient( $e['status'] );
            $err_res = false;  
        } else {
            dealer_map_check_api_no_valid_transient($e['status']);
        }
    return $err_res;
}

/**
 * Setting transient around Geocoder test code response
 *
 * @since 1.0
 * @return boolen valid
 */
function dealer_map_check_api_valid_transient( $valid ) {

    $valid_tra = 'dealer_map_valid_ok';

    if ( false === ($valid_api = get_transient( $valid_tra ) ) ) {
        $valid_api = $valid;
        set_transient( $valid_tra, $valid_api, 0 );
    } else {
        delete_transient( $valid_tra );
        set_transient( $valid_tra, $valid, 0 );
    }

    return $valid_api;
}

/**
 * Setting transient around Geocoder test code response
 *
 * @since 1.0
 * @return boolen if there was no valid transient
 */
function dealer_map_check_api_no_valid_transient( $valid ) {

    $trans = 'dealer_map_valid_ok';

    if ( false !== get_transient( $trans ) ) {
        delete_transient( $trans );
        set_transient( $trans, $valid, 0 ); 
    } else {
       return;
    }
 
}

/**
 * Checking transient around Geocoder test code response
 *
 * @since 1.0
 * @return string If there was no valid transient API or transient itself value
 */
function dealer_map_api_status() {

    if ( false === ($value = get_transient( 'dealer_map_valid_ok' ))) {
       return __( 'No Valid API', 'wp-dealer-map' );
    } else {
        return $value;
    }
} 
 
/**
 * Check if there is valid api keys saved
 *
 * @since 1.0
 * @return void
 */
 function dealer_map_api_keys_checker() {     

    global $dealer_map_settings;
    $api = '';
    $api_k = isset( $dealer_map_settings['api_server_key'] ) ? true : false;
    $api_v = dealer_map_api_status() === 'OK' ? true : false;
    
    if ( $api_k && $api_v ) {
       $api = $dealer_map_settings['api_server_key'];
    }
    return $api;
} 

/**
 * Get the url to the admin-ajax.php
 *
 * @since 1.0
 * @return string $ajax_url URL
 */
function dealer_map_get_ajax_url() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    return $ajax_url;
}


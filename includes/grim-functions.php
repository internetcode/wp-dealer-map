<?php
/**
 * Get the current plugin settings.
 *
 * 
 * @since 1.0
 * @return array $setting The current plugin settings
 */
function grim_get_settings() {

    $settings = get_option( 'grim_settings' );
    return $settings;
}

/**
 * Get the available map types.
 *
 * @since 1.0
 * @return array $map_types The available map types
 */
function grim_get_map_types() {

    $map_types = array(
        'roadmap'   => __( 'Roadmap', 'grim' ),
        'satellite' => __( 'Satellite', 'grim' ),
        'hybrid'    => __( 'Hybrid', 'grim' ),
        'terrain'   => __( 'Terrain', 'grim' )
    );

    return $map_types;
}


/**
 * @since 1.0
 * @param string $address  The address to geocode
 * @return array $response response of Google Geocode API service or WP_error
 */
function grim_call_geocode_api( $address ) {
    $response = '';
    
    if( grim_api_status() === 'OK' ) { 
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . grim_get_api();
        $response = wp_remote_get( $url );
    }

    return $response;
}

/**
 * @since 1.0
 * @return string $key Google API key if is setted and if is valid, otherwise return empty string  
 */
function grim_get_api() {
    global $grim_settings;
    $key = '';
    $api_key = (isset($grim_settings['api_server_key']) && grim_api_status() === 'OK') ? $grim_settings['api_server_key'] : false;

    if( $api_key ) {
       $key = '&key='. $grim_settings['api_server_key']; 
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
function grim_get_address_coordinates( $address ) {

    $latlong   = array();
    $response = grim_call_geocode_api( $address );

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
function grim_check_coordinates_transient( $address ) {

    $nameit   = explode( ',', $address );
    $trans_name = 'grim_' . trim( strtolower( $nameit[0] ) ) . '_loc';   

    if ( false === ( $location = get_transient( $trans_name ) ) ) {
        $location = grim_get_address_coordinates( $address );

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
function grim_deregister_other_gmaps() {

    global $wp_scripts;

    foreach ( $wp_scripts->registered as $index => $script ) {
        if ( ( strpos( $script->src, 'maps.google.com' ) !== false ) || ( strpos( $script->src, 'maps.googleapis.com' ) !== false ) && ( $script->handle !== 'grim-gmap' ) ) {
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
function grim_get_distance_unit() {
    
    global $grim_settings;
    $unit = isset($grim_settings['distance_unit']) ? $grim_settings['distance_unit'] : 'mi';

    return $unit;
}

/**
 * Return Geocoder status
 *
 * @since 1.0
 * @return boolen Geocoder status if OK false else true
 */
function grim_check_api($map_api) {
    $addr = 'New York, US';
    $err_res = true;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($addr) . '&libraries=places&key=' . $map_api .'';
    $e = wp_remote_get( $url );

        if ( !is_wp_error( $e ) ) {
            $e = json_decode( $e['body'], true );
        }
        if ( $e['status'] == 'OK' ) {
            grim_check_api_valid_transient( $e['status'] );
            $err_res = false;  
        } else {
            grim_check_api_no_valid_transient($e['status']);
        }
    return $err_res;
}

/**
 * Setting transient around Geocoder test code response
 *
 * @since 1.0
 * @return boolen valid
 */
function grim_check_api_valid_transient( $valid ) {

    $valid_tra = 'grim_valid_ok';

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
function grim_check_api_no_valid_transient( $valid ) {

    $trans = 'grim_valid_ok';

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
function grim_api_status() {
    
    if ( false === ($value = get_transient( 'grim_valid_ok' ))) {
       $value = _e( 'No Valid API', 'grim' );
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
 function grim_api_keys_checker() {     

    global $grim_settings;
    $api = '';
    $api_k = isset( $grim_settings['api_server_key'] ) ? true : false;
    $api_v = grim_api_status() === 'OK' ? true : false;
    
    if ( $api_k && $api_v ) {
       $api = $grim_settings['api_server_key'];
    }
    return $api;
} 

/**
 * Get the url to the admin-ajax.php
 *
 * @since 1.0
 * @return string $ajax_url URL
 */
function grim_get_ajax_url() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    return $ajax_url;
}


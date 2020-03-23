<?php 
if( isset($dealer_map_settings['api_server_key']) && dealer_map_api_status() === 'OK' ) {

    $mheight = isset($dealer_map_settings['map_height']) ? $dealer_map_settings['map_height'] : '450';
    $height = $mheight .'px';
    $zipCity = esc_attr(__('Zip Code/City', 'wp-dealer-map'));
    //Button CSS function
    $btn_css = $this->button_css();

    $output = "";
    $output .= '<div id="wp-dealer-map" class="wp-dealer-map col col-12">' . "\r\n";
    $output .= '<div id="dealer-map-error" style="display:none"></div>' . "\r\n";
    $output .= '<div class="header_html"></div>' . "\r\n";
    $output .= '<div class="search_bar">' . "\r\n";
    $output .= '<div class="main_search_bar">' . "\r\n";
    $output .= '<label class="main_search_label" for="address_search">'. esc_attr(__('ZIP code or City', 'wp-dealer-map') ) .'</label>' . "\r\n";
    $output .= '<input type="text" id="address_search" name="address_search" value="'. $zipCity .'" onfocus="if(this.value==\''. $zipCity .'\'){this.value=\' \'}">' . "\r\n";
    $output .= '</div>' . "\r\n";

    if ( $dealer_map_settings['radius_dropdown'] || $dealer_map_settings['results_dropdown']  ) {

        if ( $dealer_map_settings['radius_dropdown'] ) {
        	$output .= '<div class="search_within_distance">' . "\r\n";
            $output .= '<label for="within_distance" class="distance_label">' . esc_attr( __( 'Distance', 'wp-dealer-map' ) ) . '</label>' . "\r\n";
            $output .= '<select id="within_distance" name="within_distance">' . "\r\n";
            $output .= $this->dealer_map_dropdown_list( 'search_radius' ) . "\r\n";
            $output .= '</select>' . "\r\n";
            $output .= '</div>' . "\r\n";
        }

        if ( $dealer_map_settings['results_dropdown'] ) {
            $output .= '<div class="search_limit">' . "\r\n";
            $output .= '<label class="search_limit_label" for="limit">' . esc_attr( __( 'Results', 'wp-dealer-map' ) ) . '</label>' . "\r\n";
            $output .= '<select id="limit" name="limit">' . "\r\n";
            $output .= $this->dealer_map_dropdown_list( 'max_results' ) . "\r\n";
            $output .= '</select>' . "\r\n";
            $output .= '</div>' . "\r\n";
        } 

    }

    $output .= '<button id="submitBtn" class="'. $btn_css .'" onclick="codeAddress()">' . esc_attr(  __( 'Search', 'wp-dealer-map' ) ) . '</button>' . "\r\n";
    $output .= '<div style="clear:both"></div>' . "\r\n";
    $output .= '</div>' . "\r\n";



    $output .= '<div id="store_map" style="height: '. $height .'"></div>' . "\r\n";
    $output .= '<div id="store_finded" class="store_finded"></div>' . "\r\n";    
    $output .= '<div class="addresses" id="addresses_list" style="min-height: 100px">' . "\r\n";
    $output .= '<ul class="row">' . "\r\n";
    $output .= '<li class="no_stores_found"><div class="no_stores_found">' . esc_attr( __('Enter your city or zip code in input above.', 'wp-dealer-map') ) . '</div></li>' . "\r\n";
    $output .= '</ul>' . "\r\n";
    $output .= '</div>' . "\r\n";
    $output .= '<div class="addresses" id="directions_text" style="height: '. $height .'; display:none">' . "\r\n";
    $output .= '<a href="#"" onclick="end_directions(); return false;" class="return_to_results">&lt;&lt;' . esc_attr( __('Back to Results', 'wp-dealer-map') ) . '</a>' . "\r\n";
    $output .= '<div id="direction_destination"></div>' . "\r\n";
    $output .= '<div id="directions_steps"></div>' . "\r\n";
    $output .= '</div>' . "\r\n";
    $output .= '<div id="d_colons" class="footer_html"></div>' . "\r\n";
    $output .= '</div>' . "\r\n";

    echo $output;

} else {
   echo sprintf('<div id="dealer-map-error" class="col col-12">%s</div>', esc_html__('No Valid API', 'wp-dealer-map'));
}

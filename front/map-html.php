<?php 
if( isset($grim_settings['api_server_key']) && grim_api_status() === 'OK' ) {

    $mheight = isset($grim_settings['map_height']) ? $grim_settings['map_height'] : '450';
    $height = $mheight .'px';
    $zipCity = esc_attr(__('Zip Code/City', 'grim'));
    //Button CSS function
    $btn_css = $this->button_css();

    $output = "";
    $output .= '<div id="storefinder" class="col col-12">' . "\r\n";
    $output .= "\t" . '<div id="grim-dealer-error" style="display:none"></div>' . "\r\n";
    $output .= "\t\t" . '<div class="header_html"></div>' . "\r\n";
    $output .= "\t\t\t" . '<div class="search_bar">' . "\r\n";
    $output .= "\t\t\t" . '<div class="main_search_bar">' . "\r\n";
    $output .= "\t\t\t\t" . '<label class="main_search_label" for="address_search">'. esc_attr(__('ZIP code or City', 'grim') ) .'</label>' . "\r\n";
    $output .= "\t\t\t\t" . '<input type="text" id="address_search" name="address_search" value="'. $zipCity .'" onfocus="if(this.value==\''. $zipCity .'\'){this.value=\' \'}">' . "\r\n";
    $output .= "\t\t\t" . '</div>' . "\r\n";

    if ( $grim_settings['radius_dropdown'] || $grim_settings['results_dropdown']  ) {

        if ( $grim_settings['radius_dropdown'] ) {
        	$output .= "\t\t\t" . '<div class="search_within_distance">' . "\r\n";
            $output .= "\t\t\t\t" . '<label for="within_distance" class="distance_label">' . esc_attr( __( 'Distance', 'grim' ) ) . '</label>' . "\r\n";
            $output .= "\t\t\t\t\t" . '<select id="within_distance" name="within_distance">' . "\r\n";
            $output .= "\t\t\t\t\t\t" . $this->grim_dropdown_list( 'search_radius' ) . "\r\n";
            $output .= "\t\t\t\t\t" . '</select>' . "\r\n";
            $output .= "\t\t\t\t" . '</div>' . "\r\n";
        }

        if ( $grim_settings['results_dropdown'] ) {
            $output .= "\t\t\t\t" . '<div class="search_limit">' . "\r\n";
            $output .= "\t\t\t\t\t" . '<label class="search_limit_label" for="limit">' . esc_attr( __( 'Results', 'grim' ) ) . '</label>' . "\r\n";
            $output .= "\t\t\t\t\t" . '<select id="limit" name="limit">' . "\r\n";
            $output .= "\t\t\t\t\t\t" . $this->grim_dropdown_list( 'max_results' ) . "\r\n";
            $output .= "\t\t\t\t\t" . '</select>' . "\r\n";
            $output .= "\t\t\t\t" . '</div>' . "\r\n";
        } 

    }

    $output .= "\t\t\t\t" . '<button id="submitBtn" class="'. $btn_css .'" onclick="codeAddress()">' . esc_attr(  __( 'Search', 'grim' ) ) . '</button>' . "\r\n";

    $output .= "\t\t" . '<div style="clear:both"></div>' . "\r\n";
    $output .= "\t\t" . '</div>' . "\r\n";



    $output .= "\t" . '<div id="store_map" style="height: '. $height .'"></div>' . "\r\n";
    $output .= "\t" . '<div id="store_finded" class="store_finded"></div>' . "\r\n";    
    $output .= "\t" . '<div class="addresses" id="addresses_list" style="min-height: 100px">' . "\r\n";
    $output .= "\t" . '<ul class="row">' . "\r\n";
    $output .= "\t\t" . '<li class="no_stores_found"><div class="no_stores_found">' . esc_attr( __('Enter your city or zip code in input above.', 'grim') ) . '</div></li>' . "\r\n";
    $output .= "\t\t\t" . '</ul>' . "\r\n";
    $output .= "\t\t" . '</div>' . "\r\n";
    $output .= "\t\t" . '<div class="addresses" id="directions_text" style="height: '. $height .'; display:none">' . "\r\n";
    $output .= "\t\t\t" . '<a href="#"" onclick="end_directions(); return false;" class="return_to_results">&lt;&lt;' . esc_attr( __('Back to Results', 'grim') ) . '</a>' . "\r\n";
    $output .= "\t\t\t" . '<div  id="direction_destination"></div>' . "\r\n";
    $output .= "\t\t\t\t" . '<div id="directions_steps">' . "\r\n";
    $output .= "\t\t\t\t" . '</div>' . "\r\n";
    $output .= "\t\t" . '</div>' . "\r\n";
    $output .= "\t\t" . '<div id="d_colons" class="footer_html">' . "\r\n";
    $output .= "\t\t\t" . '</div>' . "\r\n";

    echo $output;

} else {
   echo '<div id="grim-dealer-error" class="col col-12">'. esc_html__('No Valid API', 'grim') .'</div>';
}

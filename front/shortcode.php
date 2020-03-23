<?php
/**
 * Shortcode class
 *
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Dealer_Frontend_Shortcode' ) ) {

    /**
     * Frontend of the dealer locator shortcode
     *
     * @since 1.0.0
     */
    class Dealer_Frontend_Shortcode {

        /**
         * Shortcode attributes from shortcode tag
         * @var $short_atts
         */
        public $short_atts = null;

        /**
         * Class constructor
         */
        public function __construct() {
    
            //Action and filters of class
            add_action( 'wp_ajax_dealer_search',        array( $this, 'dealer_search' ) );
            add_action( 'wp_ajax_nopriv_dealer_search', array( $this, 'dealer_search' ) );
            add_shortcode( 'dealer_map',              array( $this, 'show_store_locator' ) );
            add_filter( 'the_content',                  array( $this, 'remove_duplicated_locator' ));
        }

        /**
         * Handle the Ajax search on the frontend.
         *
         * @since 1.0.0
         * @return json List of store locations in JSON OBJECT format that are located within search radius
         */
        public function dealer_search($args = array()) {

           global $wpdb, $dealer_map, $dealer_map_settings;
            //Security check
            $security = (isset($_GET['security'])) ? $_GET['security'] : '';
            if ( ! wp_verify_nonce( $security, 'dealer_map_nonce' ) )
                die ( 'Busted!');

            $store_data = array();
            $table = $wpdb->prefix . 'dealer_map_stores';
            $unit = (dealer_map_get_distance_unit() == 'km') ? 'Km' : 'Mi';
            /*
             * Set the correct earth radius in either km or miles.
             * We need this to calculate the distance between two coordinates.
             */
            $placeholder_values[] = ( dealer_map_get_distance_unit() == 'km' ) ? 6371 : 3959;

            // The placeholder values for the prepared statement in the SQL query.
            if ( empty( $args ) ) {
                $args = $_GET;
            }
            $lat = (isset($args['lat'])) ? $args['lat'] : 33;
            $lng = (isset($args['lng'])) ? $args['lng'] : -84;
            $rad = (isset($args['radius'])) ? $args['radius'] : 20;
            $limit = (isset($args['limit'])) ? $args['limit'] : 10;

            array_push( $placeholder_values, $lat, $lng, $lat, $rad, $limit );

            $sql_part = 'HAVING distance < %s ORDER BY distance LIMIT 0, %d';

            //$store = $wpdb->get_results( 
            //        $wpdb->prepare("SELECT *, 3956 * 2 * ASIN(SQRT( POWER(SIN((lat - %s) * pi()/180 / 2), 2) + COS(lat * pi()/180) * COS(%s * pi()/180) * POWER(SIN((lng - %s) * pi()/180 / 2), 2) ))AS distance FROM $table WHERE active = 1 $sql_part", $lat, $lat, $lng, $rad) );
        
            $stores = $wpdb->prepare("SELECT *, ( %d * acos( cos( radians(%s) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(%s) ) + sin( radians(%s) ) * sin( radians( lat ) ) ) ) AS distance FROM $table WHERE active = 1 $sql_part", $placeholder_values);

            $store_data = $wpdb->get_results($stores, OBJECT);

            if ( $store_data === false ) {
                wp_send_json_error();
            } else {
                $store_results = array();
                $end_result = array();
                foreach ($store_data as $k => $v) {
                    /* If we have a valid thumb id, get the src */
                    if ( absint ( $store_data[$k]->thumb_id ) ) {
                        $thumb_src = wp_get_attachment_image_src( $store_data[$k]->thumb_id );
                        $store_data[$k]->thumb_src = $thumb_src[0];
                    } else {
                        $store_data[$k]->thumb_src = '';
                    }
                    $web = ($store_data[$k]->url !== null) ? esc_url( $store_data[$k]->url ) : '';
                    $pin_icon = ($store_data[$k]->proseries === '1') ? 'pro' : 'main';
                    /* Sanitize the results before they are returned */
                    /*Not added yet 'hours'       => wpautop( strip_tags( stripslashes( $store_data[$k]->hours ) ) ), */
                    $store_result[] = array (
                        'store_id'    => absint( $store_data[$k]->id ),
                        'store'       => sanitize_text_field( stripslashes( $store_data[$k]->name ) ),
                        'address'     => sanitize_text_field( stripslashes( $store_data[$k]->address ) ),
                        'address2'    => sanitize_text_field( stripslashes( $store_data[$k]->address2 ) ),
                        'city'        => sanitize_text_field( stripslashes( $store_data[$k]->city ) ),
                        'state'       => sanitize_text_field( stripslashes( $store_data[$k]->state ) ),
                        'zip'         => sanitize_text_field( stripslashes( $store_data[$k]->zip ) ),
                        'country'     => sanitize_text_field( stripslashes( $store_data[$k]->country ) ),   
                        'distance'    => number_format($store_data[$k]->distance, 1).''. $unit,
                        'lat'         => $store_data[$k]->lat,
                        'lng'         => $store_data[$k]->lng,
                        'pin_icon'    => $pin_icon,
                        'pro'         => $store_data[$k]->proseries,
                        'summary'     => $this->summary($store_data[$k]->name, $store_data[$k]->address, $store_data[$k]->city,
                                         $store_data[$k]->state, $store_data[$k]->zip, $store_data[$k]->country, $store_data[$k]->phone ),
                        'description' => wpautop( strip_tags( stripslashes( $store_data[$k]->description ) ) ), 
                        'phone'       => sanitize_text_field( stripslashes( $store_data[$k]->phone ) ), 
                        'fax'         => sanitize_text_field( stripslashes( $store_data[$k]->fax ) ),
                        'email'       => sanitize_email( $store_data[$k]->email ),  
                        'website'     => $web,
                      //'thumb'       => esc_url( $store_data[$k]->thumb_src )  
                     );
                        $end_result[absint( $store_data[$k]->id )] = array( 
                                       '<span class="name">' . stripslashes( $store_data[$k]->name ) . '</span><br />',
                                       '<span class="address">' . stripslashes( $store_data[$k]->address ) . '</span><br />',
                                       '<span class="city">' . stripslashes( $store_data[$k]->city ) . ', '.'</span>',
                                       '<span class="prov_state">' . stripslashes( $store_data[$k]->state ) . ' '. '</span>',
                                       '<span class="postal_zip">' . stripslashes( $store_data[$k]->zip ) . ', '.'</span>',
                                       '<span class="country">'. stripslashes( $store_data[$k]->country ) . '</span><br /><br />',
                                        esc_html__('Ph', 'wp-dealer-map') . ': <span class="phone">'. stripslashes( $store_data[$k]->phone ) . '</span><br />',
                                        esc_html__('Fax', 'wp-dealer-map') .': <span class="phone">'. stripslashes( $store_data[$k]->fax ) . '</span><br />',
                                       '<span class="website"> <a href="'. $web .'" target="_blank"></a></span>'
                        );
                }  
            
                $store_results['stores'] = $store_result;
                $store_results['popup'] = $end_result;   
                $store_results['you'] = array(
                       'lat' => $lat,
                       'lng' => $lng
                );   
                wp_send_json( $store_results );

            }   
            wp_die();
        }

        /**
         * Summary building function
         * @since  1.0.0 (description)
         * @param  string $name    
         * @param  string $address 
         * @param  string $city    
         * @param  string $state   
         * @param  string $zip     
         * @param  string $country 
         * @param  string $phone   
         * @return string|html  
         */
        public function summary($name, $address, $city, $state, $zip, $country, $phone ) {
           $sum = '';
           $sum .= '<span class="name">' . stripslashes($name) . '</span>';
           $sum .= '<span class="address">' . stripslashes($address) . '</span><br />';
           $sum .= '<span class="city">' . stripslashes($city) . ', '.'</span>';
           $sum .= '<span class="prov_state">' . stripslashes($state) . ' '. '</span>';
           $sum .= '<span class="postal_zip">' . stripslashes($zip) . ', '. '</span>';
           $sum .= '<span class="country">'. stripslashes($country) . '</span><br />';
           $sum .= '<span class="phone">'. stripslashes($phone) . '</span>';
           return $sum;
        }

        /**
         * Handle the [dealer_map] shortcode.
         *
         * @since 1.0.0
         * @param  array  $atts   Shortcode attributes
         * @return string $output The map-html template
         */
        public function show_store_locator( $atts ) {

            global $dealer_map, $dealer_map_settings;

                include DEALER_PLUGIN_DIR . 'front/dealer-data.php';

                //Enqueue JS and get final parameters
                wp_enqueue_style('dealer_map_css');
                wp_enqueue_script('dealer_map_api');
                wp_localize_script('dealer_map_handler_script', 'dealer_map', $parameters);
                wp_enqueue_script('dealer_map_handler_script');

                ob_start();
                include DEALER_PLUGIN_DIR . 'front/map-html.php';
                return ob_get_clean();
            
        }
        
        /**
         * Handle the map-html.php result address number of columns css classes
         *
         * @since 1.0.0
         * @param $attribut Boolen true if used shortcode attribute result_columns
         * @param $colons string Number of columns results added to shortcode or to settings page 
         * @return string CSS classes
         * 
         */
        public function column_css( $attribut = false, $colons ) {
            global $dealer_map_settings;
            $css = '';

            if ( isset( $dealer_map_settings['num_of_columns']) || $attribut ) {
                $var = ($attribut === true) ? $colons : $dealer_map_settings['num_of_columns'];
                switch ( $var ) {
                    case '2':
                        $css = 'colo-2';
                        break;
                    case '3':
                        $css = 'colo-3';
                        break;
                    case '4':
                        $css = 'colo-4';
                        break;
                    case '5':
                        $css = 'colo-5';
                        break;
                    case '6':
                        $css = 'colo-6';
                        break;
                    default:
                        $css = 'no_stores_found';
                        break;
                }
            }
            return $css;
        }

        /**
         * Handle the map-html.php search button css classes
         *
         * @since 1.0.0
         * @return string CSS classes
         */
        public function button_css() {
            global $dealer_map_settings;
            $classes = '';

            if ( isset( $dealer_map_settings['button_css'] ) ) {
                switch ($dealer_map_settings['button_css']) {
                    case 'red':
                        $classes = 'btn red-gradient';
                        break;
                    case 'blue':
                        $classes = 'btn blue-gradient';
                        break;
                    case 'green':
                        $classes = 'btn green-gradient';
                        break;
                    case 'white':
                        $classes = 'btn white-gradient';
                        break;
                    case 'black':
                        $classes = 'btn black-gradient';
                        break;
                    default:
                        $classes = 'btn ';
                        break;
                }
            }
            return $classes;
        }


        /**
         * Handle the [dealer_map] shortcode attributes
         *
         * @since 1.0.0
         * @param array $atts Shortcode attributes
         */
        public function check_dealer_map_shortcode_atts( $atts ) {

            if ( isset( $atts['default_start'] ) && $atts['default_start'] !== '' ) {
                $this->short_atts['att']['showdef'] = $atts['default_start']; //only can be 0 or 1, true or false
            }

            if ( isset( $atts['start_address'] ) && $atts['start_address'] ) {
                $coordinates = dealer_map_check_coordinates_transient( $atts['start_address'] );

                if ( isset( $coordinates ) && array_key_exists('lat', $coordinates ) ) {
                    //lattitude/longitude coordinates from gecoding G service
                    $this->short_atts['att']['coordinates'] = $coordinates; 
                }
            }
            
            if ( isset( $atts['maptype'] ) && array_key_exists( $atts['maptype'], dealer_map_get_map_types() ) ) {
                $this->short_atts['att']['maptype'] = $atts['maptype']; //only roadmap, satellite, hybrid, terrain accepted
            }

            if ( isset( $atts['result_columns'] ) && $atts['result_columns'] !== '' ) {
                $this->short_atts['att']['colnum'] = $atts['result_columns']; //only 2, 3, 4, 5, 6 columns accepted
            }

        }


        /**
         * Handling the dropdown list for search radius or limit options
         *
         * @since 1.0.0
         * @param  string $list     Name of the list radius or limit
         * @return string $dropdown A list with all available options for the dropdown
         */
        public function dealer_map_dropdown_list( $list ) {

            global $dealer_map_settings;

            $dropdown = '';
            $expand      = explode( ',', $dealer_map_settings[$list] );

            // Only show the distance unit if we are dealing with the search radius.
            if ( $list == 'search_radius' ) {
                $distance_unit = ' '. esc_attr( dealer_map_get_distance_unit() );
            } else {
                $distance_unit = '';
            }

            foreach ( $expand as $key => $setted_value ) {
                
                if ( strpos( $setted_value, '[' ) !== false ) {
                    $setted_value = filter_var( $setted_value, FILTER_SANITIZE_NUMBER_INT );
                    $selected = 'selected="selected" ';
                } else {
                    $selected = '';
                }

                $dropdown .= '<option ' . $selected . 'value="'. absint( $setted_value ) .'">'. absint( $setted_value ) . $distance_unit .'</option>';
            }

            return $dropdown;
        }

        /**
         * Remove all other instances from pages and post
         * only one shortcode executed per post/page
         * @since 1.0.0 (description)
         * @param $content  string All content from page where shortcode [dealer_map_delaers] used
         * @return $content string striped out all other shorcodes except first one
         */
        public function remove_duplicated_locator($content){ 
            
            //check if page has our shortcode
            if( has_shortcode( $content, 'dealer_map' ) ) {
                $pattern = get_shortcode_regex(array('dealer_map'));
                if( preg_match_all( '/'. $pattern .'/s', $content, $matches ) ) {
                    if( count($matches[0]) > 1 ) { 
                        // Find start position of shortcodes codes from $content variable
                        $start =  strpos($content, '[dealer_map]');
                        $end = strrpos($content, '[dealer_map]');

                        // Return error message as multiple shortcodes added
                        $error = (is_admin() != true) ? esc_html_e( 'Error, only one shortcode per page allowed!', 'wp-dealer-map' ) : '';
                        $content = $error . '' . substr_replace($content, '', $start, $end);
                    }
                }
            } 
            return $content; 
        }

    }
}

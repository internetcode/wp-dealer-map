<?php
/**
 * Plugin settings.
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Dealer_Map_Settings' ) ) {
    
	class Dealer_Map_Settings {
                        
        public function __construct() {
            add_action( 'admin_init', array( $this, 'register_settings' ) );
        }

        /**
         * Register the settings.
         * 
         * @since 1.0.0
         * @return void
         */
        public function register_settings() {
            register_setting( 'dealer_map_settings', 'dealer_map_settings', array( $this, 'sanitize_settings' ) );
        }
            
        /**
         * Sanitize the submitted plugin settings.
         * 
         * @since 1.0.0
         * @return array $output The setting values
         */
		public function sanitize_settings() {

            global $dealer_map_settings, $dealer_map_admin;

            /* Google Maps API settings tab */ 
            if ( empty($dealer_map_settings['api_server_key']) && !empty($_POST['dealer_map_api']['server_key']) ) {
                if (dealer_map_check_api($_POST['dealer_map_api']['server_key']) ) {
                    $this->settings_error( 'wrong_map_api' );
                } else {
                    $output['api_valid'] = sanitize_text_field( $_POST['dealer_map_api']['server_key'] );
                    $output['api_server_key'] = sanitize_text_field( $_POST['dealer_map_api']['server_key'] );
                }
            } elseif ( !empty($dealer_map_settings['api_server_key']) ) { 
                if ( $_POST['dealer_map_api']['server_key'] != $dealer_map_settings['api_valid'] ) {
                    dealer_map_check_api($_POST['dealer_map_api']['server_key']);
                    $this->settings_error( 'error_map_recheck' );
                } else {
                    $output['api_valid'] = sanitize_text_field( $_POST['dealer_map_api']['server_key'] );
                    $output['api_server_key'] = sanitize_text_field( $_POST['dealer_map_api']['server_key'] );
                }  
            } else {
                $this->settings_error( 'error_map_api' ); 
            }

			$output['api_language']          = wp_filter_nohtml_kses( $_POST['dealer_map_api']['language'] );
			$output['api_region']            = wp_filter_nohtml_kses( $_POST['dealer_map_api']['region'] );

            /* Search settings tab */
            // Check for a valid start latitude value, otherwise we use the default.
            if ( !empty( $_POST['dealer_map_search']['def_lat'] ) ) {
                $output['def_lat'] = sanitize_text_field( $_POST['dealer_map_search']['def_lat'] );
            } else {
                $this->settings_error( 'start_lat' );
                $output['def_lat'] = '40.730610';
            }

            // Check for a valid start longitude value, otherwise we use the default.
            if ( !empty( $_POST['dealer_map_search']['def_lng'] ) ) {
                $output['def_lng'] = sanitize_text_field( $_POST['dealer_map_search']['def_lng'] );
            } else {
                $this->settings_error( 'start_lng' );
                $output['def_lng'] = '-73.935242';
            }

            // Check for a valid default range, otherwise we use the default.
            if ( !empty( $_POST['dealer_map_search']['def_range'] ) ) {
                $output['def_range'] = sanitize_text_field( $_POST['dealer_map_search']['def_range'] );
            } else {
                $this->settings_error( 'range_limit' );
                $output['def_range'] = '25';
            }

            // Check for a valid default limit, otherwise we use the default.
            if ( !empty( $_POST['dealer_map_search']['def_limit'] ) ) {
                $output['def_limit'] = sanitize_text_field( $_POST['dealer_map_search']['def_limit'] );
            } else {
                $this->settings_error( 'range_limit' );
                $output['def_limit'] = '10';
            }

            // Check for valid value, otherwise set to false.
            $output['show_def'] = isset( $_POST['dealer_map_search']['show_def'] ) ? 1 : 0;

            // Check for a valid start longitude value, otherwise we use the default.
            if ( !empty( $_POST['dealer_map_search']['defaddress'] ) ) {
                $output['defaddress'] = sanitize_text_field( $_POST['dealer_map_search']['defaddress'] );
            } else {
                $this->settings_error( 'd_address' );
                $output['defaddress'] = '10001';
            }
                        
            $output['results_dropdown']     = isset( $_POST['dealer_map_search']['results_dropdown'] ) ? 1 : 0;
            $output['radius_dropdown']      = isset( $_POST['dealer_map_search']['radius_dropdown'] ) ? 1 : 0;
            
            $output['distance_unit'] = ( $_POST['dealer_map_search']['distance_unit'] == 'km' ) ? 'km' : 'mi';
			
			// Check for a valid max results value, otherwise we use the default.
			if ( !empty( $_POST['dealer_map_search']['max_results'] ) ) {
				$output['max_results'] = sanitize_text_field( $_POST['dealer_map_search']['max_results'] );
			} else {
				$this->settings_error( 'max_results' );
				$output['max_results'] = '20';
			}
			
			// See if a search radius value exist, otherwise we use the default.
			if ( !empty( $_POST['dealer_map_search']['radius'] ) ) {
				$output['search_radius'] = sanitize_text_field( $_POST['dealer_map_search']['radius'] );
			} else {
				$this->settings_error( 'search_radius' );
				$output['search_radius'] = '25';
			}
            
            /* Layout settings tab */
            $output['map_height'] = sanitize_text_field( $_POST['dealer_map_layout']['map_height'] );                      
			$output['api_maptype'] = wp_filter_nohtml_kses( $_POST['dealer_map_layout']['maptype'] );
            $output['api_zoom'] = wp_filter_nohtml_kses( $_POST['dealer_map_layout']['zoom'] );
            $output['marker_event'] = ( $_POST['dealer_map_layout']['marker_event'] == 'click' ) ? 'click' : 'mouseover';
            $output['marker_effect'] = ( $_POST['dealer_map_layout']['marker_effect'] == 'drop' ) ? 'drop' : 'bounce';
            $output['num_of_columns'] = ( $_POST['dealer_map_layout']['num_of_columns'] ) ? $_POST['dealer_map_layout']['num_of_columns'] : '4';
            $output['button_css'] = ( $_POST['dealer_map_layout']['btn_class'] ) ? $_POST['dealer_map_layout']['btn_class'] : '--';

            /* Addition settings tab */
            $output['remove_all'] = ( $_POST['dealer_map_addition']['remove_all'] == 'remove' ) ? 'remove' : 'keep';
            $output['keep_table'] = ( $_POST['dealer_map_addition']['keep_table'] == 'remove' ) ? 'remove' : 'keep';
            //Updating options function
            $this->maybe_keep($output['remove_all'], $output['keep_table']);

            $output['environment'] = ( $_POST['dealer_map_addition']['environment'] == 'production' ) ? 'production' : 'developing';
            $output['gmap_scripts'] =  isset( $_POST['dealer_map_addition']['gmap_scripts'] ) ? 1 : 0;  
            
			return $output;
		}

        /**
         * Maybe update options added by plugin_activated_hook
         * 
         * @since 1.0.0
         * @param strings Added from plugin settings page Additional tab
         * @return void
         */
        private function maybe_keep( $remove_all, $keep_table ) {
            $old_remove = get_option('dealer_map_remove_all');
            $old_keep = get_option('dealer_map_keep_table');
            //maybe update
            if( $old_remove != $remove_all ) {
                update_option( 'dealer_map_remove_all', $remove_all, true );
            }

            if( $old_keep != $remove_all ) {
                update_option( 'dealer_map_keep_table', $keep_table, true );
            }
        }

    
       
        /**
         * Handle the different validation errors for the plugin settings.
         * 
         * @since 1.0.0
         * @param string $error_type Contains the type of validation error that occured
         * @return void
         */
		private function settings_error( $error_type ) {
            
			switch ( $error_type ) {
                case 'wrong_map_api':
                    $error_msg = __( 'Please enter Valid Google Maps API keys.', 'wp-dealer-map' );   
                    break;
                case 'error_map_api':
                    $error_msg = __( 'Please enter Google Maps API keys.', 'wp-dealer-map' );   
                    break;
                case 'error_map_recheck':
                    $error_msg = __( 'Please type again your Google Maps API keys.', 'wp-dealer-map' );   
                    break;    
				case 'max_results':
					$error_msg = __( 'The max results field cannot be empty, the default value has been restored.', 'wp-dealer-map' );	
					break;
				case 'search_radius':
					$error_msg = __( 'The search radius field cannot be empty, the default value has been restored.', 'wp-dealer-map' );	
					break;	
                case 'start_lat':
					$error_msg = __( 'Please provide the Latitude that can be used as a starting point', 'wp-dealer-map' );
					break;
                case 'start_lng':
                    $error_msg = __( 'Please provide the Longitude that can be used as a starting point', 'wp-dealer-map' );
                    break;
                case 'range_limit':
                    $error_msg = __( 'Please provide valid range & limit for default dealers', 'wp-dealer-map' );
                    break;   
                case 'd_address':
                    $error_msg = __( 'Please provide valid ZIP that can be used as a starting point', 'wp-dealer-map' );
                    break;      
			}
			
			add_settings_error( 'setting-errors', esc_attr( 'settings_fail' ), $error_msg, 'error' );
		}

        /**
         * Options for the google map type list.
         *
         * @since 1.0.0
         * @param  string      $lista
         * @return string|void $option_list The html for the selected list
         */
        public function get_map_list() {

                $map_types = array(
                    __( 'Roadmap', 'wp-dealer-map' )   => 'roadmap',
                    __( 'Satellite', 'wp-dealer-map' ) => 'satellite',
                    __( 'Hybrid', 'wp-dealer-map' )    => 'hybrid',
                    __( 'Terrain', 'wp-dealer-map' )   => 'terrain'
                );

            return $this->get_api_options( $map_types, 'maptype' );
        }

        /**
         * Options for the map zoom list.
         *
         * @since 1.0.0
         * @param  string      $lista
         * @return string|void $option_list The html for the selected list
         */
        public function get_zoom_list() {

                $map_types = array(
                    __( '1. World', 'wp-dealer-map' )   => 1,
                    __( '2.', 'wp-dealer-map' )    => 2,
                    __( '3.', 'wp-dealer-map' )    => 3,
                    __( '4.', 'wp-dealer-map' )   => 4,
                    __( '5. Continent', 'wp-dealer-map' )   => 5,
                    __( '6.', 'wp-dealer-map' )   => 6,
                    __( '7.', 'wp-dealer-map' )   => 7,
                    __( '8.', 'wp-dealer-map' )   => 8,
                    __( '9.', 'wp-dealer-map' )   => 9,
                    __( '10. City', 'wp-dealer-map' )   => 10,
                    __( '11.', 'wp-dealer-map' )   => 11,
                    __( '12.', 'wp-dealer-map' )   => 12,
                    __( '13.', 'wp-dealer-map' )   => 13,
                    __( '14.', 'wp-dealer-map' )   => 14,
                    __( '14. Streets', 'wp-dealer-map' )   => 15,
                    __( '16.', 'wp-dealer-map' )   => 16,
                    __( '17.', 'wp-dealer-map' )   => 17,
                    __( '18.', 'wp-dealer-map' )   => 18,
                    __( '19.', 'wp-dealer-map' )   => 19,
                    __( '20. Buildings', 'wp-dealer-map' )   => 20,
                );

            return $this->get_api_options( $map_types, 'zoom' );
        }
        
        /**
         * Options for the language list.
         *
         * @since 1.0.0
         * @param  string      $lista 
         * @return string|void $option_list The html for the selected list
         */
		public function get_api_lang_list() {

				$api_lang_list = array ( 	
					__('Select your language', 'wp-dealer-map')    => '',
					__('English', 'wp-dealer-map')                 => 'en',
					__('Arabic', 'wp-dealer-map')                  => 'ar',
					__('Basque', 'wp-dealer-map')                  => 'eu',
					__('Bulgarian', 'wp-dealer-map')               => 'bg',
					__('Bengali', 'wp-dealer-map')                 => 'bn',
					__('Catalan', 'wp-dealer-map')                 => 'ca',
					__('Czech', 'wp-dealer-map')                   => 'cs',
					__('Danish', 'wp-dealer-map')                  => 'da',
					__('German', 'wp-dealer-map')                  => 'de',
					__('Greek', 'wp-dealer-map')                   => 'el',
					__('English (Australian)', 'wp-dealer-map')    => 'en-AU',
					__('English (Great Britain)', 'wp-dealer-map') => 'en-GB',
					__('Spanish', 'wp-dealer-map')                 => 'es',
					__('Farsi', 'wp-dealer-map')                   => 'fa',
					__('Finnish', 'wp-dealer-map')                 => 'fi',
					__('Filipino', 'wp-dealer-map')                => 'fil',
					__('French', 'wp-dealer-map')                  => 'fr',
					__('Galician', 'wp-dealer-map')                => 'gl',
					__('Gujarati', 'wp-dealer-map')                => 'gu',
					__('Hindi', 'wp-dealer-map')                   => 'hi',
					__('Croatian', 'wp-dealer-map')                => 'hr',
					__('Hungarian', 'wp-dealer-map')               => 'hu',
					__('Indonesian', 'wp-dealer-map')              => 'id',
					__('Italian', 'wp-dealer-map')                 => 'it',
					__('Hebrew', 'wp-dealer-map')                  => 'iw',
					__('Japanese', 'wp-dealer-map')                => 'ja',
					__('Kannada', 'wp-dealer-map')                 => 'kn',
					__('Korean', 'wp-dealer-map')                  => 'ko',
					__('Lithuanian', 'wp-dealer-map')              => 'lt',
					__('Latvian', 'wp-dealer-map')                 => 'lv',
					__('Malayalam', 'wp-dealer-map')               => 'ml',
					__('Marathi', 'wp-dealer-map')                 => 'mr',
					__('Dutch', 'wp-dealer-map')                   => 'nl',
					__('Norwegian', 'wp-dealer-map')               => 'no',
					__('Norwegian Nynorsk', 'wp-dealer-map')       => 'nn',
					__('Polish', 'wp-dealer-map')                  => 'pl',
					__('Portuguese', 'wp-dealer-map')              => 'pt',
					__('Portuguese (Brazil)', 'wp-dealer-map')     => 'pt-BR',
					__('Portuguese (Portugal)', 'wp-dealer-map')   => 'pt-PT',
					__('Romanian', 'wp-dealer-map')                => 'ro',
					__('Russian', 'wp-dealer-map')                 => 'ru',
					__('Slovak', 'wp-dealer-map')                  => 'sk',
					__('Slovenian', 'wp-dealer-map')               => 'sl',
					__('Serbian', 'wp-dealer-map')                 => 'sr',
					__('Swedish', 'wp-dealer-map')                 => 'sv',
					__('Tagalog', 'wp-dealer-map')                 => 'tl',
					__('Tamil', 'wp-dealer-map')                   => 'ta',
					__('Telugu', 'wp-dealer-map')                  => 'te',
					__('Thai', 'wp-dealer-map')                    => 'th',
					__('Turkish', 'wp-dealer-map')                 => 'tr',
					__('Ukrainian', 'wp-dealer-map')               => 'uk',
					__('Vietnamese', 'wp-dealer-map')              => 'vi',
					__('Chinese (Simplified)', 'wp-dealer-map')    => 'zh-CN',
					__('Chinese (Traditional)' ,'wp-dealer-map')   => 'zh-TW'
			);

            return $this->get_api_options( $api_lang_list, 'language' );
        }

        /**
         * Options for the region list.
         *
         * @since 1.0.0
         * @param  string      $lista
         * @return string|void $option_list The html for the selected list
         */        	
		public function get_api_reg_list() { 

                    $api_reg_list = array (
                        __('Select your region', 'wp-dealer-map')               => '',
                        __('Afghanistan', 'wp-dealer-map')                      => 'AF',
                        __('Albania', 'wp-dealer-map')                          => 'AL',
                        __('Algeria', 'wp-dealer-map')                          => 'DZ',
                        __('American Samoa', 'wp-dealer-map')                   => 'AZ',
                        __('Andorra', 'wp-dealer-map')                          => 'AD',
                        __('Angola', 'wp-dealer-map')                           => 'AO',
                        __('Anguilla', 'wp-dealer-map')                         => 'AI',
                        __('Antarctica', 'wp-dealer-map')                       => 'AQ',
                        __('Antigua and Barbuda', 'wp-dealer-map')              => 'AG',
                        __('Argentina', 'wp-dealer-map')                        => 'AR',
                        __('Armenia', 'wp-dealer-map')                          => 'AM',
                        __('Aruba', 'wp-dealer-map')                            => 'AW',
                        __('Ascension Island', 'wp-dealer-map')                 => 'AC',
                        __('Australia', 'wp-dealer-map')                        => 'AU',
                        __('Austria', 'wp-dealer-map')                          => 'AT',
                        __('Azerbaijan', 'wp-dealer-map')                       => 'AZ',
                        __('Bahamas', 'wp-dealer-map')                          => 'BS',
                        __('Bahrain', 'wp-dealer-map')                          => 'BH',
                        __('Bangladesh', 'wp-dealer-map')                       => 'BD',
                        __('Barbados', 'wp-dealer-map')                         => 'BB',
                        __('Belarus', 'wp-dealer-map')                          => 'BY',
                        __('Belgium', 'wp-dealer-map')                          => 'BE',
                        __('Belize', 'wp-dealer-map')                           => 'BZ',
                        __('Benin', 'wp-dealer-map')                            => 'BJ',
                        __('Bermuda', 'wp-dealer-map')                          => 'BM',
                        __('Bhutan', 'wp-dealer-map')                           => 'BT',
                        __('Bolivia', 'wp-dealer-map')                          => 'BO',
                        __('Bosnia and Herzegovina', 'wp-dealer-map')           => 'BA',
                        __('Botswana', 'wp-dealer-map')                         => 'BW',
                        __('Bouvet Island', 'wp-dealer-map')                    => 'BV',
                        __('Brazil', 'wp-dealer-map')                           => 'BR',
                        __('British Indian Ocean Territory', 'wp-dealer-map')   => 'IO',
                        __('British Virgin Islands', 'wp-dealer-map')           => 'VG',
                        __('Brunei', 'wp-dealer-map')                           => 'BN',
                        __('Bulgaria', 'wp-dealer-map')                         => 'BG',
                        __('Burkina Faso', 'wp-dealer-map')                     => 'BF',
                        __('Burundi', 'wp-dealer-map')                          => 'BI',
                        __('Cambodia', 'wp-dealer-map')                         => 'KH',
                        __('Cameroon', 'wp-dealer-map')                         => 'CM',
                        __('Canada', 'wp-dealer-map')                           => 'CA',
                        __('Canary Islands', 'wp-dealer-map')                   => 'IC',
                        __('Cape Verde', 'wp-dealer-map')                       => 'CV',
                        __('Caribbean Netherlands', 'wp-dealer-map')            => 'BQ',
                        __('Cayman Islands', 'wp-dealer-map')                   => 'KY',
                        __('Central African Republic', 'wp-dealer-map')         => 'CF',
                        __('Ceuta and Melilla', 'wp-dealer-map')                => 'EA',
                        __('Chad', 'wp-dealer-map')                             => 'TD',
                        __('Chile', 'wp-dealer-map')                            => 'CL',
                        __('China', 'wp-dealer-map')                            => 'CN',
                        __('Christmas Island', 'wp-dealer-map')                 => 'CX',
                        __('Clipperton Island', 'wp-dealer-map')                => 'CP',
                        __('Cocos (Keeling) Islands', 'wp-dealer-map')          => 'CC',
                        __('Colombia', 'wp-dealer-map')                         => 'CO',
                        __('Comoros', 'wp-dealer-map')                          => 'KM',
                        __('Congo (DRC)', 'wp-dealer-map')                      => 'CD',
                        __('Congo (Republic)', 'wp-dealer-map')                 => 'CG',
                        __('Cook Islands', 'wp-dealer-map')                     => 'CK',
                        __('Costa Rica', 'wp-dealer-map')                       => 'CR',
                        __('Croatia', 'wp-dealer-map')                          => 'HR',
                        __('Cuba', 'wp-dealer-map')                             => 'CU',
                        __('Curaçao', 'wp-dealer-map')                          => 'CW',
                        __('Cyprus', 'wp-dealer-map')                           => 'CY',
                        __('Czech Republic', 'wp-dealer-map')                   => 'CZ',
                        __('Côte d\'Ivoire', 'wp-dealer-map')                   => 'CI',
                        __('Denmark', 'wp-dealer-map')                          => 'DK',
                        __('Djibouti', 'wp-dealer-map')                         => 'DJ',
                        __('Democratic Republic of the Congo', 'wp-dealer-map') => 'CD',
                        __('Dominica', 'wp-dealer-map')                         => 'dM',
                        __('Dominican Republic', 'wp-dealer-map')               => 'DO',
                        __('Ecuador', 'wp-dealer-map')                          => 'EC',
                        __('Egypt', 'wp-dealer-map')                            => 'EG',
                        __('El Salvador', 'wp-dealer-map')                      => 'SV',
                        __('Equatorial Guinea', 'wp-dealer-map')                => 'GQ',
                        __('Eritrea', 'wp-dealer-map')                          => 'ER',
                        __('Estonia', 'wp-dealer-map')                          => 'EE',
                        __('Ethiopia', 'wp-dealer-map')                         => 'ET',
                        __('Falkland Islands(Islas Malvinas)', 'wp-dealer-map') => 'FK',
                        __('Faroe Islands', 'wp-dealer-map')                    => 'FO',
                        __('Fiji', 'wp-dealer-map')                             => 'FJ',
                        __('Finland', 'wp-dealer-map')                          => 'FI',
                        __('France', 'wp-dealer-map')                           => 'FR',
                        __('French Guiana', 'wp-dealer-map')                    => 'GF',
                        __('French Polynesia', 'wp-dealer-map')                 => 'PF',
                        __('French Southern Territories', 'wp-dealer-map')      => 'TF',
                        __('Gabon', 'wp-dealer-map')                            => 'GA',
                        __('Gambia', 'wp-dealer-map')                           => 'GM',
                        __('Georgia', 'wp-dealer-map')                          => 'GE',
                        __('Germany', 'wp-dealer-map')                          => 'DE',
                        __('Ghana', 'wp-dealer-map')                            => 'GH',
                        __('Gibraltar', 'wp-dealer-map')                        => 'GI',
                        __('Great Britain', 'wp-dealer-map')                    => 'GB',
                        __('Greece', 'wp-dealer-map')                           => 'GR',
                        __('Greenland', 'wp-dealer-map')                        => 'GL',
                        __('Grenada', 'wp-dealer-map')                          => 'GD',
                        __('Guam', 'wp-dealer-map')                             => 'GU',
                        __('Guadeloupe', 'wp-dealer-map')                       => 'GP',
                        __('Guam', 'wp-dealer-map')                             => 'GU',
                        __('Guatemala', 'wp-dealer-map')                        => 'GT',
                        __('Guernsey', 'wp-dealer-map')                         => 'GG',
                        __('Guine', 'wp-dealer-map')                            => 'GN',
                        __('Guinea-Bissau', 'wp-dealer-map')                    => 'GW',
                        __('Guyana', 'wp-dealer-map')                           => 'GY',
                        __('Haiti', 'wp-dealer-map')                            => 'HT',
                        __('Heard and McDonald Islands', 'wp-dealer-map')       => 'HM',
                        __('Honduras', 'wp-dealer-map')                         => 'HN',
                        __('Hong Kong', 'wp-dealer-map')                        => 'HK',
                        __('Hungary', 'wp-dealer-map')                          => 'HU',
                        __('Iceland', 'wp-dealer-map')                          => 'IS',
                        __('India', 'wp-dealer-map')                            => 'IN',
                        __('Indonesia', 'wp-dealer-map')                        => 'ID',
                        __('Iran', 'wp-dealer-map')                             => 'IR',
                        __('Iraq', 'wp-dealer-map')                             => 'IQ',
                        __('Ireland', 'wp-dealer-map')                          => 'IE',
                        __('Isle of Man', 'wp-dealer-map')                      => 'IM',
                        __('Israel', 'wp-dealer-map')                           => 'IL',
                        __('Italy', 'wp-dealer-map')                            => 'IT',
                        __('Jamaica', 'wp-dealer-map')                          => 'JM',
                        __('Japan', 'wp-dealer-map')                            => 'JP',
                        __('Jersey', 'wp-dealer-map')                           => 'JE',
                        __('Jordan', 'wp-dealer-map')                           => 'JO',
                        __('Kazakhstan', 'wp-dealer-map')                       => 'KZ',
                        __('Kenya', 'wp-dealer-map')                            => 'KE',
                        __('Kiribati', 'wp-dealer-map')                         => 'KI',
                        __('Kosovo', 'wp-dealer-map')                           => 'XK',
                        __('Kuwait', 'wp-dealer-map')                           => 'KW',
                        __('Kyrgyzstan', 'wp-dealer-map')                       => 'KG',
                        __('Laos', 'wp-dealer-map')                             => 'LA',
                        __('Latvia', 'wp-dealer-map')                           => 'LV',
                        __('Lebanon', 'wp-dealer-map')                          => 'LB',
                        __('Lesotho', 'wp-dealer-map')                          => 'LS',
                        __('Liberia', 'wp-dealer-map')                          => 'LR',
                        __('Libya', 'wp-dealer-map')                            => 'LY',
                        __('Liechtenstein', 'wp-dealer-map')                    => 'LI',
                        __('Lithuania', 'wp-dealer-map')                        => 'LT',
                        __('Luxembourg', 'wp-dealer-map')                       => 'LU',
                        __('Macau', 'wp-dealer-map')                            => 'MO',
                        __('Macedonia (FYROM)', 'wp-dealer-map')                => 'MK',
                        __('Madagascar', 'wp-dealer-map')                       => 'MG',
                        __('Malawi', 'wp-dealer-map')                           => 'MW',
                        __('Malaysia ', 'wp-dealer-map')                        => 'MY',
                        __('Maldives ', 'wp-dealer-map')                        => 'MV',
                        __('Mali', 'wp-dealer-map')                             => 'ML',
                        __('Malta', 'wp-dealer-map')                            => 'MT',
                        __('Marshall Islands', 'wp-dealer-map')                 => 'MH',
                        __('Martinique', 'wp-dealer-map')                       => 'MQ',
                        __('Mauritania', 'wp-dealer-map')                       => 'MR',
                        __('Mauritius', 'wp-dealer-map')                        => 'MU',
                        __('Mayotte', 'wp-dealer-map')                          => 'YT',
                        __('Mexico', 'wp-dealer-map')                           => 'MX',
                        __('Micronesia', 'wp-dealer-map')                       => 'FM',
                        __('Moldova', 'wp-dealer-map')                          => 'MD',
                        __('Monaco' ,'wp-dealer-map')                           => 'MC',
                        __('Mongolia', 'wp-dealer-map')                         => 'MN',
                        __('Montenegro', 'wp-dealer-map')                       => 'ME',
                        __('Montserrat', 'wp-dealer-map')                       => 'MS',
                        __('Morocco', 'wp-dealer-map')                          => 'MA',
                        __('Mozambique', 'wp-dealer-map')                       => 'MZ',
                        __('Myanmar (Burma)', 'wp-dealer-map')                  => 'MM',
                        __('Namibia', 'wp-dealer-map')                          => 'NA',
                        __('Nauru', 'wp-dealer-map')                            => 'NR',
                        __('Nepal', 'wp-dealer-map')                            => 'NP',
                        __('Netherlands', 'wp-dealer-map')                      => 'NL',
                        __('Netherlands Antilles', 'wp-dealer-map')             => 'AN',
                        __('New Caledonia', 'wp-dealer-map')                    => 'NC',
                        __('New Zealand', 'wp-dealer-map')                      => 'NZ',
                        __('Nicaragua', 'wp-dealer-map')                        => 'NI',
                        __('Niger', 'wp-dealer-map')                            => 'NE',
                        __('Nigeria', 'wp-dealer-map')                          => 'NG',
                        __('Niue', 'wp-dealer-map')                             => 'NU',
                        __('Norfolk Island', 'wp-dealer-map')                   => 'NF',
                        __('North Korea', 'wp-dealer-map')                      => 'KP',
                        __('Northern Mariana Islands', 'wp-dealer-map')         => 'MP',
                        __('Norway', 'wp-dealer-map')                           => 'NO',
                        __('Oman', 'wp-dealer-map')                             => 'OM',
                        __('Pakistan', 'wp-dealer-map')                         => 'PK',
                        __('Palau', 'wp-dealer-map')                            => 'PW',
                        __('Palestine', 'wp-dealer-map')                        => 'PS',
                        __('Panama' ,'wp-dealer-map')                           => 'PA',
                        __('Papua New Guinea', 'wp-dealer-map')                 => 'PG',
                        __('Paraguay' ,'wp-dealer-map')                         => 'PY',
                        __('Peru', 'wp-dealer-map')                             => 'PE',
                        __('Philippines', 'wp-dealer-map')                      => 'PH',
                        __('Pitcairn Islands', 'wp-dealer-map')                 => 'PN',
                        __('Poland', 'wp-dealer-map')                           => 'PL',
                        __('Portugal', 'wp-dealer-map')                         => 'PT',
                        __('Puerto Rico', 'wp-dealer-map')                      => 'PR',
                        __('Qatar', 'wp-dealer-map')                            => 'QA',
                        __('Reunion', 'wp-dealer-map')                          => 'RE',
                        __('Romania', 'wp-dealer-map')                          => 'RO',
                        __('Russia', 'wp-dealer-map')                           => 'RU',
                        __('Rwanda', 'wp-dealer-map')                           => 'RW',
                        __('Saint Helena', 'wp-dealer-map')                     => 'SH',
                        __('Saint Kitts and Nevis', 'wp-dealer-map')            => 'KN',
                        __('Saint Vincent and the Grenadines', 'wp-dealer-map') => 'VC',
                        __('Saint Lucia', 'wp-dealer-map')                      => 'LC',
                        __('Samoa', 'wp-dealer-map')                            => 'WS',
                        __('San Marino', 'wp-dealer-map')                       => 'SM',
                        __('São Tomé and Príncipe', 'wp-dealer-map')            => 'ST',
                        __('Saudi Arabia', 'wp-dealer-map')                     => 'SA',
                        __('Senegal', 'wp-dealer-map')                          => 'SN',
                        __('Serbia', 'wp-dealer-map')                           => 'RS',
                        __('Seychelles', 'wp-dealer-map')                       => 'SC',
                        __('Sierra Leone', 'wp-dealer-map')                     => 'SL',
                        __('Singapore', 'wp-dealer-map')                        => 'SG',
                        __('Sint Maarten', 'wp-dealer-map')                     => 'SX',
                        __('Slovakia', 'wp-dealer-map')                         => 'SK',
                        __('Slovenia', 'wp-dealer-map')                         => 'SI',
                        __('Solomon Islands', 'wp-dealer-map')                  => 'SB',
                        __('Somalia', 'wp-dealer-map')                          => 'SO',
                        __('South Africa', 'wp-dealer-map')                     => 'ZA',
                        __('South Georgia and South Sandwich Islands', 'wp-dealer-map') => 'GS',
                        __('South Korea', 'wp-dealer-map')                      => 'KR',
                        __('South Sudan', 'wp-dealer-map')                      => 'SS',
                        __('Spain', 'wp-dealer-map')                            => 'ES',
                        __('Sri Lanka', 'wp-dealer-map')                        => 'LK',
                        __('Sudan', 'wp-dealer-map')                            => 'SD',
                        __('Swaziland', 'wp-dealer-map')                        => 'SZ',
                        __('Sweden', 'wp-dealer-map')                           => 'SE',
                        __('Switzerland', 'wp-dealer-map')                      => 'CH',
                        __('Syria', 'wp-dealer-map')                            => 'SY',
                        __('São Tomé & Príncipe', 'wp-dealer-map')              => 'ST',
                        __('Taiwan', 'wp-dealer-map')                           => 'TW',
                        __('Tajikistan', 'wp-dealer-map')                       => 'TJ',
                        __('Tanzania', 'wp-dealer-map')                         => 'TZ',
                        __('Thailand', 'wp-dealer-map')                         => 'TH',
                        __('Timor-Leste', 'wp-dealer-map')                      => 'TL',
                        __('Tokelau' ,'wp-dealer-map')                          => 'TK',
                        __('Togo', 'wp-dealer-map')                             => 'TG',
                        __('Tokelau' ,'wp-dealer-map')                          => 'TK',
                        __('Tonga', 'wp-dealer-map')                            => 'TO',
                        __('Trinidad and Tobago', 'wp-dealer-map')              => 'TT',
                        __('Tristan da Cunha', 'wp-dealer-map')                 => 'TA',
                        __('Tunisia', 'wp-dealer-map')                          => 'TN',
                        __('Turkey', 'wp-dealer-map')                           => 'TR',
                        __('Turkmenistan', 'wp-dealer-map')                     => 'TM',
                        __('Turks and Caicos Islands', 'wp-dealer-map')         => 'TC',
                        __('Tuvalu', 'wp-dealer-map')                           => 'TV',
                        __('Uganda', 'wp-dealer-map')                           => 'UG',
                        __('Ukraine', 'wp-dealer-map')                          => 'UA',
                        __('United Arab Emirates', 'wp-dealer-map')             => 'AE',
                        __('United Kingdom', 'wp-dealer-map')                   => 'GB',
                        __('United States', 'wp-dealer-map')                    => 'US',
                        __('Uruguay', 'wp-dealer-map')                          => 'UY',
                        __('Uzbekistan', 'wp-dealer-map')                       => 'UZ',
                        __('Vanuatu', 'wp-dealer-map')                          => 'VU',
                        __('Vatican City', 'wp-dealer-map')                     => 'VA',
                        __('Venezuela', 'wp-dealer-map')                        => 'VE',
                        __('Vietnam', 'wp-dealer-map')                          => 'VN',
                        __('Wallis Futuna', 'wp-dealer-map')                    => 'WF',
                        __('Western Sahara', 'wp-dealer-map')                   => 'EH',
                        __('Yemen', 'wp-dealer-map')                            => 'YE',
                        __('Zambia' ,'wp-dealer-map')                           => 'ZM',
                        __('Zimbabwe', 'wp-dealer-map')                         => 'ZW',
                        __('Åland Islands', 'wp-dealer-map')                    => 'AX'
                    );

                return $this->get_api_options( $api_reg_list, 'region' );		
			}
			
		/**
         * Add dropdown option values to settings option page
         * @since 1.0.0 (description)
         * @param array|string array $array_list list of values, $lista required settings list
         * @return string html
         */
		public function get_api_options( array $array_list, $lista ) {

                global $dealer_map_settings;
                $option_list = '';
				$count = 0;
				
				foreach ( $array_list as $key => $value ) {  
				
					// If no option value exist, set the first one as selected.
					if ( ( $count == 0 ) && ( empty( $dealer_map_settings['api_'. $lista] ) ) ) {
						$selected = 'selected="selected"';
					} else {
						$selected = ( $dealer_map_settings['api_'. $lista] == $value ) ? 'selected="selected"' : '';
					}
					
					$option_list .= '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $key ) . '</option>';
					$count++;
				}
												
				return $option_list;					
		}
        
    }
}        
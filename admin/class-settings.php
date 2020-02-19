<?php
/**
 * Plugin settings.
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'GRIM_Settings' ) ) {
    
	class GRIM_Settings {
                        
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
            register_setting( 'grim_settings', 'grim_settings', array( $this, 'sanitize_settings' ) );
        }
            
        /**
         * Sanitize the submitted plugin settings.
         * 
         * @since 1.0.0
         * @return array $output The setting values
         */
		public function sanitize_settings() {

            global $grim_settings, $grim_admin;

            /* Google Maps API settings tab */ 
            if ( empty($grim_settings['api_server_key']) && !empty($_POST['grim_api']['server_key']) ) {
                if (grim_check_api($_POST['grim_api']['server_key']) ) {
                    $this->settings_error( 'wrong_map_api' );
                } else {
                    $output['api_valid'] = sanitize_text_field( $_POST['grim_api']['server_key'] );
                    $output['api_server_key'] = sanitize_text_field( $_POST['grim_api']['server_key'] );
                }
            } elseif ( !empty($grim_settings['api_server_key']) ) { 
                if ( $_POST['grim_api']['server_key'] != $grim_settings['api_valid'] ) {
                    grim_check_api($_POST['grim_api']['server_key']);
                    $this->settings_error( 'error_map_recheck' );
                } else {
                    $output['api_valid'] = sanitize_text_field( $_POST['grim_api']['server_key'] );
                    $output['api_server_key'] = sanitize_text_field( $_POST['grim_api']['server_key'] );
                }  
            } else {
                $this->settings_error( 'error_map_api' ); 
            }

			$output['api_language']          = wp_filter_nohtml_kses( $_POST['grim_api']['language'] );
			$output['api_region']            = wp_filter_nohtml_kses( $_POST['grim_api']['region'] );

            /* Search settings tab */
            // Check for a valid start latitude value, otherwise we use the default.
            if ( !empty( $_POST['grim_search']['def_lat'] ) ) {
                $output['def_lat'] = sanitize_text_field( $_POST['grim_search']['def_lat'] );
            } else {
                $this->settings_error( 'start_lat' );
                $output['def_lat'] = '40.730610';
            }

            // Check for a valid start longitude value, otherwise we use the default.
            if ( !empty( $_POST['grim_search']['def_lng'] ) ) {
                $output['def_lng'] = sanitize_text_field( $_POST['grim_search']['def_lng'] );
            } else {
                $this->settings_error( 'start_lng' );
                $output['def_lng'] = '-73.935242';
            }

            // Check for a valid default range, otherwise we use the default.
            if ( !empty( $_POST['grim_search']['def_range'] ) ) {
                $output['def_range'] = sanitize_text_field( $_POST['grim_search']['def_range'] );
            } else {
                $this->settings_error( 'range_limit' );
                $output['def_range'] = '25';
            }

            // Check for a valid default limit, otherwise we use the default.
            if ( !empty( $_POST['grim_search']['def_limit'] ) ) {
                $output['def_limit'] = sanitize_text_field( $_POST['grim_search']['def_limit'] );
            } else {
                $this->settings_error( 'range_limit' );
                $output['def_limit'] = '10';
            }

            // Check for valid value, otherwise set to false.
            $output['show_def'] = isset( $_POST['grim_search']['show_def'] ) ? 1 : 0;

            // Check for a valid start longitude value, otherwise we use the default.
            if ( !empty( $_POST['grim_search']['defaddress'] ) ) {
                $output['defaddress'] = sanitize_text_field( $_POST['grim_search']['defaddress'] );
            } else {
                $this->settings_error( 'd_address' );
                $output['defaddress'] = '10001';
            }
                        
            $output['results_dropdown']     = isset( $_POST['grim_search']['results_dropdown'] ) ? 1 : 0;
            $output['radius_dropdown']      = isset( $_POST['grim_search']['radius_dropdown'] ) ? 1 : 0;
            
            $output['distance_unit'] = ( $_POST['grim_search']['distance_unit'] == 'km' ) ? 'km' : 'mi';
			
			// Check for a valid max results value, otherwise we use the default.
			if ( !empty( $_POST['grim_search']['max_results'] ) ) {
				$output['max_results'] = sanitize_text_field( $_POST['grim_search']['max_results'] );
			} else {
				$this->settings_error( 'max_results' );
				$output['max_results'] = '20';
			}
			
			// See if a search radius value exist, otherwise we use the default.
			if ( !empty( $_POST['grim_search']['radius'] ) ) {
				$output['search_radius'] = sanitize_text_field( $_POST['grim_search']['radius'] );
			} else {
				$this->settings_error( 'search_radius' );
				$output['search_radius'] = '25';
			}
            
            /* Layout settings tab */
            $output['map_height'] = sanitize_text_field( $_POST['grim_layout']['map_height'] );                      
			$output['api_maptype']      = wp_filter_nohtml_kses( $_POST['grim_layout']['maptype'] );
            $output['api_zoom']      = wp_filter_nohtml_kses( $_POST['grim_layout']['zoom'] );
            $output['marker_event'] = ( $_POST['grim_layout']['marker_event'] == 'click' ) ? 'click' : 'mouseover';
            $output['marker_effect'] = ( $_POST['grim_layout']['marker_effect'] == 'drop' ) ? 'drop' : 'bounce';
            $output['num_of_columns'] = ( $_POST['grim_layout']['num_of_columns'] ) ? $_POST['grim_layout']['num_of_columns'] : '4';
            $output['button_css'] = ( $_POST['grim_layout']['btn_class'] ) ? $_POST['grim_layout']['btn_class'] : '--';

            /* Addition settings tab */
            $output['remove_all'] = ( $_POST['grim_addition']['remove_all'] == 'remove' ) ? 'remove' : 'keep';
            $output['keep_table'] = ( $_POST['grim_addition']['keep_table'] == 'remove' ) ? 'remove' : 'keep';
            $output['environment'] = ( $_POST['grim_addition']['environment'] == 'production' ) ? 'production' : 'developing';
            $output['gmap_scripts'] =  isset( $_POST['grim_addition']['gmap_scripts'] ) ? 1 : 0;  
            
			return $output;
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
                    $error_msg = __( 'Please enter Valid Google Maps API keys.', 'grim' );   
                    break;
                case 'error_map_api':
                    $error_msg = __( 'Please enter Google Maps API keys.', 'grim' );   
                    break;
                case 'error_map_recheck':
                    $error_msg = __( 'Please type again your Google Maps API keys.', 'grim' );   
                    break;    
				case 'max_results':
					$error_msg = __( 'The max results field cannot be empty, the default value has been restored.', 'grim' );	
					break;
				case 'search_radius':
					$error_msg = __( 'The search radius field cannot be empty, the default value has been restored.', 'grim' );	
					break;	
                case 'start_lat':
					$error_msg = __( 'Please provide the Latitude that can be used as a starting point', 'grim' );
					break;
                case 'start_lng':
                    $error_msg = __( 'Please provide the Longitude that can be used as a starting point', 'grim' );
                    break;
                case 'range_limit':
                    $error_msg = __( 'Please provide valid range & limit for default dealers', 'grim' );
                    break;   
                case 'd_address':
                    $error_msg = __( 'Please provide valid ZIP that can be used as a starting point', 'grim' );
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
                    __( 'Roadmap', 'grim' )   => 'roadmap',
                    __( 'Satellite', 'grim' ) => 'satellite',
                    __( 'Hybrid', 'grim' )    => 'hybrid',
                    __( 'Terrain', 'grim' )   => 'terrain'
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
                    __( '1. World', 'grim' )   => 1,
                    __( '2.', 'grim' )    => 2,
                    __( '3.', 'grim' )    => 3,
                    __( '4.', 'grim' )   => 4,
                    __( '5. Continent', 'grim' )   => 5,
                    __( '6.', 'grim' )   => 6,
                    __( '7.', 'grim' )   => 7,
                    __( '8.', 'grim' )   => 8,
                    __( '9.', 'grim' )   => 9,
                    __( '10. City', 'grim' )   => 10,
                    __( '11.', 'grim' )   => 11,
                    __( '12.', 'grim' )   => 12,
                    __( '13.', 'grim' )   => 13,
                    __( '14.', 'grim' )   => 14,
                    __( '14. Streets', 'grim' )   => 15,
                    __( '16.', 'grim' )   => 16,
                    __( '17.', 'grim' )   => 17,
                    __( '18.', 'grim' )   => 18,
                    __( '19.', 'grim' )   => 19,
                    __( '20. Buildings', 'grim' )   => 20,
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
					__('Select your language', 'grim')    => '',
					__('English', 'grim')                 => 'en',
					__('Arabic', 'grim')                  => 'ar',
					__('Basque', 'grim')                  => 'eu',
					__('Bulgarian', 'grim')               => 'bg',
					__('Bengali', 'grim')                 => 'bn',
					__('Catalan', 'grim')                 => 'ca',
					__('Czech', 'grim')                   => 'cs',
					__('Danish', 'grim')                  => 'da',
					__('German', 'grim')                  => 'de',
					__('Greek', 'grim')                   => 'el',
					__('English (Australian)', 'grim')    => 'en-AU',
					__('English (Great Britain)', 'grim') => 'en-GB',
					__('Spanish', 'grim')                 => 'es',
					__('Farsi', 'grim')                   => 'fa',
					__('Finnish', 'grim')                 => 'fi',
					__('Filipino', 'grim')                => 'fil',
					__('French', 'grim')                  => 'fr',
					__('Galician', 'grim')                => 'gl',
					__('Gujarati', 'grim')                => 'gu',
					__('Hindi', 'grim')                   => 'hi',
					__('Croatian', 'grim')                => 'hr',
					__('Hungarian', 'grim')               => 'hu',
					__('Indonesian', 'grim')              => 'id',
					__('Italian', 'grim')                 => 'it',
					__('Hebrew', 'grim')                  => 'iw',
					__('Japanese', 'grim')                => 'ja',
					__('Kannada', 'grim')                 => 'kn',
					__('Korean', 'grim')                  => 'ko',
					__('Lithuanian', 'grim')              => 'lt',
					__('Latvian', 'grim')                 => 'lv',
					__('Malayalam', 'grim')               => 'ml',
					__('Marathi', 'grim')                 => 'mr',
					__('Dutch', 'grim')                   => 'nl',
					__('Norwegian', 'grim')               => 'no',
					__('Norwegian Nynorsk', 'grim')       => 'nn',
					__('Polish', 'grim')                  => 'pl',
					__('Portuguese', 'grim')              => 'pt',
					__('Portuguese (Brazil)', 'grim')     => 'pt-BR',
					__('Portuguese (Portugal)', 'grim')   => 'pt-PT',
					__('Romanian', 'grim')                => 'ro',
					__('Russian', 'grim')                 => 'ru',
					__('Slovak', 'grim')                  => 'sk',
					__('Slovenian', 'grim')               => 'sl',
					__('Serbian', 'grim')                 => 'sr',
					__('Swedish', 'grim')                 => 'sv',
					__('Tagalog', 'grim')                 => 'tl',
					__('Tamil', 'grim')                   => 'ta',
					__('Telugu', 'grim')                  => 'te',
					__('Thai', 'grim')                    => 'th',
					__('Turkish', 'grim')                 => 'tr',
					__('Ukrainian', 'grim')               => 'uk',
					__('Vietnamese', 'grim')              => 'vi',
					__('Chinese (Simplified)', 'grim')    => 'zh-CN',
					__('Chinese (Traditional)' ,'grim')   => 'zh-TW'
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
                        __('Select your region', 'grim')               => '',
                        __('Afghanistan', 'grim')                      => 'AF',
                        __('Albania', 'grim')                          => 'AL',
                        __('Algeria', 'grim')                          => 'DZ',
                        __('American Samoa', 'grim')                   => 'AZ',
                        __('Andorra', 'grim')                          => 'AD',
                        __('Angola', 'grim')                           => 'AO',
                        __('Anguilla', 'grim')                         => 'AI',
                        __('Antarctica', 'grim')                       => 'AQ',
                        __('Antigua and Barbuda', 'grim')              => 'AG',
                        __('Argentina', 'grim')                        => 'AR',
                        __('Armenia', 'grim')                          => 'AM',
                        __('Aruba', 'grim')                            => 'AW',
                        __('Ascension Island', 'grim')                 => 'AC',
                        __('Australia', 'grim')                        => 'AU',
                        __('Austria', 'grim')                          => 'AT',
                        __('Azerbaijan', 'grim')                       => 'AZ',
                        __('Bahamas', 'grim')                          => 'BS',
                        __('Bahrain', 'grim')                          => 'BH',
                        __('Bangladesh', 'grim')                       => 'BD',
                        __('Barbados', 'grim')                         => 'BB',
                        __('Belarus', 'grim')                          => 'BY',
                        __('Belgium', 'grim')                          => 'BE',
                        __('Belize', 'grim')                           => 'BZ',
                        __('Benin', 'grim')                            => 'BJ',
                        __('Bermuda', 'grim')                          => 'BM',
                        __('Bhutan', 'grim')                           => 'BT',
                        __('Bolivia', 'grim')                          => 'BO',
                        __('Bosnia and Herzegovina', 'grim')           => 'BA',
                        __('Botswana', 'grim')                         => 'BW',
                        __('Bouvet Island', 'grim')                    => 'BV',
                        __('Brazil', 'grim')                           => 'BR',
                        __('British Indian Ocean Territory', 'grim')   => 'IO',
                        __('British Virgin Islands', 'grim')           => 'VG',
                        __('Brunei', 'grim')                           => 'BN',
                        __('Bulgaria', 'grim')                         => 'BG',
                        __('Burkina Faso', 'grim')                     => 'BF',
                        __('Burundi', 'grim')                          => 'BI',
                        __('Cambodia', 'grim')                         => 'KH',
                        __('Cameroon', 'grim')                         => 'CM',
                        __('Canada', 'grim')                           => 'CA',
                        __('Canary Islands', 'grim')                   => 'IC',
                        __('Cape Verde', 'grim')                       => 'CV',
                        __('Caribbean Netherlands', 'grim')            => 'BQ',
                        __('Cayman Islands', 'grim')                   => 'KY',
                        __('Central African Republic', 'grim')         => 'CF',
                        __('Ceuta and Melilla', 'grim')                => 'EA',
                        __('Chad', 'grim')                             => 'TD',
                        __('Chile', 'grim')                            => 'CL',
                        __('China', 'grim')                            => 'CN',
                        __('Christmas Island', 'grim')                 => 'CX',
                        __('Clipperton Island', 'grim')                => 'CP',
                        __('Cocos (Keeling) Islands', 'grim')          => 'CC',
                        __('Colombia', 'grim')                         => 'CO',
                        __('Comoros', 'grim')                          => 'KM',
                        __('Congo (DRC)', 'grim')                      => 'CD',
                        __('Congo (Republic)', 'grim')                 => 'CG',
                        __('Cook Islands', 'grim')                     => 'CK',
                        __('Costa Rica', 'grim')                       => 'CR',
                        __('Croatia', 'grim')                          => 'HR',
                        __('Cuba', 'grim')                             => 'CU',
                        __('Curaçao', 'grim')                          => 'CW',
                        __('Cyprus', 'grim')                           => 'CY',
                        __('Czech Republic', 'grim')                   => 'CZ',
                        __('Côte d\'Ivoire', 'grim')                   => 'CI',
                        __('Denmark', 'grim')                          => 'DK',
                        __('Djibouti', 'grim')                         => 'DJ',
                        __('Democratic Republic of the Congo', 'grim') => 'CD',
                        __('Dominica', 'grim')                         => 'dM',
                        __('Dominican Republic', 'grim')               => 'DO',
                        __('Ecuador', 'grim')                          => 'EC',
                        __('Egypt', 'grim')                            => 'EG',
                        __('El Salvador', 'grim')                      => 'SV',
                        __('Equatorial Guinea', 'grim')                => 'GQ',
                        __('Eritrea', 'grim')                          => 'ER',
                        __('Estonia', 'grim')                          => 'EE',
                        __('Ethiopia', 'grim')                         => 'ET',
                        __('Falkland Islands(Islas Malvinas)', 'grim') => 'FK',
                        __('Faroe Islands', 'grim')                    => 'FO',
                        __('Fiji', 'grim')                             => 'FJ',
                        __('Finland', 'grim')                          => 'FI',
                        __('France', 'grim')                           => 'FR',
                        __('French Guiana', 'grim')                    => 'GF',
                        __('French Polynesia', 'grim')                 => 'PF',
                        __('French Southern Territories', 'grim')      => 'TF',
                        __('Gabon', 'grim')                            => 'GA',
                        __('Gambia', 'grim')                           => 'GM',
                        __('Georgia', 'grim')                          => 'GE',
                        __('Germany', 'grim')                          => 'DE',
                        __('Ghana', 'grim')                            => 'GH',
                        __('Gibraltar', 'grim')                        => 'GI',
                        __('Great Britain', 'grim')                    => 'GB',
                        __('Greece', 'grim')                           => 'GR',
                        __('Greenland', 'grim')                        => 'GL',
                        __('Grenada', 'grim')                          => 'GD',
                        __('Guam', 'grim')                             => 'GU',
                        __('Guadeloupe', 'grim')                       => 'GP',
                        __('Guam', 'grim')                             => 'GU',
                        __('Guatemala', 'grim')                        => 'GT',
                        __('Guernsey', 'grim')                         => 'GG',
                        __('Guine', 'grim')                            => 'GN',
                        __('Guinea-Bissau', 'grim')                    => 'GW',
                        __('Guyana', 'grim')                           => 'GY',
                        __('Haiti', 'grim')                            => 'HT',
                        __('Heard and McDonald Islands', 'grim')       => 'HM',
                        __('Honduras', 'grim')                         => 'HN',
                        __('Hong Kong', 'grim')                        => 'HK',
                        __('Hungary', 'grim')                          => 'HU',
                        __('Iceland', 'grim')                          => 'IS',
                        __('India', 'grim')                            => 'IN',
                        __('Indonesia', 'grim')                        => 'ID',
                        __('Iran', 'grim')                             => 'IR',
                        __('Iraq', 'grim')                             => 'IQ',
                        __('Ireland', 'grim')                          => 'IE',
                        __('Isle of Man', 'grim')                      => 'IM',
                        __('Israel', 'grim')                           => 'IL',
                        __('Italy', 'grim')                            => 'IT',
                        __('Jamaica', 'grim')                          => 'JM',
                        __('Japan', 'grim')                            => 'JP',
                        __('Jersey', 'grim')                           => 'JE',
                        __('Jordan', 'grim')                           => 'JO',
                        __('Kazakhstan', 'grim')                       => 'KZ',
                        __('Kenya', 'grim')                            => 'KE',
                        __('Kiribati', 'grim')                         => 'KI',
                        __('Kosovo', 'grim')                           => 'XK',
                        __('Kuwait', 'grim')                           => 'KW',
                        __('Kyrgyzstan', 'grim')                       => 'KG',
                        __('Laos', 'grim')                             => 'LA',
                        __('Latvia', 'grim')                           => 'LV',
                        __('Lebanon', 'grim')                          => 'LB',
                        __('Lesotho', 'grim')                          => 'LS',
                        __('Liberia', 'grim')                          => 'LR',
                        __('Libya', 'grim')                            => 'LY',
                        __('Liechtenstein', 'grim')                    => 'LI',
                        __('Lithuania', 'grim')                        => 'LT',
                        __('Luxembourg', 'grim')                       => 'LU',
                        __('Macau', 'grim')                            => 'MO',
                        __('Macedonia (FYROM)', 'grim')                => 'MK',
                        __('Madagascar', 'grim')                       => 'MG',
                        __('Malawi', 'grim')                           => 'MW',
                        __('Malaysia ', 'grim')                        => 'MY',
                        __('Maldives ', 'grim')                        => 'MV',
                        __('Mali', 'grim')                             => 'ML',
                        __('Malta', 'grim')                            => 'MT',
                        __('Marshall Islands', 'grim')                 => 'MH',
                        __('Martinique', 'grim')                       => 'MQ',
                        __('Mauritania', 'grim')                       => 'MR',
                        __('Mauritius', 'grim')                        => 'MU',
                        __('Mayotte', 'grim')                          => 'YT',
                        __('Mexico', 'grim')                           => 'MX',
                        __('Micronesia', 'grim')                       => 'FM',
                        __('Moldova', 'grim')                          => 'MD',
                        __('Monaco' ,'grim')                           => 'MC',
                        __('Mongolia', 'grim')                         => 'MN',
                        __('Montenegro', 'grim')                       => 'ME',
                        __('Montserrat', 'grim')                       => 'MS',
                        __('Morocco', 'grim')                          => 'MA',
                        __('Mozambique', 'grim')                       => 'MZ',
                        __('Myanmar (Burma)', 'grim')                  => 'MM',
                        __('Namibia', 'grim')                          => 'NA',
                        __('Nauru', 'grim')                            => 'NR',
                        __('Nepal', 'grim')                            => 'NP',
                        __('Netherlands', 'grim')                      => 'NL',
                        __('Netherlands Antilles', 'grim')             => 'AN',
                        __('New Caledonia', 'grim')                    => 'NC',
                        __('New Zealand', 'grim')                      => 'NZ',
                        __('Nicaragua', 'grim')                        => 'NI',
                        __('Niger', 'grim')                            => 'NE',
                        __('Nigeria', 'grim')                          => 'NG',
                        __('Niue', 'grim')                             => 'NU',
                        __('Norfolk Island', 'grim')                   => 'NF',
                        __('North Korea', 'grim')                      => 'KP',
                        __('Northern Mariana Islands', 'grim')         => 'MP',
                        __('Norway', 'grim')                           => 'NO',
                        __('Oman', 'grim')                             => 'OM',
                        __('Pakistan', 'grim')                         => 'PK',
                        __('Palau', 'grim')                            => 'PW',
                        __('Palestine', 'grim')                        => 'PS',
                        __('Panama' ,'grim')                           => 'PA',
                        __('Papua New Guinea', 'grim')                 => 'PG',
                        __('Paraguay' ,'grim')                         => 'PY',
                        __('Peru', 'grim')                             => 'PE',
                        __('Philippines', 'grim')                      => 'PH',
                        __('Pitcairn Islands', 'grim')                 => 'PN',
                        __('Poland', 'grim')                           => 'PL',
                        __('Portugal', 'grim')                         => 'PT',
                        __('Puerto Rico', 'grim')                      => 'PR',
                        __('Qatar', 'grim')                            => 'QA',
                        __('Reunion', 'grim')                          => 'RE',
                        __('Romania', 'grim')                          => 'RO',
                        __('Russia', 'grim')                           => 'RU',
                        __('Rwanda', 'grim')                           => 'RW',
                        __('Saint Helena', 'grim')                     => 'SH',
                        __('Saint Kitts and Nevis', 'grim')            => 'KN',
                        __('Saint Vincent and the Grenadines', 'grim') => 'VC',
                        __('Saint Lucia', 'grim')                      => 'LC',
                        __('Samoa', 'grim')                            => 'WS',
                        __('San Marino', 'grim')                       => 'SM',
                        __('São Tomé and Príncipe', 'grim')            => 'ST',
                        __('Saudi Arabia', 'grim')                     => 'SA',
                        __('Senegal', 'grim')                          => 'SN',
                        __('Serbia', 'grim')                           => 'RS',
                        __('Seychelles', 'grim')                       => 'SC',
                        __('Sierra Leone', 'grim')                     => 'SL',
                        __('Singapore', 'grim')                        => 'SG',
                        __('Sint Maarten', 'grim')                     => 'SX',
                        __('Slovakia', 'grim')                         => 'SK',
                        __('Slovenia', 'grim')                         => 'SI',
                        __('Solomon Islands', 'grim')                  => 'SB',
                        __('Somalia', 'grim')                          => 'SO',
                        __('South Africa', 'grim')                     => 'ZA',
                        __('South Georgia and South Sandwich Islands', 'grim') => 'GS',
                        __('South Korea', 'grim')                      => 'KR',
                        __('South Sudan', 'grim')                      => 'SS',
                        __('Spain', 'grim')                            => 'ES',
                        __('Sri Lanka', 'grim')                        => 'LK',
                        __('Sudan', 'grim')                            => 'SD',
                        __('Swaziland', 'grim')                        => 'SZ',
                        __('Sweden', 'grim')                           => 'SE',
                        __('Switzerland', 'grim')                      => 'CH',
                        __('Syria', 'grim')                            => 'SY',
                        __('São Tomé & Príncipe', 'grim')              => 'ST',
                        __('Taiwan', 'grim')                           => 'TW',
                        __('Tajikistan', 'grim')                       => 'TJ',
                        __('Tanzania', 'grim')                         => 'TZ',
                        __('Thailand', 'grim')                         => 'TH',
                        __('Timor-Leste', 'grim')                      => 'TL',
                        __('Tokelau' ,'grim')                          => 'TK',
                        __('Togo', 'grim')                             => 'TG',
                        __('Tokelau' ,'grim')                          => 'TK',
                        __('Tonga', 'grim')                            => 'TO',
                        __('Trinidad and Tobago', 'grim')              => 'TT',
                        __('Tristan da Cunha', 'grim')                 => 'TA',
                        __('Tunisia', 'grim')                          => 'TN',
                        __('Turkey', 'grim')                           => 'TR',
                        __('Turkmenistan', 'grim')                     => 'TM',
                        __('Turks and Caicos Islands', 'grim')         => 'TC',
                        __('Tuvalu', 'grim')                           => 'TV',
                        __('Uganda', 'grim')                           => 'UG',
                        __('Ukraine', 'grim')                          => 'UA',
                        __('United Arab Emirates', 'grim')             => 'AE',
                        __('United Kingdom', 'grim')                   => 'GB',
                        __('United States', 'grim')                    => 'US',
                        __('Uruguay', 'grim')                          => 'UY',
                        __('Uzbekistan', 'grim')                       => 'UZ',
                        __('Vanuatu', 'grim')                          => 'VU',
                        __('Vatican City', 'grim')                     => 'VA',
                        __('Venezuela', 'grim')                        => 'VE',
                        __('Vietnam', 'grim')                          => 'VN',
                        __('Wallis Futuna', 'grim')                    => 'WF',
                        __('Western Sahara', 'grim')                   => 'EH',
                        __('Yemen', 'grim')                            => 'YE',
                        __('Zambia' ,'grim')                           => 'ZM',
                        __('Zimbabwe', 'grim')                         => 'ZW',
                        __('Åland Islands', 'grim')                    => 'AX'
                    );

                return $this->get_api_options( $api_reg_list, 'region' );		
			}
			
			

		public function get_api_options( array $array_list, $lista ) {

                global $grim_settings;
                $option_list = '';
				$count = 0;
				
				foreach ( $array_list as $key => $value ) {  
				
					// If no option value exist, set the first one as selected.
					if ( ( $count == 0 ) && ( empty( $grim_settings['api_'. $lista] ) ) ) {
						$selected = 'selected="selected"';
					} else {
						$selected = ( $grim_settings['api_'. $lista] == $value ) ? 'selected="selected"' : '';
					}
					
					$option_list .= '<option value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $key ) . '</option>';
					$count++;
				}
												
				return $option_list;					
		}
        
    }
}        
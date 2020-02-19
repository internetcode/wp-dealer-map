<?php
/**
 * Admin class
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'GRIM_Admin' ) ) {

    /**
     * Backend of the dealer store locator
     *
     * @since 1.0
     */
	class GRIM_Admin {

        /**
         * @since 1.0
         * @var settings_page
         */
        public $settings_page;

        /**
         * Class constructor
         */
		function __construct() {

            $this->includes();

            add_action( 'init',                                 array( $this, 'init' ) );
            add_action( 'admin_menu',                           array( $this, 'create_admin_menu' ) );
            add_action( 'admin_menu',                           array( $this, 'add_submenu_pages' ) );
            add_action( 'plugins_loaded',                       array( $this, 'wp_export_loaded' ) );
            add_action( 'admin_enqueue_scripts',                array( $this, 'admin_scripts' ) );
            add_action( 'wp_ajax_csv_grim_import',              array( $this, 'csv_grim_import_callback') );
            add_filter( 'plugin_action_links_' . GRIM_BASENAME, array( $this, 'add_action_links' ), 10, 2 );
            add_filter( 'option_page_capability_grim_settings', array( $this, 'grim_capability' ) );

		}

        /**
         * Include the required files.
         *
         * @since 1.0
         * @return void
         */
        public function includes() {
            require_once( GRIM_PLUGIN_DIR . 'admin/class-metaboxes.php' );
            require_once( GRIM_PLUGIN_DIR . 'admin/class-settings.php' );
		}

        /**
         * Include valid capability filter for dealers pages
         *
         * @since 1.0
         * @return void
         */
        public function grim_capability($capability) {
            return 'edit_others_posts';
        }

        /**
         * Init the settings classes.
         *
         * @since 1.0
         * @return void
         */
		public function init() {            
            $this->settings_page = new GRIM_Settings();
		}

        /**
         * Init export/import actions
         *
         * @since 1.0
         * @return void
         */
        public function wp_export_loaded() {
            $this->handle_csv_export_action();
        }


        /**
         * Add the admin menu pages.
         *
         * @since 1.0
         * @return void
         */
		public function create_admin_menu() {

            $sub_menu = apply_filters( 'grim_sub_menu_items', array(
                        'page_title'  => __( 'Dealer Stores', 'grim' ),
                        'menu_title'  => __( 'Dealer Stores', 'grim' ),
                        'caps'        => 'manage_grim_settings',
                        'menu_slug'   => __FILE__,
                        'function'    => array( $this, 'load_page' ),
                        'icon'        =>  GRIM_URL . 'admin/img/map-pin-red.png',
                )
            );

            add_menu_page( $sub_menu['page_title'], $sub_menu['menu_title'], $sub_menu['caps'], $sub_menu['menu_slug'], $sub_menu['function'], $sub_menu['icon'], 3 );
        }
        /*
        * Adding Dealers Submenu pages
        */
        public function add_submenu_pages() {
            add_submenu_page(__FILE__, __('Add new', 'grim'), __('Add new', 'grim'), 'manage_grim_settings', 'grimdealers/locations_form', array($this, 'grim_locations_form_page_handler'));
            add_submenu_page( __FILE__, __('All Locations', 'grim'), __('Dealer Locations', 'grim'), 'manage_grim_settings', 'grimdealers/grim_locations', array($this, 'grim_locations_page_handler')
            );
        }

        /**
         * Render table dealers locations page
         * @return void
         */
        public function grim_locations_page_handler() {
                global $wpdb;
               
                $table = new Grim_List_Table();
                $table->prepare_items();
               
                $message = '';
                if ('delete' === $table->current_action()) {
                       $count = (is_array($_REQUEST['id'])) ? count($_REQUEST['id']) : '1'; 
                       $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'grim'), $count ) . '</p></div>';       
                } ?>     
                <div class="wrap">
                     <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                        <h2><?php _e('Dealer Locations', 'grim')?> 
                            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=grimdealers/locations_form');?>">
                               <?php _e('Add new', 'grim')?>
                            </a>
                        </h2>
                    <?php echo $message; ?>
                    <form id="locations-table" method="GET">
                        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                        <input type="text" name="search" value=""/>
                        <input type="submit" alt="Search" value="<?php _e('Search', 'grim') ?>" />
                        <input id="show_all" type="button" name="show_all" value="<?php _e('Reset', 'grim') ?>" />
                        <?php $table->display() ?>
                    </form>
                </div>
            <?php
        }
          
        /**
        * Input form page handler additional code
        */
        public function grim_locations_form_page_handler() {
            require_once 'add-settings.php';
        }

        /**
        * Simple function that validates data and retrieve bool on success
        * and error message(s) on error
        *
        * @param $item
        * @return bool|string
        */
        public function grim_validate_data($item) {

            $messages = array();
        
            if (empty($item['name'])) $messages[] = __('Name is required', 'grim');
            if (empty($item['address'])) $messages[] = __('Address is in wrong format', 'grim');
            if (is_int($item['lat'])) $messages[] = __('Latitude is in wrong format', 'grim');
            if (is_int($item['lng'])) $messages[] = __('Longitude is in wrong format', 'grim');
            if (!ctype_digit($item['active'])) $messages[] = __('Active only can be 1 or 0', 'grim');
            if (!ctype_digit($item['proseries'])) $messages[] = __('Pro Series only can be 1 or 0', 'grim');
        
            if (empty($messages)) 
                return true;

            return implode('<br />', $messages);
        }
        
        /**
         * Load input form for adding new dealers stores
         *
         * @since 1.0
         * @param $item 
         * @return html Input form for adding new dealers stores
         */
        public function grim_locations_form_meta_box_handler( $item ) { ?>
        
        <button class="grim-latlng button"> <?php _e('Geocode', 'grim') ?></button>
        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
            <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="name"><?php _e('Name', 'grim')?></label>
                </th>
                <td>
                    <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['name'])) ?>"
                            size="50" class="code" placeholder="<?php _e('Store name', 'grim')?>" required autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="address"><?php _e('Address', 'grim')?></label>
                </th>
                <td>
                    <input id="address" name="address" type="address" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['address'])) ?>"
                            size="50" class="code" placeholder="<?php _e('Store Address', 'grim')?>" required autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="lat"><?php _e('Latitude', 'grim')?></label>
                </th>
                <td>
                    <input id="latitude" name="lat" type="number" step="0.000001" style="width: 95%" value="<?php echo esc_attr($item['lat'])?>"
                            size="50" class="code" placeholder="<?php _e('Latitude', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="lng"><?php _e('Longitude', 'grim')?></label>
                </th>
                <td>
                    <input id="longitude" name="lng" type="number" step="0.000001" style="width: 95%" value="<?php echo esc_attr($item['lng'])?>"
                            size="50" class="code" placeholder="<?php _e('Longitude', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="active"><?php _e('Active', 'grim')?></label>
                </th>
                <td>
                    <input id="active" name="active" type="number" min="0" max="1" step="1" style="width: 95%" value="<?php echo esc_attr($item['active'])?>"
                            size="50" class="code" placeholder="<?php _e('Active 1 or 0', 'grim')?>" required autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="address2"><?php _e('Address 2', 'grim')?></label>
                </th>
                <td>
                    <input id="address2" name="address2" type="address" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['address2'])) ?>"
                            size="50" class="code" placeholder="<?php _e('Store Address2', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="city"><?php _e('City', 'grim')?></label>
                </th>
                <td>
                    <input id="city" name="city" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['city'])) ?>"
                            size="50" class="code" placeholder="<?php _e('City', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="state"><?php _e('State', 'grim')?></label>
                </th>
                <td>
                    <input id="state" name="state" type="text" style="width: 95%" value="<?php echo esc_attr($item['state'])?>"
                            size="50" class="code" placeholder="<?php _e('State', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="zip"><?php _e('Postal / Zip', 'grim')?></label>
                </th>
                <td>
                    <input id="zip" name="zip" type="text" style="width: 95%" value="<?php echo esc_attr($item['zip'])?>"
                            size="50" class="code" placeholder="<?php _e('Postal / Zip', 'grim')?>" autocomplete="off" maxlength="5">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="country"><?php _e('Country', 'grim')?></label>
                </th>
                <td>
                    <input id="country" name="country" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['country'])) ?>"
                            size="50" class="code" placeholder="<?php _e('Country', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="description"><?php _e('Description', 'grim')?></label>
                </th>
                <td>
                    <input id="description" name="description" type="text" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['description'])) ?>"
                            size="50" class="code" placeholder="<?php _e('Description', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="phone"><?php _e('Phone', 'grim')?></label>
                </th>
                <td>
                    <input id="phone" name="phone" type="text" style="width: 95%" value="<?php echo esc_attr($item['phone'])?>"
                            size="50" class="code" placeholder="<?php _e('Phone', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="fax"><?php _e('Fax', 'grim')?></label>
                </th>
                <td>
                    <input id="fax" name="fax" type="text" style="width: 95%" value="<?php echo esc_attr($item['fax'])?>"
                            size="50" class="code" placeholder="<?php _e('Fax', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="url"><?php _e('Website URL', 'grim')?></label>
                </th>
                <td>
                    <input id="url" name="url" type="url" style="width: 95%" value="<?php echo esc_attr($item['url'])?>"
                            size="50" class="code" placeholder="<?php _e('Website URL', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="email"><?php _e('Email', 'grim')?></label>
                </th>
                <td>
                    <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr(stripslashes($item['email'])) ?>"
                            size="50" class="code" placeholder="<?php _e('Email', 'grim')?>" autocomplete="off">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label class="disabled" for="thumb_id"><?php _e('Image thumbnail', 'grim')?></label>
                </th>
                <td>
                    <input id="thumb_id" name="thumb_id" type="text" style="width: 95%" value="<?php echo esc_attr($item['thumb_id'])?>"
                            size="50" class="code" placeholder="<?php _e('Image thumbnail', 'grim')?>" autocomplete="off" disabled="true">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="proseries"><?php _e('PRO', 'grim')?></label>
                </th>
                <td>
                    <input id="proseries" name="proseries" type="number" min="0" max="1" step="1" style="width: 95%" value="<?php echo esc_attr($item['proseries'])?>"
                            size="50" class="code" placeholder="<?php _e('Pro Series 1 or 0', 'grim')?>" required autocomplete="off">
                </td>
            </tr>

            </tbody>
        </table>
        <button class="grim-latlng button"> <?php _e('Geocode', 'grim') ?></button>
        <div id="grim-map" class="grim-map-admin"></div>

        <?php
        }


        /**
         * Load additional map settings page
         *
         * @since 1.0
         * @return void
         */
        public function load_page() {
            require 'map-settings.php';
        }

        /**
        * @since 1.0
        * @param $api_keys Valid Google api keys, or empty string
        * @return array Admin side variables  
        */
        private function admin_varibles($api_keys) {
            $adm_var = array( 
                'ajax_image' => plugin_dir_url( __FILE__ ) . 'images/loading.gif', 
                'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                'api_keys'   => $api_keys,
                'upl_title'  => __('Choose File', 'grim'),
                'upl_text'   => __('Choose CSV File', 'grim'),
                'upl_sure'   => __('Are you sure', 'grim'),
                'geo_alert'  => __('Geocode was not successful for the following reason', 'grim'),
                'api_alert'  => __('No Valid API keys', 'grim') );

            return $adm_var;
        }

        /**
         * Load required admin script.
         *
         * @since 1.0
         * @return void
         */
		public function admin_scripts() {
            global $grim_settings;

            $min = ( isset($grim_settings['environment']) && $grim_settings['environment'] != 'production' ) ? '' : '.min';
            $api_keys = grim_api_keys_checker();
            $screen = get_current_screen()->id;
            $url = 'https://maps.googleapis.com/maps/api/js?key=' . $api_keys;

            wp_enqueue_script( 'jquery-ui-tabs' );  // For admin panel page tabs
            wp_enqueue_script( 'jquery-ui-dialog' );  // For admin panel popup alerts

            // Deregister other plugins/themes Google Map scripts so they can't break things, only if needed
            if(isset($grim_settings['gmap_scripts']) && $grim_settings['gmap_scripts'] == true) {
                grim_deregister_other_gmaps();
            }

            if( !empty( $api_keys ) && $screen == 'dealer-stores_page_grimdealers/locations_form' ) {
                wp_enqueue_script( 'grim-gmap', $url, array(), null, true );
            }

            wp_enqueue_script( 'grim_admin_js', plugins_url( '/js/grim_admin'. $min .'.js', __FILE__ ), array('jquery'), GRIM_VERSION_NUM, true );
            wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css' );
            wp_enqueue_style( 'grim-admin-css', plugins_url( '/css/style'. $min .'.css', __FILE__ ), false );
            wp_localize_script( 'grim_admin_js', 'grim_admin_vars', $this->admin_varibles($api_keys) );
            //jQuery cookies for staying on same tab after exit from settings page
            wp_enqueue_script( 'jquery_cookie', plugins_url( '/js/jquery.cookie.js', __FILE__ ), array('jquery'), GRIM_VERSION_NUM, true );
            wp_enqueue_media();

        }

        /**
         * Handler CSV file export
         */
        public function handle_csv_export_action() {
            global $wpdb;
            $base = $wpdb->prefix . 'grim_stores';
            $is_exist = ($wpdb->get_var("SHOW TABLES LIKE '$base'") == $base) ? true : false;
            if ( (isset( $_POST[ 'export_to_csv_button' ] )) &&  $is_exist ) {
                if ( ! current_user_can( 'manage_grim_settings' ) ) {
                   wp_die( 'Error! Only site admin can perform this operation' );
                }
                $this->csv_export($base);
            }  
        }

        // Helper function for .csv file exportation
        public function csv_export( $getTable ) {
            ob_end_clean();
            global $wpdb;
            $field       = '';
            $getField    = '';

            if ( $getTable ) {
                $result      = $wpdb->get_results( "SELECT * FROM $getTable" );
                $requestedTable  = $wpdb->get_results( "SHOW COLUMNS FROM " . $getTable );

                if ( ! $requestedTable ) {
                //error occurred
                wp_die( "Can't get database columns info." );
                }

                $fieldsCount = count( $requestedTable );

                foreach ( $requestedTable as $column ) {
                $getField .= $column->Field . ',';
                }

                $sub         = substr_replace( $getField, '', -1 );
                $fields      = $sub . "\n"; // Get fields names
                $csv_file_name   = 'GRIM DEALER STORES' .'_'. date( 'mdy_His' ) . '.csv';

                // Get fields values with last comma excluded
                foreach ( $result as $row ) {
                foreach ( $row as $data ) {
                    $value   = str_replace( array( "\n", "\n\r", "\r\n", "\r" ), "\t", $data ); // Replace new line with tab
                    $value   = str_getcsv( $value, ",", "\"", "\\" ); // SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
                    $fields  .= $value[ 0 ] . ','; // Separate fields with comma
                }
                $fields  = substr_replace( $fields, '', -1 ); // Remove extra space at end of string
                $fields  .= "\n"; // Force new line if loop complete
                }

                //header("Content-type: text/x-csv");
                header( "Content-type: text/csv" );
                header( "Content-Transfer-Encoding: binary" );
                header( "Content-Disposition: attachment; filename=" . $csv_file_name );
                header( "Content-type: application/x-msdownload" );
                header( "Pragma: no-cache" );
                header( "Expires: 0" );

                echo $fields;
                exit;
            }
        }

        // Ajax call to process .csv file for column count
        public function csv_grim_import_callback() {

            // Get file upload url
            $file_upload_url = $_POST[ 'file_upload_url' ];

            // Open the .csv file and get it's contents
            if ( ( $fh = @fopen( $_POST[ 'file_upload_url' ], 'r' )) !== false ) {

            // Set variables
            $values = array();

            // Assign .csv rows to array
            while ( ( $row = fgetcsv( $fh )) !== false ) {  // Get file contents and set up row array
                //$values[] = '("' . implode('", "', $row) . '")';  // Each new line of .csv file becomes an array
                $rows[] = array( implode( '", "', $row ) );
            }

            // Get a single array from the multi-array... and process it to count the individual columns
            $first_array_elm = reset( $rows );
            $xplode_string   = explode( ", ", $first_array_elm[ 0 ] );

            // Count array entries
            $column_count = count( $xplode_string );
            } else {
            $column_count = 'There was an error extracting data from the.csv file. Please ensure the file is a proper .csv format.';
            }

            // Set response variable to be returned to jquery
            $response = json_encode( array( 'column_count' => $column_count ) );
            header( "Content-Type: application/json" );
            echo $response;
            die();
        }

        /**
         * Array remove slashes  
         */
        public function grim_deep_stripslashes($item) {

            $item = is_array($item) ?
                array_map('stripslashes_deep', $item) :
                stripslashes($item);

            return $item;
        }

        /**
         * Add link to the plugin action row.
         *
         * @since 1.0.0
         * @param  array  $links The existing action links
         * @param  string $file  The file path of the current plugin
         * @return array  $links The modified links
         */
        public function add_action_links( $links, $file ) {

            if ( strpos( $file, 'grimdealers.php' ) !== false ) {
                $settings_link = '<a href="' . admin_url( 'admin.php?page=grimdealers/admin/class-admin.php' ) . '" title="'. __( 'View Dealers Store Locator Settings', 'grim' ) .'">' 
                . __( 'Settings', 'grim' ) . '</a>';
                array_unshift( $links, $settings_link );
            }

            return $links;
        }

    }    
	$GLOBALS['grim_admin'] = new GRIM_Admin();
}
<?php
/**
 * Admin class
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Dealer_Map_Admin' ) ) {

    /**
     * Backend of the dealer store locator
     * @since 1.0.0
     */
	class Dealer_Map_Admin {

        /**
         * @since 1.0.0
         * @var settings_page
         */
        public $settings_page;

        /**
         * Class constructor
         */
		function __construct() {

            $this->includes();

            add_action( 'init',                                       array( $this, 'init' ) );
            add_action( 'admin_menu',                                 array( $this, 'create_admin_menu' ) );
            add_action( 'admin_menu',                                 array( $this, 'add_submenu_pages' ) );
            add_action( 'plugins_loaded',                             array( $this, 'wp_export_loaded' ) );
            add_action( 'admin_enqueue_scripts',                      array( $this, 'admin_scripts' ) );
            add_action( 'wp_ajax_csv_dealer_map_import',              array( $this, 'csv_dealer_map_import_callback') );
            add_filter( 'plugin_action_links_' . DEALER_BASENAME,     array( $this, 'add_action_links' ), 10, 2 );
            add_filter( 'option_page_capability_dealer_map_settings', array( $this, 'dealer_map_capability' ) );

		}

        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes() {
            require_once( DEALER_PLUGIN_DIR . 'admin/class-metaboxes.php' );
            require_once( DEALER_PLUGIN_DIR . 'admin/class-settings.php' );
		}

        /**
         * Include valid capability filter for dealers pages
         *
         * @since 1.0.0
         * @return void
         */
        public function dealer_map_capability($capability) {
            return 'edit_others_posts';
        }

        /**
         * Init the settings classes.
         *
         * @since 1.0.0
         * @return void
         */
		public function init() {            
            $this->settings_page = new Dealer_Map_Settings();
		}

        /**
         * Init export/import actions
         *
         * @since 1.0.0
         * @return void
         */
        public function wp_export_loaded() {
            $this->handle_csv_export_action();
        }


        /**
         * Add the admin menu pages.
         *
         * @since 1.0.0
         * @return void
         */
		public function create_admin_menu() {

            $sub_menu = apply_filters( 'dealer_map_sub_menu_items', array(
                        'page_title'  => __( 'Dealer Stores', 'wp-dealer-map' ),
                        'menu_title'  => __( 'Dealer Stores', 'wp-dealer-map' ),
                        'caps'        => 'manage_dealer_map_settings',
                        'menu_slug'   => __FILE__,
                        'function'    => array( $this, 'load_page' ),
                        'icon'        =>  DEALER_URL . 'admin/img/map-pin-red.png',
                )
            );

            add_menu_page( $sub_menu['page_title'], $sub_menu['menu_title'], $sub_menu['caps'], $sub_menu['menu_slug'], $sub_menu['function'], $sub_menu['icon'], 3 );
        }
        /*
        * Adding Dealers Submenu pages
        */
        public function add_submenu_pages() {
            add_submenu_page(__FILE__, __('Add new', 'wp-dealer-map'), __('Add new', 'wp-dealer-map'), 'manage_dealer_map_settings', 'dealer_map/locations_form', array($this, 'dealer_map_locations_form_page_handler')
            );
            add_submenu_page( __FILE__, __('All Locations', 'wp-dealer-map'), __('Dealer Locations', 'wp-dealer-map'), 'manage_dealer_map_settings', 'dealer_map/dealer_map_locations', array($this, 'dealer_map_locations_page_handler')
            );
        }

        /**
         * Render table dealers locations page
         * @since 1.0.0
         * @return void
         */
        public function dealer_map_locations_page_handler() {
                global $wpdb;
               
                $table = new Dealer_Map_List_Table();
                $table->prepare_items();

                // verify the nonce field generated near the bulk actions menu
                if( !isset($_REQUEST['_wpnonce']) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $table->_args['plural'] ) ) 
                    wp_die('Busted!');

                $message = '';
                if ('delete' === $table->current_action()) {
                       $count = (is_array($_REQUEST['id'])) ? count($_REQUEST['id']) : '1'; 
                       $message = '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible updated below-h2"><p>' . sprintf(__('Items deleted: %d', 'wp-dealer-map'), $count ) . '</p>
                       <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'wp-dealer-map') .'</span>
                       </button></div>';       
                } ?>     
                <div class="wrap">
                     <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                        <h2><?php _e('Dealer Locations', 'wp-dealer-map')?> 
                            <a class="add-new-h2" href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=dealer_map/locations_form')); ?>">
                               <?php esc_html_e('Add new', 'wp-dealer-map')?>
                            </a>
                        </h2>
                    <?php echo $message; ?>
                    <form id="locations-table" method="GET">
                        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>"/>
                        <input type="text" name="search" value=""/>
                        <input type="submit" alt="Search" value="<?php esc_attr_e('Search', 'wp-dealer-map') ?>" />
                        <input id="show_all" type="button" name="show_all" value="<?php esc_attr_e('Reset', 'wp-dealer-map') ?>" />
                        <?php $table->display(); ?>
                    </form>
                </div>
            <?php
        }
          
        /**
        * Input form page handler additional code
        * @since 1.0.0
        */
        public function dealer_map_locations_form_page_handler() {
            require_once 'locations/locations-settings.php';
        }

        /**
         * Validates if $zipCode is a 5 digit number in the 12345 format.
         * 
         * @since 1.0.0 (description)
         * @param $zipCode
         * @return bool true or false
         */
        private function check_zip_code($zipCode) {
            return (preg_match('#[0-9]{5}#', $zipCode)) ? true : false;
        }

        /**
        * Sanitize / validate inputed items function that validates items 
        *
        * @param $item
        * @since 1.0.0
        * @return bool|strings
        */
        private function sanitize_items($items) {
            $array_items = array();

            $array_items['id'] = (!empty($items['id']) && $items['id'] != '0') ? sanitize_text_field($items['id']) : '0';
            $array_items['name'] = (!empty($items['name'])) ? sanitize_text_field( $items['name'] ) : '';
            $array_items['address'] = (!empty($items['address'])) ? sanitize_text_field( $items['address'] ) : '';
            $array_items['lat'] = (!empty($items['lat'])) ? sanitize_text_field( $items['lat'] ) : '';
            $array_items['lng'] = (!empty($items['lng'])) ? sanitize_text_field( $items['lng'] ) : '';
            $array_items['active'] = (!empty($items['active']) && $items['active'] == 1) ? '1' : '0';
            $array_items['address2'] = (!empty($items['address2'])) ? sanitize_text_field( $items['address2'] ) : '';
            $array_items['city'] = (!empty($items['city'])) ? sanitize_text_field( $items['city'] ) : '';
            $array_items['state'] = (!empty($items['state'])) ? sanitize_text_field( $items['state'] ) : '';
            $array_items['zip'] = (!empty($items['zip']) && $this->check_zip_code($items['zip'])) ? sanitize_text_field( $items['zip'] ) : '';
            $array_items['country'] = (!empty($items['country'])) ? sanitize_text_field( $items['country'] ) : '';
            $array_items['description'] = (!empty($items['description'])) ? sanitize_text_field( $items['description'] ) : '';
            $array_items['phone'] = (!empty($items['phone'])) ? sanitize_text_field( $items['phone'] ) : '';
            $array_items['fax'] = (!empty($items['fax'])) ? sanitize_text_field( $items['fax'] ) : '';
            $array_items['url'] = (!empty($items['url'])) ? esc_url_raw( $items['url'] ) : '';
            $array_items['email'] = (!empty($items['email']) && is_email( $items['email'] )) ? sanitize_text_field( $items['email'] ) : '';
            $array_items['thumb_id'] = (!empty($items['thumb_id'])) ? sanitize_text_field( $items['thumb_id'] ) : '';
            $array_items['proseries'] = (!empty($items['proseries']) && $items['proseries'] == 1) ? '1' : '0';

            return $array_items;

        }

        /**
        * Simple function that validates data and retrieve bool on success
        * and error message(s) on error
        *
        * @param $item
        * @since 1.0.0
        * @return bool|string
        */
        public function dealer_map_validate_data($item) {
            $messages = array();

            if (empty($item['name'])) $messages[] = __('Name is required', 'wp-dealer-map');
            if (empty($item['address'])) $messages[] = __('Address is in wrong format', 'wp-dealer-map');
            if (is_int($item['lat'])) $messages[] = __('Latitude is in wrong format', 'wp-dealer-map');
            if (is_int($item['lng'])) $messages[] = __('Longitude is in wrong format', 'wp-dealer-map');
            if (!ctype_digit($item['active'])) $messages[] = __('Active only can be 1 or 0', 'wp-dealer-map');
            if (!ctype_digit($item['proseries'])) $messages[] = __('Pro Series only can be 1 or 0', 'wp-dealer-map');
        
            if (empty($messages)) 
                return true;

            return implode('<br />', $messages);
        }
        
        /**
         * Load input form for adding new dealers stores
         *
         * @since 1.0.0
         * @param $item 
         * @return html Input form for adding new dealers stores
         */
        public function dealer_map_locations_form_meta_box_handler( $item ) { 
            include 'locations/locations-form.php';
        }


        /**
         * Load additional map settings page
         *
         * @since 1.0.0
         * @return void
         */
        public function load_page() {
            require 'map-settings.php';
        }

        /**
        * @since 1.0.0
        * @param $api_keys Valid Google api keys, or empty string
        * @return array Admin side variables  
        */
        private function admin_varibles($api_keys) {
            $adm_var = array( 
                'ajax_image' => plugin_dir_url( __FILE__ ) . 'images/loading.gif', 
                'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                'api_keys'   => $api_keys,
                'upl_title'  => __('Choose File', 'wp-dealer-map'),
                'upl_text'   => __('Choose CSV File', 'wp-dealer-map'),
                'upl_sure'   => __('Are you sure', 'wp-dealer-map'),
                'geo_alert'  => __('Geocode was not successful for the following reason', 'wp-dealer-map'),
                'api_alert'  => __('No Valid API keys', 'wp-dealer-map') );

            return $adm_var;
        }

        /**
         * Load required admin script.
         *
         * @since 1.0.0
         * @return void
         */
		public function admin_scripts($hook) {
            global $dealer_map_settings, $pagenow;

            // Deregister other plugins/themes Google Map scripts so they can't break things, only if needed
            if(isset($dealer_map_settings['gmap_scripts']) && $dealer_map_settings['gmap_scripts'] == true) {
                dealer_map_deregister_other_gmaps();
            }

            if( 'admin.php' != $pagenow ) {
                return;
            }

            $min = ( isset($dealer_map_settings['environment']) && $dealer_map_settings['environment'] != 'production' ) ? '' : '.min';
            $api_keys = dealer_map_api_keys_checker();
            $screen = get_current_screen()->base;
            $url = 'https://maps.googleapis.com/maps/api/js?key=' . $api_keys;

            if( $hook == $screen ) {
                wp_enqueue_script( 'jquery-ui-tabs' );  // For admin panel page tabs
                wp_enqueue_script( 'jquery-ui-dialog' );  // For admin panel popup alerts
                wp_enqueue_style( 'jquery-style', DEALER_URL . 'admin/css/jquery-ui.css' ); //jquery-ui.css locally
                //enqueue admin scripts
                wp_enqueue_script( 'dealer_map_admin_js', DEALER_URL . 'admin/js/dealer_map_admin'. $min .'.js', array('jquery'), DEALER_VERSION_NUM, true );
                wp_localize_script( 'dealer_map_admin_js', 'dealer_map_admin_vars', $this->admin_varibles($api_keys) );
                //jQuery cookies for staying on same tab after exit from settings page
                wp_enqueue_script( 'jquery_cookie', DEALER_URL . 'admin/js/jquery.cookie.js', array('jquery'), DEALER_VERSION_NUM, true );
                if ( !did_action( 'wp_enqueue_media' ) ) {
                    wp_enqueue_media();
                }
                //admin plugin css
                wp_enqueue_style( 'dealer_map-admin-css', DEALER_URL .'admin/css/style'. $min .'.css', array(), DEALER_VERSION_NUM, 'all' );
            }

            if( !empty( $api_keys ) && $screen == 'dealer-stores_page_dealer_map/locations_form' ) {
                wp_enqueue_script( 'dealer_map_gmap', $url, array(), null, true );
            }

        }

        /**
         * Handler CSV file export
         * @since 1.0.0
         */
        public function handle_csv_export_action() {
            global $wpdb;
            $base = $wpdb->prefix . 'dealer_map_stores';
            $is_exist = ($wpdb->get_var("SHOW TABLES LIKE '$base'") == $base) ? true : false;
            if ( (isset( $_POST[ 'export_to_csv_button' ] )) &&  $is_exist ) {
                if ( ! current_user_can( 'manage_dealer_map_settings' ) ) {
                   wp_die( 'Error! Only site admin can perform this operation' );
                }
                $this->csv_export($base);
            }  
        }

        /**
         * Helper function for .csv file exportation
         * @since 1.0.0 (description)
         */
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
                $csv_file_name   = 'DEALER DEALER STORES' .'_'. date( 'mdy_His' ) . '.csv';

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

        /**
         * Ajax call to process .csv file for column count
         *
         * @since 1.0.0 (description)
         * @return json response
         */
        public function csv_dealer_map_import_callback() {

            // Get file upload url
            $file_upload_url = sanitize_url( $_POST[ 'file_upload_url' ] );

            // Open the .csv file and get it's contents
            if ( ( $fh = @fopen( $file_upload_url, 'r' )) !== false ) {

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
         * @since 1.0.0 (description)  
         */
        public function dealer_map_deep_stripslashes($item) {

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

            if ( strpos( $file, 'wp_dealer_map.php' ) !== false ) {
                $settings_link = '<a href="' . admin_url( 'admin.php?page=dealer_map/admin/class-admin.php' ) . '" title="'. esc_attr__( 'View Dealers Store Locator Settings', 'wp-dealer-map' ) .'">' 
                . esc_html__( 'Settings', 'wp-dealer-map' ) . '</a>';
                array_unshift( $links, $settings_link );
            }

            return $links;
        }

    }    
	$GLOBALS['dealer_map_admin'] = new Dealer_Map_Admin();
}
<?php
/*
Plugin Name: WP Dealer Map
Description: WP plugin for showing nearby dealer stores around typed zip, city or full address. It can be showed on maps within chosen radius. 
Plugin URI: https://www.assist4web.com/wp-dealer-map 
Author: Assist4web
Author URI: https://www.assist4web.com/
Version: 1.0.0
Text Domain: wp-dealer-map
Domain Path: /languages/
License: GPL v2
*/
/*  Copyright 2020  Assist4web team (email : info@assist4web.com)

WP Dealer Map is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
WP Dealer Map is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with WP Dealer Map. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/

if ( !class_exists( 'Dealer_Map' ) ) {

	class Dealer_Map {
        
        /**
         * Class constructor
         */          
        function __construct() {
            $plugin_name = 'WP Dealer Map';
            global $network_wide;

            $this->define_constants();
            $this->includes();
            $this->dealer_map_create_db();
            $this->plugin_settings();

            //Load translations
            add_action('plugins_loaded', array($this, 'dealer_map_load_textdomain'));
          
            // Load classes
            $this->front   = new Dealer_Public($plugin_name, DEALER_VERSION_NUM );
            $this->shortcode   = new Dealer_Frontend_Shortcode();

            register_activation_hook( __FILE__, array( $this, 'activate_plug' ) );

        }
        
        /**
         * Setup plugin constants.
         *
         * @since 1.0.0
         * @return void
         */
        public function define_constants() {

            if ( !defined( 'DEALER_VERSION_NUM' ) )
                define( 'DEALER_VERSION_NUM', '1.0.0' );

            if ( !defined( 'DEALER_URL' ) )
                define( 'DEALER_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'DEALER_BASENAME' ) )
                define( 'DEALER_BASENAME', plugin_basename( __FILE__ ) );

            if ( !defined( 'DEALER_PLUGIN_DIR' ) )
                define( 'DEALER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes() {

            require_once( DEALER_PLUGIN_DIR . 'includes/dealer-map-functions.php' );

            if ( is_admin() || defined( 'WP_CLI' ) && WP_CLI ) {
               require_once( DEALER_PLUGIN_DIR . 'admin/roles.php' ); 
               require_once( DEALER_PLUGIN_DIR . 'admin/class-admin.php' );
            }
            require_once( DEALER_PLUGIN_DIR . 'front/front-class.php' );
            require_once( DEALER_PLUGIN_DIR . 'front/shortcode.php' );
            require_once( DEALER_PLUGIN_DIR . 'includes/onactivated.php' );
        }
        
        /**
         * Setup the plugin settings.
         *
         * @since 1.0.0
         * @return void
         */
        public function plugin_settings() {
            global $dealer_map_settings;
            $dealer_map_settings = dealer_map_get_settings();
        }

        /**
         * Create new dealer_map store database
         * 
         * @since 1.0.0
         * @return void
         */
        function dealer_map_create_db() {

           global $wpdb;
           $version = get_option( 'dealer_db_version', DEALER_VERSION_NUM );
           $charset_collate = $wpdb->get_charset_collate();
           $table_name = $wpdb->prefix . 'dealer_map_stores';
       
           $sql = "CREATE TABLE $table_name (
               id int(11) NOT NULL AUTO_INCREMENT,
               name VARCHAR( 60 ) NOT NULL,
               address VARCHAR( 80 ) NOT NULL,
               lat FLOAT(10, 6) NOT NULL,
               lng FLOAT(10, 6) NOT NULL,
               address2 varchar(255) NULL,
               city varchar(255) NULL,
               state varchar(255) NULL,
               zip varchar(100) NULL,
               country varchar(255) NULL,
               description text NULL,
               phone varchar(100) NULL,
               fax varchar(100) NULL,
               url varchar(255) NULL,
               email varchar(255) NULL,
               thumb_id bigint(20) unsigned NOT NULL,
               active tinyint(1) NULL default 1,
               proseries tinyint(1) NULL default 0,
               PRIMARY KEY  (id)
               ) $charset_collate;";

           require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
           dbDelta( $sql );

           add_option( 'dealer_db_version', $version );
  
        }

        /**
         * Load plugin strings for translations
         * @since 1.0.0 (description) 
         */
        public function dealer_map_load_textdomain() {
          //load translate strings
          load_plugin_textdomain( 'wp-dealer-map', false, dirname( DEALER_BASENAME ) . '/languages/' ); 
        }
        
        /**
         * Install the plugin data.
         *
         * @since 1.0.0
         * @return void
         */
        public function activate_plug( $network_wide ) {
          
          //add default options
          add_option('dealer_map_remove_all', 'remove');
          add_option('dealer_map_keep_table', 'remove');

          dealer_map_activate( $network_wide );
        }

	}
	
	$GLOBALS['wp-dealer-map'] = new Dealer_Map();
}

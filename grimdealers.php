<?php
/*
Plugin Name: WP Dealer Map
Description: WP plugin for showing nearby dealer stores around typed zip, city, or address. It can be showed on maps within chosen radius. 
Plugin URI: https://www.assist4web.com/dealer-store-locator 
Author: Assist4web
Author URI: https://www.assist4web.com/
Version: 1.0
Text Domain: grim
Domain Path: /languages/
License: GPL v3
*/
/*  Copyright 2020  Assist4web team  (email : info@assist4web.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301 USA
*/

if ( !class_exists( 'Grim_locator' ) ) {

	class Grim_locator {
        
        /**
         * Class constructor
         */          
        function __construct() {
            $plugin_name = 'WP Dealer Map';

            $this->define_constants();
            $this->includes();
            $this->grim_create_db();
            $this->plugin_settings();

            //Load translations
            add_action('plugins_loaded', array($this, 'grim_load_textdomain'));
          
            // Load classes
            $this->front   = new GRIM_Public($plugin_name, GRIM_VERSION_NUM );
            $this->shortcode   = new GRIM_Frontend_Shortcode();           
            register_activation_hook( __FILE__, array( $this, 'activate_plug' ) );
            register_uninstall_hook( __FILE__, array( $this, 'deactivate_plug' ) );
        }
        
        /**
         * Setup plugin constants.
         *
         * @since 1.0.0
         * @return void
         */
        public function define_constants() {

            if ( !defined( 'GRIM_VERSION_NUM' ) )
                define( 'GRIM_VERSION_NUM', '1.0' );

            if ( !defined( 'GRIM_URL' ) )
                define( 'GRIM_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'GRIM_BASENAME' ) )
                define( 'GRIM_BASENAME', plugin_basename( __FILE__ ) );

            if ( !defined( 'GRIM_PLUGIN_DIR' ) )
                define( 'GRIM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes() {

            require_once( GRIM_PLUGIN_DIR . 'includes/grim-functions.php' );

            if ( is_admin() || defined( 'WP_CLI' ) && WP_CLI ) {
               require_once( GRIM_PLUGIN_DIR . 'admin/roles.php' ); 
               require_once( GRIM_PLUGIN_DIR . 'admin/class-admin.php' );
            }
            require_once( GRIM_PLUGIN_DIR . 'front/front-class.php' );
            require_once( GRIM_PLUGIN_DIR . 'front/shortcode.php' );
        }
        
        /**
         * Setup the plugin settings.
         *
         * @since 1.0.0
         * @return void
         */
        public function plugin_settings() {
            global $grim_settings;
            $grim_settings = grim_get_settings();
        }

        /**
         * Create new grim store database
         * 
         * @since 1.0.0
         * @return void
         */
        function grim_create_db() {

           global $wpdb;
           $version = get_option( 'grim_version', '1.0' );
           $charset_collate = $wpdb->get_charset_collate();
           $table_name = $wpdb->prefix . 'grim_stores';
       
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
    
          if ( version_compare( $version, '2.0' ) < 0 ) {
               $sql = "CREATE TABLE $table_name (
               id int(11) unsigned NOT NULL AUTO_INCREMENT,
               name VARCHAR( 60 ) NOT NULL,
               address VARCHAR( 80 ) NOT NULL,
               lat FLOAT(10, 6) NOT NULL,
               lng FLOAT(10, 6) NOT NULL,
               address varchar(255) NULL,
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
                dbDelta( $sql );
    
            update_option( 'grim_version', '2.0' );
           }   
        }

        /**
         * Load plugin strings for translations
         * @since 1.0 (description) 
         */
        public function grim_load_textdomain() {
          //load translate strings
          load_plugin_textdomain( 'grim', false, dirname( GRIM_BASENAME ) . '/languages/' ); 
        }
        
        /**
         * Install the plugin data.
         *
         * @since 1.0
         * @return void
         */
        public function activate_plug( $network_wide ) {
          require_once( GRIM_PLUGIN_DIR . 'includes/onactivated.php' );
          grim_activate( $network_wide );
        }

        /**
         * Remove plugin data.
         *
         * @since 1.0
         * @return void
         */
        public function deactivate_plug( $network_wide ) {
          require_once( GRIM_PLUGIN_DIR . 'includes/onactivated.php' );
          grim_uninstall( $network_wide );
        }
	}
	
	$GLOBALS['grim'] = new Grim_locator();
}

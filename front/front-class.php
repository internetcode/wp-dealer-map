<?php

/**
 * The public-facing functionality of the plugin.
 */
class Grim_Public {

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @param string    $plugin_name       The name of the plugin.
	 * @param string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts' ) );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function enqueue_styles() {
	    $this->enqueue_map_styles();
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		global $grim_settings;
        
        $min = ( isset($grim_settings['environment']) && $grim_settings['environment'] != 'production' ) ? '' : '.min';
        $api = (isset($grim_settings['api_server_key']) && grim_api_status() === 'OK') ? $grim_settings['api_server_key'] : '';
        if ( $api != '') {
        	$api_lang = ( isset($grim_settings['api_language']) && !empty($grim_settings['api_language']) ) ? $grim_settings['api_language'] : 'us';
        	$api_reg =  ( isset($grim_settings['api_region']) && !empty($grim_settings['api_region']) ) ? $grim_settings['api_region'] : 'US';
            $lang = '&amp;language=' . $api_lang;
            $reg = '&amp;region=' . $api_reg;
               
        	wp_register_script('grim_map',  '//maps.googleapis.com/maps/api/js?key='. $api . $lang . $reg, array(), null, true );
        	wp_register_script( 'grim_handler_script', GRIM_URL . 'front/js/grim-handler'. $min .'.js', array( 'jquery' ), array('jQuery'), $this->version, true );
        }
    }

    /**
	 * Register the styling css file for the public-facing side of the site.
	 *
	 * @since 1.0
	 */
	public function enqueue_map_styles() {
		if ( null !== get_option('grim_settings') ) {
			wp_register_style('grim_map_css',  GRIM_URL . 'front/css/grim-map.css', null, $this->version, 'all' );
		}	
	}

}

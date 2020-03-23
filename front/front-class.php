<?php

/**
 * The public-facing functionality of the plugin.
 */
class Dealer_Public {

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
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
	    $this->enqueue_map_styles();
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		global $dealer_map_settings;
        
        $min = ( isset($dealer_map_settings['environment']) && $dealer_map_settings['environment'] != 'production' ) ? '' : '.min';
        $api = (isset($dealer_map_settings['api_server_key']) && dealer_map_api_status() === 'OK') ? $dealer_map_settings['api_server_key'] : '';
        if ( $api != '') {
        	$api_lang = ( isset($dealer_map_settings['api_language']) && !empty($dealer_map_settings['api_language']) ) ? $dealer_map_settings['api_language'] : 'us';
        	$api_reg =  ( isset($dealer_map_settings['api_region']) && !empty($dealer_map_settings['api_region']) ) ? $dealer_map_settings['api_region'] : 'US';
            $lang = '&amp;language=' . $api_lang;
            $reg = '&amp;region=' . $api_reg;
               
        	wp_register_script('dealer_map_api',  '//maps.googleapis.com/maps/api/js?key='. $api . $lang . $reg, array(), null, true );
        	wp_register_script( 'dealer_map_handler_script', DEALER_URL . 'front/js/dealer-map-handler'. $min .'.js', array( 'jquery' ), array('jQuery'), $this->version, true );
        }
    }

    /**
	 * Register the styling css file for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_map_styles() {
		if ( null !== get_option('dealer_map_settings') ) {
			$min = ( isset($dealer_map_settings['environment']) && $dealer_map_settings['environment'] != 'production' ) ? '' : '.min';
			wp_register_style('dealer_map_css',  DEALER_URL . 'front/css/dealer-map'. $min .'.css', null, $this->version, 'all' );
		}	
	}

}

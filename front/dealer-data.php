<?php
//Properties
$colons = '';
$dealer_map_nonce = wp_create_nonce('dealer_map_nonce');
$sitename = get_bloginfo('name');
$instance = uniqid('wp-dealer-map');
$current_user = wp_get_current_user();
$ajaxurl =  admin_url( 'admin-ajax.php' );
$showdef = isset($dealer_map_settings['show_def']) ? $dealer_map_settings['show_def'] : false;
$start_lat = isset($dealer_map_settings['def_lat']) ? $dealer_map_settings['def_lat'] : 40.730610;
$start_lng = isset($dealer_map_settings['def_lng']) ? $dealer_map_settings['def_lng'] : -73.935242;
$defaddress = isset($dealer_map_settings['defaddress']) ? $dealer_map_settings['defaddress'] : '10001';
$map_type = isset($dealer_map_settings['api_maptype']) ? $dealer_map_settings['api_maptype'] : 'roadmap';
$colnum = $this->column_css( false, $colons );
$units = (dealer_map_get_distance_unit() == 'km') ? __('Kilometers', 'wp-dealer-map') : __('Miles', 'wp-dealer-map');

//All acceptable Shortcode parameters
$atts = shortcode_atts( array(
                'default_start'       => '',
                'start_address'       => '',
                'result_columns'      => '',
                'maptype'             => '',
        ), $atts );

$this->check_dealer_map_shortcode_atts( $atts );
//var_dump($this->short_atts());

//Check and render all shortcode tag if any added to shortcode
if($this->short_atts !== null ) {
    if(isset($this->short_atts['att']['showdef']) && $this->short_atts['att']['showdef'] != '') {
       $showdef = $this->short_atts['att']['showdef'];
    }
    if(isset($this->short_atts['att']['coordinates']) && $this->short_atts['att']['coordinates'] != '') {
       $start_lat = $this->short_atts['att']['coordinates']['lat'];
       $start_lng = $this->short_atts['att']['coordinates']['lng'];
       $defaddress = $atts['start_address'];
    }
    if(isset($this->short_atts['att']['maptype']) && $this->short_atts['att']['maptype'] != '') {
       $map_type = $this->short_atts['att']['maptype'];  
    }
    if(isset($this->short_atts['att']['colnum']) && $this->short_atts['att']['colnum'] != '') {
       $colons = $this->short_atts['att']['colnum'];
       $colnum = $this->column_css( true, $colons);
    } 
}
    
    $parameters = array(
        'mainIcon'              => DEALER_URL . 'front/markers/pin-red.png',
        'proIcon'               => DEALER_URL . 'front/markers/map-pro.png',
        'proSerie'              => DEALER_URL . 'front/markers/pro-serie.png',
        'youIcon'               => DEALER_URL . 'front/markers/pin-black.png',
        'myshadow'              => DEALER_URL . 'front/markers/shad.png',
        'notmain'               => DEALER_URL . 'front/markers/pin-lightblue.png',
        'start_lat'             => $start_lat,
        'start_lng'             => $start_lng,
        'units'                 => $units,
        'showdef'               => $showdef,
        'defadress'             => $defaddress,
        'defrange'              => isset($dealer_map_settings['def_range']) ? $dealer_map_settings['def_range'] : '25',
        'deflimit'              => isset($dealer_map_settings['def_limit']) ? $dealer_map_settings['def_limit'] : '10',
        'map_type'              => $map_type,
        'map_zoom'              => isset($dealer_map_settings['api_zoom']) ? $dealer_map_settings['api_zoom'] : 4,
        'marker_event'          => isset($dealer_map_settings['marker_event']) ? $dealer_map_settings['marker_event'] : 'click',
        'drop_b'                => isset($dealer_map_settings['marker_effect']) ? $dealer_map_settings['marker_effect'] : 'drop',
        'get_direct'            => __('Get Directions', 'wp-dealer-map'),
        'nostores'              => __('No Stores Found', 'wp-dealer-map'),
        'yourlocation'          => __('You Are Here', 'wp-dealer-map'),
        'aj_error'              => __('There was an error, try again in a couple minutes.', 'wp-dealer-map'),
        'error_nos'             => __('No Stores Found! Please try different search terms.', 'wp-dealer-map'),
        'error_nod'             => __('No Stores Found! Please choose different DEFAULT stores settings.', 'wp-dealer-map'),
        'address_err'           => __('Error finding your address please review your criteria and try again', 'wp-dealer-map'),
        'error_onpage'          => __('Error, something went wrong please reload page.', 'wp-dealer-map'),
        'found'                 => __('Dealers found', 'wp-dealer-map'),
        'from'                  => __('from', 'wp-dealer-map'),
        'colnum'                => $colnum,
        'ajaxurl'               => isset($ajaxurl) ? $ajaxurl : '',
        'instance'		        => isset($instance) ? $instance : '',
        'dealer_mapnonce'		=> isset($dealer_map_nonce) ? $dealer_map_nonce : ''
    );
   $parameters = apply_filters( 'dealer_map_filter_params', $parameters);

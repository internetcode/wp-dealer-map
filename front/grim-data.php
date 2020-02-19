<?php
//Properties
$colons = '';
$grim_nonce = wp_create_nonce('grim-nonce');
$sitename = get_bloginfo('name');
$instance = uniqid('grim');
$current_user = wp_get_current_user();
$ajaxurl =  admin_url( 'admin-ajax.php' );
$showdef = isset($grim_settings['show_def']) ? $grim_settings['show_def'] : false;
$start_lat = isset($grim_settings['def_lat']) ? $grim_settings['def_lat'] : 40.730610;
$start_lng = isset($grim_settings['def_lng']) ? $grim_settings['def_lng'] : -73.935242;
$defaddress = isset($grim_settings['defaddress']) ? $grim_settings['defaddress'] : '10001';
$map_type = isset($grim_settings['api_maptype']) ? $grim_settings['api_maptype'] : 'roadmap';
$colnum = $this->column_css( false, $colons );
$units = (grim_get_distance_unit() == 'km') ? __('Kilometers', 'grim') : __('Miles', 'grim');

//All acceptable Shortcode parameters
$atts = shortcode_atts( array(
                'default_start'       => '',
                'start_address'       => '',
                'result_columns'      => '',
                'maptype'             => '',
        ), $atts );

$this->check_grim_shortcode_atts( $atts );
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
        'mainIcon'              => GRIM_URL . 'front/markers/pin-red.png',
        'proIcon'               => GRIM_URL . 'front/markers/map-pro.png',
        'proSerie'              => GRIM_URL . 'front/markers/pro-serie.png',
        'youIcon'               => GRIM_URL . 'front/markers/pin-black.png',
        'myshadow'              => GRIM_URL . 'front/markers/shad.png',
        'notmain'               => GRIM_URL . 'front/markers/pin-lightblue.png',
        'start_lat'             => $start_lat,
        'start_lng'             => $start_lng,
        'units'                 => $units,
        'showdef'               => $showdef,
        'defadress'             => $defaddress,
        'defrange'              => isset($grim_settings['def_range']) ? $grim_settings['def_range'] : '25',
        'deflimit'              => isset($grim_settings['def_limit']) ? $grim_settings['def_limit'] : '10',
        'map_type'              => $map_type,
        'map_zoom'              => isset($grim_settings['api_zoom']) ? $grim_settings['api_zoom'] : 4,
        'marker_event'          => isset($grim_settings['marker_event']) ? $grim_settings['marker_event'] : 'click',
        'drop_b'                => isset($grim_settings['marker_effect']) ? $grim_settings['marker_effect'] : 'drop',
        'get_direct'            => __('Get Directions', 'grim'),
        'nostores'              => __('No Stores Found', 'grim'),
        'yourlocation'          => __('You Are Here', 'grim'),
        'aj_error'              => __('There was an error, try again in a couple minutes.', 'grim'),
        'error_nos'             => __('No Stores Found! Please try different search terms.', 'grim'),
        'error_nod'             => __('No Stores Found! Please choose different DEFAULT stores settings.', 'grim'),
        'address_err'           => __('Error finding your address please review your criteria and try again', 'grim'),
        'error_onpage'          => __('Error, something went wrong please reload page.', 'grim'),
        'found'                 => __('Dealers found', 'grim'),
        'from'                  => __('from', 'grim'),
        'colnum'                => $colnum,
        'ajaxurl'               => isset($ajaxurl) ? $ajaxurl : '',
        'instance'		        => isset($instance) ? $instance : '',
        'grimnonce'		        => isset($grim_nonce) ? $grim_nonce : ''
    );
   $parameters = apply_filters( 'grim_filter_params', $parameters);

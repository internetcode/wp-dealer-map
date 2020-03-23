<?php
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb, $dealer_map_admin, $dealer_map_settings; 

    $dealer_map_api_server = (isset($dealer_map_settings['api_server_key'])) ? $dealer_map_settings['api_server_key'] : '';
    $dealer_map_results_drop = (isset($dealer_map_settings['results_dropdown'])) ? $dealer_map_settings['results_dropdown'] : true;
    $dealer_map_show_default = (isset($dealer_map_settings['show_def'])) ? $dealer_map_settings['show_def'] : false;
    $dealer_map_radius_drop = (isset($dealer_map_settings['radius_dropdown'])) ? $dealer_map_settings['radius_dropdown'] : true;
    $dealer_map_distance_unit = (isset($dealer_map_settings['distance_unit'])) ? $dealer_map_settings['distance_unit'] : 'mi';
    $dealer_map_max_results = (isset($dealer_map_settings['max_results'])) ? $dealer_map_settings['max_results'] : '10';
    $dealer_map_max_radius = (isset($dealer_map_settings['search_radius'])) ? $dealer_map_settings['search_radius'] : '20';
    $dealer_map_def_lat = (isset($dealer_map_settings['def_lat'])) ? $dealer_map_settings['def_lat'] : '40.730610';
    $dealer_map_def_lng = (isset($dealer_map_settings['def_lng'])) ? $dealer_map_settings['def_lng'] : '-73.935242';
    $dealer_map_def_range = (isset($dealer_map_settings['def_range'])) ? $dealer_map_settings['def_range'] : '25';
    $dealer_map_def_limit = (isset($dealer_map_settings['def_limit'])) ? $dealer_map_settings['def_limit'] : '10';
    $dealer_map_d_address = (isset($dealer_map_settings['defaddress'])) ? $dealer_map_settings['defaddress'] : '10001';
    $dealer_map_marker_event = (isset($dealer_map_settings['marker_event'])) ? $dealer_map_settings['marker_event'] : 'click';
    $dealer_map_marker_effect = (isset($dealer_map_settings['marker_effect'])) ? $dealer_map_settings['marker_effect'] : 'drop';
    $dealer_map_map_height = (isset($dealer_map_settings['map_height'])) ? $dealer_map_settings['map_height'] : '450';
    $dealer_map_num_columns = (isset($dealer_map_settings['num_of_columns'])) ? $dealer_map_settings['num_of_columns'] : '4';
    $dealer_map_button_class = (isset($dealer_map_settings['button_css'])) ? $dealer_map_settings['button_css'] : '--'; 
    $dealer_map_delete_all = (isset($dealer_map_settings['remove_all'])) ? $dealer_map_settings['remove_all'] : 'remove';
    $dealer_map_keep_table = (isset($dealer_map_settings['keep_table'])) ? $dealer_map_settings['keep_table'] : 'remove';
    $dealer_map_environment = (isset($dealer_map_settings['environment'])) ? $dealer_map_settings['environment'] : 'production'; 
    $dealer_map_oth_script = (isset($dealer_map_settings['gmap_scripts'])) ? $dealer_map_settings['gmap_scripts'] : false; ?>

<div id="dealer_map-wrap" class="wrap dealer_map-settings">
	<h2><?php esc_html_e( 'Dealer Stores Settings', 'wp-dealer-map' ); ?></h2>

    <?php
    settings_errors(); ?>

    <div id="general">
        <ul>
            <li><a href="#tabs-1"><?php esc_html_e( 'Google Maps API Settings', 'wp-dealer-map' ); ?></a></li>
            <li><a href="#tabs-2"><?php esc_html_e( 'Search Settings', 'wp-dealer-map' ); ?></a></li>
            <li><a href="#tabs-3"><?php esc_html_e( 'Layout Settings', 'wp-dealer-map' ); ?></a></li>
            <li><a href="#tabs-4"><?php esc_html_e( 'Additional', 'wp-dealer-map' ); ?></a></li>
            <li><a href="#tabs-5"><?php esc_html_e( 'Export/Import', 'wp-dealer-map' ); ?></a></li>
        </ul>

    <div id="tabs-1">
        <form id="dealer_map-settings-form" method="post" action="options.php" autocomplete="off" accept-charset="utf-8">
            <div class="metabox-holder">
                <div id="dealer_map-api-settings" class="postbox">
                    <h3 class="hndle"><span><?php esc_html_e( 'Google Maps API', 'wp-dealer-map' ); ?></span></h3>
                    <div class="inside">
                        <p>
                            <label for="dealer_map-api-server-key"><?php esc_html_e( 'Google Map API key', 'wp-dealer-map' ); ?>:</label> 
                            <input type="text" value="<?php echo esc_attr( $dealer_map_api_server ); ?>" name="dealer_map_api[server_key]"  class="textinput" size="40" id="dealer_map-api-server-key">
                        </p>
                        <p>
                            <label><?php esc_html_e( 'API Status:', 'wp-dealer-map' ); ?></label>
                            <span class="api-stat"><?php echo esc_html(dealer_map_api_status()); ?></span> 
                        </p>
                        <p>
                            <label for="dealer_map-api-language"><?php esc_html_e( 'Map language', 'wp-dealer-map' ); ?>:</label> 
                            <select id="dealer_map-api-language" name="dealer_map_api[language]">
                                <?php echo $dealer_map_admin->settings_page->get_api_lang_list(); ?>          	
                            </select>
                        </p>
                        <p>
                            <label for="dealer_map-api-region"><?php esc_html_e( 'Map region', 'wp-dealer-map' ); ?>:</label> 
                            <select id="dealer_map-api-region" name="dealer_map_api[region]">
                                <?php echo $dealer_map_admin->settings_page->get_api_reg_list(); ?>
                            </select>
                        </p>

                        <p class="submit">
                            <input type="submit" value="<?php esc_attr_e( 'Save Changes', 'wp-dealer-map' ); ?>" class="button-primary">
                        </p>
                    </div>
                </div>
            </div>
        
            </div>

            <div id="tabs-2">
                <div class="metabox-holder">
                    <div id="dealer_map-search-settings" class="postbox">
                        <h3 class="hndle"><span><?php esc_html_e( 'Search', 'wp-dealer-map' ); ?></span></h3>
                        <div class="inside">
                            <div class="padd">
                             <h4><?php esc_html_e( 'Starting Point Of Map', 'wp-dealer-map' ); ?></h4>
                             <small><?php esc_html_e( 'You can copy/paste from any of your dealer stores', 'wp-dealer-map' ); ?></small>
                             <p>
                                <label for="dealer_map-def-latitude"><?php esc_html_e( 'Default Starting Latitude', 'wp-dealer-map' ); ?>::</label>
                                <input type="number" step="0.000001" value="<?php echo esc_attr( $dealer_map_def_lat ); ?>" name="dealer_map_search[def_lat]" class="textinput" size="25" id="dealer_map-def-latitude">
                             </p>
                             <p>
                                <label for="dealer_map-def-longitude"><?php esc_html_e( 'Default Starting Longitude', 'wp-dealer-map' ); ?>::</label>
                                <input type="number" step="0.000001" value="<?php echo esc_attr( $dealer_map_def_lng ); ?>" name="dealer_map_search[def_lng]" class="textinput" size="25" id="dealer_map-def-longitude">
                             </p>
                             <p>
                                <label for="dealer_map-def-range"><?php esc_html_e( 'Default range', 'wp-dealer-map' ); ?>::</label>
                                <input type="number" step="5" value="<?php echo esc_attr( $dealer_map_def_range ); ?>" name="dealer_map_search[def_range]" class="textinput" size="10" id="dealer_map-def-range">
                             </p>
                             <p>
                                <label for="dealer_map-def-limit"><?php esc_html_e( 'Default limit', 'wp-dealer-map' ); ?>::</label>
                                <input type="number" step="2" value="<?php echo esc_attr( $dealer_map_def_limit ); ?>" name="dealer_map_search[def_limit]" class="textinput" size="10" id="dealer_map-def-limit">
                             </p>
                            </div>
                            <p>
                                <label for="dealer_map-show-default"><?php esc_html_e( 'Show default dealers on map?', 'wp-dealer-map' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $dealer_map_show_default, true ); ?> name="dealer_map_search[show_def]" id="dealer_map-show-default">
                            </p>
                            <p>
                                <label for="dealer_map-default-address"><?php esc_html_e( 'Default ZIP Starting Point', 'wp-dealer-map' ); ?>::</label>
                                <input type="number" value="<?php echo esc_attr( $dealer_map_d_address ); ?>" name="dealer_map_search[defaddress]" class="textinput" size="25" id="dealer_map-default-address">
                             </p>
                            <p>
                                <label for="dealer_map-results-dropdown"><?php esc_html_e( 'Show the max results dropdown?', 'wp-dealer-map' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $dealer_map_results_drop, true ); ?> name="dealer_map_search[results_dropdown]" id="dealer_map-results-dropdown">
                            </p>
                            <p>
                                <label for="dealer_map-radius-dropdown"><?php esc_html_e( 'Show the search radius dropdown?', 'wp-dealer-map' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $dealer_map_radius_drop, true ); ?> name="dealer_map_search[radius_dropdown]" id="dealer_map-radius-dropdown">
                            </p>
                            <p>
                                <label for="dealer_map-distance-unit"><?php esc_html_e( 'Distance unit', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="km" <?php checked( 'km', $dealer_map_distance_unit ); ?> name="dealer_map_search[distance_unit]" id="dealer_map-distance-km">
                                    <label for="dealer_map-distance-km"><?php esc_html_e( 'km', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="mi" <?php checked( 'mi', $dealer_map_distance_unit ); ?> name="dealer_map_search[distance_unit]" id="dealer_map-distance-mi">
                                    <label for="dealer_map-distance-mi"><?php esc_html_e( 'mi', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="dealer_map-max-results"><?php esc_html_e( 'Max search results', 'wp-dealer-map' ); ?>:</label>
                                <input type="text" value="<?php echo esc_attr( $dealer_map_max_results ); ?>" name="dealer_map_search[max_results]" class="textinput" size="40" id="dealer_map-max-results">
                            </p>
                            <p>
                                <label for="dealer_map-search-radius"><?php esc_html_e( 'Search radius options', 'wp-dealer-map' ); ?>:</label>
                                <input type="text" value="<?php echo esc_attr( $dealer_map_max_radius ); ?>" name="dealer_map_search[radius]" class="textinput" size="40" id="dealer_map-search-radius">
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php esc_html_e( 'Save Changes', 'wp-dealer-map' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div id="tabs-3">
                <div class="metabox-holder">
                    <div id="dealer_map-layout-settings" class="postbox">
                        <h3 class="hndle"><span><?php esc_html_e( 'Layout', 'wp-dealer-map' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="dealer_map-map-height"><?php esc_html_e( 'Map height (in pixels)', 'wp-dealer-map' ); ?>:</label>
                                <input type="number" step="10" max="2000" value="<?php echo esc_attr( $dealer_map_map_height ); ?>" name="dealer_map_layout[map_height]" class="textinput" size="10" id="dealer_map-map-height">
                            </p>
                            <p>
                                <label for="dealer_map-layout-type"><?php esc_html_e( 'Select map type', 'wp-dealer-map' ); ?>:</label>
                                <select id="dealer_map-layout-type" name="dealer_map_layout[maptype]">
                                    <?php echo $dealer_map_admin->settings_page->get_map_list(); ?>
                                </select>
                            </p>
                            <p>
                                <label for="dealer_map-layout-zoom"><?php esc_html_e( 'Select zoom level', 'wp-dealer-map' ); ?>:</label>
                                <select id="dealer_map-layout-zoom" name="dealer_map_layout[zoom]">
                                    <?php echo $dealer_map_admin->settings_page->get_zoom_list(); ?>
                                </select>
                            </p>
                            <p>
                                <label for="dealer_map-marker-event"><?php esc_html_e( 'Show infobox on', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="click" <?php checked( 'click', $dealer_map_marker_event ); ?> name="dealer_map_layout[marker_event]" id="dealer_map-marker-click">
                                    <label for="dealer_map-marker-click"><?php esc_html_e( 'Click', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="mouseover" <?php checked( 'mouseover', $dealer_map_marker_event ); ?> name="dealer_map_layout[marker_event]" id="dealer_map-marker-mouseover">
                                    <label for="dealer_map-marker-mouseover"><?php esc_html_e( 'Mouseover', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="dealer_map-marker-effect"><?php esc_html_e( 'Marker effect on', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="drop" <?php checked( 'drop', $dealer_map_marker_effect ); ?> name="dealer_map_layout[marker_effect]" id="dealer_map-marker-drop">
                                    <label for="dealer_map-marker-drop"><?php esc_html_e( 'Drop', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="bounce" <?php checked( 'bounce', $dealer_map_marker_effect ); ?> name="dealer_map_layout[marker_effect]" id="dealer_map-marker-bounce">
                                    <label for="dealer_map-marker-bounce"><?php esc_html_e( 'Bounce', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                             <p>
                                <label for="dealer_map-address_columns"><?php esc_html_e( 'Number of result columns', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="2" <?php checked( '2', $dealer_map_num_columns ); ?> name="dealer_map_layout[num_of_columns]" id="dealer_map-marker-two">
                                    <label for="dealer_map-marker-two"><?php esc_html_e( 'Two', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="3" <?php checked( '3', $dealer_map_num_columns ); ?> name="dealer_map_layout[num_of_columns]" id="dealer_map-marker-three">
                                    <label for="dealer_map-marker-three"><?php esc_html_e( 'Three', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="4" <?php checked( '4', $dealer_map_num_columns ); ?> name="dealer_map_layout[num_of_columns]" id="dealer_map-marker-four">
                                    <label for="dealer_map-marker-four"><?php esc_html_e( 'Four', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="5" <?php checked( '5', $dealer_map_num_columns ); ?> name="dealer_map_layout[num_of_columns]" id="dealer_map-marker-five">
                                    <label for="dealer_map-marker-five"><?php esc_html_e( 'Five', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="6" <?php checked( '6', $dealer_map_num_columns ); ?> name="dealer_map_layout[num_of_columns]" id="dealer_map-marker-six">
                                    <label for="dealer_map-marker-six"><?php esc_html_e( 'Six', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="dealer_map-button-css"><?php esc_html_e( 'Search button styling', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="--" <?php checked( '--', $dealer_map_button_class ); ?> name="dealer_map_layout[btn_class]" id="dealer_map-btn-def">
                                    <label for="dealer_map-btn-def"><?php esc_html_e( 'Default', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="blue" <?php checked( 'blue', $dealer_map_button_class ); ?> name="dealer_map_layout[btn_class]" id="dealer_map-btn-blue">
                                    <label for="dealer_map-btn-blue"><?php esc_html_e( 'Blue', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="red" <?php checked( 'red', $dealer_map_button_class ); ?> name="dealer_map_layout[btn_class]" id="dealer_map-btn-red">
                                    <label for="dealer_map-btn-red"><?php esc_html_e( 'Red', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="green" <?php checked( 'green', $dealer_map_button_class ); ?> name="dealer_map_layout[btn_class]" id="dealer_map-btn-green">
                                    <label for="dealer_map-btn-green"><?php esc_html_e( 'Green', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="white" <?php checked( 'white', $dealer_map_button_class ); ?> name="dealer_map_layout[btn_class]" id="dealer_map-btn-white">
                                    <label for="dealer_map-btn-white"><?php esc_html_e( 'White', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="black" <?php checked( 'black', $dealer_map_button_class ); ?> name="dealer_map_layout[btn_class]" id="dealer_map-btn-black">
                                    <label for="dealer_map-btn-black"><?php esc_html_e( 'Black', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>                
                            <p class="submit">
                                <input type="submit" value="<?php esc_attr_e( 'Save Changes', 'wp-dealer-map' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>
            <div id="tabs-4">
                <div class="metabox-holder">
                    <div id="dealer_map-addition-settings" class="postbox">
                        <h3 class="hndle"><span><?php esc_html_e( 'Uninstall/Environment', 'wp-dealer-map' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <small><?php esc_html_e( 'Note: Delete all option will delete the database table no matter below settings', 'wp-dealer-map' ); ?></small>
                                <br />
                                <label for="dealer_map-delete-all"><?php esc_html_e( 'Delete all data', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="remove" <?php checked( 'remove', $dealer_map_delete_all ); ?> name="dealer_map_addition[remove_all]" id="dealer_map-delete-all">
                                    <label for="dealer_map-delete-all"><?php esc_html_e( 'Remove', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="keep" <?php checked( 'keep', $dealer_map_delete_all ); ?> name="dealer_map_addition[remove_all]" id="dealer_map-keep">
                                    <label for="dealer_map-keep"><?php esc_html_e( 'Keep', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="dealer_map-keep-table"><?php esc_html_e( 'Keep table', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="remove" <?php checked( 'remove', $dealer_map_keep_table ); ?> name="dealer_map_addition[keep_table]" id="dealer_map-keep-remove">
                                    <label for="dealer_map-keep-remove"><?php esc_html_e( 'Remove', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="keep" <?php checked( 'keep', $dealer_map_keep_table ); ?> name="dealer_map_addition[keep_table]" id="dealer_map-keep-keep">
                                    <label for="dealer_map-keep-keep"><?php esc_html_e( 'Keep', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                            <div class="padd">
                            <h4><?php esc_html_e( 'Choose between producting & developing stage', 'wp-dealer-map' ); ?></h4>
                            <p>
                                <label for="dealer_map-environment"><?php esc_html_e( 'Environment', 'wp-dealer-map' ); ?>:</label>                          
                                <span class="dealer_map-radioboxes">
                                    <input type="radio" autocomplete="off" value="production" <?php checked( 'production', $dealer_map_environment ); ?> name="dealer_map_addition[environment]" id="dealer_map-en-production">
                                    <label for="dealer_map-en-production"><?php esc_html_e( 'Production', 'wp-dealer-map' ); ?></label>
                                    <input type="radio" autocomplete="off" value="developing" <?php checked( 'developing', $dealer_map_environment ); ?> name="dealer_map_addition[environment]" id="dealer_map-en-developing">
                                    <label for="dealer_map-en-developing"><?php esc_html_e( 'Developing', 'wp-dealer-map' ); ?></label>
                                </span>
                            </p>
                            <h5><?php esc_html_e( 'Deregister other G-maps scripts if necceasary, only if they break things', 'wp-dealer-map' ); ?></h5>
                            <p>
                                <label for="dealer_map-dereg_scripts"><?php esc_html_e( 'Deregister other maps?', 'wp-dealer-map' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $dealer_map_oth_script, true ); ?> name="dealer_map_addition[gmap_scripts]" id="dealer_map-dereg_scripts">
                            </p>
                            </div>              
                            <p class="submit">
                                <input type="submit" value="<?php esc_html_e( 'Save Changes', 'wp-dealer-map' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <?php settings_fields( 'dealer_map_settings' ); ?>

        </form>

            <div id="tabs-5">
                <form id="dealer_map_form" method="post" action="">
                    <div class="metabox-holder">
                        <div class="postbox">
                            <h3 class="hndle"><span><?php esc_html_e( 'Export/Import Stores to Database', 'wp-dealer-map' ); ?></span></h3>
                            <div class="inside">
                                <table class="form-table"> 
                                    <tr valign="top"><th scope="row"><?php esc_html_e( 'Select Input File:', 'wp-dealer-map' ); ?></th>
                                        <td>
                                            <?php $repop_file = isset( $_POST[ 'csv_file' ] ) ? sanitize_text_field($_POST[ 'csv_file' ]) : null; ?>
                                            <input id="csv_file" name="csv_file"  type="text" size="70" value="<?php echo $repop_file; ?>" />
                                            <input id="csv_file_button" type="button" class="button-primary" value="Upload" />
                                            <button type="submit" class="remove_image_button button">&times;</button>
                                            <br><?php esc_html_e( 'File must end with a .csv extension.', 'wp-dealer-map' ); ?>
                                        </td>
                                    </tr>
                                    <tr valign="top"><th scope="row"><?php esc_html_e( 'Update Database Rows:', 'wp-dealer-map' ); ?></th>
                                        <td>
                                            <input id="update_db" name="update_db" type="checkbox" />
                                            <br><?php esc_html_e( 'Will update exisiting database rows when a duplicated primary key is encountered.', 'wp-dealer-map' ); ?>
                                            <br><?php esc_html_e( 'Defaults to all rows inserted as new rows.', 'wp-dealer-map' ); ?>
                                        </td>
                                    </tr>
                                </table>
        <?php
            $error_message       = '';
            $success_message     = '';
            $message_info_style  = '';
            $table_select = $wpdb->prefix . 'dealer_map_stores';
        // If button is clicked to "Import"
        if ( isset( $_POST[ 'execute_button' ] ) ) {

        // If the "Select Input File" input field is empty
        if ( empty( $_POST[ 'csv_file' ] ) ) {
        $error_message .= '* ' . __( 'No Input File URL. Please select one from Media library.', 'wp-dealer-map' ) . '<br />';
        }
        // Check that "Input File" has proper .csv file extension
        $ext = pathinfo( $_POST[ 'csv_file' ], PATHINFO_EXTENSION );
        if ( $ext !== 'csv' ) {
        $error_message .= '* ' . __( 'Wrong File format. Please choose a valid .csv file.', 'wp-dealer-map' );
        }

        // File is correct .csv format; continue
        if ( ! empty( $_POST[ 'csv_file' ] ) && ($ext === 'csv') ) {
            $db_cols = $wpdb->get_col( "DESC " . $table_select, 0 );  // Array of db column names
        }
        // Get the number of columns from the hidden input field (re-auto-populated via jquery)
        $numColumns = 18;

        // Open the .csv file and get it's contents
        $myCSV   = (isset($repop_file)) ? $repop_file : null;
        $path    = ($myCSV != null) ? parse_url( $myCSV, PHP_URL_PATH ) : false;
        $myCSV   = ($path != false) ? $_SERVER[ 'DOCUMENT_ROOT' ] . $path : false;

        if ( $myCSV && ( $fh = @fopen( $myCSV, 'r' )) !== false ) {

            // Set variables
            $values      = array();
            $too_many    = '';  // Used to alert users if columns do not match

            while ( ( $row = fgetcsv( $fh )) !== false ) {  // Get file contents and set up row array
            if ( count( $row ) == $numColumns ) {  // If .csv column count matches db column count
                $row = array_map( function($v) {
                return esc_sql( $v );
                }, $row );
                $values[] = '("' . implode( '", "', $row ) . '")';  // Each new line of .csv file becomes an array
            }
            }

            //Escape first data row in Dealer Database
            $num_var = 1;  
            // If user input number exceeds available .csv rows
            if ( $num_var > count( $values ) ) {
                $error_message   .= '* ' . __( 'CSV exceeds the number of entries in database. Please check your .csv file and correct it.', 'wp-dealer-map' ) . '<br />';
                $too_many    = 'true';  // set alert variable
            }
            // Else splice array and remove number (rows) user selected
            else {
                $values = array_slice( $values, $num_var );
            }

            // If there are no rows in the .csv file AND the user DID NOT input more rows than available from the .csv file
            if ( empty( $values ) && ($too_many !== 'true') ) {
            $error_message   .= '* ' . __( 'Columns do not match.', 'wp-dealer-map' ) . '<br />';
            $error_message   .= '* ' . __( 'The number of columns in the database does not match the number of columns attempting to be imported from the .csv file.', 'wp-dealer-map' ) . '<br />';
            $error_message   .= '* ' . __( 'Please verify the number of columns attempting to be imported in the "Select Input File" exactly matches the number of columns displayed below.', 'wp-dealer-map' ) . '<br />';
            } else {
            // If the user DID NOT input more rows than are available from the .csv file
            if ( $too_many !== 'true' ) {

                $db_query_update = '';
                $db_query_insert = '';

                // Format $db_cols to a string
                $db_cols_implode = implode( ',', $db_cols );

                // Format $values to a string
                $values_implode = implode( ',', $values );


                // If "Update Rows" was checked
                if ( isset( $_POST[ 'update_db' ] ) ) {

                // Setup sql 'on duplicate update' loop
                $updateOnDuplicate = ' ON DUPLICATE KEY UPDATE ';
                foreach ( $db_cols as $db_col ) {
                    $updateOnDuplicate .= "$db_col=VALUES($db_col),";
                }
                $updateOnDuplicate = rtrim( $updateOnDuplicate, ',' );


                $sql         = 'INSERT INTO ' . $table_select . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode . $updateOnDuplicate;
                $db_query_update = $wpdb->query( $sql );
                } else {
                $sql         = 'INSERT INTO ' . $table_select . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode;
                $db_query_insert = $wpdb->query( $sql );
                }

                // If db db_query_update is successful
                if ( $db_query_update ) {
                $success_message = __( 'Success! Store locations updated successfully.', 'wp-dealer-map' );
                }
                // If db db_query_insert is successful
                elseif ( $db_query_insert ) {
                $success_message = __( 'Success! Store locations updated successfully.', 'wp-dealer-map' );
                $success_message .= '<br /><strong>' . count( $values ) . '</strong> ' . __( 'record(s) were inserted into the', 'wp-dealer-map' ) . ' <strong>' . $table_select . '</strong> ' . __( 'location database table.', 'wp-dealer-map' );
                }
                // If db db_query_insert is successful AND there were no rows to udpate
                elseif ( ($db_query_update === 0) && ($db_query_insert === '') ) {
                $message_info_style .= '* ' . __( 'All .csv values already exist in the database.', 'wp-dealer-map' ) . '<br />';
                } else {
                $error_message   .= '* ' . __( 'There was a problem with the database query.', 'wp-dealer-map' ) . '<br />';
                $error_message   .= '* ' . __( 'A duplicate entry was found in the database for a .csv file entry.', 'wp-dealer-map' ) . '<br />';
                $error_message   .= '* ' . __( 'If necessary; please use the option above to "Update Database Rows".', 'wp-dealer-map' ) . '<br />';
                }
            }
            }
        } else {
            $error_message .= '* ' . __( 'No valid .csv file was found at the specified url. Please check the "Select Input File" field and ensure it points to a valid .csv file.', 'wp-dealer-map' ) . '<br />';
        }
        }
        // If Delete button was clicked
    if ( ! empty( $_POST[ 'delete_db_button_hidden' ] ) ) {

        $truncate     = 'TRUNCATE TABLE ' . $table_select;
        $del_success = $wpdb->query( $truncate );

        if ( $del_success ) {
        $success_message .= __( 'Success!  The location database has been deleted successfully.', 'wp-dealer-map' );
        } else {
        $error_message .= '* ' . __( 'Error deleting table. Please verify the table exists.', 'wp-dealer-map' );
        }
    }    

    // If there is a message - info-style
    if ( ! empty( $message_info_style ) ) {
        echo '<div class="info_message_dismiss">';
        echo $message_info_style;
        echo '<br /><em>(' . __( 'Click to dismiss', 'wp-dealer-map' ) . ')</em>';
        echo '</div>';
    }

    // If there is an error message 
    if ( ! empty( $error_message ) ) {
        echo '<div class="error_message">';
        echo $error_message;
        echo '<br /><em>(' . __( 'Click to dismiss', 'wp-dealer-map' ) . ')</em>';
        echo '</div>';
    }

    // If there is a success message
    if ( ! empty( $success_message ) ) {
        echo '<div class="success_message">';
        echo $success_message;
        echo '<br /><em>(' . __( 'Click to dismiss', 'wp-dealer-map' ) . ')</em>';
        echo '</div>';
    } ?>
                    <p class="submit">
                        <input id="execute_button" name="execute_button" type="submit" class="button-primary" value="<?php esc_attr_e( 'Import to DB', 'wp-dealer-map' ) ?>" />
                        <input id="export_to_csv_button" name="export_to_csv_button" type="submit" class="button-secondary" value="<?php esc_attr_e( 'Export to CSV', 'wp-dealer-map' ) ?>" />
                        <input id="delete_db_button" name="delete_db_button" type="button" class="button-secondary" value="<?php esc_attr_e( 'Delete Table', 'wp-dealer-map' ) ?>" />
                        <input type="hidden" id="delete_db_button_hidden" name="delete_db_button_hidden" value="" />
                    </p>
                </form>
                            <div id="dialog-confirm" title="<?php esc_attr_e( 'Delete database table?', 'wp-dealer-map' ); ?>">
                               <p>
                                  <span class="ui-icon ui-icon-alert dialog-confirm"></span>
                                  <?php esc_html_e( 'This location table will be permanently deleted and cannot be recovered. Proceed?', 'wp-dealer-map' ); ?>
                               </p>
                        </div>
                    </div>     
                </div>         
            </div>
            <div class="example-csv">
            <h4><?php echo esc_html( __('Example of required CSV file') ); ?></h4> 
                <img src="<?php echo esc_url(DEALER_URL . 'img/examplecsv.png'); ?>">
            </div>              
        </div>           
    </div>
</div>


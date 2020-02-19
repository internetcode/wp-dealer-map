<?php
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb, $grim_admin, $grim_settings; 

    $grim_api_server = (isset($grim_settings['api_server_key'])) ? $grim_settings['api_server_key'] : '';
    $grim_results_drop = (isset($grim_settings['results_dropdown'])) ? $grim_settings['results_dropdown'] : true;
    $grim_show_default = (isset($grim_settings['show_def'])) ? $grim_settings['show_def'] : false;
    $grim_radius_drop = (isset($grim_settings['radius_dropdown'])) ? $grim_settings['radius_dropdown'] : true;
    $grim_distance_unit = (isset($grim_settings['distance_unit'])) ? $grim_settings['distance_unit'] : 'mi';
    $grim_max_results = (isset($grim_settings['max_results'])) ? $grim_settings['max_results'] : '10';
    $grim_max_radius = (isset($grim_settings['search_radius'])) ? $grim_settings['search_radius'] : '20';
    $grim_def_lat = (isset($grim_settings['def_lat'])) ? $grim_settings['def_lat'] : '40.730610';
    $grim_def_lng = (isset($grim_settings['def_lng'])) ? $grim_settings['def_lng'] : '-73.935242';
    $grim_def_range = (isset($grim_settings['def_range'])) ? $grim_settings['def_range'] : '25';
    $grim_def_limit = (isset($grim_settings['def_limit'])) ? $grim_settings['def_limit'] : '10';
    $grim_d_address = (isset($grim_settings['defaddress'])) ? $grim_settings['defaddress'] : '10001';
    $grim_marker_event = (isset($grim_settings['marker_event'])) ? $grim_settings['marker_event'] : 'click';
    $grim_marker_effect = (isset($grim_settings['marker_effect'])) ? $grim_settings['marker_effect'] : 'drop';
    $grim_map_height = (isset($grim_settings['map_height'])) ? $grim_settings['map_height'] : '450';
    $grim_num_columns = (isset($grim_settings['num_of_columns'])) ? $grim_settings['num_of_columns'] : 'four';
    $grim_button_class = (isset($grim_settings['button_css'])) ? $grim_settings['button_css'] : '--'; 
    $grim_delete_all = (isset($grim_settings['remove_all'])) ? $grim_settings['remove_all'] : 'remove';
    $grim_keep_table = (isset($grim_settings['keep_table'])) ? $grim_settings['keep_table'] : 'remove';
    $grim_environment = (isset($grim_settings['environment'])) ? $grim_settings['environment'] : 'production'; 
    $grim_oth_script = (isset($grim_settings['gmap_scripts'])) ? $grim_settings['gmap_scripts'] : false; ?>

<div id="grim-wrap" class="wrap grim-settings">
	<h2>Grim Dealer <?php _e( 'Settings', 'grim' ); ?></h2>

    <?php
    settings_errors(); ?>

    <div id="general">
        <ul>
            <li><a href="#tabs-1"><?php _e( 'Google Maps API Settings', 'grim' ); ?></a></li>
            <li><a href="#tabs-2"><?php _e( 'Search Settings', 'grim' ); ?></a></li>
            <li><a href="#tabs-3"><?php _e( 'Layout Settings', 'grim' ); ?></a></li>
            <li><a href="#tabs-4"><?php _e( 'Additional', 'grim' ); ?></a></li>
            <li><a href="#tabs-5"><?php _e( 'Export/Import', 'grim' ); ?></a></li>
        </ul>

    <div id="tabs-1">
        <form id="grim-settings-form" method="post" action="options.php" autocomplete="off" accept-charset="utf-8">
            <div class="metabox-holder">
                <div id="grim-api-settings" class="postbox">
                    <h3 class="hndle"><span><?php _e( 'Google Maps API', 'grim' ); ?></span></h3>
                    <div class="inside">
                        <p>
                            <label for="grim-api-server-key"><?php _e( 'Google Map API key', 'grim' ); ?>:</label> 
                            <input type="text" value="<?php echo esc_attr( $grim_api_server ); ?>" name="grim_api[server_key]"  class="textinput" size="40" id="grim-api-server-key">
                        </p>
                        <p>
                            <label><?php _e( 'API Status:', 'grim' ); ?></label>
                            <span class="api-stat"><?php echo grim_api_status(); ?></span> 
                        </p>
                        <p>
                            <label for="grim-api-language"><?php _e( 'Map language', 'grim' ); ?>:</label> 
                            <select id="grim-api-language" name="grim_api[language]">
                                <?php echo $grim_admin->settings_page->get_api_lang_list(); ?>          	
                            </select>
                        </p>
                        <p>
                            <label for="grim-api-region"><?php _e( 'Map region', 'grim' ); ?>:</label> 
                            <select id="grim-api-region" name="grim_api[region]">
                                <?php echo $grim_admin->settings_page->get_api_reg_list(); ?>
                            </select>
                        </p>

                        <p class="submit">
                            <input type="submit" value="<?php _e( 'Save Changes', 'grim' ); ?>" class="button-primary">
                        </p>
                    </div>
                </div>
            </div>
        
            </div>

            <div id="tabs-2">
                <div class="metabox-holder">
                    <div id="grim-search-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Search', 'grim' ); ?></span></h3>
                        <div class="inside">
                            <div class="padd">
                             <h4><?php _e( 'Starting Point Of Map', 'grim' ); ?></h4>
                             <small><?php _e( 'You can copy/paste from any of your dealer stores', 'grim' ); ?></small>
                             <p>
                                <label for="grim-def-latitude"><?php _e( 'Default Starting Latitude', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="number" step="0.000001" value="<?php echo esc_attr( $grim_def_lat ); ?>" name="grim_search[def_lat]" class="textinput" size="25" id="grim-def-latitude">
                             </p>
                             <p>
                                <label for="grim-def-longitude"><?php _e( 'Default Starting Longitude', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="number" step="0.000001" value="<?php echo esc_attr( $grim_def_lng ); ?>" name="grim_search[def_lng]" class="textinput" size="25" id="grim-def-longitude">
                             </p>
                             <p>
                                <label for="grim-def-range"><?php _e( 'Default range', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="number" step="5" value="<?php echo esc_attr( $grim_def_range ); ?>" name="grim_search[def_range]" class="textinput" size="10" id="grim-def-range">
                             </p>
                             <p>
                                <label for="grim-def-limit"><?php _e( 'Default limit', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="number" step="2" value="<?php echo esc_attr( $grim_def_limit ); ?>" name="grim_search[def_limit]" class="textinput" size="10" id="grim-def-limit">
                             </p>
                            </div>
                            <p>
                                <label for="grim-show-default"><?php _e( 'Show default dealers on map?', 'grim' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $grim_show_default, true ); ?> name="grim_search[show_def]" id="grim-show-default">
                            </p>
                            <p>
                                <label for="grim-default-address"><?php _e( 'Default ZIP Starting Point', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="number" value="<?php echo esc_attr( $grim_d_address ); ?>" name="grim_search[defaddress]" class="textinput" size="25" id="grim-default-address">
                             </p>
                            <p>
                                <label for="grim-results-dropdown"><?php _e( 'Show the max results dropdown?', 'grim' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $grim_results_drop, true ); ?> name="grim_search[results_dropdown]" id="grim-results-dropdown">
                            </p>
                            <p>
                                <label for="grim-radius-dropdown"><?php _e( 'Show the search radius dropdown?', 'grim' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $grim_radius_drop, true ); ?> name="grim_search[radius_dropdown]" id="grim-radius-dropdown">
                            </p>
                            <p>
                                <label for="grim-distance-unit"><?php _e( 'Distance unit', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="km" <?php checked( 'km', $grim_distance_unit ); ?> name="grim_search[distance_unit]" id="grim-distance-km">
                                    <label for="grim-distance-km"><?php _e( 'km', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="mi" <?php checked( 'mi', $grim_distance_unit ); ?> name="grim_search[distance_unit]" id="grim-distance-mi">
                                    <label for="grim-distance-mi"><?php _e( 'mi', 'grim' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="grim-max-results"><?php _e( 'Max search results', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="text" value="<?php echo esc_attr( $grim_max_results ); ?>" name="grim_search[max_results]" class="textinput" size="40" id="grim-max-results">
                            </p>
                            <p>
                                <label for="grim-search-radius"><?php _e( 'Search radius options', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="text" value="<?php echo esc_attr( $grim_max_radius ); ?>" name="grim_search[radius]" class="textinput" size="40" id="grim-search-radius">
                            </p>
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'grim' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <div id="tabs-3">
                <div class="metabox-holder">
                    <div id="grim-layout-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Layout', 'grim' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <label for="grim-map-height"><?php _e( 'Map height (in pixels)', 'grim' ); ?>:<span class="grim-info"></label>
                                <input type="number" step="10" max="2000" value="<?php echo esc_attr( $grim_map_height ); ?>" name="grim_layout[map_height]" class="textinput" size="10" id="grim-map-height">
                            </p>
                            <p>
                                <label for="grim-layout-type"><?php _e( 'Select map type', 'grim' ); ?>:<span class="grim-info"></label>
                                <select id="grim-layout-type" name="grim_layout[maptype]">
                                    <?php echo $grim_admin->settings_page->get_map_list(); ?>
                                </select>
                            </p>
                            <p>
                                <label for="grim-layout-zoom"><?php _e( 'Select zoom level', 'grim' ); ?>:<span class="grim-info"></label>
                                <select id="grim-layout-zoom" name="grim_layout[zoom]">
                                    <?php echo $grim_admin->settings_page->get_zoom_list(); ?>
                                </select>
                            </p>
                            <p>
                                <label for="grim-marker-event"><?php _e( 'Show infobox on', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="click" <?php checked( 'click', $grim_marker_event ); ?> name="grim_layout[marker_event]" id="grim-marker-click">
                                    <label for="grim-marker-click"><?php _e( 'Click', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="mouseover" <?php checked( 'mouseover', $grim_marker_event ); ?> name="grim_layout[marker_event]" id="grim-marker-mouseover">
                                    <label for="grim-marker-mouseover"><?php _e( 'Mouseover', 'grim' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="grim-marker-effect"><?php _e( 'Marker effect on', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="drop" <?php checked( 'drop', $grim_marker_effect ); ?> name="grim_layout[marker_effect]" id="grim-marker-drop">
                                    <label for="grim-marker-drop"><?php _e( 'Drop', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="bounce" <?php checked( 'bounce', $grim_marker_effect ); ?> name="grim_layout[marker_effect]" id="grim-marker-bounce">
                                    <label for="grim-marker-bounce"><?php _e( 'Bounce', 'grim' ); ?></label>
                                </span>
                            </p>
                             <p>
                                <label for="grim-address_columns"><?php _e( 'Number of result columns', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="2" <?php checked( '2', $grim_num_columns ); ?> name="grim_layout[num_of_columns]" id="grim-marker-two">
                                    <label for="grim-marker-two"><?php _e( 'Two', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="3" <?php checked( '3', $grim_num_columns ); ?> name="grim_layout[num_of_columns]" id="grim-marker-three">
                                    <label for="grim-marker-three"><?php _e( 'Three', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="4" <?php checked( '4', $grim_num_columns ); ?> name="grim_layout[num_of_columns]" id="grim-marker-four">
                                    <label for="grim-marker-four"><?php _e( 'Four', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="5" <?php checked( '5', $grim_num_columns ); ?> name="grim_layout[num_of_columns]" id="grim-marker-five">
                                    <label for="grim-marker-five"><?php _e( 'Five', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="6" <?php checked( '6', $grim_num_columns ); ?> name="grim_layout[num_of_columns]" id="grim-marker-six">
                                    <label for="grim-marker-six"><?php _e( 'Six', 'grim' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="grim-button-css"><?php _e( 'Search button styling', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="--" <?php checked( '--', $grim_button_class ); ?> name="grim_layout[btn_class]" id="grim-btn-def">
                                    <label for="grim-btn-def"><?php _e( 'Default', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="blue" <?php checked( 'blue', $grim_button_class ); ?> name="grim_layout[btn_class]" id="grim-btn-blue">
                                    <label for="grim-btn-blue"><?php _e( 'Blue', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="red" <?php checked( 'red', $grim_button_class ); ?> name="grim_layout[btn_class]" id="grim-btn-red">
                                    <label for="grim-btn-red"><?php _e( 'Red', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="green" <?php checked( 'green', $grim_button_class ); ?> name="grim_layout[btn_class]" id="grim-btn-green">
                                    <label for="grim-btn-green"><?php _e( 'Green', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="white" <?php checked( 'white', $grim_button_class ); ?> name="grim_layout[btn_class]" id="grim-btn-white">
                                    <label for="grim-btn-white"><?php _e( 'White', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="black" <?php checked( 'black', $grim_button_class ); ?> name="grim_layout[btn_class]" id="grim-btn-black">
                                    <label for="grim-btn-black"><?php _e( 'Black', 'grim' ); ?></label>
                                </span>
                            </p>                
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'grim' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>
            <div id="tabs-4">
                <div class="metabox-holder">
                    <div id="grim-addition-settings" class="postbox">
                        <h3 class="hndle"><span><?php _e( 'Uninstall/Environment', 'grim' ); ?></span></h3>
                        <div class="inside">
                            <p>
                                <small><?php _e( 'Note: Delete all option will delete the database table no matter below settings', 'grim' ); ?></small>
                                <br />
                                <label for="grim-delete-all"><?php _e( 'Delete all data', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="remove" <?php checked( 'remove', $grim_delete_all ); ?> name="grim_addition[remove_all]" id="grim-delete-all">
                                    <label for="grim-delete-all"><?php _e( 'Remove', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="keep" <?php checked( 'keep', $grim_delete_all ); ?> name="grim_addition[remove_all]" id="grim-keep">
                                    <label for="grim-keep"><?php _e( 'Keep', 'grim' ); ?></label>
                                </span>
                            </p>
                            <p>
                                <label for="grim-keep-table"><?php _e( 'Keep table', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="remove" <?php checked( 'remove', $grim_keep_table ); ?> name="grim_addition[keep_table]" id="grim-keep-remove">
                                    <label for="grim-keep-remove"><?php _e( 'Remove', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="keep" <?php checked( 'keep', $grim_keep_table ); ?> name="grim_addition[keep_table]" id="grim-keep-keep">
                                    <label for="grim-keep-keep"><?php _e( 'Keep', 'grim' ); ?></label>
                                </span>
                            </p>
                            <div class="padd">
                            <h4><?php _e( 'Choose between producting & developing stage', 'grim' ); ?></h4>
                            <p>
                                <label for="grim-environment"><?php _e( 'Environment', 'grim' ); ?>:</label>                          
                                <span class="grim-radioboxes">
                                    <input type="radio" autocomplete="off" value="production" <?php checked( 'production', $grim_environment ); ?> name="grim_addition[environment]" id="grim-en-production">
                                    <label for="grim-en-production"><?php _e( 'Production', 'grim' ); ?></label>
                                    <input type="radio" autocomplete="off" value="developing" <?php checked( 'developing', $grim_environment ); ?> name="grim_addition[environment]" id="grim-en-developing">
                                    <label for="grim-en-developing"><?php _e( 'Developing', 'grim' ); ?></label>
                                </span>
                            </p>
                            <h5><?php _e( 'Deregister other G-maps scripts if necceasary, only if they break things', 'grim' ); ?></h5>
                            <p>
                                <label for="grim-dereg_scripts"><?php _e( 'Deregister other maps?', 'grim' ); ?></label>
                                <input type="checkbox" value="" <?php checked( $grim_oth_script, true ); ?> name="grim_addition[gmap_scripts]" id="grim-dereg_scripts">
                            </p>
                            </div>              
                            <p class="submit">
                                <input type="submit" value="<?php _e( 'Save Changes', 'grim' ); ?>" class="button-primary">
                            </p>
                        </div>        
                    </div>   
                </div>  
            </div>

            <?php settings_fields( 'grim_settings' ); ?>

        </form>

            <div id="tabs-5">
                <form id="grim_form" method="post" action="">
                    <div class="metabox-holder">
                        <div class="postbox">
                            <h3 class="hndle"><span><?php _e( 'Export/Import Stores to Database', 'grim' ); ?></span></h3>
                            <div class="inside">
                                <table class="form-table"> 
                                    <tr valign="top"><th scope="row"><?php _e( 'Select Input File:', 'grim' ); ?></th>
                                        <td>
                                            <?php $repop_file = isset( $_POST[ 'csv_file' ] ) ? $_POST[ 'csv_file' ] : null; ?>
                                            <input id="csv_file" name="csv_file"  type="text" size="70" value="<?php echo $repop_file; ?>" />
                                            <input id="csv_file_button" type="button" class="button-primary" value="Upload" />
                                            <button type="submit" class="remove_image_button button">&times;</button>
                                            <br><?php _e( 'File must end with a .csv extension.', 'grim' ); ?>
                                        </td>
                                    </tr>
                                    <tr valign="top"><th scope="row"><?php _e( 'Update Database Rows:', 'grim' ); ?></th>
                                        <td>
                                            <input id="update_db" name="update_db" type="checkbox" />
                                            <br><?php _e( 'Will update exisiting database rows when a duplicated primary key is encountered.', 'grim' ); ?>
                                            <br><?php _e( 'Defaults to all rows inserted as new rows.', 'grim' ); ?>
                                        </td>
                                    </tr>
                                </table>
        <?php
            $error_message       = '';
            $success_message     = '';
            $message_info_style  = '';
            $table_select = $wpdb->prefix . 'grim_stores';
        // If button is clicked to "Import"
        if ( isset( $_POST[ 'execute_button' ] ) ) {

        // If the "Select Input File" input field is empty
        if ( empty( $_POST[ 'csv_file' ] ) ) {
        $error_message .= '* ' . __( 'No Input File URL. Please select one from Media library.', 'grim' ) . '<br />';
        }
        // Check that "Input File" has proper .csv file extension
        $ext = pathinfo( $_POST[ 'csv_file' ], PATHINFO_EXTENSION );
        if ( $ext !== 'csv' ) {
        $error_message .= '* ' . __( 'Wrong File format. Please choose a valid .csv file.', 'grim' );
        }

        // File is correct .csv format; continue
        if ( ! empty( $_POST[ 'csv_file' ] ) && ($ext === 'csv') ) {
            $db_cols = $wpdb->get_col( "DESC " . $table_select, 0 );  // Array of db column names
        }
        // Get the number of columns from the hidden input field (re-auto-populated via jquery)
        $numColumns = 18;

        // Open the .csv file and get it's contents
        $myCSV   = $_POST[ 'csv_file' ];
        $path    = parse_url( $myCSV, PHP_URL_PATH );
        $myCSV   = $_SERVER[ 'DOCUMENT_ROOT' ] . $path;

        if ( ( $fh = @fopen( $myCSV, 'r' )) !== false ) {

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

            //Escape first data row in Grim Database
            $num_var = 1;  
            // If user input number exceeds available .csv rows
            if ( $num_var > count( $values ) ) {
                $error_message   .= '* ' . __( 'CSV exceeds the number of entries in database. Please check your .csv file and correct it.', 'grim' ) . '<br />';
                $too_many    = 'true';  // set alert variable
            }
            // Else splice array and remove number (rows) user selected
            else {
                $values = array_slice( $values, $num_var );
            }

            // If there are no rows in the .csv file AND the user DID NOT input more rows than available from the .csv file
            if ( empty( $values ) && ($too_many !== 'true') ) {
            $error_message   .= '* ' . __( 'Columns do not match.', 'grim' ) . '<br />';
            $error_message   .= '* ' . __( 'The number of columns in the database does not match the number of columns attempting to be imported from the .csv file.', 'grim' ) . '<br />';
            $error_message   .= '* ' . __( 'Please verify the number of columns attempting to be imported in the "Select Input File" exactly matches the number of columns displayed below.', 'grim' ) . '<br />';
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
                $success_message = __( 'Success! Store locations updated successfully.', 'grim' );
                }
                // If db db_query_insert is successful
                elseif ( $db_query_insert ) {
                $success_message = __( 'Success! Store locations updated successfully.', 'grim' );
                $success_message .= '<br /><strong>' . count( $values ) . '</strong> ' . __( 'record(s) were inserted into the', 'grim' ) . ' <strong>' . $table_select . '</strong> ' . __( 'location database table.', 'grim' );
                }
                // If db db_query_insert is successful AND there were no rows to udpate
                elseif ( ($db_query_update === 0) && ($db_query_insert === '') ) {
                $message_info_style .= '* ' . __( 'All .csv values already exist in the database.', 'grim' ) . '<br />';
                } else {
                $error_message   .= '* ' . __( 'There was a problem with the database query.', 'grim' ) . '<br />';
                $error_message   .= '* ' . __( 'A duplicate entry was found in the database for a .csv file entry.', 'grim' ) . '<br />';
                $error_message   .= '* ' . __( 'If necessary; please use the option above to "Update Database Rows".', 'grim' ) . '<br />';
                }
            }
            }
        } else {
            $error_message .= '* ' . __( 'No valid .csv file was found at the specified url. Please check the "Select Input File" field and ensure it points to a valid .csv file.', 'grim' ) . '<br />';
        }
        }
        // If Delete button was clicked
    if ( ! empty( $_POST[ 'delete_db_button_hidden' ] ) ) {

        $truncate     = 'TRUNCATE TABLE ' . $table_select;
        $del_success = $wpdb->query( $truncate );

        if ( $del_success ) {
        $success_message .= __( 'Success!  The location database has been deleted successfully.', 'grim' );
        } else {
        $error_message .= '* ' . __( 'Error deleting table. Please verify the table exists.', 'grim' );
        }
    }    

    // If there is a message - info-style
    if ( ! empty( $message_info_style ) ) {
        echo '<div class="info_message_dismiss">';
        echo $message_info_style;
        echo '<br /><em>(' . __( 'Click to dismiss', 'grim' ) . ')</em>';
        echo '</div>';
    }

    // If there is an error message 
    if ( ! empty( $error_message ) ) {
        echo '<div class="error_message">';
        echo $error_message;
        echo '<br /><em>(' . __( 'Click to dismiss', 'grim' ) . ')</em>';
        echo '</div>';
    }

    // If there is a success message
    if ( ! empty( $success_message ) ) {
        echo '<div class="success_message">';
        echo $success_message;
        echo '<br /><em>(' . __( 'Click to dismiss', 'grim' ) . ')</em>';
        echo '</div>';
    } ?>
                    <p class="submit">
                        <input id="execute_button" name="execute_button" type="submit" class="button-primary" value="<?php _e( 'Import to DB', 'grim' ) ?>" />
                        <input id="export_to_csv_button" name="export_to_csv_button" type="submit" class="button-secondary" value="<?php _e( 'Export to CSV', 'grim' ) ?>" />
                        <input id="delete_db_button" name="delete_db_button" type="button" class="button-secondary" value="<?php _e( 'Delete Table', 'grim' ) ?>" />
                        <input type="hidden" id="delete_db_button_hidden" name="delete_db_button_hidden" value="" />
                    </p>
                </form>
                            <div id="dialog-confirm" title="<?php _e( 'Delete database table?', 'grim' ); ?>">
                               <p>
                                  <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
                                  <?php _e( 'This location table will be permanently deleted and cannot be recovered. Proceed?', 'grim' ); ?>
                               </p>
                        </div>
                    </div>     
                </div>         
            </div>
            <div class="example-csv">
            <h4><?php echo esc_html( __('Example of required CSV file') ); ?></h4> 
                <img src="<?php echo GRIM_URL . 'img/examplecsv.png'; ?>">
            </div>              
        </div>           
    </div>
</div>


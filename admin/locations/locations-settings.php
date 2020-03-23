<?php
global $wpdb;
    $table_name = $wpdb->prefix . 'dealer_map_stores'; 

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'name' => '',
        'address' => '',
        'lat' => null,
        'lng' => null,
        'active' => null,
        'address2' => '',
        'city' => null,
        'state' => null,
        'zip' => null,
        'country' => null,
        'description' => null,
        'phone' => null,
        'fax' => null,
        'url' => null,
        'email' => null,
        'thumb_id' => '',
        'proseries' => 0,
    );

    // here we are verifying does this request is post back and have correct nonce
    // and have correct nonce
    $nonce = (empty($_REQUEST['nonce'])) ? esc_attr($_REQUEST['page']) : esc_attr($_REQUEST['nonce']);  
  
    if (wp_verify_nonce( $nonce, 'nonce')) {
        // combine our default item with request params
        $allitems = shortcode_atts($default, $_REQUEST);
        //sanitize input $_REQUEST
        $item = $this->sanitize_items($allitems);
        //Strip slashes from all items
        $item = $this->dealer_map_deep_stripslashes($item);   
        // validate data, strip lat/lng to 6 decimals and if all ok save item to database
        $item_valid = $this->dealer_map_validate_data($item);
        //if valid call save item to database
        if ($item_valid === true) {
            // if id is zero insert otherwise update
            if ($item['id'] == '0') {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'wp-dealer-map');
                } else {
                    $notice = __('There was an error while saving item', 'wp-dealer-map');
                }
            } else {
                // var_dump($item);
                $result = $wpdb->update($table_name, $item, array('id' => intval($item['id'])));
                if ($result) {
                    $message = __('Item was successfully updated', 'wp-dealer-map');
                } else {
                    $notice = __('There was an error while updating item', 'wp-dealer-map');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", esc_sql( $_REQUEST['id'] ) ), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wp-dealer-map');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('locations_form_meta_box', 'Location data', array($this, 'dealer_map_locations_form_meta_box_handler'), 'location', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php esc_html_e('Location', 'wp-dealer-map') ?> 
            <a class="add-new-h2" href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=dealer_map/dealer_map_locations')); ?>">
            <?php _e('Back to list', 'wp-dealer-map') ?></a>
            <a class="add-new-h2" href="<?php echo esc_url(get_admin_url(get_current_blog_id(), 'admin.php?page=dealer_map/locations_form')); ?>">
            <?php esc_html_e('Add New Location', 'wp-dealer-map') ?></a>
    </h2>

    <?php if (!empty($notice)) : ?>
    <div id="setting-error-settings_error" class="notice notice-error settings-error is-dismissible error">
        <p><?php echo $notice ?></p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'wp-dealer-map'); ?></span>
        </button>
    </div>
    <?php endif;?>

    <?php if (!empty($message)) : ?>
    <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible updated">
        <p><?php echo $message ?></p>
        <button type="button" class="notice-dismiss">
             <span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'wp-dealer-map'); ?></span>
        </button>
    </div>          
    <?php endif; ?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('nonce'); ?>"/>
        <input type="hidden" name="id" value="<?php echo esc_attr($item['id']); ?>"/>
        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <input type="submit" value="<?php esc_attr_e('Save Location', 'wp-dealer-map')?>" style="margin-bottom: 20px;" class="button-primary" name="submit">
                    <?php do_meta_boxes('location', 'normal', $item); ?>
                    <input type="submit" value="<?php esc_attr_e('Save Location', 'wp-dealer-map')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>


<?php
global $wpdb;
    $table_name = $wpdb->prefix . 'grim_stores'; 

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
    $c_nonce = wp_create_nonce('nonce');
    $nonce = (empty($_REQUEST['nonce'])) ? $_REQUEST['page'] : $_REQUEST['nonce'];  
  
    if (wp_verify_nonce( $nonce, 'nonce')) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, strip lat/lng to 6 decimals and if all ok save item to database
        $item_valid = $this->grim_validate_data($item);
        //strip all unecessary slashes
        $item = $this->grim_deep_stripslashes($item);
        if ($item_valid === true) {
            // if id is zero insert otherwise update
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'grim');
                } else {
                    $notice = __('There was an error while saving item', 'grim');
                }
            } else {
                // var_dump($item);
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'grim');
                } else {
                    $notice = __('There was an error while updating item', 'grim');
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
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'grim');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('locations_form_meta_box', 'Location data', array($this, 'grim_locations_form_meta_box_handler'), 'location', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Location', 'grim') ?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=grimdealers/grim_locations');?>">
            <?php _e('Back to list', 'grim') ?></a>
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=grimdealers/locations_form');?>">
            <?php _e('Add New Location', 'grim') ?></a>
    </h2>

    <?php if (!empty($notice)) : ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>

    <?php if (!empty($message)) : ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif; ?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" id="nonce" value="<?php echo $c_nonce; ?>"/>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <input type="submit" value="<?php _e('Save Location', 'grim')?>" style="margin-bottom: 20px;" class="button-primary" name="submit">
                    <?php do_meta_boxes('location', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save Location', 'grim')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>


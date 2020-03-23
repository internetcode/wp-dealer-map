<?php
/**
 * Handle the metaboxes
 *
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

    /**
    * Dealer_Map_List_Table class that will display our custom table
    * records in nice table
    */
class Dealer_Map_List_Table extends WP_List_Table {
    /**
        * [REQUIRED] this is a default column renderer
        *
        * @param $item - row (key, value array)
        * @param $column_name - string (key)
        * @return HTML
        */
    public function column_default($item, $column_name) {
        return $item[$column_name];
    }

    /**
        * [OPTIONAL] this is example, how to render column with actions,
        * when you hover row "Edit | Delete" links showed
        *
        * @param $item - row (key, value array)
        * @return HTML
        */
    public function column_name($item) {

        $actions = array(
            'edit' => sprintf('<a href="?page=dealer_map/locations_form&id=%s">%s</a>', intval($item['id']), __('Edit', 'wp-dealer-map')),
            'delete' => sprintf('<a href="?page=dealer_map/dealer_map_locations&action=delete&id=%s">%s</a>', intval($item['id']), __('Delete', 'wp-dealer-map')),
        );

        return sprintf('%s %s',
            stripslashes($item['name']),
            $this->row_actions($actions)
        );
    }

    /**
        * [REQUIRED] this is how checkbox column renders
        *
        * @param $item - row (key, value array)
        * @return HTML
        */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
        * [REQUIRED] This method return columns to display in table
        * you can skip columns that you do not want to show
        * like content, or description
        *
        * @return array
        */
    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'name' => __('Name', 'wp-dealer-map'),
            'address' => __('Address', 'wp-dealer-map'),
            'lat' => __('Latitude', 'wp-dealer-map'),
            'lng' => __('Longitude', 'wp-dealer-map'),
            'active' => __('Active', 'wp-dealer-map'),
            'address2' => __('Address2', 'wp-dealer-map'),
            'city' => __('City', 'wp-dealer-map'),
            'state' => __('State', 'wp-dealer-map'),
            'zip' => __('Zip', 'wp-dealer-map'),
            'country' => __('Country', 'wp-dealer-map'),
            'description' => __('Description', 'wp-dealer-map'),
            'phone' => __('Phone', 'wp-dealer-map'),
            'fax' => __('Fax', 'wp-dealer-map'),
            'url' => __('URL', 'wp-dealer-map'),
            'email' => __('Email', 'wp-dealer-map'),
            'thumb_id' => __('Image', 'wp-dealer-map'),
            'proseries' => __('PRO', 'wp-dealer-map')
        );
        return $columns;
    }

    /**
        * [OPTIONAL] This method return columns that may be used to sort table
        * all strings in array - is column names
        * notice that true on name column means that its default sort
        *
        * @return array
        */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array('name', true),
            'address' => array('address', false),
            'lat' => array('lat', false),
            'lng' => array('lng', false),
            'active' => array('active', false),
            'address2' => array('address2', false),
            'city' => array('city', false),
            'state' => array('state', false),
            'zip' => array('zip', false),
            'country' => array('country', false),
            'description' => array('description', false),
            'phone' => array('phone', false),
            'fax' => array('fax', false),
            'url' => array('url', false),
            'email' => array('email', false),
            'thumb_id' => array('thumb_id', false),
            'proseries' => __('PRO', 'wp-dealer-map')
        );
        return $sortable_columns;
    }

    /**
        * [OPTIONAL] Return array of bult actions if has any
        *
        * @return array
        */
    public function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
        * [OPTIONAL] This method processes bulk actions
        * it can be outside of class
        * it can not use wp_redirect coz there is output already
        * in this example we are processing delete action
        * message about successful deletion will be shown on page in next part
        */
    public function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dealer_map_stores'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {

            $ids = ( isset($_GET['id']) && !empty($_GET['id']) ) ? esc_sql($_GET['id']) : array();
            if(is_array($ids)) 
            $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    private function search_through() {
        $is_search = false;

        if(isset($_GET['search']) && $_GET['search'] != '') {
            $is_search = true;
        } else if (isset($_GET['show_all'])) {
          $is_search = false; 
        }
        return $is_search;
    }

    /**
        * [REQUIRED] This is the most important method
        *
        * It will get rows from database and prepare them to be showed in table
        */
    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dealer_map_stores'; // do not forget about tables prefix

        $per_page = 20; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();
        $is_search = $this->search_through();
        $search = '';
        if(isset($_REQUEST['search'])) {
            $search = sanitize_text_field($_REQUEST['search']);
        }    
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? ($per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? sanitize_sql_orderby($_REQUEST['orderby']) : 'name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? esc_sql($_REQUEST['order']) : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        if($is_search) {
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE name LIKE %s 
                OR city LIKE %s 
                OR state LIKE %s 
                OR zip LIKE %s ORDER BY $orderby $order LIMIT %d OFFSET %d", $search, $search, $search, $search, $per_page, $paged), ARRAY_A); 
        } else {
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        }

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

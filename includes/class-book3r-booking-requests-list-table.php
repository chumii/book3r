<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Book3r_Booking_Requests_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Booking Request', 'book3r'),
            'plural'   => __('Booking Requests', 'book3r'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        $columns = [
            'cb'              => '<input type="checkbox" />',
            'arrival_date'    => __('Arrival Date', 'book3r'),
            'departure_date'  => __('Departure Date', 'book3r'),
            'preferred_room'  => __('Preferred Room', 'book3r'),
            'num_guests'      => __('Number of Guests', 'book3r'),
            'children_under_6'=> __('Children Under 6', 'book3r'),
            'first_name'      => __('First Name', 'book3r'),
            'last_name'       => __('Last Name', 'book3r'),
            'email'           => __('Email', 'book3r'),
            'phone'           => __('Phone', 'book3r'),
            'address'         => __('Address', 'book3r'),
            'postal_code'     => __('Postal Code', 'book3r'),
            'city'            => __('City', 'book3r'),
            'country'         => __('Country', 'book3r'),
            'status'          => __('Status', 'book3r'),
            'created_at'      => __('Created At', 'book3r')
        ];

        return $columns;
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="booking[]" value="%s" />', $item['id']
        );
    }

    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'arrival_date':
            case 'departure_date':
            case 'preferred_room':
            case 'num_guests':
            case 'children_under_6':
            case 'first_name':
            case 'last_name':
            case 'email':
            case 'phone':
            case 'address':
            case 'postal_code':
            case 'city':
            case 'country':
            case 'status':
            case 'created_at':
                return esc_html($item[$column_name]);
            default:
                return print_r($item, true);
        }
    }

    protected function get_sortable_columns() {
        return [
            'arrival_date'    => ['arrival_date', false],
            'departure_date'  => ['departure_date', false],
            'preferred_room'  => ['preferred_room', false],
            'num_guests'      => ['num_guests', false],
            'children_under_6'=> ['children_under_6', false],
            'first_name'      => ['first_name', false],
            'last_name'       => ['last_name', false],
            'email'           => ['email', false],
            'phone'           => ['phone', false],
            'postal_code'     => ['postal_code', false],
            'city'            => ['city', false],
            'country'         => ['country', false],
            'status'          => ['status', false],
            'created_at'      => ['created_at', false]
        ];
    }

    public function prepare_items() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'book3r_bookings';
        $query = "SELECT * FROM $table_name";

        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'created_at';
        $order = !empty($_GET['order']) ? $_GET['order'] : 'desc';
        $query .= " ORDER BY $orderby $order";

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_pages = ceil($total_items / $per_page);

        if (!empty($current_page) && !empty($per_page)) {
            $offset = ($current_page - 1) * $per_page;
            $query .= " LIMIT $offset, $per_page";
        }

        $this->items = $wpdb->get_results($query, ARRAY_A);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => $total_pages
        ]);
    }

    public function no_items() {
        _e('No booking requests found.', 'book3r');
    }

    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }
}

<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Book3r_Customers_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Customer', 'book3r'),
            'plural'   => __('Customers', 'book3r'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        $columns = [
            'cb'            => '<input type="checkbox" />',
            'first_name'    => __('First Name', 'book3r'),
            'last_name'     => __('Last Name', 'book3r'),
            'email'         => __('Email', 'book3r'),
            'phone'         => __('Phone', 'book3r'),
            'address'       => __('Address', 'book3r'),
            'postal_code'   => __('Postal Code', 'book3r'),
            'city'          => __('City', 'book3r'),
            'country'       => __('Country', 'book3r'),
            'created_at'    => __('Created At', 'book3r')
        ];

        return $columns;
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="customer[]" value="%s" />', $item['id']
        );
    }

    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'first_name':
            case 'last_name':
            case 'email':
            case 'phone':
            case 'address':
            case 'postal_code':
            case 'city':
            case 'country':
            case 'created_at':
                return esc_html($item[$column_name]);
            default:
                return print_r($item, true);
        }
    }

    protected function get_sortable_columns() {
        return [
            'first_name' => ['first_name', false],
            'last_name'  => ['last_name', false],
            'email'      => ['email', false],
            'phone'      => ['phone', false],
            'postal_code'=> ['postal_code', false],
            'city'       => ['city', false],
            'country'    => ['country', false],
            'created_at' => ['created_at', false]
        ];
    }

    public function prepare_items() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'book3r_customers';
        $query = "SELECT * FROM $table_name";

        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'last_name';
        $order = !empty($_GET['order']) ? $_GET['order'] : 'asc';
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
        _e('No customers found.', 'book3r');
    }

    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }
}

<?php

class Book3r_Activator {

    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Create the bookings table
        $table_name = $wpdb->prefix . 'book3r_bookings';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            arrival_date date NOT NULL,
            departure_date date NOT NULL,
            preferred_room varchar(255) NOT NULL,
            num_guests int NOT NULL,
            children_under_6 int NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            address text NOT NULL,
            postal_code varchar(20) NOT NULL,
            city varchar(100) NOT NULL,
            country varchar(100) NOT NULL,
            message text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'New',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Create the customers table
        $table_name = $wpdb->prefix . 'book3r_customers';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            address text NOT NULL,
            postal_code varchar(20) NOT NULL,
            city varchar(100) NOT NULL,
            country varchar(100) NOT NULL,
            note text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);

        self::register_gallery_post_type();
        flush_rewrite_rules();
    }

    public static function register_gallery_post_type() {
        $labels = array(
            'name' => 'Galerien',
            'singular_name' => 'Galerie',
            'menu_name' => 'Galerien',
            'name_admin_bar' => 'Galerie',
            'add_new' => 'Neue Galerie hinzufügen',
            'add_new_item' => 'Neue Galerie hinzufügen',
            'new_item' => 'Neue Galerie',
            'edit_item' => 'Galerie bearbeiten',
            'view_item' => 'Galerie anzeigen',
            'all_items' => 'Alle Galerien',
            'search_items' => 'Galerien durchsuchen',
            'parent_item_colon' => 'Übergeordnete Galerie:',
            'not_found' => 'Keine Galerien gefunden.',
            'not_found_in_trash' => 'Keine Galerien im Papierkorb gefunden.'
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'galleries'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 20,
            'supports' => array('title', 'editor'),
            'menu_icon' => 'dashicons-format-gallery',
            'show_in_rest' => true
        );

        register_post_type('book3r_gallery', $args);
    }
}

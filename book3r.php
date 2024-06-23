<?php
/*
Plugin Name: Book3r
Plugin URI: http://example.com/
Description: Buchungs- und Wohnungsverwaltung
Version: 1.0.0
Author: Jonas D.
Author URI: http://example.com/
License: GPL2
*/

// Suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Log when the plugin is loaded
error_log('Book3r plugin loaded');

// Load plugin text domain for translations
function book3r_load_textdomain() {
    load_plugin_textdomain('book3r', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'book3r_load_textdomain');

// Enqueue admin scripts
function book3r_enqueue_admin_scripts($hook) {
    // Only enqueue on the relevant admin pages
    if ($hook == 'toplevel_page_book3r-dashboard' || strpos($hook, 'book3r') !== false) {
        wp_enqueue_script('admin-sorting', plugin_dir_url(__FILE__) . 'includes/js/admin-sorting.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'book3r_enqueue_admin_scripts');


// Update database schema
function book3r_update_database_schema() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'book3r_customers';

    // Check if the note column exists
    $column = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'note'");
    if (empty($column)) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "ALTER TABLE $table_name ADD COLUMN note TEXT DEFAULT ''";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
add_action('admin_init', 'book3r_update_database_schema');

// Activation hook
register_activation_hook(__FILE__, 'activate_book3r');
register_activation_hook(__FILE__, array('Book3r_Activator', 'activate'));


function activate_book3r() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-book3r-activator.php';
    Book3r_Activator::activate();
}

// Include the main class file
require_once plugin_dir_path(__FILE__) . 'includes/class-book3r.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-book3r-booking-form.php';

function run_book3r() {
    $plugin = new Book3r();
    $plugin->run();

    // Initialize the booking form
    new Book3r_Booking_Form();
}

run_book3r();

<?php

class Book3r {

    public function __construct() {
        $this->load_dependencies();
        add_action('admin_init', array($this, 'handle_customer_form_submission'));
		add_action('admin_init', array($this, 'handle_add_customer_form_submission'));
        add_action('admin_init', array($this, 'handle_booking_form_submission'));
    }

    private function load_dependencies() {
        require_once plugin_dir_path(__FILE__) . 'class-book3r-booking-form.php';
        require_once plugin_dir_path(__FILE__) . 'class-book3r-customers.php';
        require_once plugin_dir_path(__FILE__) . 'class-book3r-booking-requests.php';
        require_once plugin_dir_path(__FILE__) . 'class-book3r-gallery.php';
    }

    public function run() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Book3r Dashboard',
            'Book3r',
            'manage_options',
            'book3r-dashboard',
            array($this, 'display_dashboard'),
            'dashicons-admin-home',
            6
        );
    
        add_submenu_page(
            'book3r-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'book3r-dashboard',
            array($this, 'display_dashboard')
        );
    
        add_submenu_page(
            'book3r-dashboard',
            'Kunden',
            'Kunden',
            'manage_options',
            'book3r-customers',
            array($this, 'display_customers')
        );

        add_submenu_page(
            'book3r-dashboard',
            'Buchungsanfragen',
            'Buchungsanfragen',
            'manage_options',
            'book3r-booking-requests',
            array($this, 'display_booking_requests')
        );

        add_submenu_page(
            null,
            'Kunden bearbeiten',
            'Kunden bearbeiten',
            'manage_options',
            'book3r-edit-customer',
            array($this, 'display_edit_customer')
        );

        add_submenu_page(
            null,
            'Buchungsanfrage bearbeiten',
            'Buchungsanfrage bearbeiten',
            'manage_options',
            'book3r-edit-booking',
            array($this, 'display_edit_booking')
        );
    }

    public function display_dashboard() {
        global $wpdb;
        $customer_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}book3r_customers");
        ?>
        <div class="wrap">
            <h1>Book3r Dashboard</h1>
            <p>Willkommen</p>
            <p>Anzahl Kunden: <?php echo $customer_count; ?></p>
        </div>
        <?php
    }

    public function display_customers() {
        $customers = new Book3r_Customers();
        $customers->display_customers_page();
    }

    public function display_booking_requests() {
        $booking_requests = new Book3r_Booking_Requests();
        $booking_requests->display_booking_requests_page();
    }

    public function display_edit_customer() {
        $customers = new Book3r_Customers();
        $customers->display_edit_customer_page();
    }

    public function display_edit_booking() {
        $booking_requests = new Book3r_Booking_Requests();
        $booking_requests->display_edit_booking_page();
    }

    public function handle_customer_form_submission() {
		if (isset($_POST['book3r_edit_customer_nonce']) && wp_verify_nonce($_POST['book3r_edit_customer_nonce'], 'book3r_edit_customer')) {
			global $wpdb;
	
			$customer_id = intval($_POST['customer_id']);
			$data = array(
				'first_name' => sanitize_text_field($_POST['first_name']),
				'last_name' => sanitize_text_field($_POST['last_name']),
				'email' => sanitize_email($_POST['email']),
				'phone' => sanitize_text_field($_POST['phone']),
				'address' => sanitize_textarea_field($_POST['address']),
				'postal_code' => sanitize_text_field($_POST['postal_code']),
				'city' => sanitize_text_field($_POST['city']),
				'country' => sanitize_text_field($_POST['country']),
				'note' => sanitize_textarea_field($_POST['note'])
			);
	
			$result = $wpdb->update("{$wpdb->prefix}book3r_customers", $data, array('id' => $customer_id));
	
			if ($result !== false) {
				wp_safe_redirect(admin_url('admin.php?page=book3r-customers'));
				exit;
			} else {
				error_log('Failed to update customer.');
			}
		}
	}

	public function handle_add_customer_form_submission() {
        if (isset($_POST['book3r_add_customer_nonce']) && wp_verify_nonce($_POST['book3r_add_customer_nonce'], 'book3r_add_customer')) {
            global $wpdb;

            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $email = sanitize_email($_POST['email']);

            // Check if customer already exists
            $existing_customer = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}book3r_customers WHERE first_name = %s AND last_name = %s AND email = %s",
                    $first_name,
                    $last_name,
                    $email
                )
            );

            if ($existing_customer) {
                // Customer already exists
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error"><p>Customer with the same First Name, Last Name, and Email already exists.</p></div>';
                });
            } else {
                // Create new customer
                $data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => sanitize_text_field($_POST['phone']),
                    'address' => sanitize_textarea_field($_POST['address']),
                    'postal_code' => sanitize_text_field($_POST['postal_code']),
                    'city' => sanitize_text_field($_POST['city']),
                    'country' => sanitize_text_field($_POST['country']),
                    'note' => sanitize_textarea_field($_POST['note']),
                    'created_at' => current_time('mysql')
                );

                $wpdb->insert("{$wpdb->prefix}book3r_customers", $data);

                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success"><p>New customer created successfully.</p></div>';
                });

                // Redirect to avoid form resubmission
                wp_safe_redirect(admin_url('admin.php?page=book3r-customers'));
                exit;
            }
        }
    }
	

    public function handle_booking_form_submission() {
        if (isset($_POST['book3r_edit_booking_nonce']) && wp_verify_nonce($_POST['book3r_edit_booking_nonce'], 'book3r_edit_booking')) {
            global $wpdb;

            $booking_id = intval($_POST['booking_id']);
            $data = array(
                'arrival_date' => sanitize_text_field($_POST['arrival_date']),
                'departure_date' => sanitize_text_field($_POST['departure_date']),
                'preferred_room' => sanitize_text_field($_POST['preferred_room']),
                'num_guests' => intval($_POST['num_guests']),
                'children_under_6' => intval($_POST['children_under_6']),
                'first_name' => sanitize_text_field($_POST['first_name']),
                'last_name' => sanitize_text_field($_POST['last_name']),
                'email' => sanitize_email($_POST['email']),
                'phone' => sanitize_text_field($_POST['phone']),
                'address' => sanitize_textarea_field($_POST['address']),
                'postal_code' => sanitize_text_field($_POST['postal_code']),
                'city' => sanitize_text_field($_POST['city']),
                'country' => sanitize_text_field($_POST['country']),
                'message' => sanitize_textarea_field($_POST['message']),
                'status' => sanitize_text_field($_POST['status'])
            );

            $result = $wpdb->update("{$wpdb->prefix}book3r_bookings", $data, array('id' => $booking_id));

            if ($result !== false) {
                wp_safe_redirect(admin_url('admin.php?page=book3r-booking-requests'));
                exit;
            } else {
                error_log('Failed to update booking request.');
            }
        }
    }
}

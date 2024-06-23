<?php

class Book3r_Booking_Form {

    public function __construct() {
        add_shortcode('book3r_booking_form', array($this, 'display_booking_form'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_nopriv_book3r_submit_booking', array($this, 'handle_form_submission'));
        add_action('wp_ajax_book3r_submit_booking', array($this, 'handle_form_submission'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('book3r-booking-form', plugin_dir_url(__FILE__) . 'js/book3r-booking-form.js', array('jquery'), null, true);

        wp_localize_script('book3r-booking-form', 'book3r_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    public function display_booking_form() {
        ob_start();
        ?>
        <form id="book3r-booking-form" method="POST">
            <label for="arrival_date">Anreise</label>
            <input type="text" id="arrival_date" name="arrival_date" required>
            
            <label for="departure_date">Abreise</label>
            <input type="text" id="departure_date" name="departure_date" required>
            
            <label for="preferred_room">bevorz. Wohung</label>
            <input type="text" id="preferred_room" name="preferred_room" required>
            
            <label for="num_guests">Anzahl Gäste</label>
            <input type="number" id="num_guests" name="num_guests" required>
            
            <label for="children_under_6">davon unter 6</label>
            <input type="number" id="children_under_6" name="children_under_6" required>
            
            <label for="first_name">Vorname</label>
            <input type="text" id="first_name" name="first_name" required>
            
            <label for="last_name">Nachname</label>
            <input type="text" id="last_name" name="last_name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="phone">Telefon</label>
            <input type="text" id="phone" name="phone" required>
            
            <label for="address">Adresse</label>
            <input type="text" id="address" name="address" required>
            
            <label for="postal_code">PLZ</label>
            <input type="text" id="postal_code" name="postal_code" required>
            
            <label for="city">Ort</label>
            <input type="text" id="city" name="city" required>
            
            <label for="country">Land</label>
            <input type="text" id="country" name="country" required>
            
            <label for="message">Nachricht</label>
            <textarea id="message" name="message"></textarea>
            
            <input type="hidden" name="action" value="book3r_submit_booking">
            <?php wp_nonce_field('book3r_booking_form_nonce', 'book3r_booking_form_nonce_field'); ?>
            
            <input type="submit" value="Anfrage abschicken">
        </form>
        <div id="book3r-booking-form-response"></div>
        <?php
        return ob_get_clean();
    }

    public function handle_form_submission() {
        check_ajax_referer('book3r_booking_form_nonce', 'book3r_booking_form_nonce_field');

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
            'status' => 'New',
            'created_at' => current_time('mysql')
        );

        global $wpdb;
        $table_name = $wpdb->prefix . 'book3r_bookings';
        $wpdb->insert($table_name, $data);

        $this->maybe_update_customer($data);

        // Send emails
        $this->send_email_notifications($data);

        wp_send_json_success('Your booking request has been submitted.');
    }

    private function maybe_update_customer($data) {
        global $wpdb;
        $customer_table = $wpdb->prefix . 'book3r_customers';

        $customer = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $customer_table WHERE first_name = %s AND last_name = %s AND email = %s",
                $data['first_name'],
                $data['last_name'],
                $data['email']
            )
        );

        if ($customer) {
            $updated_data = array(
                'phone' => $data['phone'],
                'address' => $data['address'],
                'postal_code' => $data['postal_code'],
                'city' => $data['city'],
                'country' => $data['country'],
            );
            $wpdb->update(
                $customer_table,
                $updated_data,
                array('id' => $customer->id)
            );
        } else {
            $customer_data = array(
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'postal_code' => $data['postal_code'],
                'city' => $data['city'],
                'country' => $data['country'],
                'created_at' => current_time('mysql')
            );
            $wpdb->insert($customer_table, $customer_data);
        }
    }

    private function send_email_notifications($data) {
        $admin_email = get_option('admin_email');
        
        // Guest email
        $guest_subject = 'Ihre Buchungsanfrage';
        $guest_message = "Vielen Dank für Ihre Buchungsanfrage. Wir werden uns schnellsmöglich mit Ihnen in Verbindung setzen.\n\n";
        $guest_message .= "Anreise: {$data['arrival_date']}\n";
        $guest_message .= "Abreise: {$data['departure_date']}\n";
        $guest_message .= "bevorz. Wohung: {$data['preferred_room']}\n";
        $guest_message .= "Anzahl Gäste: {$data['num_guests']}\n";
        $guest_message .= "davon unter 6: {$data['children_under_6']}\n";
        $guest_message .= "Vorname: {$data['first_name']}\n";
        $guest_message .= "Nachname: {$data['last_name']}\n";
        $guest_message .= "Email: {$data['email']}\n";
        $guest_message .= "Telefon: {$data['phone']}\n";
        $guest_message .= "Adresse: {$data['address']}\n";
        $guest_message .= "PLZ: {$data['postal_code']}\n";
        $guest_message .= "Ort: {$data['city']}\n";
        $guest_message .= "Land: {$data['country']}\n";
        $guest_message .= "Nachricht: {$data['message']}\n";

        // Admin email
        $admin_subject = 'Neue Buchungsanfrage';
        $admin_message = "Eine neue Buchungsanfrage ist eingegangen!.\n\n";
        foreach ($data as $key => $value) {
            $admin_message .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
        }

        // Send the emails
        wp_mail($data['email'], $guest_subject, $guest_message);
        wp_mail($admin_email, $admin_subject, $admin_message);
    }
}

<?php

class Book3r_Booking_Requests {

    public function __construct() {
        // Any initialization if needed
    }

    public function display_booking_requests_page() {
        $booking_requests_list_table = new Book3r_Booking_Requests_List_Table();
        $booking_requests_list_table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e('Booking Requests', 'book3r'); ?></h1>
            <form method="post">
                <?php
                $booking_requests_list_table->display();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function display_edit_booking_page() {
        global $wpdb;

        $booking_id = intval($_GET['id']);
        $booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}book3r_bookings WHERE id = %d", $booking_id));

        ?>
        <div class="wrap">
            <h1>Buchungsanfrage bearbeiten</h1>
            <form method="post">
                <?php wp_nonce_field('book3r_edit_booking', 'book3r_edit_booking_nonce'); ?>
                <input type="hidden" name="booking_id" value="<?php echo esc_attr($booking_id); ?>">
                <table class="form-table">
                    <tr>
                        <th>Anreise</th>
                        <td><input type="text" name="arrival_date" value="<?php echo esc_attr($booking->arrival_date); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Abreise</th>
                        <td><input type="text" name="departure_date" value="<?php echo esc_attr($booking->departure_date); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Bevorz. Wohnung</th>
                        <td><input type="text" name="preferred_room" value="<?php echo esc_attr($booking->preferred_room); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Anzahl Gäste</th>
                        <td><input type="number" name="num_guests" value="<?php echo esc_attr($booking->num_guests); ?>" required></td>
                    </tr>
                    <tr>
                        <th>davon unter 6</th>
                        <td><input type="number" name="children_under_6" value="<?php echo esc_attr($booking->children_under_6); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Vorname</th>
                        <td><input type="text" name="first_name" value="<?php echo esc_attr($booking->first_name); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Nachname</th>
                        <td><input type="text" name="last_name" value="<?php echo esc_attr($booking->last_name); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><input type="email" name="email" value="<?php echo esc_attr($booking->email); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Telefon</th>
                        <td><input type="text" name="phone" value="<?php echo esc_attr($booking->phone); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Adresse</th>
                        <td><textarea name="address" required><?php echo esc_textarea($booking->address); ?></textarea></td>
                    </tr>
                    <tr>
                        <th>PLZ</th>
                        <td><input type="text" name="postal_code" value="<?php echo esc_attr($booking->postal_code); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Ort</th>
                        <td><input type="text" name="city" value="<?php echo esc_attr($booking->city); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Land</th>
                        <td><input type="text" name="country" value="<?php echo esc_attr($booking->country); ?>" required></td>
                    </tr>
                    <tr>
                        <th>Nachricht</th>
                        <td><textarea name="message"><?php echo esc_textarea($booking->message); ?></textarea></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <select name="status">
                                <option value="Neu" <?php selected($booking->status, 'Neu'); ?>>Neu</option>
                                <option value="Bestätigt" <?php selected($booking->status, 'Bestätigt'); ?>>Bestätigt</option>
                                <option value="Abgelehnt" <?php selected($booking->status, 'Abgelehnt'); ?>>Abgelehnt</option>
                                <option value="In Bearbeitung" <?php selected($booking->status, 'In Bearbeitung'); ?>>In Bearbeitung</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'book3r'); ?>"></p>
            </form>
        </div>
        <?php
    }
}

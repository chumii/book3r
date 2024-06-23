<?php

class Book3r_Customers {

	public function __construct() {
		// init
	}
	
	public function display_customers_page() {
		$customers_list_table = new Book3r_Customers_List_Table();
		$customers_list_table->prepare_items();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e('Customers', 'book3r'); ?></h1>
			<a href="#" class="page-title-action" id="add-new-customer"><?php _e('Add New', 'book3r'); ?></a>
			<form method="post">
				<?php
				$customers_list_table->display();
				?>
			</form>
		</div>
		<?php
	}
	

	public function display_edit_customer_page() {
		global $wpdb;
	
		$customer_id = intval($_GET['id']);
		$customer = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}book3r_customers WHERE id = %d", $customer_id));
	
		?>
		<div class="wrap">
			<h1>Kunde bearbeiten</h1>
			<form method="post">
				<?php wp_nonce_field('book3r_edit_customer', 'book3r_edit_customer_nonce'); ?>
				<input type="hidden" name="customer_id" value="<?php echo esc_attr($customer_id); ?>">
				<table class="form-table">
					<tr>
						<th>Vorname</th>
						<td><input type="text" name="first_name" value="<?php echo esc_attr($customer->first_name); ?>" required></td>
					</tr>
					<tr>
						<th>Nachname</th>
						<td><input type="text" name="last_name" value="<?php echo esc_attr($customer->last_name); ?>" required></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><input type="email" name="email" value="<?php echo esc_attr($customer->email); ?>" required></td>
					</tr>
					<tr>
						<th>Telefon</th>
						<td><input type="text" name="phone" value="<?php echo esc_attr($customer->phone); ?>" required></td>
					</tr>
					<tr>
						<th>Adresse</th>
						<td><textarea name="address" required><?php echo esc_textarea($customer->address); ?></textarea></td>
					</tr>
					<tr>
						<th>PLZ</th>
						<td><input type="text" name="postal_code" value="<?php echo esc_attr($customer->postal_code); ?>" required></td>
					</tr>
					<tr>
						<th>Ort</th>
						<td><input type="text" name="city" value="<?php echo esc_attr($customer->city); ?>" required></td>
					</tr>
					<tr>
						<th>Land</th>
						<td><input type="text" name="country" value="<?php echo esc_attr($customer->country); ?>" required></td>
					</tr>
					<tr>
						<th>Notiz</th>
						<td><textarea name="note"><?php echo esc_textarea($customer->note); ?></textarea></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="Save Changes"></p>
			</form>
		</div>
		<?php
	}	
	
}
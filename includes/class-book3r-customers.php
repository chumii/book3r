<?php

class Book3r_Customers {

	public function __construct() {
		// init
	}

	public function display_customers_page() {
		global $wpdb;
		$customers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}book3r_customers ORDER BY last_name ASC");
		?>
		<div class="wrap">
			<h1>Kunden</h1>
			<button id="add-new-customer" class="button button-primary">Kunde hinzufügen</button>
			<div id="new-customer-form" style="display: none;">
				<h2>Neuer Kunde</h2>
				<form method="post">
					<?php wp_nonce_field('book3r_add_customer', 'book3r_add_customer_nonce'); ?>
					<table class="form-table">
						<tr>
							<th>Vorname</th>
							<td><input type="text" name="first_name" required></td>
						</tr>
						<tr>
							<th>Nachname</th>
							<td><input type="text" name="last_name" required></td>
						</tr>
						<tr>
							<th>Email</th>
							<td><input type="email" name="email" required></td>
						</tr>
						<tr>
							<th>Telefon</th>
							<td><input type="text" name="phone" required></td>
						</tr>
						<tr>
							<th>Adresse</th>
							<td><textarea name="address" required></textarea></td>
						</tr>
						<tr>
							<th>PLZ</th>
							<td><input type="text" name="postal_code" required></td>
						</tr>
						<tr>
							<th>Ort</th>
							<td><input type="text" name="city" required></td>
						</tr>
						<tr>
							<th>Land</th>
							<td><input type="text" name="country" required></td>
						</tr>
						<tr>
							<th>Notiz</th>
							<td><textarea name="note"></textarea></td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="Kunde erstellen"></p>
				</form>
			</div>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th class="sortable">Vorname</th>
						<th class="sortable">Nachname</th>
						<th class="sortable">Email</th>
						<th class="sortable">Telefon</th>
						<th class="sortable">Adresse</th>
						<th class="sortable">PLZ</th>
						<th class="sortable">Ort</th>
						<th class="sortable">Land</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($customers as $customer): ?>
					<tr>
						<td><?php echo esc_html($customer->first_name); ?></td>
						<td><?php echo esc_html($customer->last_name); ?></td>
						<td><?php echo esc_html($customer->email); ?></td>
						<td><?php echo esc_html($customer->phone); ?></td>
						<td><?php echo esc_html($customer->address); ?></td>
						<td><?php echo esc_html($customer->postal_code); ?></td>
						<td><?php echo esc_html($customer->city); ?></td>
						<td><?php echo esc_html($customer->country); ?></td>
						<td>
							<a href="<?php echo admin_url('admin.php?page=book3r-edit-customer&id=' . $customer->id); ?>">Edit</a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<script type="text/javascript">
			document.getElementById('add-new-customer').addEventListener('click', function() {
				var form = document.getElementById('new-customer-form');
				if (form.style.display === 'none') {
					form.style.display = 'block';
				} else {
					form.style.display = 'none';
				}
			});
		</script>
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
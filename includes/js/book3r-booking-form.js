jQuery(document).ready(function($) {
	$('#arrival_date, #departure_date').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$('#book3r-booking-form').on('submit', function(e) {
		e.preventDefault();

		var formData = $(this).serialize();

		$.post(book3r_ajax_object.ajax_url, formData, function(response) {
			if (response.success) {
				$('#book3r-booking-form-response').html('<p>' + response.data + '</p>');
				$('#book3r-booking-form')[0].reset();
			} else {
				$('#book3r-booking-form-response').html('<p>' + response.data + '</p>');
			}
		});
	});
});

jQuery(document).ready(function($) {
    var file_frame;

    $('#book3r-add-gallery-image').on('click', function(event) {
        event.preventDefault();

        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Bilder auswählen',
            button: {
                text: 'Bilder hinzufügen'
            },
            multiple: true
        });

        file_frame.on('select', function() {
            var attachments = file_frame.state().get('selection').toJSON();

            attachments.forEach(function(attachment) {
                $('#book3r-gallery-images-list').append(
                    '<li>' +
                    '<img src="' + attachment.url + '" style="width: 150px; height: auto;">' +
                    '<input type="radio" name="book3r_main_image" value="' + attachment.id + '"> Hauptbild' +
                    '<a href="#" class="book3r-remove-image">Bild entfernen</a>' +
                    '<input type="hidden" name="book3r_gallery_images[]" value="' + attachment.id + '">' +
                    '</li>'
                );
            });
        });

        file_frame.open();
    });

    $('#book3r-gallery-images-list').on('click', '.book3r-remove-image', function(event) {
        event.preventDefault();
        $(this).closest('li').remove();
    });
});

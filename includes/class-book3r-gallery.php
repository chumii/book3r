<?php

class Book3r_Gallery {

    public function __construct() {
        $this->setup();
    }

    public function setup() {
		add_action('init', array($this, 'register_gallery_post_type'));
		add_action('add_meta_boxes', array($this, 'add_gallery_metabox'));
		add_action('save_post', array($this, 'save_gallery_images'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
		add_shortcode('book3r_gallery', array($this, 'render_gallery_shortcode'));
		add_action('init', array($this, 'register_gallery_block'));
	}

    public function register_gallery_post_type() {
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
			'show_in_rest' => true // Enable REST API support
		);
	
		register_post_type('book3r_gallery', $args);
	}

    public function add_gallery_metabox() {
        add_meta_box(
            'book3r_gallery_images',
            'Galerie Bilder',
            array($this, 'gallery_images_metabox_callback'),
            'book3r_gallery',
            'normal',
            'high'
        );
    }

    public function gallery_images_metabox_callback($post) {
        wp_nonce_field('book3r_save_gallery_images', 'book3r_gallery_images_nonce');

        $gallery_images = get_post_meta($post->ID, '_book3r_gallery_images', true);
        $main_image = get_post_meta($post->ID, '_book3r_main_image', true);

        ?>
        <div id="book3r-gallery-images-container">
            <ul id="book3r-gallery-images-list">
                <?php if ($gallery_images): ?>
                    <?php foreach ($gallery_images as $image_id): ?>
                        <li>
                            <img src="<?php echo wp_get_attachment_url($image_id); ?>" style="width: 150px; height: auto;">
                            <input type="radio" name="book3r_main_image" value="<?php echo esc_attr($image_id); ?>" <?php checked($image_id, $main_image); ?>> Hauptbild
                            <a href="#" class="book3r-remove-image">Bild entfernen</a>
                            <input type="hidden" name="book3r_gallery_images[]" value="<?php echo esc_attr($image_id); ?>">
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <input type="button" id="book3r-add-gallery-image" class="button" value="Bilder hinzufügen">
        </div>
        <script type="text/javascript">
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
        </script>
        <?php
    }

    public function save_gallery_images($post_id) {
        if (!isset($_POST['book3r_gallery_images_nonce']) || !wp_verify_nonce($_POST['book3r_gallery_images_nonce'], 'book3r_save_gallery_images')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['book3r_gallery_images'])) {
            update_post_meta($post_id, '_book3r_gallery_images', $_POST['book3r_gallery_images']);
        } else {
            delete_post_meta($post_id, '_book3r_gallery_images');
        }

        if (isset($_POST['book3r_main_image'])) {
            update_post_meta($post_id, '_book3r_main_image', sanitize_text_field($_POST['book3r_main_image']));
        } else {
            delete_post_meta($post_id, '_book3r_main_image');
        }
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook == 'post.php' || $hook == 'post-new.php') {
            global $post;
            if ('book3r_gallery' === $post->post_type) {
                wp_enqueue_media();
                wp_enqueue_script('book3r-gallery', plugin_dir_url(__FILE__) . 'js/book3r-gallery.js', array('jquery'), null, true);
            }
        }
    }

    public function render_gallery_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts, 'book3r_gallery');

        $gallery_id = intval($atts['id']);
        if (!$gallery_id) {
            return 'Galerie ID fehlt.';
        }

        $gallery_images = get_post_meta($gallery_id, '_book3r_gallery_images', true);
        if (!$gallery_images) {
            return 'Keine Bilder in dieser Galerie.';
        }

        $main_image = get_post_meta($gallery_id, '_book3r_main_image', true);
        if (!$main_image && !empty($gallery_images)) {
            $main_image = $gallery_images[0];
        }

        ob_start();
        ?>
        <div class="book3r-gallery">
            <div class="book3r-main-image">
                <img src="<?php echo wp_get_attachment_url($main_image); ?>" alt="">
            </div>
            <div class="book3r-thumbnails">
                <?php foreach ($gallery_images as $image_id): ?>
                    <img src="<?php echo wp_get_attachment_url($image_id); ?>" alt="" style="width: 100px; height: auto;">
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_gallery_block() {
		if (!function_exists('register_block_type')) {
			return;
		}
	
		wp_register_script(
			'book3r-gallery-block',
			plugins_url('js/book3r-gallery-block.js', __FILE__),
			array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'),
			filemtime(plugin_dir_path(__FILE__) . 'js/book3r-gallery-block.js')
		);
	
		register_block_type('book3r/gallery', array(
			'editor_script' => 'book3r-gallery-block',
			'render_callback' => array($this, 'render_gallery_block')
		));
	}
	
	public function render_gallery_block($attributes) {
		if (!isset($attributes['galleryId'])) {
			return 'Galerie ID fehlt.';
		}
	
		return $this->render_gallery_shortcode(array('id' => $attributes['galleryId']));
	}
	
}

new Book3r_Gallery();

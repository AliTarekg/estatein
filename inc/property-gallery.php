<?php
/**
 * Property gallery: a small native meta box using the core media modal.
 *
 * ACF free has no gallery field, and a gallery is the one piece of property
 * content that is a list of images, so it is stored as attachment IDs in the
 * "gallery" post meta. Editors pick and order images in the media modal.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expose the gallery to the REST API, so the block editor saves it with the
 * post (and marks the post as changed when the selection changes).
 */
function estatein_register_gallery_meta() {
	register_post_meta(
		'property',
		'gallery',
		array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'sanitize_callback' => 'estatein_sanitize_gallery',
			'auth_callback'     => function ( $allowed, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
			},
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'integer' ),
				),
			),
		)
	);
}
add_action( 'init', 'estatein_register_gallery_meta' );

/**
 * Keep only positive attachment IDs.
 *
 * @param mixed $value Raw value.
 * @return int[]
 */
function estatein_sanitize_gallery( $value ) {
	return array_values( array_filter( array_map( 'absint', (array) $value ) ) );
}

/**
 * Register the meta box.
 */
function estatein_gallery_meta_box() {
	add_meta_box( 'estatein-gallery', __( 'Property Gallery', 'estatein' ), 'estatein_gallery_meta_box_html', 'property', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'estatein_gallery_meta_box' );

/**
 * Meta box markup.
 *
 * @param WP_Post $post Current property.
 */
function estatein_gallery_meta_box_html( $post ) {
	$ids = estatein_gallery_ids( $post->ID, false );
	wp_nonce_field( 'estatein_gallery', 'estatein_gallery_nonce' );
	?>
	<p class="description"><?php esc_html_e( 'Images shown in the gallery on the property page, in this order. The featured image is used as the first image when the gallery is empty.', 'estatein' ); ?></p>
	<ul class="estatein-gallery" data-estatein-gallery-list style="display:flex;flex-wrap:wrap;gap:8px;margin:12px 0;padding:0;list-style:none">
		<?php foreach ( $ids as $id ) : ?>
			<li><?php echo wp_get_attachment_image( $id, array( 80, 80 ) ); ?></li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="estatein_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" data-estatein-gallery-input>
	<button type="button" class="button" data-estatein-gallery-pick><?php esc_html_e( 'Select images', 'estatein' ); ?></button>
	<button type="button" class="button-link button-link-delete" data-estatein-gallery-clear><?php esc_html_e( 'Clear gallery', 'estatein' ); ?></button>
	<?php
}

/**
 * Save the gallery.
 *
 * @param int $post_id Post ID.
 */
function estatein_gallery_save( $post_id ) {
	if ( ! isset( $_POST['estatein_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estatein_gallery_nonce'] ) ), 'estatein_gallery' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['estatein_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['estatein_gallery'] ) ) : '';
	$ids = estatein_sanitize_gallery( explode( ',', $raw ) );

	if ( $ids ) {
		update_post_meta( $post_id, 'gallery', $ids );
	} else {
		delete_post_meta( $post_id, 'gallery' );
	}
}
add_action( 'save_post_property', 'estatein_gallery_save' );

/**
 * Media modal script, only on the property edit screen.
 *
 * @param string $hook Admin page hook.
 */
function estatein_gallery_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'property' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'estatein-admin-gallery', ESTATEIN_URI . '/assets/js/admin-gallery.js', array( 'jquery' ), filemtime( ESTATEIN_DIR . '/assets/js/admin-gallery.js' ), true );
	wp_localize_script(
		'estatein-admin-gallery',
		'estateinGallery',
		array(
			'title'  => __( 'Property gallery', 'estatein' ),
			'button' => __( 'Use these images', 'estatein' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'estatein_gallery_admin_assets' );

/**
 * Gallery attachment IDs for a property.
 *
 * @param int  $post_id          Property ID.
 * @param bool $include_featured Prepend the featured image when it is not already in the list.
 * @return int[]
 */
function estatein_gallery_ids( $post_id, $include_featured = true ) {
	$ids = get_post_meta( $post_id, 'gallery', true );
	$ids = is_array( $ids ) ? array_values( array_filter( array_map( 'absint', $ids ) ) ) : array();

	// Only keep images that still exist.
	$ids = array_values( array_filter( $ids, 'wp_attachment_is_image' ) );

	$thumb = (int) get_post_thumbnail_id( $post_id );
	if ( $include_featured && $thumb && ! in_array( $thumb, $ids, true ) ) {
		array_unshift( $ids, $thumb );
	}

	return $ids;
}

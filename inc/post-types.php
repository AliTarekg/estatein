<?php
/**
 * Custom post types: Property, Testimonial, FAQ.
 *
 * Kept in the theme for this assessment so the site is self-contained. In a
 * client project these would live in a small site plugin, so content
 * survives a theme change.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build the standard label set for a post type.
 *
 * @param string $singular Singular name.
 * @param string $plural   Plural name.
 * @return array
 */
function estatein_post_type_labels( $singular, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		/* translators: %s: post type singular name. */
		'add_new_item'       => sprintf( __( 'Add New %s', 'estatein' ), $singular ),
		/* translators: %s: post type singular name. */
		'edit_item'          => sprintf( __( 'Edit %s', 'estatein' ), $singular ),
		/* translators: %s: post type singular name. */
		'new_item'           => sprintf( __( 'New %s', 'estatein' ), $singular ),
		/* translators: %s: post type singular name. */
		'view_item'          => sprintf( __( 'View %s', 'estatein' ), $singular ),
		/* translators: %s: post type plural name. */
		'search_items'       => sprintf( __( 'Search %s', 'estatein' ), $plural ),
		/* translators: %s: post type plural name. */
		'not_found'          => sprintf( __( 'No %s found.', 'estatein' ), strtolower( $plural ) ),
		/* translators: %s: post type plural name. */
		'all_items'          => sprintf( __( 'All %s', 'estatein' ), $plural ),
		'menu_name'          => $plural,
	);
}

/**
 * Register the post types.
 */
function estatein_register_post_types() {
	register_post_type(
		'property',
		array(
			'labels'        => estatein_post_type_labels( __( 'Property', 'estatein' ), __( 'Properties', 'estatein' ) ),
			'public'        => true,
			'has_archive'   => 'properties',
			'rewrite'       => array(
				'slug'       => 'properties',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 5,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels'              => estatein_post_type_labels( __( 'Testimonial', 'estatein' ), __( 'Testimonials', 'estatein' ) ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 6,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'faq',
		array(
			'labels'              => estatein_post_type_labels( __( 'FAQ', 'estatein' ), __( 'FAQs', 'estatein' ) ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-editor-help',
			'menu_position'       => 7,
			'supports'            => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'estatein_register_post_types' );

/**
 * Show price and type in the Properties list table so editors can scan listings.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function estatein_property_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['estatein_price'] = __( 'Price', 'estatein' );
			$new['estatein_type']  = __( 'Type', 'estatein' );
		}
	}
	return $new;
}
add_filter( 'manage_property_posts_columns', 'estatein_property_columns' );

/**
 * Fill the custom Properties columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function estatein_property_column_content( $column, $post_id ) {
	if ( 'estatein_price' === $column ) {
		echo esc_html( estatein_format_price( estatein_field( 'price', '', $post_id ) ) );
	} elseif ( 'estatein_type' === $column ) {
		echo esc_html( estatein_field( 'property_type', '', $post_id ) );
	}
}
add_action( 'manage_property_posts_custom_column', 'estatein_property_column_content', 10, 2 );

/**
 * Properties archive: 9 per page (3 x 3 grid), editor-defined order, and the
 * ?type= filter from the archive page.
 *
 * @param WP_Query $query Main query.
 */
function estatein_property_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'property' ) ) {
		return;
	}

	$query->set( 'posts_per_page', 9 );
	$query->set(
		'orderby',
		array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		)
	);

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
	$type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

	if ( '' !== $type ) {
		$query->set(
			'meta_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- single indexed key.
			array(
				array(
					'key'   => 'property_type',
					'value' => $type,
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'estatein_property_archive_query' );

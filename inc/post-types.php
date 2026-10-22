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

	register_post_type(
		'team_member',
		array(
			'labels'              => estatein_post_type_labels( __( 'Team Member', 'estatein' ), __( 'Team', 'estatein' ) ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-groups',
			'menu_position'       => 8,
			'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
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
 * Property types offered in the editor, the archive filter and the request form.
 *
 * @return string[]
 */
function estatein_property_types() {
	return array( 'Villa', 'Apartment', 'Townhouse', 'Cottage', 'Penthouse' );
}

/**
 * Range filters on the Properties archive. Each option maps to a numeric
 * meta range, so the form and the query share one definition.
 *
 * @return array
 */
function estatein_property_range_filters() {
	return array(
		'price' => array(
			'key'     => 'price',
			'label'   => __( 'Pricing Range', 'estatein' ),
			'icon'    => 'cash',
			'options' => array(
				'0-500000'       => __( 'Under $500,000', 'estatein' ),
				'500000-1000000' => __( '$500,000 – $1,000,000', 'estatein' ),
				'1000000-'       => __( 'Over $1,000,000', 'estatein' ),
			),
		),
		'size'  => array(
			'key'     => 'area',
			'label'   => __( 'Property Size', 'estatein' ),
			'icon'    => 'cube',
			'options' => array(
				'0-1500'    => __( 'Under 1,500 sq ft', 'estatein' ),
				'1500-3000' => __( '1,500 – 3,000 sq ft', 'estatein' ),
				'3000-'     => __( 'Over 3,000 sq ft', 'estatein' ),
			),
		),
		// "built", not "year": "year" is a public WordPress query var and would turn the request into a date archive.
		'built' => array(
			'key'     => 'year_built',
			'label'   => __( 'Build Year', 'estatein' ),
			'icon'    => 'calendar',
			'options' => array(
				'0-1999'    => __( 'Before 2000', 'estatein' ),
				'2000-2014' => __( '2000 – 2014', 'estatein' ),
				'2015-'     => __( '2015 or newer', 'estatein' ),
			),
		),
	);
}

/**
 * Distinct locations of published properties, for the Location filter.
 *
 * @return string[]
 */
function estatein_property_locations() {
	global $wpdb;

	$locations = wp_cache_get( 'estatein_locations', 'estatein' );
	if ( false === $locations ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- cached below; no API returns distinct meta values.
		$locations = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = %s AND pm.meta_value <> '' AND p.post_type = %s AND p.post_status = 'publish'
				ORDER BY pm.meta_value ASC",
				'location',
				'property'
			)
		);
		wp_cache_set( 'estatein_locations', $locations, 'estatein', HOUR_IN_SECONDS );
	}

	return $locations;
}

/**
 * Current archive filter values, sanitised. Read-only GET parameters.
 *
 * @return array{q:string,location:string,type:string,price:string,size:string,built:string}
 */
function estatein_property_filter_values() {
	$values = array();
	foreach ( array( 'q', 'location', 'type', 'price', 'size', 'built' ) as $key ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
		$values[ $key ] = isset( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : '';
	}
	return $values;
}

/**
 * Properties archive: 9 per page (3 x 3 grid), editor-defined order, keyword
 * search and the location / type / price / size / year filters.
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

	$filters    = estatein_property_filter_values();
	$meta_query = array();

	// "q" rather than "s": a real "s" would turn the archive into a site-wide search page.
	if ( '' !== $filters['q'] ) {
		$query->set( 's', $filters['q'] );
	}

	if ( '' !== $filters['location'] ) {
		$meta_query[] = array(
			'key'   => 'location',
			'value' => $filters['location'],
		);
	}

	if ( in_array( $filters['type'], estatein_property_types(), true ) ) {
		$meta_query[] = array(
			'key'   => 'property_type',
			'value' => $filters['type'],
		);
	}

	foreach ( estatein_property_range_filters() as $param => $filter ) {
		if ( ! isset( $filter['options'][ $filters[ $param ] ] ) ) {
			continue;
		}
		list( $min, $max ) = array_pad( explode( '-', $filters[ $param ] ), 2, '' );

		$meta_query[] = '' === $max
			? array(
				'key'     => $filter['key'],
				'value'   => (int) $min,
				'compare' => '>=',
				'type'    => 'NUMERIC',
			)
			: array(
				'key'     => $filter['key'],
				'value'   => array( (int) $min, (int) $max ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			);
	}

	if ( $meta_query ) {
		$query->set( 'meta_query', $meta_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small listing, user-driven filters.
	}
}
add_action( 'pre_get_posts', 'estatein_property_archive_query' );

/**
 * Keep the Location filter fresh when a property changes.
 */
function estatein_flush_location_cache() {
	wp_cache_delete( 'estatein_locations', 'estatein' );
}
add_action( 'save_post_property', 'estatein_flush_location_cache' );

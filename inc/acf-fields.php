<?php
/**
 * ACF field groups, registered in code.
 *
 * Registering in PHP (instead of the ACF UI) keeps fields under version
 * control and makes them deploy with the theme. Only features available in
 * free ACF are used (group, link, image, select, number), no Pro repeaters.
 *
 * Every template reads these through estatein_field() with a design default,
 * so the site still renders if ACF is not active.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Small builder to keep field definitions readable and keys unique.
 *
 * @param string $prefix Key prefix (group or parent field).
 * @param string $name   Field name.
 * @param string $label  Field label.
 * @param string $type   ACF field type.
 * @param array  $extra  Extra ACF settings.
 * @return array
 */
function estatein_acf( $prefix, $name, $label, $type = 'text', $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_estatein_' . $prefix . '_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

/**
 * Register all field groups.
 */
function estatein_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/*
	 * Property details.
	 */
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_property',
			'title'    => __( 'Property Details', 'estatein' ),
			'position' => 'side',
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'property' ) ) ),
			'fields'   => array(
				estatein_acf( 'property', 'price', __( 'Price (USD)', 'estatein' ), 'number', array( 'min' => 0, 'prepend' => '$' ) ),
				estatein_acf( 'property', 'bedrooms', __( 'Bedrooms', 'estatein' ), 'number', array( 'min' => 0 ) ),
				estatein_acf( 'property', 'bathrooms', __( 'Bathrooms', 'estatein' ), 'number', array( 'min' => 0, 'step' => 0.5 ) ),
				estatein_acf(
					'property',
					'property_type',
					__( 'Property type', 'estatein' ),
					'select',
					array(
						'choices'       => array(
							'Villa'     => 'Villa',
							'Apartment' => 'Apartment',
							'Townhouse' => 'Townhouse',
							'Cottage'   => 'Cottage',
							'Penthouse' => 'Penthouse',
						),
						'default_value' => 'Villa',
					)
				),
				estatein_acf( 'property', 'location', __( 'Location', 'estatein' ) ),
				estatein_acf( 'property', 'featured', __( 'Show in "Featured Properties"', 'estatein' ), 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
			),
		)
	);

	/*
	 * Testimonial details. Title = headline, editor = quote, featured image = avatar.
	 */
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_testimonial',
			'title'    => __( 'Client', 'estatein' ),
			'position' => 'side',
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'testimonial' ) ) ),
			'fields'   => array(
				estatein_acf( 'testimonial', 'rating', __( 'Rating (1–5)', 'estatein' ), 'number', array( 'min' => 1, 'max' => 5, 'default_value' => 5 ) ),
				estatein_acf( 'testimonial', 'client_name', __( 'Client name', 'estatein' ) ),
				estatein_acf( 'testimonial', 'client_location', __( 'Client location', 'estatein' ), 'text', array( 'placeholder' => 'USA, California' ) ),
			),
		)
	);

	/*
	 * Home page sections. Shown only on the page set as the static front page.
	 */
	$stat_fields = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: stat number. */
		$stat_fields[] = estatein_acf( 'hero', 'stat_' . $i . '_value', sprintf( __( 'Stat %d value', 'estatein' ), $i ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) );
		/* translators: %d: stat number. */
		$stat_fields[] = estatein_acf( 'hero', 'stat_' . $i . '_label', sprintf( __( 'Stat %d label', 'estatein' ), $i ), 'text', array( 'wrapper' => array( 'width' => 60 ) ) );
	}

	$feature_fields = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$feature_fields[] = estatein_acf(
			'features',
			'feature_' . $i,
			/* translators: %d: feature number. */
			sprintf( __( 'Feature %d', 'estatein' ), $i ),
			'group',
			array(
				'layout'     => 'row',
				'sub_fields' => array(
					estatein_acf( 'feature_' . $i, 'title', __( 'Title', 'estatein' ) ),
					estatein_acf( 'feature_' . $i, 'link', __( 'Link', 'estatein' ), 'link', array( 'return_format' => 'array' ) ),
				),
			)
		);
	}

	$section_group = function ( $name, $label ) {
		return estatein_acf(
			'home',
			$name,
			$label,
			'group',
			array(
				'layout'     => 'block',
				'sub_fields' => array(
					estatein_acf( $name, 'title', __( 'Title', 'estatein' ) ),
					estatein_acf( $name, 'text', __( 'Intro', 'estatein' ), 'textarea', array( 'rows' => 2 ) ),
					estatein_acf( $name, 'button', __( 'Button', 'estatein' ), 'link', array( 'return_format' => 'array' ) ),
				),
			)
		);
	};

	acf_add_local_field_group(
		array(
			'key'            => 'group_estatein_home',
			'title'          => __( 'Home Page Sections', 'estatein' ),
			'location'       => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
			'hide_on_screen' => array( 'the_content' ),
			'fields'         => array(
				estatein_acf( 'home', 'tab_hero', __( 'Hero', 'estatein' ), 'tab' ),
				estatein_acf(
					'home',
					'hero',
					__( 'Hero', 'estatein' ),
					'group',
					array(
						'layout'     => 'block',
						'sub_fields' => array_merge(
							array(
								estatein_acf( 'hero', 'title', __( 'Title', 'estatein' ) ),
								estatein_acf( 'hero', 'text', __( 'Intro', 'estatein' ), 'textarea', array( 'rows' => 2 ) ),
								estatein_acf( 'hero', 'secondary_button', __( 'Outline button', 'estatein' ), 'link', array( 'return_format' => 'array', 'wrapper' => array( 'width' => 50 ) ) ),
								estatein_acf( 'hero', 'primary_button', __( 'Primary button', 'estatein' ), 'link', array( 'return_format' => 'array', 'wrapper' => array( 'width' => 50 ) ) ),
								estatein_acf( 'hero', 'image', __( 'Hero image', 'estatein' ), 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ),
							),
							$stat_fields
						),
					)
				),
				estatein_acf( 'home', 'tab_features', __( 'Features', 'estatein' ), 'tab' ),
				estatein_acf(
					'home',
					'features',
					__( 'Feature cards', 'estatein' ),
					'group',
					array(
						'layout'     => 'block',
						'sub_fields' => $feature_fields,
					)
				),
				estatein_acf( 'home', 'tab_sections', __( 'Section headings', 'estatein' ), 'tab' ),
				$section_group( 'properties_section', __( 'Featured Properties', 'estatein' ) ),
				$section_group( 'testimonials_section', __( 'Testimonials', 'estatein' ) ),
				$section_group( 'faq_section', __( 'FAQs', 'estatein' ) ),
			),
		)
	);
}
add_action( 'acf/init', 'estatein_register_acf_fields' );

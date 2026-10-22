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
				estatein_acf( 'property', 'area', __( 'Area (sq ft)', 'estatein' ), 'number', array( 'min' => 0 ) ),
				estatein_acf( 'property', 'year_built', __( 'Year built', 'estatein' ), 'number', array( 'min' => 1800, 'max' => 2100 ) ),
				estatein_acf(
					'property',
					'property_type',
					__( 'Property type', 'estatein' ),
					'select',
					array(
						'choices'       => array_combine( estatein_property_types(), estatein_property_types() ),
						'default_value' => 'Villa',
					)
				),
				estatein_acf( 'property', 'location', __( 'Location', 'estatein' ), 'text', array( 'placeholder' => 'Malibu, California' ) ),
				estatein_acf( 'property', 'tagline', __( 'Category tagline', 'estatein' ), 'text', array( 'placeholder' => 'Coastal Escapes - Where Waves Beckon' ) ),
				estatein_acf( 'property', 'featured', __( 'Show in "Featured Properties"', 'estatein' ), 'true_false', array( 'ui' => 1, 'default_value' => 1 ) ),
			),
		)
	);

	/*
	 * Features list and pricing breakdown, shown on the property page.
	 * Empty pricing fields fall back to estimates based on the price.
	 */
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_property_more',
			'title'    => __( 'Features & Pricing', 'estatein' ),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'property' ) ) ),
			'fields'   => array(
				estatein_acf( 'property', 'features', __( 'Key features and amenities', 'estatein' ), 'textarea', array( 'rows' => 6, 'instructions' => __( 'One feature per line.', 'estatein' ) ) ),
				estatein_acf( 'property', 'pricing_transfer_tax', __( 'Property transfer tax ($)', 'estatein' ), 'number', array( 'min' => 0, 'wrapper' => array( 'width' => 33 ) ) ),
				estatein_acf( 'property', 'pricing_legal_fees', __( 'Legal fees ($)', 'estatein' ), 'number', array( 'min' => 0, 'wrapper' => array( 'width' => 33 ) ) ),
				estatein_acf( 'property', 'pricing_inspection', __( 'Home inspection ($)', 'estatein' ), 'number', array( 'min' => 0, 'wrapper' => array( 'width' => 33 ) ) ),
				estatein_acf( 'property', 'pricing_insurance', __( 'Property insurance, annual ($)', 'estatein' ), 'number', array( 'min' => 0, 'wrapper' => array( 'width' => 33 ) ) ),
				estatein_acf( 'property', 'pricing_property_tax', __( 'Property taxes, monthly ($)', 'estatein' ), 'number', array( 'min' => 0, 'wrapper' => array( 'width' => 33 ) ) ),
				estatein_acf( 'property', 'pricing_hoa', __( 'HOA fee, monthly ($)', 'estatein' ), 'number', array( 'min' => 0, 'wrapper' => array( 'width' => 33 ) ) ),
				estatein_acf( 'property', 'pricing_down_payment', __( 'Down payment (%)', 'estatein' ), 'number', array( 'min' => 0, 'max' => 100, 'placeholder' => 20, 'wrapper' => array( 'width' => 33 ) ) ),
			),
		)
	);

	/*
	 * Team member. Title = name, featured image = photo.
	 */
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_team',
			'title'    => __( 'Team Member', 'estatein' ),
			'position' => 'side',
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'team_member' ) ) ),
			'fields'   => array(
				estatein_acf( 'team', 'role', __( 'Role', 'estatein' ) ),
				estatein_acf( 'team', 'email', __( 'Email (for "Say Hello")', 'estatein' ), 'email' ),
				estatein_acf( 'team', 'twitter_url', __( 'X / Twitter URL', 'estatein' ), 'url', array( 'instructions' => __( 'Empty: links to the company account set in the Customizer.', 'estatein' ) ) ),
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

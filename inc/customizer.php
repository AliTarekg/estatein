<?php
/**
 * Site-wide options in Appearance > Customize > Estatein.
 *
 * These pieces repeat on every page (announcement banner, closing CTA,
 * footer, social links), so they belong to the site rather than to a page.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults, taken from the Figma copy.
 *
 * @return array
 */
function estatein_mod_defaults() {
	return array(
		'banner_enabled' => true,
		'banner_text'    => __( '✨Discover Your Dream Property with Estatein', 'estatein' ),
		'banner_label'   => __( 'Learn More', 'estatein' ),
		'banner_url'     => '/properties/',
		'contact_label'  => __( 'Contact Us', 'estatein' ),
		'contact_url'    => '/contact-us/',
		'cta_title'      => __( 'Start Your Real Estate Journey Today', 'estatein' ),
		'cta_text'       => __( "Your dream property is just a click away. Whether you're looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.", 'estatein' ),
		'cta_label'      => __( 'Explore Properties', 'estatein' ),
		'cta_url'        => '/properties/',
		'copyright'      => __( '©2023 Estatein. All Rights Reserved.', 'estatein' ),
		'terms_url'      => '/terms-and-conditions/',
		'contact_email'  => 'info@estatein.com',
		'contact_phone'  => '+1 (123) 456-7890',
		'contact_hq'     => __( 'Main Headquarters', 'estatein' ),
		'contact_hq_url' => '/contact-us/#offices',
		'social_facebook'  => '#',
		'social_linkedin'  => '#',
		'social_twitter'   => '#',
		'social_youtube'   => '#',
		'social_instagram' => '#',
	);
}

/**
 * Read a theme option with its default.
 *
 * Relative URLs ("/properties/") are resolved against the site URL so the
 * defaults work on any install, including a subdirectory.
 *
 * @param string $key Option key.
 * @return mixed
 */
function estatein_mod( $key ) {
	$defaults = estatein_mod_defaults();
	$value    = get_theme_mod( 'estatein_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );

	if ( is_string( $value ) && '_url' === substr( $key, -4 ) && 0 === strpos( $value, '/' ) ) {
		$value = home_url( $value );
	}

	return $value;
}

/**
 * Register Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function estatein_customize_register( $wp_customize ) {
	$defaults = estatein_mod_defaults();

	$wp_customize->add_panel(
		'estatein',
		array(
			'title'    => __( 'Estatein', 'estatein' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'estatein_header' => array(
			'title'  => __( 'Header & Banner', 'estatein' ),
			'fields' => array(
				'banner_enabled' => array( __( 'Show announcement banner', 'estatein' ), 'checkbox' ),
				'banner_text'    => array( __( 'Banner text', 'estatein' ), 'text' ),
				'banner_label'   => array( __( 'Banner link label', 'estatein' ), 'text' ),
				'banner_url'     => array( __( 'Banner link URL', 'estatein' ), 'url' ),
				'contact_label'  => array( __( 'Header button label', 'estatein' ), 'text' ),
				'contact_url'    => array( __( 'Header button URL', 'estatein' ), 'url' ),
			),
		),
		'estatein_cta'    => array(
			'title'  => __( 'Closing Call to Action', 'estatein' ),
			'fields' => array(
				'cta_title' => array( __( 'Title', 'estatein' ), 'text' ),
				'cta_text'  => array( __( 'Text', 'estatein' ), 'textarea' ),
				'cta_label' => array( __( 'Button label', 'estatein' ), 'text' ),
				'cta_url'   => array( __( 'Button URL', 'estatein' ), 'url' ),
			),
		),
		'estatein_contact' => array(
			'title'  => __( 'Contact Details', 'estatein' ),
			'fields' => array(
				'contact_email'  => array( __( 'Email', 'estatein' ), 'text' ),
				'contact_phone'  => array( __( 'Phone', 'estatein' ), 'text' ),
				'contact_hq'     => array( __( 'Headquarters label', 'estatein' ), 'text' ),
				'contact_hq_url' => array( __( 'Headquarters link', 'estatein' ), 'url' ),
			),
		),
		'estatein_footer' => array(
			'title'  => __( 'Footer & Social', 'estatein' ),
			'fields' => array(
				'copyright'        => array( __( 'Copyright line', 'estatein' ), 'text' ),
				'terms_url'        => array( __( 'Terms & Conditions URL', 'estatein' ), 'url' ),
				'social_facebook'  => array( __( 'Facebook URL', 'estatein' ), 'url' ),
				'social_linkedin'  => array( __( 'LinkedIn URL', 'estatein' ), 'url' ),
				'social_twitter'   => array( __( 'X / Twitter URL', 'estatein' ), 'url' ),
				'social_youtube'   => array( __( 'YouTube URL', 'estatein' ), 'url' ),
				'social_instagram' => array( __( 'Instagram URL (contact page)', 'estatein' ), 'url' ),
			),
		),
	);

	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title' => $section['title'],
				'panel' => 'estatein',
			)
		);

		foreach ( $section['fields'] as $key => $field ) {
			list( $label, $type ) = $field;

			$sanitize = 'sanitize_text_field';
			if ( 'checkbox' === $type ) {
				$sanitize = 'wp_validate_boolean';
			} elseif ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			} elseif ( 'url' === $type ) {
				$sanitize = 'estatein_sanitize_url';
			}

			$wp_customize->add_setting(
				'estatein_' . $key,
				array(
					'default'           => $defaults[ $key ],
					'sanitize_callback' => $sanitize,
				)
			);

			$wp_customize->add_control(
				'estatein_' . $key,
				array(
					'label'   => $label,
					'section' => $section_id,
					'type'    => 'url' === $type ? 'text' : $type,
				)
			);
		}
	}
}
add_action( 'customize_register', 'estatein_customize_register' );

/**
 * Allow full URLs, site-relative paths ("/contact-us/") and "#".
 *
 * @param string $value Raw value.
 * @return string
 */
function estatein_sanitize_url( $value ) {
	$value = trim( (string) $value );

	if ( '#' === $value || 0 === strpos( $value, '/' ) ) {
		return sanitize_text_field( $value );
	}

	return esc_url_raw( $value );
}

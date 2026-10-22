<?php
/**
 * Template helpers.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read an ACF field with a fallback.
 *
 * The theme must render correctly even before ACF is installed or before
 * an editor fills a field, so every field read goes through here with a
 * sensible default taken from the Figma design.
 *
 * @param string     $name    Field name (dot notation reaches into groups, e.g. "hero.title").
 * @param mixed      $default Value used when the field is empty or ACF is missing.
 * @param int|string $post_id Post ID, "option", or false for the current post.
 * @return mixed
 */
function estatein_field( $name, $default = '', $post_id = false ) {
	$parts = explode( '.', $name );
	$first = array_shift( $parts );

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $first, $post_id );
	} elseif ( empty( $parts ) && ( $post_id || get_the_ID() ) && is_numeric( $post_id ? $post_id : get_the_ID() ) ) {
		// Without ACF, simple fields are still plain post meta: read them directly.
		$value = get_post_meta( (int) ( $post_id ? $post_id : get_the_ID() ), $first, true );
	} else {
		return $default;
	}

	foreach ( $parts as $key ) {
		$value = ( is_array( $value ) && isset( $value[ $key ] ) ) ? $value[ $key ] : null;
	}

	if ( null === $value || '' === $value || false === $value || array() === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Normalise an ACF link field into url/title/target, with defaults.
 *
 * @param mixed  $link          ACF link array (or empty).
 * @param string $default_title Label used when the field is empty.
 * @param string $default_url   URL used when the field is empty.
 * @return array{url:string,title:string,target:string}
 */
function estatein_link( $link, $default_title, $default_url ) {
	$link = is_array( $link ) ? $link : array();

	return array(
		'url'    => ! empty( $link['url'] ) ? $link['url'] : $default_url,
		'title'  => ! empty( $link['title'] ) ? $link['title'] : $default_title,
		'target' => ! empty( $link['target'] ) ? $link['target'] : '',
	);
}

/**
 * Print link attributes (href, and target/rel for new tabs).
 *
 * @param array $link Normalised link from estatein_link().
 */
function estatein_link_attrs( $link ) {
	printf( 'href="%s"', esc_url( $link['url'] ) );
	if ( '_blank' === $link['target'] ) {
		echo ' target="_blank" rel="noopener noreferrer"';
	}
}

/**
 * Return an inline SVG icon from assets/icons.
 *
 * Inline SVGs inherit currentColor and avoid extra HTTP requests. Each icon
 * file is read once per request.
 *
 * @param string $name  Icon file name without extension.
 * @param string $class Extra class names.
 * @return string Safe SVG markup, or an empty string if the icon is missing.
 */
function estatein_icon( $name, $class = '' ) {
	static $cache = array();

	$name = sanitize_file_name( $name );

	if ( ! isset( $cache[ $name ] ) ) {
		$path            = ESTATEIN_DIR . '/assets/icons/' . $name . '.svg';
		$cache[ $name ]  = is_readable( $path ) ? (string) file_get_contents( $path ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	if ( '' === $cache[ $name ] ) {
		return '';
	}

	$classes = trim( 'icon icon-' . $name . ' ' . $class );

	return preg_replace( '/<svg\b/', '<svg class="' . esc_attr( $classes ) . '" aria-hidden="true" focusable="false"', $cache[ $name ], 1 );
}

/**
 * Print an icon (escaped by construction, see estatein_icon()).
 *
 * @param string $name  Icon name.
 * @param string $class Extra class names.
 */
function estatein_the_icon( $name, $class = '' ) {
	echo estatein_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-owned SVG files.
}

/**
 * URL of a file in assets/images, or an empty string if it does not exist.
 *
 * @param string $file File name inside assets/images.
 * @return string
 */
function estatein_asset_url( $file ) {
	$path = ESTATEIN_DIR . '/assets/images/' . $file;

	return file_exists( $path ) ? ESTATEIN_URI . '/assets/images/' . $file : '';
}

/**
 * Render an image that may come from the media library (ID / ACF array) or
 * from the theme's assets folder (file name), with lazy loading by default.
 *
 * @param int|array|string $image   Attachment ID, ACF image array, or asset file name.
 * @param string           $size    Registered image size for attachments.
 * @param array            $attrs   Extra attributes (alt, class, loading, fetchpriority...).
 * @return string HTML or empty string.
 */
function estatein_image( $image, $size = 'large', $attrs = array() ) {
	$attrs = wp_parse_args(
		$attrs,
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	if ( is_array( $image ) && isset( $image['ID'] ) ) {
		$image = (int) $image['ID'];
	}

	if ( is_numeric( $image ) && (int) $image > 0 ) {
		return wp_get_attachment_image( (int) $image, $size, false, $attrs );
	}

	if ( is_string( $image ) && '' !== $image ) {
		$url = estatein_asset_url( $image );
		if ( '' === $url ) {
			return '';
		}

		$html = '<img src="' . esc_url( $url ) . '"';
		foreach ( $attrs as $key => $value ) {
			$html .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $html . '>';
	}

	return '';
}

/**
 * Format a numeric price for display: 550000 -> $550,000.
 *
 * @param mixed $price Raw price.
 * @return string
 */
function estatein_format_price( $price ) {
	if ( '' === $price || null === $price ) {
		return '';
	}

	if ( ! is_numeric( $price ) ) {
		return (string) $price;
	}

	return '$' . number_format_i18n( (float) $price );
}

/**
 * Zero-padded counter used by carousels: 1 -> "01".
 *
 * @param int $number Number.
 * @return string
 */
function estatein_pad( $number ) {
	return str_pad( (string) absint( $number ), 2, '0', STR_PAD_LEFT );
}

/**
 * The four feature cards shown under the home hero and on the Services page.
 * Titles and links are edited once, on the Home page (ACF "Features" tab).
 *
 * @return array[] Items with icon, title and url.
 */
function estatein_feature_items() {
	$front    = (int) get_option( 'page_on_front' );
	$services = home_url( '/services/' );
	$defaults = array(
		1 => array( __( 'Find Your Dream Home', 'estatein' ), 'feature-home', get_post_type_archive_link( 'property' ) ),
		2 => array( __( 'Unlock Property Value', 'estatein' ), 'feature-value', $services . '#valuation' ),
		3 => array( __( 'Effortless Property Management', 'estatein' ), 'feature-management', $services . '#management' ),
		4 => array( __( 'Smart Investments, Informed Decisions', 'estatein' ), 'feature-investment', $services . '#investments' ),
	);

	$items = array();
	foreach ( $defaults as $n => $default ) {
		$link    = estatein_link( $front ? estatein_field( 'features.feature_' . $n . '.link', array(), $front ) : array(), '', $default[2] );
		$items[] = array(
			'icon'  => $default[1],
			'title' => $front ? estatein_field( 'features.feature_' . $n . '.title', $default[0], $front ) : $default[0],
			'url'   => $link['url'],
		);
	}

	return $items;
}

/**
 * Format a whole number with an optional unit: 2500 -> "2,500 Square Feet".
 *
 * @param mixed  $number Number.
 * @param string $unit   Unit label.
 * @return string
 */
function estatein_format_number( $number, $unit = '' ) {
	if ( ! is_numeric( $number ) ) {
		return (string) $number;
	}
	return trim( number_format_i18n( (float) $number ) . ' ' . $unit );
}

/**
 * Render the shared section heading (sparkle mark, title, intro, CTA button).
 *
 * @param array $args {
 *     @type string $id         Heading element ID (used by aria-labelledby).
 *     @type string $title      Section title.
 *     @type string $text       Intro paragraph.
 *     @type string $button     Button label.
 *     @type string $button_url Button URL.
 * }
 */
function estatein_section_heading( $args ) {
	get_template_part( 'template-parts/components/section-heading', null, $args );
}

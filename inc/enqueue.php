<?php
/**
 * Styles, scripts and front-end performance tweaks.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pick the minified build in production, the readable source when debugging.
 *
 * @param string $relative Path relative to the theme, without ".min".
 * @return string Relative path to load.
 */
function estatein_asset_path( $relative ) {
	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$min   = preg_replace( '/\.(css|js)$/', '.min.$1', $relative );

	return ( ! $debug && file_exists( ESTATEIN_DIR . '/' . $min ) ) ? $min : $relative;
}

/**
 * Enqueue front-end assets.
 */
function estatein_enqueue_assets() {
	// Urbanist is the only typeface in the design: load just the weights used.
	wp_enqueue_style(
		'estatein-fonts',
		'https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts versions itself.
	);

	$css = estatein_asset_path( 'assets/css/main.css' );
	wp_enqueue_style( 'estatein-main', ESTATEIN_URI . '/' . $css, array( 'estatein-fonts' ), filemtime( ESTATEIN_DIR . '/' . $css ) );

	$js = estatein_asset_path( 'assets/js/main.js' );
	wp_enqueue_script(
		'estatein-main',
		ESTATEIN_URI . '/' . $js,
		array(),
		filemtime( ESTATEIN_DIR . '/' . $js ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'estatein-main',
		'estateinI18n',
		array(
			'of'       => __( 'of', 'estatein' ),
			'readMore' => __( 'Read More', 'estatein' ),
			'readLess' => __( 'Read Less', 'estatein' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'estatein_enqueue_assets' );

/**
 * Preconnect to Google Fonts so the font CSS and files start downloading early.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function estatein_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'estatein_resource_hints', 10, 2 );

/**
 * Remove front-end weight this theme does not use.
 */
function estatein_trim_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
}
add_action( 'init', 'estatein_trim_head' );

/**
 * The theme ships its own styles and no block-based templates, so the global
 * block-library CSS is only needed on regular posts/pages that use blocks.
 */
function estatein_dequeue_block_css() {
	if ( is_front_page() || is_post_type_archive( 'property' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'estatein_dequeue_block_css', 100 );

<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function estatein_setup() {
	load_theme_textdomain( 'estatein', ESTATEIN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'estatein' ),
			'footer'  => __( 'Footer columns (top-level items become column headings)', 'estatein' ),
		)
	);

	// Card image ratio from Figma: 432 x 318.
	add_image_size( 'estatein-card', 864, 636, true );
	// Hero building, right half of a 1920px frame.
	add_image_size( 'estatein-hero', 1000, 814, true );
	// Testimonial avatar, displayed at 60px.
	add_image_size( 'estatein-avatar', 120, 120, true );
}
add_action( 'after_setup_theme', 'estatein_setup' );

/**
 * Default nav menu fallback, so the header matches the design before a menu
 * is assigned in Appearance > Menus.
 */
function estatein_primary_menu_fallback() {
	$properties_url = get_post_type_archive_link( 'property' );

	$items = array(
		array( home_url( '/' ), __( 'Home', 'estatein' ), is_front_page() ),
		array( home_url( '/about-us/' ), __( 'About Us', 'estatein' ), is_page( 'about-us' ) ),
		array( $properties_url ? $properties_url : home_url( '/properties/' ), __( 'Properties', 'estatein' ), is_post_type_archive( 'property' ) || is_singular( 'property' ) ),
		array( home_url( '/services/' ), __( 'Services', 'estatein' ), is_page( 'services' ) ),
	);

	echo '<ul id="primary-menu" class="nav__list">';
	foreach ( $items as $item ) {
		list( $url, $label, $is_current ) = $item;
		printf(
			'<li class="menu-item%1$s"><a class="nav__link" href="%2$s"%3$s>%4$s</a></li>',
			$is_current ? ' current-menu-item' : '',
			esc_url( $url ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Add a class to menu links so the active "pill" style can target them.
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args Menu args.
 * @return array
 */
function estatein_menu_link_attributes( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$atts['class'] = 'nav__link';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'estatein_menu_link_attributes', 10, 3 );

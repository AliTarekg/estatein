<?php
/**
 * Estatein theme bootstrap.
 *
 * Keeps functions.php small: each concern lives in its own file under /inc.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

define( 'ESTATEIN_VERSION', '1.1.0' );
define( 'ESTATEIN_DIR', get_template_directory() );
define( 'ESTATEIN_URI', get_template_directory_uri() );

$estatein_includes = array(
	'inc/helpers.php',     // Small template helpers (icons, fields, formatting).
	'inc/setup.php',       // Theme supports, menus, image sizes.
	'inc/enqueue.php',     // Styles, scripts, fonts, performance tweaks.
	'inc/post-types.php',  // Property, Testimonial, FAQ and Team post types; archive filters.
	'inc/acf-fields.php',  // ACF field groups registered in code.
	'inc/page-sections.php', // Editable sections of the About, Services and Contact templates.
	'inc/property-gallery.php', // Property gallery meta box (core media modal).
	'inc/property-details.php', // Property features and pricing breakdown.
	'inc/customizer.php',  // Site-wide options: banner, contact details, socials, footer.
	'inc/forms.php',       // Contact, property inquiry, property request and newsletter forms.
	'inc/seo.php',         // Meta description, Open Graph, schema.
	'inc/demo-content.php', // One-time sample content on theme activation.
);

foreach ( $estatein_includes as $estatein_file ) {
	require_once ESTATEIN_DIR . '/' . $estatein_file;
}

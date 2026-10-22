<?php
/**
 * Basic on-page SEO: meta description, Open Graph tags, structured data.
 *
 * Steps aside automatically when a dedicated SEO plugin (Yoast, Rank Math,
 * SEOPress, AIOSEO) is active, to avoid duplicate tags.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a full SEO plugin is handling meta tags.
 *
 * @return bool
 */
function estatein_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Description for the current view.
 *
 * @return string
 */
function estatein_meta_description() {
	if ( is_singular() ) {
		$post = get_queried_object();
		// Space after block-level closing tags so paragraphs do not run together.
		$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( preg_replace( '#</(p|h[1-6]|li)>#i', '$0 ', strip_shortcodes( $post->post_content ) ) );

		// Designed templates keep their copy in section fields, not in the editor.
		$intros = array(
			'page-about.php'    => 'journey',
			'page-services.php' => 'intro',
			'page-contact.php'  => 'intro',
		);
		$template = get_page_template_slug( $post );
		if ( '' === trim( $text ) && isset( $intros[ $template ] ) ) {
			$section = estatein_section( $intros[ $template ], $post->ID, $template );
			$text    = isset( $section['text'] ) ? $section['text'] : '';
		}
	} elseif ( is_post_type_archive( 'property' ) ) {
		$text = __( 'Browse homes, apartments and investment properties for sale with Estatein.', 'estatein' );
	} else {
		$text = get_bloginfo( 'description' );
	}

	if ( is_front_page() ) {
		$text = estatein_field( 'hero.text', __( 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.', 'estatein' ), get_option( 'page_on_front' ) );
	}

	if ( '' === trim( (string) $text ) ) {
		$text = get_bloginfo( 'description' );
	}

	return wp_trim_words( (string) $text, 28, '…' );
}

/**
 * Print meta description and Open Graph tags.
 */
function estatein_print_meta_tags() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}

	$description = estatein_meta_description();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image       = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : estatein_asset_url( 'hero-building.webp' );

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( 'property' ) ? 'product' : 'website' );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}

	// Core prints canonicals for singular views only. Filtered listings point
	// to the unfiltered page so search engines index one URL per page.
	if ( is_post_type_archive( 'property' ) ) {
		$paged = max( 1, (int) get_query_var( 'paged' ) );
		$link  = get_post_type_archive_link( 'property' );
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $paged > 1 ? trailingslashit( $link ) . user_trailingslashit( 'page/' . $paged, 'paged' ) : $link ) );
	}
}
add_action( 'wp_head', 'estatein_print_meta_tags', 1 );

/**
 * JSON-LD structured data: the business on the home page, the listing on a property page.
 */
function estatein_print_schema() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}

	$schema = null;

	if ( is_front_page() ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'RealEstateAgent',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
		);
	} elseif ( is_singular( 'property' ) ) {
		$id     = get_queried_object_id();
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'RealEstateListing',
			'name'        => get_the_title( $id ),
			'url'         => get_permalink( $id ),
			'description' => estatein_meta_description(),
		);

		if ( has_post_thumbnail( $id ) ) {
			$schema['image'] = get_the_post_thumbnail_url( $id, 'large' );
		}

		$price = estatein_field( 'price', '', $id );
		if ( is_numeric( $price ) ) {
			$schema['offers'] = array(
				'@type'         => 'Offer',
				'price'         => (float) $price,
				'priceCurrency' => 'USD',
			);
		}
	}

	if ( $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'estatein_print_schema', 20 );

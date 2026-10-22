<?php
/**
 * Search form.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form newsletter" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="visually-hidden" for="<?php echo esc_attr( $estatein_search_id ); ?>"><?php esc_html_e( 'Search for:', 'estatein' ); ?></label>
	<input class="newsletter__input" id="<?php echo esc_attr( $estatein_search_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'estatein' ); ?>">
	<button class="newsletter__submit" type="submit" aria-label="<?php esc_attr_e( 'Search', 'estatein' ); ?>"><?php estatein_the_icon( 'arrow-right' ); ?></button>
</form>

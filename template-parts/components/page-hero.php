<?php
/**
 * Intro band at the top of inner pages (title + optional intro), with the
 * Figma line artwork in the background.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_title = isset( $args['title'] ) ? $args['title'] : get_the_title();
$estatein_text  = isset( $args['text'] ) ? $args['text'] : '';
$estatein_class = isset( $args['class'] ) ? ' ' . $args['class'] : '';
?>
<section class="page-hero<?php echo esc_attr( $estatein_class ); ?>" aria-labelledby="page-title">
	<div class="container">
		<h1 class="page-hero__title" id="page-title"><?php echo esc_html( $estatein_title ); ?></h1>
		<?php if ( $estatein_text ) : ?>
			<p class="page-hero__intro"><?php echo esc_html( $estatein_text ); ?></p>
		<?php endif; ?>
	</div>
</section>

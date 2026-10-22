<?php
/**
 * Closing call to action shown above the footer on every page.
 * Content is managed in Appearance > Customize > Estatein > Closing Call to Action.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cta" aria-labelledby="cta-title">
	<span class="cta__pattern cta__pattern--left" aria-hidden="true"></span>
	<span class="cta__pattern cta__pattern--right" aria-hidden="true"></span>
	<div class="container cta__inner">
		<div class="cta__text">
			<h2 class="cta__title" id="cta-title"><?php echo esc_html( estatein_mod( 'cta_title' ) ); ?></h2>
			<p class="cta__intro"><?php echo esc_html( estatein_mod( 'cta_text' ) ); ?></p>
		</div>
		<a class="btn btn--primary btn--lg cta__btn" href="<?php echo esc_url( estatein_mod( 'cta_url' ) ); ?>"><?php echo esc_html( estatein_mod( 'cta_label' ) ); ?></a>
	</div>
</section>

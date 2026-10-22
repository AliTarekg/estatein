<?php
/**
 * Call-to-action card with the line pattern background (Services page).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_cta = wp_parse_args(
	$args,
	array(
		'cta_title' => '',
		'cta_text'  => '',
		'cta_label' => '',
		'cta_url'   => '',
		'stacked'   => false,
	)
);
?>
<div class="cta-card<?php echo $estatein_cta['stacked'] ? ' cta-card--stacked' : ''; ?>">
	<div class="cta-card__head">
		<h3 class="cta-card__title"><?php echo esc_html( $estatein_cta['cta_title'] ); ?></h3>
		<?php if ( ! $estatein_cta['stacked'] && $estatein_cta['cta_url'] ) : ?>
			<a class="btn btn--dark" href="<?php echo esc_url( $estatein_cta['cta_url'] ); ?>"><?php echo esc_html( $estatein_cta['cta_label'] ); ?></a>
		<?php endif; ?>
	</div>
	<p class="cta-card__text"><?php echo esc_html( $estatein_cta['cta_text'] ); ?></p>
	<?php if ( $estatein_cta['stacked'] && $estatein_cta['cta_url'] ) : ?>
		<a class="btn btn--dark cta-card__btn" href="<?php echo esc_url( $estatein_cta['cta_url'] ); ?>"><?php echo esc_html( $estatein_cta['cta_label'] ); ?></a>
	<?php endif; ?>
</div>

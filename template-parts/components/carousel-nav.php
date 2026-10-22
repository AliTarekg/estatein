<?php
/**
 * Carousel footer: "01 of 10" counter and previous / next buttons.
 * The script in assets/js/main.js keeps it in sync with the track.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_total = isset( $args['total'] ) ? (int) $args['total'] : 1;
$estatein_label = isset( $args['label'] ) ? $args['label'] : '';
?>
<div class="carousel__nav">
	<p class="carousel__count" aria-live="polite">
		<span class="carousel__current" data-carousel-current>01</span>
		<span class="carousel__of"><?php esc_html_e( 'of', 'estatein' ); ?> <span data-carousel-total><?php echo esc_html( estatein_pad( $estatein_total ) ); ?></span></span>
	</p>
	<div class="carousel__buttons">
		<?php /* translators: %s: carousel name, e.g. "properties". */ ?>
		<button class="icon-button icon-button--round" type="button" data-carousel-prev aria-label="<?php echo esc_attr( sprintf( __( 'Previous %s', 'estatein' ), $estatein_label ) ); ?>" disabled>
			<?php estatein_the_icon( 'arrow-left' ); ?>
		</button>
		<?php /* translators: %s: carousel name, e.g. "properties". */ ?>
		<button class="icon-button icon-button--round" type="button" data-carousel-next aria-label="<?php echo esc_attr( sprintf( __( 'Next %s', 'estatein' ), $estatein_label ) ); ?>">
			<?php estatein_the_icon( 'arrow-right' ); ?>
		</button>
	</div>
</div>

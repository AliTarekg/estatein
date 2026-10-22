<?php
/**
 * FAQ card. Must be called inside The Loop.
 * The excerpt is the teaser shown on the card; "Read More" reveals the full
 * answer in place (an enhancement over the static design, with no page load).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_answer_id = 'faq-answer-' . get_the_ID();
$estatein_answer    = get_the_content();
?>
<article class="card faq-card">
	<h3 class="faq-card__question"><?php the_title(); ?></h3>
	<p class="faq-card__teaser"><?php echo esc_html( get_the_excerpt() ); ?></p>

	<?php if ( '' !== trim( $estatein_answer ) ) : ?>
		<div class="faq-card__answer" id="<?php echo esc_attr( $estatein_answer_id ); ?>" hidden>
			<?php echo wp_kses_post( wpautop( $estatein_answer ) ); ?>
		</div>
		<button class="btn btn--dark btn--sm faq-card__toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $estatein_answer_id ); ?>" data-faq-toggle>
			<?php esc_html_e( 'Read More', 'estatein' ); ?>
		</button>
	<?php endif; ?>
</article>

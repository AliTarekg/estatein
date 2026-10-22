<?php
/**
 * Testimonial card. Must be called inside The Loop.
 * Title = headline, content = quote, featured image = client photo.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id       = get_the_ID();
$estatein_rating   = max( 1, min( 5, (int) estatein_field( 'rating', 5, $estatein_id ) ) );
$estatein_name     = estatein_field( 'client_name', '', $estatein_id );
$estatein_location = estatein_field( 'client_location', '', $estatein_id );
?>
<figure class="card testimonial-card">
	<?php /* translators: %d: star rating out of 5. */ ?>
	<div class="stars" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'estatein' ), $estatein_rating ) ); ?>">
		<?php for ( $estatein_i = 0; $estatein_i < $estatein_rating; $estatein_i++ ) : ?>
			<span class="stars__item"><?php estatein_the_icon( 'star' ); ?></span>
		<?php endfor; ?>
	</div>

	<h3 class="testimonial-card__title"><?php the_title(); ?></h3>
	<blockquote class="testimonial-card__quote">
		<?php echo wp_kses_post( wpautop( get_the_content() ) ); ?>
	</blockquote>

	<figcaption class="person">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'estatein-avatar', array( 'class' => 'person__avatar', 'alt' => '', 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<span class="person__avatar person__avatar--initials" aria-hidden="true"><?php echo esc_html( mb_substr( (string) $estatein_name, 0, 1 ) ); ?></span>
		<?php endif; ?>
		<span class="person__text">
			<span class="person__name"><?php echo esc_html( $estatein_name ); ?></span>
			<span class="person__meta"><?php echo esc_html( $estatein_location ); ?></span>
		</span>
	</figcaption>
</figure>

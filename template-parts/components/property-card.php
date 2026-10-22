<?php
/**
 * Property card. Used in the home carousel, the archive grid and related listings.
 * Must be called inside The Loop.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id        = get_the_ID();
$estatein_beds      = estatein_field( 'bedrooms', '', $estatein_id );
$estatein_baths     = estatein_field( 'bathrooms', '', $estatein_id );
$estatein_type      = estatein_field( 'property_type', '', $estatein_id );
$estatein_price     = estatein_field( 'price', '', $estatein_id );
$estatein_permalink = get_permalink();
$estatein_title_id  = 'property-' . $estatein_id . '-title';
?>
<article class="card property-card" aria-labelledby="<?php echo esc_attr( $estatein_title_id ); ?>">
	<a class="property-card__media" href="<?php echo esc_url( $estatein_permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'estatein-card', array( 'class' => 'property-card__img', 'alt' => '', 'loading' => 'lazy', 'sizes' => '(min-width: 1200px) 432px, (min-width: 768px) 45vw, 90vw' ) ); ?>
		<?php else : ?>
			<span class="property-card__placeholder"><?php estatein_the_icon( 'building' ); ?></span>
		<?php endif; ?>
	</a>

	<div class="property-card__body">
		<h3 class="property-card__title" id="<?php echo esc_attr( $estatein_title_id ); ?>">
			<a href="<?php echo esc_url( $estatein_permalink ); ?>"><?php the_title(); ?></a>
		</h3>
		<p class="property-card__excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 14, '...' ) ); ?>
			<a class="text-link" href="<?php echo esc_url( $estatein_permalink ); ?>"><?php esc_html_e( 'Read More', 'estatein' ); ?><span class="visually-hidden"> <?php esc_html_e( 'about', 'estatein' ); ?> <?php the_title(); ?></span></a>
		</p>

		<ul class="chips" aria-label="<?php esc_attr_e( 'Key features', 'estatein' ); ?>">
			<?php if ( '' !== $estatein_beds ) : ?>
				<?php /* translators: %s: number of bedrooms. */ ?>
				<li class="chip"><?php estatein_the_icon( 'bed' ); ?><?php echo esc_html( sprintf( _n( '%s-Bedroom', '%s-Bedroom', (int) $estatein_beds, 'estatein' ), $estatein_beds ) ); ?></li>
			<?php endif; ?>
			<?php if ( '' !== $estatein_baths ) : ?>
				<?php /* translators: %s: number of bathrooms. */ ?>
				<li class="chip"><?php estatein_the_icon( 'bath' ); ?><?php echo esc_html( sprintf( __( '%s-Bathroom', 'estatein' ), $estatein_baths ) ); ?></li>
			<?php endif; ?>
			<?php if ( $estatein_type ) : ?>
				<li class="chip"><?php estatein_the_icon( 'building' ); ?><?php echo esc_html( $estatein_type ); ?></li>
			<?php endif; ?>
		</ul>

		<div class="property-card__footer">
			<?php if ( '' !== $estatein_price ) : ?>
				<p class="price">
					<span class="price__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span>
					<span class="price__value"><?php echo esc_html( estatein_format_price( $estatein_price ) ); ?></span>
				</p>
			<?php endif; ?>
			<a class="btn btn--primary property-card__btn" href="<?php echo esc_url( $estatein_permalink ); ?>">
				<?php esc_html_e( 'View Property Details', 'estatein' ); ?><span class="visually-hidden">: <?php the_title(); ?></span>
			</a>
		</div>
	</div>
</article>

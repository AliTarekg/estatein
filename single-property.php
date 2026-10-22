<?php
/**
 * Single property.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		$estatein_id       = get_the_ID();
		$estatein_price    = estatein_field( 'price', '', $estatein_id );
		$estatein_location = estatein_field( 'location', '', $estatein_id );
		$estatein_beds     = estatein_field( 'bedrooms', '', $estatein_id );
		$estatein_baths    = estatein_field( 'bathrooms', '', $estatein_id );
		$estatein_type     = estatein_field( 'property_type', '', $estatein_id );
		$estatein_inquiry  = add_query_arg( 'property', rawurlencode( get_the_title() ), home_url( '/contact-us/' ) ) . '#contact-form';
		?>
		<article class="section property-single">
			<div class="container">
				<header class="property-single__head">
					<div>
						<h1 class="property-single__title"><?php the_title(); ?></h1>
						<?php if ( $estatein_location ) : ?>
							<p class="property-single__location"><?php echo esc_html( $estatein_location ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( '' !== $estatein_price ) : ?>
						<p class="price price--lg">
							<span class="price__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span>
							<span class="price__value"><?php echo esc_html( estatein_format_price( $estatein_price ) ); ?></span>
						</p>
					<?php endif; ?>
				</header>

				<div class="property-single__media card">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
					<?php else : ?>
						<span class="property-card__placeholder"><?php estatein_the_icon( 'building' ); ?></span>
					<?php endif; ?>
				</div>

				<div class="property-single__grid">
					<div class="card property-single__description">
						<h2 class="property-single__subtitle"><?php esc_html_e( 'Description', 'estatein' ); ?></h2>
						<div class="entry-content"><?php the_content(); ?></div>

						<dl class="facts">
							<?php if ( '' !== $estatein_beds ) : ?>
								<div class="fact"><dt><?php estatein_the_icon( 'bed' ); ?><?php esc_html_e( 'Bedrooms', 'estatein' ); ?></dt><dd><?php echo esc_html( estatein_pad( $estatein_beds ) ); ?></dd></div>
							<?php endif; ?>
							<?php if ( '' !== $estatein_baths ) : ?>
								<div class="fact"><dt><?php estatein_the_icon( 'bath' ); ?><?php esc_html_e( 'Bathrooms', 'estatein' ); ?></dt><dd><?php echo esc_html( is_numeric( $estatein_baths ) && floor( $estatein_baths ) == $estatein_baths ? estatein_pad( $estatein_baths ) : $estatein_baths ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual -- numeric string vs float. ?></dd></div>
							<?php endif; ?>
							<?php if ( $estatein_type ) : ?>
								<div class="fact"><dt><?php estatein_the_icon( 'building' ); ?><?php esc_html_e( 'Type', 'estatein' ); ?></dt><dd><?php echo esc_html( $estatein_type ); ?></dd></div>
							<?php endif; ?>
						</dl>
					</div>

					<aside class="card property-single__inquiry" aria-labelledby="inquiry-title">
						<h2 class="property-single__subtitle" id="inquiry-title"><?php esc_html_e( 'Interested in this property?', 'estatein' ); ?></h2>
						<p><?php esc_html_e( 'Send us a message and an Estatein agent will get back to you with availability and viewing times.', 'estatein' ); ?></p>
						<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $estatein_inquiry ); ?>"><?php esc_html_e( 'Send an Inquiry', 'estatein' ); ?></a>
						<a class="btn btn--outline btn--lg" href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>"><?php esc_html_e( 'Back to Properties', 'estatein' ); ?></a>
					</aside>
				</div>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();

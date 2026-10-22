<?php
/**
 * Properties archive (/properties/) with a property-type filter.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
$estatein_current_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';
$estatein_types        = array( 'Villa', 'Apartment', 'Townhouse', 'Cottage', 'Penthouse' );
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'title' => __( 'Find Your Dream Property', 'estatein' ),
			'text'  => __( 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life.', 'estatein' ),
		)
	);
	?>

	<section class="section" aria-labelledby="listing-title">
		<div class="container">
			<div class="listing-bar">
				<h2 class="visually-hidden" id="listing-title"><?php esc_html_e( 'All properties', 'estatein' ); ?></h2>
				<form class="filter" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>">
					<label class="filter__label" for="filter-type"><?php esc_html_e( 'Property type', 'estatein' ); ?></label>
					<select class="filter__select" id="filter-type" name="type" data-autosubmit>
						<option value=""><?php esc_html_e( 'All types', 'estatein' ); ?></option>
						<?php foreach ( $estatein_types as $estatein_type ) : ?>
							<option value="<?php echo esc_attr( $estatein_type ); ?>" <?php selected( $estatein_current_type, $estatein_type ); ?>><?php echo esc_html( $estatein_type ); ?></option>
						<?php endforeach; ?>
					</select>
					<noscript><button class="btn btn--dark" type="submit"><?php esc_html_e( 'Filter', 'estatein' ); ?></button></noscript>
				</form>
				<p class="listing-bar__count" role="status">
					<?php
					/* translators: %d: number of properties. */
					echo esc_html( sprintf( _n( '%d property', '%d properties', (int) $wp_query->found_posts, 'estatein' ), (int) $wp_query->found_posts ) );
					?>
				</p>
			</div>

			<?php if ( have_posts() ) : ?>
				<ul class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li><?php get_template_part( 'template-parts/components/property-card' ); ?></li>
					<?php endwhile; ?>
				</ul>
				<?php
				the_posts_pagination(
					array(
						'class'     => 'pagination',
						'prev_text' => estatein_icon( 'arrow-left' ) . '<span class="visually-hidden">' . esc_html__( 'Previous page', 'estatein' ) . '</span>',
						'next_text' => '<span class="visually-hidden">' . esc_html__( 'Next page', 'estatein' ) . '</span>' . estatein_icon( 'arrow-right' ),
					)
				);
				?>
			<?php else : ?>
				<p class="empty-state"><?php esc_html_e( 'No properties match this filter yet.', 'estatein' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();

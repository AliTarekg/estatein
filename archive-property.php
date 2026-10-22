<?php
/**
 * Properties archive (/properties/): keyword search, five filters, listing
 * grid and the "Let's Make it Happen" property request form.
 *
 * Filtering happens in the main query (inc/post-types.php), so results,
 * counts and pagination are all native WordPress.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_filters   = estatein_property_filter_values();
$estatein_ranges    = estatein_property_range_filters();
$estatein_archive   = get_post_type_archive_link( 'property' );
$estatein_filtering = (bool) array_filter( $estatein_filters );
$estatein_found     = (int) $wp_query->found_posts;
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'title' => __( 'Find Your Dream Property', 'estatein' ),
			'text'  => __( 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life. With categories to suit every dreamer, your journey starts here.', 'estatein' ),
			'class' => 'page-hero--search',
		)
	);
	?>

	<form class="container property-search" id="categories" role="search" method="get" action="<?php echo esc_url( $estatein_archive ); ?>">
		<div class="property-search__bar">
			<label class="visually-hidden" for="property-q"><?php esc_html_e( 'Search for a property', 'estatein' ); ?></label>
			<input class="property-search__input" id="property-q" type="search" name="q" value="<?php echo esc_attr( $estatein_filters['q'] ); ?>" placeholder="<?php esc_attr_e( 'Search For A Property', 'estatein' ); ?>">
			<button class="btn btn--primary" type="submit"><?php estatein_the_icon( 'search' ); ?><?php esc_html_e( 'Find Property', 'estatein' ); ?></button>
		</div>

		<fieldset class="property-search__filters">
			<legend class="visually-hidden"><?php esc_html_e( 'Filter properties', 'estatein' ); ?></legend>

			<label class="filter">
				<?php estatein_the_icon( 'pin', 'filter__icon' ); ?>
				<span class="visually-hidden"><?php esc_html_e( 'Location', 'estatein' ); ?></span>
				<select class="filter__select" name="location">
					<option value=""><?php esc_html_e( 'Location', 'estatein' ); ?></option>
					<?php foreach ( estatein_property_locations() as $estatein_location ) : ?>
						<option value="<?php echo esc_attr( $estatein_location ); ?>" <?php selected( $estatein_filters['location'], $estatein_location ); ?>><?php echo esc_html( $estatein_location ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>

			<label class="filter">
				<?php estatein_the_icon( 'house', 'filter__icon' ); ?>
				<span class="visually-hidden"><?php esc_html_e( 'Property Type', 'estatein' ); ?></span>
				<select class="filter__select" name="type">
					<option value=""><?php esc_html_e( 'Property Type', 'estatein' ); ?></option>
					<?php foreach ( estatein_property_types() as $estatein_type ) : ?>
						<option value="<?php echo esc_attr( $estatein_type ); ?>" <?php selected( $estatein_filters['type'], $estatein_type ); ?>><?php echo esc_html( $estatein_type ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>

			<?php foreach ( $estatein_ranges as $estatein_param => $estatein_range ) : ?>
				<label class="filter">
					<?php estatein_the_icon( $estatein_range['icon'], 'filter__icon' ); ?>
					<span class="visually-hidden"><?php echo esc_html( $estatein_range['label'] ); ?></span>
					<select class="filter__select" name="<?php echo esc_attr( $estatein_param ); ?>">
						<option value=""><?php echo esc_html( $estatein_range['label'] ); ?></option>
						<?php foreach ( $estatein_range['options'] as $estatein_value => $estatein_label ) : ?>
							<option value="<?php echo esc_attr( $estatein_value ); ?>" <?php selected( $estatein_filters[ $estatein_param ], $estatein_value ); ?>><?php echo esc_html( $estatein_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			<?php endforeach; ?>
		</fieldset>
	</form>

	<section class="section" id="portfolio" aria-labelledby="portfolio-title">
		<div class="container">
			<?php
			estatein_section_heading(
				array(
					'id'    => 'portfolio-title',
					'title' => __( 'Discover a World of Possibilities', 'estatein' ),
					'text'  => __( 'Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home', 'estatein' ),
				)
			);
			?>

			<div class="listing-bar">
				<p class="listing-bar__count" role="status">
					<?php
					/* translators: %d: number of properties. */
					echo esc_html( sprintf( _n( '%d property', '%d properties', $estatein_found, 'estatein' ), $estatein_found ) );
					?>
				</p>
				<?php if ( $estatein_filtering ) : ?>
					<a class="text-link" href="<?php echo esc_url( $estatein_archive ); ?>"><?php esc_html_e( 'Clear search and filters', 'estatein' ); ?></a>
				<?php endif; ?>
			</div>

			<?php if ( have_posts() ) : ?>
				<ul class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li><?php get_template_part( 'template-parts/components/property-card', null, array( 'variant' => 'listing' ) ); ?></li>
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
				<div class="card empty-card">
					<p><?php esc_html_e( 'No properties match your search yet. Try fewer filters, or tell us what you are looking for below and we will find it for you.', 'estatein' ); ?></p>
					<a class="btn btn--dark" href="<?php echo esc_url( $estatein_archive ); ?>"><?php esc_html_e( 'Show all properties', 'estatein' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/forms/property-match' ); ?>
</main>
<?php
get_footer();

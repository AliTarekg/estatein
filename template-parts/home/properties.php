<?php
/**
 * Home: Featured Properties carousel.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_front   = get_queried_object_id();
$estatein_section = estatein_field( 'properties_section', array(), $estatein_front );
$estatein_button  = estatein_link( isset( $estatein_section['button'] ) ? $estatein_section['button'] : array(), __( 'View All Properties', 'estatein' ), get_post_type_archive_link( 'property' ) );

$estatein_query = new WP_Query(
	array(
		'post_type'              => 'property',
		'posts_per_page'         => 12,
		'orderby'                => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		// Show listings marked "featured"; listings saved before the field existed count as featured.
		'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small, capped query.
			'relation' => 'OR',
			array(
				'key'   => 'featured',
				'value' => '1',
			),
			array(
				'key'     => 'featured',
				'compare' => 'NOT EXISTS',
			),
		),
	)
);

if ( ! $estatein_query->have_posts() ) {
	return;
}
?>
<section class="section" id="properties" aria-labelledby="properties-title">
	<div class="container">
		<?php
		estatein_section_heading(
			array(
				'id'         => 'properties-title',
				'title'      => ! empty( $estatein_section['title'] ) ? $estatein_section['title'] : __( 'Featured Properties', 'estatein' ),
				'text'       => ! empty( $estatein_section['text'] ) ? $estatein_section['text'] : __( 'Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein. Click "View Details" for more information.', 'estatein' ),
				'button'     => $estatein_button['title'],
				'button_url' => $estatein_button['url'],
			)
		);
		?>

		<div class="carousel" data-carousel>
			<ul class="carousel__track" data-carousel-track tabindex="0" aria-label="<?php esc_attr_e( 'Featured properties', 'estatein' ); ?>">
				<?php
				while ( $estatein_query->have_posts() ) :
					$estatein_query->the_post();
					?>
					<li class="carousel__slide"><?php get_template_part( 'template-parts/components/property-card' ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php
			get_template_part(
				'template-parts/components/carousel-nav',
				null,
				array(
					'total' => $estatein_query->post_count,
					'label' => __( 'properties', 'estatein' ),
				)
			);
			?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();

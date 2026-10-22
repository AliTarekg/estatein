<?php
/**
 * Home: "What Our Clients Say" carousel.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_front   = get_queried_object_id();
$estatein_section = estatein_field( 'testimonials_section', array(), $estatein_front );
$estatein_button  = estatein_link( isset( $estatein_section['button'] ) ? $estatein_section['button'] : array(), __( 'View All Testimonials', 'estatein' ), home_url( '/about-us/#clients' ) );

$estatein_query = new WP_Query(
	array(
		'post_type'      => 'testimonial',
		'posts_per_page' => 12,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'no_found_rows'  => true,
	)
);

if ( ! $estatein_query->have_posts() ) {
	return;
}
?>
<section class="section" id="testimonials" aria-labelledby="testimonials-title">
	<div class="container">
		<?php
		estatein_section_heading(
			array(
				'id'         => 'testimonials-title',
				'title'      => ! empty( $estatein_section['title'] ) ? $estatein_section['title'] : __( 'What Our Clients Say', 'estatein' ),
				'text'       => ! empty( $estatein_section['text'] ) ? $estatein_section['text'] : __( 'Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.', 'estatein' ),
				'button'     => $estatein_button['title'],
				'button_url' => $estatein_button['url'],
			)
		);
		?>

		<div class="carousel" data-carousel>
			<ul class="carousel__track" data-carousel-track tabindex="0" aria-label="<?php esc_attr_e( 'Client testimonials', 'estatein' ); ?>">
				<?php
				while ( $estatein_query->have_posts() ) :
					$estatein_query->the_post();
					?>
					<li class="carousel__slide"><?php get_template_part( 'template-parts/components/testimonial-card' ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php
			get_template_part(
				'template-parts/components/carousel-nav',
				null,
				array(
					'total' => $estatein_query->post_count,
					'label' => __( 'testimonials', 'estatein' ),
				)
			);
			?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();

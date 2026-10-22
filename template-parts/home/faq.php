<?php
/**
 * Home: Frequently Asked Questions carousel.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_front   = get_queried_object_id();
$estatein_section = estatein_field( 'faq_section', array(), $estatein_front );
$estatein_button  = estatein_link( isset( $estatein_section['button'] ) ? $estatein_section['button'] : array(), __( "View All FAQ's", 'estatein' ), home_url( '/contact-us/#contact-form' ) );

$estatein_query = new WP_Query(
	array(
		'post_type'      => 'faq',
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
<section class="section" id="faq" aria-labelledby="faq-title">
	<div class="container">
		<?php
		estatein_section_heading(
			array(
				'id'         => 'faq-title',
				'title'      => ! empty( $estatein_section['title'] ) ? $estatein_section['title'] : __( 'Frequently Asked Questions', 'estatein' ),
				'text'       => ! empty( $estatein_section['text'] ) ? $estatein_section['text'] : __( "Find answers to common questions about Estatein's services, property listings, and the real estate process. We're here to provide clarity and assist you every step of the way.", 'estatein' ),
				'button'     => $estatein_button['title'],
				'button_url' => $estatein_button['url'],
			)
		);
		?>

		<div class="carousel" data-carousel>
			<ul class="carousel__track" data-carousel-track tabindex="0" aria-label="<?php esc_attr_e( 'Frequently asked questions', 'estatein' ); ?>">
				<?php
				while ( $estatein_query->have_posts() ) :
					$estatein_query->the_post();
					?>
					<li class="carousel__slide"><?php get_template_part( 'template-parts/components/faq-card' ); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php
			get_template_part(
				'template-parts/components/carousel-nav',
				null,
				array(
					'total' => $estatein_query->post_count,
					'label' => __( 'questions', 'estatein' ),
				)
			);
			?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();

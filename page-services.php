<?php
/**
 * Template Name: Services
 *
 * Services: intro, feature strip and three service groups (selling,
 * management, investments), each with four service cards and a call to action.
 * Copy is edited in the "Services Page Sections" fields (inc/page-sections.php).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_intro = estatein_section( 'intro' );
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'title' => $estatein_intro['title'],
			'text'  => $estatein_intro['text'],
		)
	);

	get_template_part(
		'template-parts/components/features',
		null,
		array(
			'id'    => 'service-areas',
			'label' => __( 'Service areas', 'estatein' ),
			'items' => estatein_feature_items(),
		)
	);

	foreach ( array( 'valuation', 'management', 'investments' ) as $estatein_key ) :
		$estatein_group = estatein_section( $estatein_key );
		$estatein_title = $estatein_key . '-title';
		$estatein_split = 'investments' === $estatein_key;
		?>
		<section class="section" id="<?php echo esc_attr( $estatein_key ); ?>" aria-labelledby="<?php echo esc_attr( $estatein_title ); ?>">
			<div class="container<?php echo $estatein_split ? ' split split--services' : ''; ?>">
				<div class="split__text">
					<?php
					estatein_section_heading(
						array(
							'id'    => $estatein_title,
							'title' => $estatein_group['title'],
							'text'  => $estatein_group['text'],
						)
					);

					if ( $estatein_split ) {
						get_template_part( 'template-parts/components/cta-card', null, $estatein_group + array( 'stacked' => true ) );
					}
					?>
				</div>

				<ul class="service-grid<?php echo $estatein_split ? ' service-grid--2 card-frame' : ''; ?>">
					<?php foreach ( $estatein_group['items'] as $estatein_item ) : ?>
						<li class="card service-card" id="<?php echo esc_attr( sanitize_title( $estatein_item['title'] ) ); ?>">
							<h3 class="service-card__title">
								<span class="icon-ring icon-ring--sm"><span class="icon-ring__inner"><?php estatein_the_icon( $estatein_item['icon'] ); ?></span></span>
								<?php echo esc_html( $estatein_item['title'] ); ?>
							</h3>
							<p class="service-card__text"><?php echo esc_html( $estatein_item['text'] ); ?></p>
						</li>
					<?php endforeach; ?>

					<?php if ( ! $estatein_split ) : ?>
						<li class="service-grid__cta"><?php get_template_part( 'template-parts/components/cta-card', null, $estatein_group ); ?></li>
					<?php endif; ?>
				</ul>
			</div>
		</section>
		<?php
	endforeach;

	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( get_the_content() ) ) :
			?>
			<div class="section"><div class="container entry-content"><?php the_content(); ?></div></div>
			<?php
		endif;
	endwhile;
	?>
</main>
<?php
get_footer();

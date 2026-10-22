<?php
/**
 * Template Name: About
 *
 * About Us: journey, values, achievements, process, team and clients.
 * Section copy is edited in the "About Page Sections" fields (inc/page-sections.php);
 * team members are the "Team" post type.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_journey      = estatein_section( 'journey' );
$estatein_values       = estatein_section( 'values' );
$estatein_achievements = estatein_section( 'achievements' );
$estatein_process      = estatein_section( 'process' );
$estatein_team         = estatein_section( 'team' );
$estatein_clients      = estatein_section( 'clients' );

$estatein_members = new WP_Query(
	array(
		'post_type'      => 'team_member',
		'posts_per_page' => 12,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		),
		'no_found_rows'  => true,
	)
);
?>
<main id="main" class="site-main" tabindex="-1">
	<h1 class="visually-hidden"><?php the_title(); ?></h1>

	<section class="section section--first" id="story" aria-labelledby="story-title">
		<div class="container split split--journey">
			<div class="split__text">
				<?php
				estatein_section_heading(
					array(
						'id'    => 'story-title',
						'title' => $estatein_journey['title'],
						'text'  => $estatein_journey['text'],
					)
				);
				get_template_part( 'template-parts/components/stats', null, array( 'items' => $estatein_journey['items'] ) );
				?>
			</div>
			<div class="split__media media-panel">
				<?php
				echo estatein_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in estatein_image().
					$estatein_journey['image'],
					'large',
					array(
						'alt'   => __( 'Model house held in an open hand', 'estatein' ),
						'class' => 'media-panel__img',
					)
				);
				?>
			</div>
		</div>
	</section>

	<section class="section" aria-labelledby="values-title">
		<div class="container split split--values">
			<div class="split__text">
				<?php
				estatein_section_heading(
					array(
						'id'    => 'values-title',
						'title' => $estatein_values['title'],
						'text'  => $estatein_values['text'],
					)
				);
				?>
			</div>
			<ul class="values card-frame">
				<?php foreach ( $estatein_values['items'] as $estatein_item ) : ?>
					<li class="value">
						<h3 class="value__title">
							<span class="icon-ring icon-ring--sm"><span class="icon-ring__inner"><?php estatein_the_icon( $estatein_item['icon'] ); ?></span></span>
							<?php echo esc_html( $estatein_item['title'] ); ?>
						</h3>
						<p class="value__text"><?php echo esc_html( $estatein_item['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="section" id="works" aria-labelledby="works-title">
		<div class="container">
			<?php
			estatein_section_heading(
				array(
					'id'    => 'works-title',
					'title' => $estatein_achievements['title'],
					'text'  => $estatein_achievements['text'],
				)
			);
			?>
			<ul class="grid-3">
				<?php foreach ( $estatein_achievements['items'] as $estatein_item ) : ?>
					<li class="card card--raised text-card">
						<h3 class="text-card__title"><?php echo esc_html( $estatein_item['title'] ); ?></h3>
						<p class="text-card__text"><?php echo esc_html( $estatein_item['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="section" id="how-it-works" aria-labelledby="process-title">
		<div class="container">
			<?php
			estatein_section_heading(
				array(
					'id'    => 'process-title',
					'title' => $estatein_process['title'],
					'text'  => $estatein_process['text'],
				)
			);
			?>
			<ol class="grid-3 steps">
				<?php foreach ( $estatein_process['items'] as $estatein_i => $estatein_item ) : ?>
					<li class="step">
						<?php /* translators: %s: step number, e.g. 01. */ ?>
						<p class="step__label"><?php echo esc_html( sprintf( __( 'Step %s', 'estatein' ), estatein_pad( $estatein_i + 1 ) ) ); ?></p>
						<div class="step__card">
							<h3 class="text-card__title"><?php echo esc_html( $estatein_item['title'] ); ?></h3>
							<p class="text-card__text"><?php echo esc_html( $estatein_item['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php if ( $estatein_members->have_posts() ) : ?>
		<section class="section" id="team" aria-labelledby="team-title">
			<div class="container">
				<?php
				estatein_section_heading(
					array(
						'id'    => 'team-title',
						'title' => $estatein_team['title'],
						'text'  => $estatein_team['text'],
					)
				);
				?>
				<ul class="team-grid">
					<?php
					while ( $estatein_members->have_posts() ) :
						$estatein_members->the_post();
						?>
						<li><?php get_template_part( 'template-parts/components/team-card' ); ?></li>
					<?php endwhile; ?>
				</ul>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
	?>

	<section class="section" id="clients" aria-labelledby="clients-title">
		<div class="container">
			<?php
			estatein_section_heading(
				array(
					'id'    => 'clients-title',
					'title' => $estatein_clients['title'],
					'text'  => $estatein_clients['text'],
				)
			);
			?>
			<div class="carousel" data-carousel>
				<ul class="carousel__track carousel__track--2" data-carousel-track tabindex="0" aria-label="<?php esc_attr_e( 'Our clients', 'estatein' ); ?>">
					<?php foreach ( $estatein_clients['items'] as $estatein_item ) : ?>
						<li class="carousel__slide"><?php get_template_part( 'template-parts/components/client-card', null, $estatein_item ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php
				get_template_part(
					'template-parts/components/carousel-nav',
					null,
					array(
						'total' => count( $estatein_clients['items'] ),
						'label' => __( 'clients', 'estatein' ),
					)
				);
				?>
			</div>
		</div>
	</section>

	<?php
	// Anything written in the page editor appears below the designed sections.
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

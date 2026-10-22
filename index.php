<?php
/**
 * Fallback template: blog index, search results and any view without a
 * more specific template. Uses the standard WordPress Loop.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search query. */
	$estatein_heading = sprintf( __( 'Search results for "%s"', 'estatein' ), get_search_query() );
} elseif ( is_archive() ) {
	$estatein_heading = wp_strip_all_tags( get_the_archive_title() );
} else {
	$estatein_heading = single_post_title( '', false );
	$estatein_heading = $estatein_heading ? $estatein_heading : __( 'Latest News', 'estatein' );
}
?>
<main id="main" class="site-main" tabindex="-1">
	<?php get_template_part( 'template-parts/components/page-hero', null, array( 'title' => $estatein_heading ) ); ?>

	<div class="section">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="post-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'card post-card' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php the_post_thumbnail( 'estatein-card', array( 'alt' => '' ) ); ?>
								</a>
							<?php endif; ?>
							<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
							<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							<a class="btn btn--dark btn--sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'estatein' ); ?><span class="visually-hidden">: <?php the_title(); ?></span></a>
						</article>
					<?php endwhile; ?>
				</div>
				<?php the_posts_pagination( array( 'class' => 'pagination' ) ); ?>
			<?php else : ?>
				<p class="empty-state"><?php esc_html_e( 'Nothing found. Try a different search.', 'estatein' ); ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php
get_footer();

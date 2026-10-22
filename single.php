<?php
/**
 * Single blog post.
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
		get_template_part( 'template-parts/components/page-hero' );
		?>
		<article <?php post_class( 'section' ); ?>>
			<div class="container entry-content">
				<p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'large', array( 'class' => 'entry-content__image' ) );
				}
				the_content();
				?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();

<?php
/**
 * Default page template.
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
		get_template_part( 'template-parts/components/page-hero', null, array( 'text' => has_excerpt() ? get_the_excerpt() : '' ) );
		?>
		<div class="section">
			<div class="container entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();

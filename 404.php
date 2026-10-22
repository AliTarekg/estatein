<?php
/**
 * Not found.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'title' => __( 'Page not found', 'estatein' ),
			'text'  => __( "The page you're looking for has moved or no longer exists. Let's get you back on track.", 'estatein' ),
		)
	);
	?>
	<div class="section">
		<div class="container empty-state">
			<a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'estatein' ); ?></a>
			<a class="btn btn--outline btn--lg" href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>"><?php esc_html_e( 'Browse Properties', 'estatein' ); ?></a>
		</div>
	</div>
</main>
<?php
get_footer();

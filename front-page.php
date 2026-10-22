<?php
/**
 * Front page. Each section is a template part so it can be reordered or reused.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/properties' );
	get_template_part( 'template-parts/home/testimonials' );
	get_template_part( 'template-parts/home/faq' );
	?>
</main>
<?php
get_footer();

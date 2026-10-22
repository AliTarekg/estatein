<?php
/**
 * Section heading shared by every content section:
 * sparkle mark, title, intro paragraph and an optional outline button.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_args = wp_parse_args(
	$args,
	array(
		'id'         => '',
		'title'      => '',
		'text'       => '',
		'button'     => '',
		'button_url' => '',
	)
);
?>
<div class="section-head">
	<div class="section-head__text">
		<span class="sparkles" aria-hidden="true">
			<?php estatein_the_icon( 'sparkle', 'sparkles__lg' ); ?>
			<?php estatein_the_icon( 'sparkle', 'sparkles__md' ); ?>
			<?php estatein_the_icon( 'sparkle', 'sparkles__sm' ); ?>
		</span>
		<h2 class="section-head__title" <?php echo $estatein_args['id'] ? 'id="' . esc_attr( $estatein_args['id'] ) . '"' : ''; ?>><?php echo esc_html( $estatein_args['title'] ); ?></h2>
		<?php if ( $estatein_args['text'] ) : ?>
			<p class="section-head__intro"><?php echo esc_html( $estatein_args['text'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( $estatein_args['button'] && $estatein_args['button_url'] ) : ?>
		<a class="btn btn--dark section-head__btn" href="<?php echo esc_url( $estatein_args['button_url'] ); ?>"><?php echo esc_html( $estatein_args['button'] ); ?></a>
	<?php endif; ?>
</div>

<?php
/**
 * Strip of four linked cards with a ringed icon (Home, Services, Contact).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_args = wp_parse_args(
	$args,
	array(
		'id'    => 'features',
		'label' => __( 'What we offer', 'estatein' ),
		// Each item: icon, title, url. Optional "links": [ [label, url], ... ] replaces the title with several links.
		'items' => array(),
	)
);
?>
<section class="features" id="<?php echo esc_attr( $estatein_args['id'] ); ?>" aria-label="<?php echo esc_attr( $estatein_args['label'] ); ?>">
	<ul class="features__list">
		<?php foreach ( $estatein_args['items'] as $estatein_item ) : ?>
			<li class="feature">
				<?php if ( ! empty( $estatein_item['links'] ) ) : ?>
					<div class="feature__link feature__link--multi">
						<span class="icon-ring"><span class="icon-ring__inner"><?php estatein_the_icon( $estatein_item['icon'] ); ?></span></span>
						<span class="feature__title feature__links">
							<?php foreach ( $estatein_item['links'] as $estatein_link ) : ?>
								<a href="<?php echo esc_url( $estatein_link[1] ); ?>"<?php echo 0 === strpos( $estatein_link[1], 'http' ) && false === strpos( $estatein_link[1], home_url() ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $estatein_link[0] ); ?></a>
							<?php endforeach; ?>
						</span>
						<?php estatein_the_icon( 'arrow-up-right', 'feature__arrow' ); ?>
					</div>
				<?php else : ?>
					<a class="feature__link" href="<?php echo esc_url( $estatein_item['url'] ); ?>">
						<span class="icon-ring"><span class="icon-ring__inner"><?php estatein_the_icon( $estatein_item['icon'] ); ?></span></span>
						<span class="feature__title"><?php echo esc_html( $estatein_item['title'] ); ?></span>
						<?php estatein_the_icon( 'arrow-up-right', 'feature__arrow' ); ?>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

<?php
/**
 * Row of stat cards ("200+ Happy Customers"). Values count up on load (main.js).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_items = isset( $args['items'] ) ? $args['items'] : array();
?>
<dl class="stats">
	<?php foreach ( $estatein_items as $estatein_stat ) : ?>
		<div class="stat">
			<dt class="stat__label"><?php echo esc_html( $estatein_stat['label'] ); ?></dt>
			<dd class="stat__value" data-count-up><?php echo esc_html( $estatein_stat['value'] ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>

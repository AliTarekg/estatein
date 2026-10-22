<?php
/**
 * Site logo. Priority: Customizer logo > assets/images/logo.svg (Figma export) > built-in mark + site name.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_logo_class = isset( $args['class'] ) ? ' ' . $args['class'] : '';
?>
<a class="logo<?php echo esc_attr( $estatein_logo_class ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php if ( has_custom_logo() ) : ?>
		<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'logo__img', 'alt' => get_bloginfo( 'name' ) ) ); ?>
	<?php elseif ( estatein_asset_url( 'logo.svg' ) ) : ?>
		<img class="logo__img" src="<?php echo esc_url( estatein_asset_url( 'logo.svg' ) ); ?>" width="160" height="48" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<?php else : ?>
		<?php estatein_the_icon( 'logo-mark', 'logo__mark' ); ?>
		<span class="logo__text"><?php echo esc_html( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Estatein' ); ?></span>
	<?php endif; ?>
</a>

<?php
/**
 * Site header: announcement banner + main navigation.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#141414">
	<?php // Flag JS support and hide a dismissed banner before first paint (no layout jump). ?>
	<script>document.documentElement.classList.add('js');try{if(localStorage.getItem('estateinBannerClosed')){document.documentElement.classList.add('banner-dismissed');}}catch(e){}</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'estatein' ); ?></a>

<header class="site-header" id="site-header">
	<?php if ( estatein_mod( 'banner_enabled' ) ) : ?>
		<div class="banner" id="site-banner" data-banner>
			<p class="banner__text">
				<span><?php echo esc_html( estatein_mod( 'banner_text' ) ); ?></span>
				<a class="banner__link" href="<?php echo esc_url( estatein_mod( 'banner_url' ) ); ?>"><?php echo esc_html( estatein_mod( 'banner_label' ) ); ?></a>
			</p>
			<button class="banner__close icon-button icon-button--sm" type="button" data-banner-close aria-label="<?php esc_attr_e( 'Dismiss announcement', 'estatein' ); ?>">
				<?php estatein_the_icon( 'close' ); ?>
			</button>
		</div>
	<?php endif; ?>

	<div class="nav-bar">
		<div class="container nav-bar__inner">
			<?php get_template_part( 'template-parts/components/logo' ); ?>

			<button class="nav-toggle icon-button" type="button" aria-expanded="false" aria-controls="primary-nav" data-nav-toggle>
				<span class="visually-hidden"><?php esc_html_e( 'Menu', 'estatein' ); ?></span>
				<?php estatein_the_icon( 'menu', 'nav-toggle__open' ); ?>
				<?php estatein_the_icon( 'close', 'nav-toggle__close' ); ?>
			</button>

			<nav class="nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'estatein' ); ?>" data-nav>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'nav__list',
						'menu_id'        => 'primary-menu',
						'depth'          => 1,
						'fallback_cb'    => 'estatein_primary_menu_fallback',
					)
				);
				?>
				<a class="btn btn--dark nav__cta" href="<?php echo esc_url( estatein_mod( 'contact_url' ) ); ?>"><?php echo esc_html( estatein_mod( 'contact_label' ) ); ?></a>
			</nav>
		</div>
	</div>
</header>

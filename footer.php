<?php
/**
 * Site footer: closing CTA, logo + newsletter, link columns, legal bar.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_newsletter = estatein_form_status( 'newsletter' );
$estatein_socials    = array(
	'facebook' => __( 'Facebook', 'estatein' ),
	'linkedin' => __( 'LinkedIn', 'estatein' ),
	'twitter'  => __( 'X (Twitter)', 'estatein' ),
	'youtube'  => __( 'YouTube', 'estatein' ),
);
?>

<?php get_template_part( 'template-parts/components/cta' ); ?>

<footer class="site-footer">
	<div class="container site-footer__main">
		<div class="site-footer__brand">
			<?php get_template_part( 'template-parts/components/logo' ); ?>

			<form class="newsletter" id="newsletter" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<?php estatein_form_hidden_fields( 'newsletter' ); ?>
				<label class="visually-hidden" for="newsletter-email"><?php esc_html_e( 'Email address', 'estatein' ); ?></label>
				<?php estatein_the_icon( 'mail', 'newsletter__icon' ); ?>
				<input class="newsletter__input" id="newsletter-email" type="email" name="email" placeholder="<?php esc_attr_e( 'Enter Your Email', 'estatein' ); ?>" autocomplete="email" required>
				<button class="newsletter__submit" type="submit" aria-label="<?php esc_attr_e( 'Subscribe', 'estatein' ); ?>"><?php estatein_the_icon( 'send' ); ?></button>
			</form>
			<?php if ( $estatein_newsletter ) : ?>
				<p class="form-message form-message--<?php echo esc_attr( $estatein_newsletter['type'] ); ?>" role="status"><?php echo esc_html( $estatein_newsletter['text'] ); ?></p>
			<?php endif; ?>
		</div>

		<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'estatein' ); ?>">
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-nav__columns',
						'depth'          => 2,
					)
				);
			} else {
				echo '<ul class="footer-nav__columns">';
				foreach ( estatein_footer_default_columns() as $estatein_column ) {
					printf( '<li class="menu-item-has-children"><a href="%s">%s</a><ul class="sub-menu">', esc_url( $estatein_column['url'] ), esc_html( $estatein_column['title'] ) );
					foreach ( $estatein_column['links'] as $estatein_label => $estatein_url ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $estatein_url ), esc_html( $estatein_label ) );
					}
					echo '</ul></li>';
				}
				echo '</ul>';
			}
			?>
		</nav>
	</div>

	<div class="site-footer__bar">
		<div class="container site-footer__bar-inner">
			<p class="site-footer__legal">
				<span><?php echo esc_html( estatein_mod( 'copyright' ) ); ?></span>
				<a href="<?php echo esc_url( estatein_mod( 'terms_url' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'estatein' ); ?></a>
			</p>

			<ul class="socials">
				<?php foreach ( $estatein_socials as $estatein_key => $estatein_name ) : ?>
					<?php $estatein_url = estatein_mod( 'social_' . $estatein_key ); ?>
					<?php if ( $estatein_url ) : ?>
						<li>
							<a class="icon-button icon-button--round" href="<?php echo esc_url( $estatein_url ); ?>" target="_blank" rel="noopener noreferrer">
								<?php estatein_the_icon( $estatein_key ); ?>
								<?php /* translators: %s: social network name. */ ?>
								<span class="visually-hidden"><?php echo esc_html( sprintf( __( 'Estatein on %s (opens in a new tab)', 'estatein' ), $estatein_name ) ); ?></span>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

<?php
/**
 * Client card (About page): since, name, website, domain, category, quote.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_client = wp_parse_args(
	$args,
	array(
		'since'       => '',
		'title'       => '',
		'website_url' => '',
		'domain'      => '',
		'category'    => '',
		'quote'       => '',
	)
);
?>
<article class="card card--raised client-card">
	<header class="client-card__head">
		<div>
			<p class="client-card__since"><?php echo esc_html( $estatein_client['since'] ); ?></p>
			<h3 class="client-card__name"><?php echo esc_html( $estatein_client['title'] ); ?></h3>
		</div>
		<?php if ( $estatein_client['website_url'] ) : ?>
			<a class="btn btn--dark" href="<?php echo esc_url( $estatein_client['website_url'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Visit Website', 'estatein' ); ?>
				<?php /* translators: %s: client name. */ ?>
				<span class="visually-hidden"><?php echo esc_html( sprintf( __( 'of %s (opens in a new tab)', 'estatein' ), $estatein_client['title'] ) ); ?></span>
			</a>
		<?php endif; ?>
	</header>
	<dl class="client-card__facts">
		<div>
			<dt><?php estatein_the_icon( 'apps' ); ?><?php esc_html_e( 'Domain', 'estatein' ); ?></dt>
			<dd><?php echo esc_html( $estatein_client['domain'] ); ?></dd>
		</div>
		<div>
			<dt><?php estatein_the_icon( 'bolt' ); ?><?php esc_html_e( 'Category', 'estatein' ); ?></dt>
			<dd><?php echo esc_html( $estatein_client['category'] ); ?></dd>
		</div>
	</dl>
	<blockquote class="client-card__quote">
		<p class="client-card__quote-label"><?php esc_html_e( 'What They Said', 'estatein' ); ?> <span aria-hidden="true">🤗</span></p>
		<p><?php echo esc_html( $estatein_client['quote'] ); ?></p>
	</blockquote>
</article>

<?php
/**
 * Team member card. Must be called inside The Loop (team_member post).
 * "Say Hello" opens an email to the member, or the contact form when no email is set.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id      = get_the_ID();
$estatein_name    = get_the_title();
$estatein_role    = estatein_field( 'role', '', $estatein_id );
$estatein_email   = estatein_field( 'email', '', $estatein_id );
$estatein_twitter = estatein_field( 'twitter_url', '', $estatein_id );
$estatein_twitter = $estatein_twitter ? $estatein_twitter : estatein_mod( 'social_twitter' );
$estatein_hello   = is_email( $estatein_email )
	? 'mailto:' . antispambot( $estatein_email )
	/* translators: %s: team member name. */
	: add_query_arg( 'message', rawurlencode( sprintf( __( 'Hi %s, ', 'estatein' ), $estatein_name ) ), home_url( '/contact-us/' ) ) . '#contact-form';
?>
<article class="card team-card">
	<div class="team-card__media">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail(
				'medium_large',
				array(
					'class'   => 'team-card__img',
					'alt'     => '',
					'loading' => 'lazy',
					'sizes'   => '(min-width: 1200px) 300px, (min-width: 768px) 45vw, 90vw',
				)
			);
		} else {
			echo '<span class="team-card__placeholder" aria-hidden="true">' . esc_html( mb_substr( $estatein_name, 0, 1 ) ) . '</span>';
		}
		?>
		<?php if ( $estatein_twitter && '#' !== $estatein_twitter ) : ?>
			<a class="team-card__social" href="<?php echo esc_url( $estatein_twitter ); ?>" target="_blank" rel="noopener noreferrer">
				<?php estatein_the_icon( 'twitter' ); ?>
				<?php /* translators: %s: team member name. */ ?>
				<span class="visually-hidden"><?php echo esc_html( sprintf( __( '%s on X (opens in a new tab)', 'estatein' ), $estatein_name ) ); ?></span>
			</a>
		<?php else : ?>
			<span class="team-card__social" aria-hidden="true"><?php estatein_the_icon( 'twitter' ); ?></span>
		<?php endif; ?>
	</div>
	<h3 class="team-card__name"><?php echo esc_html( $estatein_name ); ?></h3>
	<?php if ( $estatein_role ) : ?>
		<p class="team-card__role"><?php echo esc_html( $estatein_role ); ?></p>
	<?php endif; ?>
	<a class="team-card__hello" href="<?php echo esc_url( $estatein_hello ); ?>">
		<span><?php esc_html_e( 'Say Hello', 'estatein' ); ?> <span aria-hidden="true">👋</span></span>
		<span class="team-card__send" aria-hidden="true"><?php estatein_the_icon( 'send' ); ?></span>
		<?php /* translators: %s: team member name. */ ?>
		<span class="visually-hidden"><?php echo esc_html( sprintf( __( 'to %s', 'estatein' ), $estatein_name ) ); ?></span>
	</a>
</article>

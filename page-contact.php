<?php
/**
 * Template Name: Contact
 *
 * Contact page with the native inquiry form (see inc/forms.php).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_status = estatein_form_status( 'contact' );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- prefill only.
$estatein_property = isset( $_GET['property'] ) ? sanitize_text_field( wp_unslash( $_GET['property'] ) ) : '';
$estatein_inquiry_types = array(
	__( 'Buying', 'estatein' ),
	__( 'Selling', 'estatein' ),
	__( 'Renting', 'estatein' ),
	__( 'Property Management', 'estatein' ),
	__( 'Valuation', 'estatein' ),
	__( 'Other', 'estatein' ),
);
$estatein_sources = array(
	__( 'Search engine', 'estatein' ),
	__( 'Social media', 'estatein' ),
	__( 'Friend or family', 'estatein' ),
	__( 'Advertisement', 'estatein' ),
	__( 'Other', 'estatein' ),
);
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part(
			'template-parts/components/page-hero',
			null,
			array(
				'title' => __( 'Get in Touch with Estatein', 'estatein' ),
				'text'  => __( "Welcome to Estatein's Contact Us page. We're here to assist you with any inquiries, requests, or feedback you may have. Whether you're looking to buy or sell a property, explore investment opportunities, or simply want to connect, we're just a message away.", 'estatein' ),
			)
		);
		?>
		<section class="section" aria-labelledby="contact-form-title">
			<div class="container">
				<?php
				estatein_section_heading(
					array(
						'id'    => 'contact-form-title',
						'title' => __( "Let's Connect", 'estatein' ),
						'text'  => __( "We're excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein.", 'estatein' ),
					)
				);
				?>

				<form class="card contact-form" id="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate data-validate>
					<?php estatein_form_hidden_fields( 'contact' ); ?>

					<?php if ( $estatein_status ) : ?>
						<p class="form-message form-message--<?php echo esc_attr( $estatein_status['type'] ); ?>" role="status" tabindex="-1" data-focus-on-load><?php echo esc_html( $estatein_status['text'] ); ?></p>
					<?php endif; ?>

					<div class="form-grid">
						<div class="field">
							<label class="field__label" for="cf-first"><?php esc_html_e( 'First Name', 'estatein' ); ?> <span aria-hidden="true">*</span></label>
							<input class="field__input" id="cf-first" name="first_name" type="text" autocomplete="given-name" placeholder="<?php esc_attr_e( 'Enter First Name', 'estatein' ); ?>" required>
						</div>
						<div class="field">
							<label class="field__label" for="cf-last"><?php esc_html_e( 'Last Name', 'estatein' ); ?></label>
							<input class="field__input" id="cf-last" name="last_name" type="text" autocomplete="family-name" placeholder="<?php esc_attr_e( 'Enter Last Name', 'estatein' ); ?>">
						</div>
						<div class="field">
							<label class="field__label" for="cf-email"><?php esc_html_e( 'Email', 'estatein' ); ?> <span aria-hidden="true">*</span></label>
							<input class="field__input" id="cf-email" name="email" type="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Enter your Email', 'estatein' ); ?>" required>
						</div>
						<div class="field">
							<label class="field__label" for="cf-phone"><?php esc_html_e( 'Phone', 'estatein' ); ?></label>
							<input class="field__input" id="cf-phone" name="phone" type="tel" autocomplete="tel" placeholder="<?php esc_attr_e( 'Enter Phone Number', 'estatein' ); ?>">
						</div>
						<div class="field">
							<label class="field__label" for="cf-inquiry"><?php esc_html_e( 'Inquiry Type', 'estatein' ); ?></label>
							<select class="field__input field__select" id="cf-inquiry" name="inquiry_type">
								<option value=""><?php esc_html_e( 'Select Inquiry Type', 'estatein' ); ?></option>
								<?php foreach ( $estatein_inquiry_types as $estatein_option ) : ?>
									<option <?php selected( $estatein_property && __( 'Buying', 'estatein' ) === $estatein_option ); ?>><?php echo esc_html( $estatein_option ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="field">
							<label class="field__label" for="cf-source"><?php esc_html_e( 'How Did You Hear About Us?', 'estatein' ); ?></label>
							<select class="field__input field__select" id="cf-source" name="how_heard">
								<option value=""><?php esc_html_e( 'Select', 'estatein' ); ?></option>
								<?php foreach ( $estatein_sources as $estatein_option ) : ?>
									<option><?php echo esc_html( $estatein_option ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="field field--full">
							<label class="field__label" for="cf-message"><?php esc_html_e( 'Message', 'estatein' ); ?> <span aria-hidden="true">*</span></label>
							<?php /* translators: %s: property name. */ ?>
							<textarea class="field__input" id="cf-message" name="message" rows="6" placeholder="<?php esc_attr_e( 'Enter your Message here..', 'estatein' ); ?>" required><?php echo $estatein_property ? esc_textarea( sprintf( __( "Hi, I'm interested in %s. Please send me more details.", 'estatein' ), $estatein_property ) ) : ''; ?></textarea>
						</div>
					</div>

					<div class="contact-form__footer">
						<label class="checkbox">
							<input type="checkbox" name="terms" value="1" required>
							<span>
								<?php esc_html_e( 'I agree with', 'estatein' ); ?>
								<a href="<?php echo esc_url( estatein_mod( 'terms_url' ) ); ?>"><?php esc_html_e( 'Terms of Use', 'estatein' ); ?></a>
								<?php esc_html_e( 'and', 'estatein' ); ?>
								<a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : estatein_mod( 'terms_url' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'estatein' ); ?></a>
							</span>
						</label>
						<button class="btn btn--primary btn--lg" type="submit"><?php esc_html_e( 'Send Your Message', 'estatein' ); ?></button>
					</div>
				</form>

				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="entry-content contact-extra" id="offices"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php
get_footer();

<?php
/**
 * Template Name: Contact
 *
 * Contact page: contact details strip, the native contact form (see
 * inc/forms.php), office locations with category tabs, and a photo gallery.
 * Copy is edited in the "Contact Page Sections" fields; email, phone and
 * social links in Appearance > Customize > Estatein.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_intro   = estatein_section( 'intro' );
$estatein_form    = estatein_section( 'form' );
$estatein_offices = estatein_section( 'offices' );
$estatein_gallery = estatein_section( 'gallery' );

// Prefill from links such as "Send an Inquiry" or a team member's "Say Hello".
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- prefill only, escaped on output.
$estatein_prefill = isset( $_GET['message'] ) ? sanitize_textarea_field( wp_unslash( $_GET['message'] ) ) : '';
$estatein_type    = isset( $_GET['inquiry'] ) ? sanitize_text_field( wp_unslash( $_GET['inquiry'] ) ) : '';
// phpcs:enable

$estatein_email   = estatein_mod( 'contact_email' );
$estatein_phone   = estatein_mod( 'contact_phone' );
$estatein_socials = array();
foreach ( array( 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'facebook' => 'Facebook' ) as $estatein_key => $estatein_label ) {
	$estatein_url = estatein_mod( 'social_' . $estatein_key );
	if ( $estatein_url ) {
		$estatein_socials[] = array( $estatein_label, $estatein_url );
	}
}

$estatein_strip = array(
	array(
		'icon'  => 'mail-solid',
		'title' => $estatein_email,
		'url'   => 'mailto:' . antispambot( $estatein_email ),
	),
	array(
		'icon'  => 'phone',
		'title' => $estatein_phone,
		'url'   => 'tel:' . preg_replace( '/[^\d+]/', '', $estatein_phone ),
	),
	array(
		'icon'  => 'pin',
		'title' => estatein_mod( 'contact_hq' ),
		'url'   => estatein_mod( 'contact_hq_url' ),
	),
	array(
		'icon'  => 'logo-mark',
		'links' => $estatein_socials,
	),
);

$estatein_categories = array(
	'all'           => __( 'All', 'estatein' ),
	'regional'      => __( 'Regional', 'estatein' ),
	'international' => __( 'International', 'estatein' ),
);
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'title' => $estatein_intro['title'],
			'text'  => $estatein_intro['text'],
		)
	);

	get_template_part(
		'template-parts/components/features',
		null,
		array(
			'id'    => 'contact-details',
			'label' => __( 'Contact details', 'estatein' ),
			'items' => $estatein_strip,
		)
	);
	?>

	<section class="section" aria-labelledby="contact-form-title">
		<div class="container">
			<?php
			estatein_section_heading(
				array(
					'id'    => 'contact-form-title',
					'title' => $estatein_form['title'],
					'text'  => $estatein_form['text'],
				)
			);
			?>

			<form class="card form-card" id="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate data-validate>
				<?php
				estatein_form_hidden_fields( 'contact' );
				estatein_form_message( 'contact' );
				?>
				<div class="form-grid">
					<?php
					estatein_form_field( array( 'name' => 'first_name', 'id' => 'cf-first', 'label' => __( 'First Name', 'estatein' ), 'placeholder' => __( 'Enter First Name', 'estatein' ), 'required' => true, 'autocomplete' => 'given-name' ) );
					estatein_form_field( array( 'name' => 'last_name', 'id' => 'cf-last', 'label' => __( 'Last Name', 'estatein' ), 'placeholder' => __( 'Enter Last Name', 'estatein' ), 'autocomplete' => 'family-name' ) );
					estatein_form_field( array( 'name' => 'email', 'id' => 'cf-email', 'label' => __( 'Email', 'estatein' ), 'type' => 'email', 'placeholder' => __( 'Enter your Email', 'estatein' ), 'required' => true, 'autocomplete' => 'email' ) );
					estatein_form_field( array( 'name' => 'phone', 'id' => 'cf-phone', 'label' => __( 'Phone', 'estatein' ), 'type' => 'tel', 'placeholder' => __( 'Enter Phone Number', 'estatein' ), 'autocomplete' => 'tel' ) );
					estatein_form_field( array( 'name' => 'inquiry_type', 'id' => 'cf-inquiry', 'label' => __( 'Inquiry Type', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select Inquiry Type', 'estatein' ), 'options' => estatein_inquiry_types(), 'value' => $estatein_type ) );
					estatein_form_field( array( 'name' => 'how_heard', 'id' => 'cf-source', 'label' => __( 'How Did You Hear About Us?', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select', 'estatein' ), 'options' => estatein_heard_sources() ) );
					estatein_form_field( array( 'name' => 'message', 'id' => 'cf-message', 'label' => __( 'Message', 'estatein' ), 'type' => 'textarea', 'placeholder' => __( 'Enter your Message here..', 'estatein' ), 'required' => true, 'value' => $estatein_prefill, 'class' => 'field--full' ) );
					?>
				</div>
				<?php estatein_form_footer( 'cf' ); ?>
			</form>
		</div>
	</section>

	<section class="section" id="offices" aria-labelledby="offices-title">
		<div class="container">
			<?php
			estatein_section_heading(
				array(
					'id'    => 'offices-title',
					'title' => $estatein_offices['title'],
					'text'  => $estatein_offices['text'],
				)
			);
			?>
			<div class="tabs" role="group" aria-label="<?php esc_attr_e( 'Filter offices', 'estatein' ); ?>" data-filter-tabs="offices-list">
				<?php foreach ( $estatein_categories as $estatein_key => $estatein_label ) : ?>
					<button class="tabs__tab" type="button" data-filter="<?php echo esc_attr( $estatein_key ); ?>" aria-pressed="<?php echo 'all' === $estatein_key ? 'true' : 'false'; ?>"><?php echo esc_html( $estatein_label ); ?></button>
				<?php endforeach; ?>
			</div>
			<ul class="office-grid" id="offices-list" aria-live="polite">
				<?php foreach ( $estatein_offices['items'] as $estatein_office ) : ?>
					<li class="card office-card" data-category="<?php echo esc_attr( sanitize_key( $estatein_office['category'] ) ); ?>">
						<p class="office-card__label"><?php echo esc_html( $estatein_office['label'] ); ?></p>
						<h3 class="office-card__title"><?php echo esc_html( $estatein_office['title'] ); ?></h3>
						<p class="office-card__text"><?php echo esc_html( $estatein_office['text'] ); ?></p>
						<ul class="chips">
							<li><a class="chip" href="mailto:<?php echo esc_attr( antispambot( $estatein_office['email'] ) ); ?>"><?php estatein_the_icon( 'mail-solid' ); ?><?php echo esc_html( $estatein_office['email'] ); ?></a></li>
							<li><a class="chip" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $estatein_office['phone'] ) ); ?>"><?php estatein_the_icon( 'phone' ); ?><?php echo esc_html( $estatein_office['phone'] ); ?></a></li>
							<li><span class="chip"><?php estatein_the_icon( 'pin' ); ?><?php echo esc_html( $estatein_office['city'] ); ?></span></li>
						</ul>
						<a class="btn btn--primary btn--block" href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $estatein_office['title'] ) ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Get Direction', 'estatein' ); ?>
							<?php /* translators: %s: office address. */ ?>
							<span class="visually-hidden"><?php echo esc_html( sprintf( __( 'to %s (opens Google Maps in a new tab)', 'estatein' ), $estatein_office['title'] ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="section" aria-labelledby="gallery-title">
		<div class="container">
			<div class="gallery-panel">
				<?php
				$estatein_images = wp_list_pluck( $estatein_gallery['items'], 'image' );
				foreach ( $estatein_images as $estatein_n => $estatein_image ) {
					$estatein_attrs = array( 'class' => 'gallery-panel__img gallery-panel__img--' . ( $estatein_n + 1 ) );
					// Uploaded images keep their Media Library alt text; bundled defaults are decorative.
					if ( ! is_numeric( $estatein_image ) ) {
						$estatein_attrs['alt'] = '';
					}
					echo estatein_image( $estatein_image, 'large', $estatein_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in estatein_image().
				}
				?>
				<div class="gallery-panel__text">
					<?php
					estatein_section_heading(
						array(
							'id'    => 'gallery-title',
							'title' => $estatein_gallery['title'],
							'text'  => $estatein_gallery['text'],
						)
					);
					?>
				</div>
			</div>
		</div>
	</section>

	<?php
	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( get_the_content() ) ) :
			?>
			<div class="section"><div class="container entry-content"><?php the_content(); ?></div></div>
			<?php
		endif;
	endwhile;
	?>
</main>
<?php
get_footer();

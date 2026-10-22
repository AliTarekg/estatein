<?php
/**
 * Native form handlers: contact form and footer newsletter.
 *
 * No form plugin is needed: submissions go through admin-post.php, are
 * protected by a nonce and a honeypot field, sanitised, saved as a private
 * Inquiry in the dashboard and emailed to the site admin, and the visitor is
 * redirected back with a status message.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Private "Inquiries" post type: every submission is stored in the dashboard,
 * so nothing is lost on hosts where PHP mail is disabled (common on free hosting).
 */
function estatein_register_inquiries() {
	register_post_type(
		'inquiry',
		array(
			'labels'          => estatein_post_type_labels( __( 'Inquiry', 'estatein' ), __( 'Inquiries', 'estatein' ) ),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 25,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'estatein_register_inquiries' );

/**
 * Store a submission and email the site admin.
 *
 * @param string $title   Inquiry title.
 * @param string $body    Plain-text body.
 * @param array  $headers Mail headers.
 * @return bool True if the submission was saved or emailed.
 */
function estatein_store_and_notify( $title, $body, $headers = array() ) {
	$saved = wp_insert_post(
		array(
			'post_type'    => 'inquiry',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $body,
		)
	);

	$mailed = wp_mail( get_option( 'admin_email' ), $title, $body, $headers );

	return ( $saved && ! is_wp_error( $saved ) ) || $mailed;
}

/**
 * Redirect back to the form's page with a status flag.
 *
 * @param string $form   Form id.
 * @param string $status Status: sent | invalid | error.
 */
function estatein_form_redirect( $form, $status ) {
	$back = wp_get_referer();
	$back = $back ? $back : home_url( '/' );
	$back = remove_query_arg( array( 'estatein_form', 'estatein_status' ), $back );

	$anchor = 'newsletter' === $form ? '#newsletter' : '#contact-form';

	wp_safe_redirect(
		add_query_arg(
			array(
				'estatein_form'   => $form,
				'estatein_status' => $status,
			),
			$back
		) . $anchor
	);
	exit;
}

/**
 * Shared checks: nonce and honeypot.
 *
 * @param string $form Form id.
 */
function estatein_verify_submission( $form ) {
	$nonce = isset( $_POST['estatein_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['estatein_nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'estatein_' . $form ) ) {
		estatein_form_redirect( $form, 'error' );
	}

	// Bots fill every field; people never see this one.
	if ( ! empty( $_POST['estatein_website'] ) ) {
		estatein_form_redirect( $form, 'sent' );
	}
}

/**
 * Handle the contact form.
 */
function estatein_handle_contact() {
	estatein_verify_submission( 'contact' );

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified above.
	$data = array(
		'first_name' => isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '',
		'last_name'  => isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '',
		'email'      => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'phone'      => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
		'inquiry'    => isset( $_POST['inquiry_type'] ) ? sanitize_text_field( wp_unslash( $_POST['inquiry_type'] ) ) : '',
		'source'     => isset( $_POST['how_heard'] ) ? sanitize_text_field( wp_unslash( $_POST['how_heard'] ) ) : '',
		'message'    => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
		'terms'      => ! empty( $_POST['terms'] ),
	);
	// phpcs:enable

	if ( '' === $data['first_name'] || ! is_email( $data['email'] ) || '' === $data['message'] || ! $data['terms'] ) {
		estatein_form_redirect( 'contact', 'invalid' );
	}

	$body = sprintf(
		"Name: %s %s\nEmail: %s\nPhone: %s\nInquiry type: %s\nHeard about us: %s\n\n%s",
		$data['first_name'],
		$data['last_name'],
		$data['email'],
		$data['phone'],
		$data['inquiry'],
		$data['source'],
		$data['message']
	);

	$sent = estatein_store_and_notify(
		/* translators: %s: sender name. */
		sprintf( __( 'New inquiry from %s', 'estatein' ), trim( $data['first_name'] . ' ' . $data['last_name'] ) ),
		$body,
		array( 'Reply-To: ' . $data['email'] )
	);

	estatein_form_redirect( 'contact', $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_estatein_contact', 'estatein_handle_contact' );
add_action( 'admin_post_estatein_contact', 'estatein_handle_contact' );

/**
 * Handle the footer newsletter signup.
 */
function estatein_handle_newsletter() {
	estatein_verify_submission( 'newsletter' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.

	if ( ! is_email( $email ) ) {
		estatein_form_redirect( 'newsletter', 'invalid' );
	}

	$sent = estatein_store_and_notify(
		/* translators: %s: subscriber email. */
		sprintf( __( 'Newsletter signup: %s', 'estatein' ), $email ),
		/* translators: %s: subscriber email. */
		sprintf( __( 'New subscriber: %s', 'estatein' ), $email )
	);

	estatein_form_redirect( 'newsletter', $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_estatein_newsletter', 'estatein_handle_newsletter' );
add_action( 'admin_post_estatein_newsletter', 'estatein_handle_newsletter' );

/**
 * Status message for a form, read from the redirect query string.
 *
 * @param string $form Form id.
 * @return array{type:string,text:string}|null
 */
function estatein_form_status( $form ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
	if ( ! isset( $_GET['estatein_form'], $_GET['estatein_status'] ) || $form !== $_GET['estatein_form'] ) {
		return null;
	}
	$status = sanitize_key( wp_unslash( $_GET['estatein_status'] ) );
	// phpcs:enable

	$messages = array(
		'sent'    => array( 'success', __( 'Thank you! Your message has been sent.', 'estatein' ) ),
		'invalid' => array( 'error', __( 'Please fill in the required fields with valid details.', 'estatein' ) ),
		'error'   => array( 'error', __( 'Something went wrong. Please try again.', 'estatein' ) ),
	);

	if ( 'newsletter' === $form ) {
		$messages['sent'][1] = __( 'Thanks for subscribing!', 'estatein' );
	}

	if ( ! isset( $messages[ $status ] ) ) {
		return null;
	}

	return array(
		'type' => $messages[ $status ][0],
		'text' => $messages[ $status ][1],
	);
}

/**
 * Print the hidden fields every theme form needs.
 *
 * @param string $form Form id.
 */
function estatein_form_hidden_fields( $form ) {
	wp_nonce_field( 'estatein_' . $form, 'estatein_nonce' );
	printf( '<input type="hidden" name="action" value="%s">', esc_attr( 'estatein_' . $form ) );
	echo '<div class="visually-hidden" aria-hidden="true"><label>Website<input type="text" name="estatein_website" tabindex="-1" autocomplete="off"></label></div>';
}

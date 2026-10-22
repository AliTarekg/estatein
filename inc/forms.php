<?php
/**
 * Native form handling: contact form, property inquiry, property request
 * ("Let's Make it Happen") and the footer newsletter.
 *
 * No form plugin is needed. Every form posts to admin-post.php and goes
 * through one handler that checks a nonce, a honeypot field and a per-IP
 * rate limit, validates and sanitises fields from a declared spec, saves the
 * submission as a private Inquiry in the dashboard, emails the site admin,
 * and redirects back with a status message. Saving first means nothing is
 * lost on hosts where PHP mail is disabled.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Private "Inquiries" post type: every submission is stored in the dashboard.
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
 * Form definitions. Field spec: label, required, type (text|email|textarea|checkbox|choice), choices.
 *
 * @return array
 */
function estatein_form_specs() {
	$person = array(
		'first_name' => array( __( 'First Name', 'estatein' ), true ),
		'last_name'  => array( __( 'Last Name', 'estatein' ), false ),
		'email'      => array( __( 'Email', 'estatein' ), true, 'email' ),
		'phone'      => array( __( 'Phone', 'estatein' ), false ),
	);
	$terms  = array( 'terms' => array( __( 'Agreed to terms', 'estatein' ), true, 'checkbox' ) );

	return array(
		'contact'    => array(
			'anchor' => 'contact-form',
			/* translators: %s: sender name. */
			'title'  => __( 'Contact form: %s', 'estatein' ),
			'fields' => $person + array(
				'inquiry_type' => array( __( 'Inquiry type', 'estatein' ), false, 'choice', estatein_inquiry_types() ),
				'how_heard'    => array( __( 'Heard about us', 'estatein' ), false, 'choice', estatein_heard_sources() ),
				'message'      => array( __( 'Message', 'estatein' ), true, 'textarea' ),
			) + $terms,
		),
		'property'   => array(
			'anchor' => 'inquiry-form',
			/* translators: %s: sender name. */
			'title'  => __( 'Property inquiry: %s', 'estatein' ),
			'fields' => $person + array(
				'property' => array( __( 'Selected property', 'estatein' ), true ),
				'message'  => array( __( 'Message', 'estatein' ), true, 'textarea' ),
			) + $terms,
		),
		'match'      => array(
			'anchor' => 'match-form',
			/* translators: %s: sender name. */
			'title'  => __( 'Property request: %s', 'estatein' ),
			'fields' => $person + array(
				'location'       => array( __( 'Preferred location', 'estatein' ), false ),
				'property_type'  => array( __( 'Property type', 'estatein' ), false, 'choice', estatein_property_types() ),
				'bathrooms'      => array( __( 'Bathrooms', 'estatein' ), false, 'choice', array( '1', '2', '3', '4+' ) ),
				'bedrooms'       => array( __( 'Bedrooms', 'estatein' ), false, 'choice', array( '1', '2', '3', '4', '5+' ) ),
				'budget'         => array( __( 'Budget', 'estatein' ), false, 'choice', array_values( estatein_budget_options() ) ),
				'contact_method' => array( __( 'Preferred contact method', 'estatein' ), false, 'choice', array( 'phone', 'email' ) ),
				'message'        => array( __( 'Message', 'estatein' ), false, 'textarea' ),
			) + $terms,
		),
		'newsletter' => array(
			'anchor' => 'newsletter',
			/* translators: %s: subscriber email. */
			'title'  => __( 'Newsletter signup: %s', 'estatein' ),
			'fields' => array(
				'email' => array( __( 'Email', 'estatein' ), true, 'email' ),
			),
		),
	);
}

/**
 * Inquiry types for the contact form.
 *
 * @return string[]
 */
function estatein_inquiry_types() {
	return array( __( 'Buying', 'estatein' ), __( 'Selling', 'estatein' ), __( 'Renting', 'estatein' ), __( 'Property Management', 'estatein' ), __( 'Valuation', 'estatein' ), __( 'Other', 'estatein' ) );
}

/**
 * "How did you hear about us" options.
 *
 * @return string[]
 */
function estatein_heard_sources() {
	return array( __( 'Search engine', 'estatein' ), __( 'Social media', 'estatein' ), __( 'Friend or family', 'estatein' ), __( 'Advertisement', 'estatein' ), __( 'Other', 'estatein' ) );
}

/**
 * Budget options for the property request form.
 *
 * @return string[]
 */
function estatein_budget_options() {
	return array( __( 'Under $500,000', 'estatein' ), __( '$500,000 – $1,000,000', 'estatein' ), __( '$1,000,000 – $2,000,000', 'estatein' ), __( 'Over $2,000,000', 'estatein' ) );
}

/**
 * Redirect back to the form's page with a status flag.
 *
 * @param string $form   Form id.
 * @param string $status Status: sent | invalid | error | throttled.
 */
function estatein_form_redirect( $form, $status ) {
	$specs = estatein_form_specs();
	$back  = wp_get_referer();
	$back  = $back ? $back : home_url( '/' );
	$back  = remove_query_arg( array( 'estatein_form', 'estatein_status' ), $back );

	wp_safe_redirect(
		add_query_arg(
			array(
				'estatein_form'   => $form,
				'estatein_status' => $status,
			),
			$back
		) . '#' . $specs[ $form ]['anchor']
	);
	exit;
}

/**
 * Simple per-IP rate limit: at most 5 submissions per form every 10 minutes.
 *
 * REMOTE_ADDR is used on purpose (forwarded headers can be spoofed). Behind a
 * reverse proxy every visitor may share one address: raise the limit with the
 * "estatein_form_rate_limit" filter, or return 0 to turn it off.
 *
 * @param string $form Form id.
 * @return bool True when the visitor is over the limit.
 */
function estatein_form_throttled( $form ) {
	$limit = (int) apply_filters( 'estatein_form_rate_limit', 5, $form );
	if ( $limit <= 0 ) {
		return false;
	}

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'estatein_rl_' . md5( $form . '|' . $ip );
	$hit = (int) get_transient( $key );

	if ( $hit >= $limit ) {
		return true;
	}

	set_transient( $key, $hit + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

/**
 * Handle any theme form.
 */
function estatein_handle_form() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- the nonce is verified right below.
	$form  = isset( $_POST['estatein_form_id'] ) ? sanitize_key( wp_unslash( $_POST['estatein_form_id'] ) ) : '';
	$specs = estatein_form_specs();

	if ( ! isset( $specs[ $form ] ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}

	$nonce = isset( $_POST['estatein_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['estatein_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'estatein_' . $form ) ) {
		estatein_form_redirect( $form, 'error' );
	}

	// Bots fill every field; people never see this one. Pretend it worked.
	if ( ! empty( $_POST['estatein_website'] ) ) {
		estatein_form_redirect( $form, 'sent' );
	}

	if ( estatein_form_throttled( $form ) ) {
		estatein_form_redirect( $form, 'throttled' );
	}

	$data  = array();
	$valid = true;

	foreach ( $specs[ $form ]['fields'] as $name => $field ) {
		$type     = isset( $field[2] ) ? $field[2] : 'text';
		$required = $field[1];
		$raw      = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per type below.

		if ( 'checkbox' === $type ) {
			$value = ! empty( $raw ) ? __( 'Yes', 'estatein' ) : '';
		} elseif ( 'textarea' === $type ) {
			$value = sanitize_textarea_field( $raw );
		} elseif ( 'email' === $type ) {
			$value = sanitize_email( $raw );
			if ( '' !== $value && ! is_email( $value ) ) {
				$valid = false;
			}
		} else {
			$value = sanitize_text_field( $raw );
			// Choice fields only accept the offered options.
			if ( 'choice' === $type && '' !== $value && ! in_array( $value, $field[3], true ) ) {
				$value = '';
			}
		}

		if ( $required && '' === $value ) {
			$valid = false;
		}

		$data[ $name ] = array( $field[0], mb_substr( $value, 0, 'textarea' === $type ? 5000 : 200 ) );
	}
	// phpcs:enable

	if ( ! $valid ) {
		estatein_form_redirect( $form, 'invalid' );
	}

	$lines = array();
	foreach ( $data as $item ) {
		if ( '' !== $item[1] ) {
			$lines[] = $item[0] . ': ' . $item[1];
		}
	}

	$who   = isset( $data['first_name'] ) ? trim( $data['first_name'][1] . ' ' . $data['last_name'][1] ) : $data['email'][1];
	$title = sprintf( $specs[ $form ]['title'], $who );
	$body  = implode( "\n", $lines );

	$saved = wp_insert_post(
		array(
			'post_type'    => 'inquiry',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $body,
		),
		true
	);

	$headers = array();
	if ( isset( $data['email'] ) && is_email( $data['email'][1] ) ) {
		$headers[] = 'Reply-To: ' . $data['email'][1];
	}
	$mailed = wp_mail( get_option( 'admin_email' ), $title, $body, $headers );

	estatein_form_redirect( $form, ( $saved && ! is_wp_error( $saved ) ) || $mailed ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_estatein_form', 'estatein_handle_form' );
add_action( 'admin_post_estatein_form', 'estatein_handle_form' );

/**
 * Status message for a form, read from the redirect query string.
 *
 * @param string $form Form id.
 * @return array{type:string,text:string}|null
 */
function estatein_form_status( $form ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
	if ( ! isset( $_GET['estatein_form'], $_GET['estatein_status'] ) || sanitize_key( wp_unslash( $_GET['estatein_form'] ) ) !== $form ) {
		return null;
	}
	$status = sanitize_key( wp_unslash( $_GET['estatein_status'] ) );
	// phpcs:enable

	$messages = array(
		'sent'      => array( 'success', __( "Thank you! Your message has been received. We'll get back to you within one business day.", 'estatein' ) ),
		'invalid'   => array( 'error', __( 'Please fill in the required fields with valid details.', 'estatein' ) ),
		'error'     => array( 'error', __( 'Something went wrong. Please reload the page and try again.', 'estatein' ) ),
		'throttled' => array( 'error', __( 'Too many submissions in a short time. Please try again in a few minutes.', 'estatein' ) ),
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
	echo '<input type="hidden" name="action" value="estatein_form">';
	printf( '<input type="hidden" name="estatein_form_id" value="%s">', esc_attr( $form ) );
	echo '<div class="visually-hidden" aria-hidden="true"><label>Website<input type="text" name="estatein_website" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * Print a form status message (if any) for a form.
 *
 * @param string $form Form id.
 */
function estatein_form_message( $form ) {
	$status = estatein_form_status( $form );
	if ( $status ) {
		printf(
			'<p class="form-message form-message--%1$s" role="%2$s" tabindex="-1" data-focus-on-load>%3$s</p>',
			esc_attr( $status['type'] ),
			'success' === $status['type'] ? 'status' : 'alert',
			esc_html( $status['text'] )
		);
	}
}

/**
 * Print the "I agree with Terms of Use and Privacy Policy" row and submit button.
 *
 * @param string $id_prefix Unique prefix for the checkbox ID.
 */
function estatein_form_footer( $id_prefix ) {
	$terms   = estatein_mod( 'terms_url' );
	$privacy = get_privacy_policy_url() ? get_privacy_policy_url() : $terms;
	?>
	<div class="form-footer">
		<label class="checkbox" for="<?php echo esc_attr( $id_prefix ); ?>-terms">
			<input id="<?php echo esc_attr( $id_prefix ); ?>-terms" type="checkbox" name="terms" value="1" required>
			<span>
				<?php esc_html_e( 'I agree with', 'estatein' ); ?>
				<a href="<?php echo esc_url( $terms ); ?>"><?php esc_html_e( 'Terms of Use', 'estatein' ); ?></a>
				<?php esc_html_e( 'and', 'estatein' ); ?>
				<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Privacy Policy', 'estatein' ); ?></a>
			</span>
		</label>
		<button class="btn btn--primary btn--lg" type="submit"><?php esc_html_e( 'Send Your Message', 'estatein' ); ?></button>
	</div>
	<?php
}

/**
 * Print a labelled form field.
 *
 * @param array $args {
 *     @type string   $name         Field name.
 *     @type string   $id           Element ID.
 *     @type string   $label        Visible label.
 *     @type string   $type         text|email|tel|textarea|select.
 *     @type string   $placeholder  Placeholder / first select option.
 *     @type bool     $required     Required.
 *     @type string[] $options      Select options.
 *     @type string   $value        Prefilled value.
 *     @type string   $autocomplete Autocomplete token.
 *     @type string   $class        Extra wrapper class.
 *     @type bool     $readonly     Read-only text field.
 * }
 */
function estatein_form_field( $args ) {
	$a = wp_parse_args(
		$args,
		array(
			'name'         => '',
			'id'           => '',
			'label'        => '',
			'type'         => 'text',
			'placeholder'  => '',
			'required'     => false,
			'options'      => array(),
			'value'        => '',
			'autocomplete' => '',
			'class'        => '',
			'readonly'     => false,
			'icon'         => '',
		)
	);

	$common = sprintf(
		'class="field__input%1$s" id="%2$s" name="%3$s"%4$s%5$s',
		'select' === $a['type'] ? ' field__select' : '',
		esc_attr( $a['id'] ),
		esc_attr( $a['name'] ),
		$a['required'] ? ' required' : '',
		$a['autocomplete'] ? ' autocomplete="' . esc_attr( $a['autocomplete'] ) . '"' : ''
	);
	?>
	<div class="field<?php echo $a['class'] ? ' ' . esc_attr( $a['class'] ) : ''; ?>">
		<label class="field__label" for="<?php echo esc_attr( $a['id'] ); ?>">
			<?php echo esc_html( $a['label'] ); ?>
			<?php if ( $a['required'] ) : ?>
				<span class="field__required" aria-hidden="true">*</span>
			<?php endif; ?>
		</label>
		<?php if ( 'textarea' === $a['type'] ) : ?>
			<textarea <?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?> rows="5" placeholder="<?php echo esc_attr( $a['placeholder'] ); ?>"><?php echo esc_textarea( $a['value'] ); ?></textarea>
		<?php elseif ( 'select' === $a['type'] ) : ?>
			<select <?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
				<option value=""><?php echo esc_html( $a['placeholder'] ); ?></option>
				<?php foreach ( $a['options'] as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $a['value'], $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php else : ?>
			<span class="field__control<?php echo $a['icon'] ? ' field__control--icon' : ''; ?>">
				<input <?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?> type="<?php echo esc_attr( $a['type'] ); ?>" placeholder="<?php echo esc_attr( $a['placeholder'] ); ?>" value="<?php echo esc_attr( $a['value'] ); ?>"<?php echo $a['readonly'] ? ' readonly' : ''; ?>>
				<?php
				if ( $a['icon'] ) {
					estatein_the_icon( $a['icon'], 'field__icon' );
				}
				?>
			</span>
		<?php endif; ?>
	</div>
	<?php
}

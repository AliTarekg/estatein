<?php
/**
 * "Let's Make it Happen" property request form (Properties page).
 * Handled by estatein_handle_form() in inc/forms.php.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_locations = estatein_property_locations();
?>
<section class="section" aria-labelledby="match-title">
	<div class="container">
		<?php
		estatein_section_heading(
			array(
				'id'    => 'match-title',
				'title' => __( "Let's Make it Happen", 'estatein' ),
				'text'  => __( "Ready to take the first step toward your dream property? Fill out the form below, and our real estate wizards will work their magic to find your perfect match. Don't wait; let's embark on this exciting journey together.", 'estatein' ),
			)
		);
		?>
		<form class="card form-card" id="match-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate data-validate>
			<?php
			estatein_form_hidden_fields( 'match' );
			estatein_form_message( 'match' );
			?>
			<div class="form-grid form-grid--4">
				<?php
				estatein_form_field( array( 'name' => 'first_name', 'id' => 'mf-first', 'label' => __( 'First Name', 'estatein' ), 'placeholder' => __( 'Enter First Name', 'estatein' ), 'required' => true, 'autocomplete' => 'given-name' ) );
				estatein_form_field( array( 'name' => 'last_name', 'id' => 'mf-last', 'label' => __( 'Last Name', 'estatein' ), 'placeholder' => __( 'Enter Last Name', 'estatein' ), 'autocomplete' => 'family-name' ) );
				estatein_form_field( array( 'name' => 'email', 'id' => 'mf-email', 'label' => __( 'Email', 'estatein' ), 'type' => 'email', 'placeholder' => __( 'Enter your Email', 'estatein' ), 'required' => true, 'autocomplete' => 'email' ) );
				estatein_form_field( array( 'name' => 'phone', 'id' => 'mf-phone', 'label' => __( 'Phone', 'estatein' ), 'type' => 'tel', 'placeholder' => __( 'Enter Phone Number', 'estatein' ), 'autocomplete' => 'tel' ) );
				estatein_form_field( array( 'name' => 'location', 'id' => 'mf-location', 'label' => __( 'Preferred Location', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select Location', 'estatein' ), 'options' => $estatein_locations ) );
				estatein_form_field( array( 'name' => 'property_type', 'id' => 'mf-type', 'label' => __( 'Property Type', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select Property Type', 'estatein' ), 'options' => estatein_property_types() ) );
				estatein_form_field( array( 'name' => 'bathrooms', 'id' => 'mf-baths', 'label' => __( 'No. of Bathrooms', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select no. of Bathrooms', 'estatein' ), 'options' => array( '1', '2', '3', '4+' ) ) );
				estatein_form_field( array( 'name' => 'bedrooms', 'id' => 'mf-beds', 'label' => __( 'No. of Bedrooms', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select no. of Bedrooms', 'estatein' ), 'options' => array( '1', '2', '3', '4', '5+' ) ) );
				estatein_form_field( array( 'name' => 'budget', 'id' => 'mf-budget', 'label' => __( 'Budget', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select Budget', 'estatein' ), 'options' => estatein_budget_options(), 'class' => 'field--half' ) );
				?>
				<fieldset class="field field--half">
					<legend class="field__label"><?php esc_html_e( 'Preferred Contact Method', 'estatein' ); ?></legend>
					<div class="choice-group">
						<label class="choice">
							<?php estatein_the_icon( 'phone' ); ?>
							<span><?php esc_html_e( 'By Phone', 'estatein' ); ?></span>
							<input type="radio" name="contact_method" value="phone" checked>
						</label>
						<label class="choice">
							<?php estatein_the_icon( 'mail-solid' ); ?>
							<span><?php esc_html_e( 'By Email', 'estatein' ); ?></span>
							<input type="radio" name="contact_method" value="email">
						</label>
					</div>
				</fieldset>
				<?php
				estatein_form_field( array( 'name' => 'message', 'id' => 'mf-message', 'label' => __( 'Message', 'estatein' ), 'type' => 'textarea', 'placeholder' => __( 'Enter your Message here..', 'estatein' ), 'class' => 'field--full' ) );
				?>
			</div>
			<?php estatein_form_footer( 'mf' ); ?>
		</form>
	</div>
</section>

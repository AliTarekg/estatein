<?php
/**
 * Single property: gallery, description and facts, key features, inquiry
 * form, pricing breakdown and FAQs (Figma "Property Details" page).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		$estatein_id       = get_the_ID();
		$estatein_title    = get_the_title();
		$estatein_price    = estatein_field( 'price', '', $estatein_id );
		$estatein_location = estatein_field( 'location', '', $estatein_id );
		$estatein_beds     = estatein_field( 'bedrooms', '', $estatein_id );
		$estatein_baths    = estatein_field( 'bathrooms', '', $estatein_id );
		$estatein_area     = estatein_field( 'area', '', $estatein_id );
		$estatein_features = estatein_property_features( $estatein_id );
		$estatein_gallery  = estatein_gallery_ids( $estatein_id );
		$estatein_pricing  = estatein_property_pricing( $estatein_id );
		$estatein_contact  = add_query_arg(
			array(
				'inquiry' => __( 'Buying', 'estatein' ),
				/* translators: %s: property name. */
				'message' => rawurlencode( sprintf( __( "Hi, I'd like to know more about the costs for %s.", 'estatein' ), $estatein_title ) ),
			),
			home_url( '/contact-us/' )
		) . '#contact-form';
		?>
		<article class="property-single">
			<section class="section section--first" aria-labelledby="page-title">
				<div class="container">
					<header class="property-single__head">
						<div class="property-single__heading">
							<h1 class="property-single__title" id="page-title"><?php echo esc_html( $estatein_title ); ?></h1>
							<?php if ( $estatein_location ) : ?>
								<p class="chip chip--outline"><?php estatein_the_icon( 'pin' ); ?><?php echo esc_html( $estatein_location ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( '' !== $estatein_price ) : ?>
							<p class="price price--lg">
								<span class="price__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span>
								<span class="price__value"><?php echo esc_html( estatein_format_price( $estatein_price ) ); ?></span>
							</p>
						<?php endif; ?>
					</header>

					<?php if ( $estatein_gallery ) : ?>
						<div class="card gallery" data-carousel data-gallery>
							<?php if ( count( $estatein_gallery ) > 1 ) : ?>
								<ul class="gallery__thumbs" aria-label="<?php esc_attr_e( 'Choose a photo', 'estatein' ); ?>">
									<?php foreach ( $estatein_gallery as $estatein_n => $estatein_image ) : ?>
										<li>
											<button class="gallery__thumb" type="button" data-carousel-goto="<?php echo esc_attr( $estatein_n ); ?>" aria-current="<?php echo 0 === $estatein_n ? 'true' : 'false'; ?>">
												<?php echo wp_get_attachment_image( $estatein_image, 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
												<?php /* translators: %d: photo number. */ ?>
												<span class="visually-hidden"><?php echo esc_html( sprintf( __( 'Show photo %d', 'estatein' ), $estatein_n + 1 ) ); ?></span>
											</button>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<ul class="carousel__track gallery__track" data-carousel-track tabindex="0" aria-label="<?php esc_attr_e( 'Property photos', 'estatein' ); ?>">
								<?php foreach ( $estatein_gallery as $estatein_n => $estatein_image ) : ?>
									<li class="carousel__slide gallery__slide">
										<?php
										echo wp_get_attachment_image(
											$estatein_image,
											'large',
											false,
											array(
												'class'   => 'gallery__img',
												'loading' => $estatein_n < 2 ? 'eager' : 'lazy',
												'fetchpriority' => 0 === $estatein_n ? 'high' : 'auto',
												'sizes'   => '(min-width: 768px) 46vw, 90vw',
											)
										);
										?>
									</li>
								<?php endforeach; ?>
							</ul>

							<?php if ( count( $estatein_gallery ) > 1 ) : ?>
								<div class="gallery__nav">
									<button class="icon-button icon-button--round" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous photo', 'estatein' ); ?>" disabled><?php estatein_the_icon( 'arrow-left' ); ?></button>
									<span class="gallery__dots" aria-hidden="true">
										<?php foreach ( $estatein_gallery as $estatein_n => $estatein_image ) : ?>
											<span class="gallery__dot<?php echo 0 === $estatein_n ? ' is-active' : ''; ?>" data-carousel-dot></span>
										<?php endforeach; ?>
									</span>
									<button class="icon-button icon-button--round" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Next photo', 'estatein' ); ?>"><?php estatein_the_icon( 'arrow-right' ); ?></button>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="property-single__grid">
						<section class="card property-single__panel" aria-labelledby="description-title">
							<h2 class="property-single__subtitle" id="description-title"><?php esc_html_e( 'Description', 'estatein' ); ?></h2>
							<div class="entry-content"><?php the_content(); ?></div>

							<dl class="facts">
								<?php if ( '' !== $estatein_beds ) : ?>
									<div class="fact"><dt><?php estatein_the_icon( 'bed' ); ?><?php esc_html_e( 'Bedrooms', 'estatein' ); ?></dt><dd><?php echo esc_html( estatein_pad( $estatein_beds ) ); ?></dd></div>
								<?php endif; ?>
								<?php if ( '' !== $estatein_baths ) : ?>
									<div class="fact"><dt><?php estatein_the_icon( 'bath' ); ?><?php esc_html_e( 'Bathrooms', 'estatein' ); ?></dt><dd><?php echo esc_html( is_numeric( $estatein_baths ) && floor( $estatein_baths ) == $estatein_baths ? estatein_pad( $estatein_baths ) : $estatein_baths ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual -- numeric string vs float. ?></dd></div>
								<?php endif; ?>
								<?php if ( '' !== $estatein_area ) : ?>
									<div class="fact"><dt><?php estatein_the_icon( 'area' ); ?><?php esc_html_e( 'Area', 'estatein' ); ?></dt><dd><?php echo esc_html( estatein_format_number( $estatein_area, __( 'Square Feet', 'estatein' ) ) ); ?></dd></div>
								<?php endif; ?>
							</dl>
						</section>

						<?php if ( $estatein_features ) : ?>
							<section class="card property-single__panel" aria-labelledby="features-title">
								<h2 class="property-single__subtitle" id="features-title"><?php esc_html_e( 'Key Features and Amenities', 'estatein' ); ?></h2>
								<ul class="feature-list">
									<?php foreach ( $estatein_features as $estatein_feature ) : ?>
										<li><?php estatein_the_icon( 'bolt' ); ?><?php echo esc_html( $estatein_feature ); ?></li>
									<?php endforeach; ?>
								</ul>
							</section>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<section class="section" aria-labelledby="inquiry-title">
				<div class="container split split--form">
					<div class="split__text">
						<?php
						estatein_section_heading(
							array(
								'id'    => 'inquiry-title',
								/* translators: %s: property name. */
								'title' => sprintf( __( 'Inquire About %s', 'estatein' ), $estatein_title ),
								'text'  => __( 'Interested in this property? Fill out the form below, and our real estate experts will get back to you with more details, including scheduling a viewing and answering any questions you may have.', 'estatein' ),
							)
						);
						?>
					</div>
					<form class="card form-card" id="inquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate data-validate>
						<?php
						estatein_form_hidden_fields( 'property' );
						estatein_form_message( 'property' );
						?>
						<div class="form-grid form-grid--2">
							<?php
							estatein_form_field( array( 'name' => 'first_name', 'id' => 'pf-first', 'label' => __( 'First Name', 'estatein' ), 'placeholder' => __( 'Enter First Name', 'estatein' ), 'required' => true, 'autocomplete' => 'given-name' ) );
							estatein_form_field( array( 'name' => 'last_name', 'id' => 'pf-last', 'label' => __( 'Last Name', 'estatein' ), 'placeholder' => __( 'Enter Last Name', 'estatein' ), 'autocomplete' => 'family-name' ) );
							estatein_form_field( array( 'name' => 'email', 'id' => 'pf-email', 'label' => __( 'Email', 'estatein' ), 'type' => 'email', 'placeholder' => __( 'Enter your Email', 'estatein' ), 'required' => true, 'autocomplete' => 'email' ) );
							estatein_form_field( array( 'name' => 'phone', 'id' => 'pf-phone', 'label' => __( 'Phone', 'estatein' ), 'type' => 'tel', 'placeholder' => __( 'Enter Phone Number', 'estatein' ), 'autocomplete' => 'tel' ) );
							estatein_form_field(
								array(
									'name'     => 'property',
									'id'       => 'pf-property',
									'label'    => __( 'Selected Property', 'estatein' ),
									'value'    => $estatein_location ? $estatein_title . ', ' . $estatein_location : $estatein_title,
									'required' => true,
									'readonly' => true,
									'icon'     => 'pin',
									'class'    => 'field--full',
								)
							);
							estatein_form_field( array( 'name' => 'message', 'id' => 'pf-message', 'label' => __( 'Message', 'estatein' ), 'type' => 'textarea', 'placeholder' => __( 'Enter your Message here..', 'estatein' ), 'required' => true, 'class' => 'field--full' ) );
							?>
						</div>
						<?php estatein_form_footer( 'pf' ); ?>
					</form>
				</div>
			</section>

			<?php if ( $estatein_pricing ) : ?>
				<section class="section" aria-labelledby="pricing-title">
					<div class="container">
						<?php
						estatein_section_heading(
							array(
								'id'    => 'pricing-title',
								'title' => __( 'Comprehensive Pricing Details', 'estatein' ),
								/* translators: %s: property name. */
								'text'  => sprintf( __( 'At Estatein, transparency is key. We want you to have a clear understanding of all costs associated with your property investment. Below, we break down the pricing for %s to help you make an informed decision', 'estatein' ), $estatein_title ),
							)
						);
						?>
						<p class="note">
							<strong class="note__label"><?php esc_html_e( 'Note', 'estatein' ); ?></strong>
							<span><?php esc_html_e( 'The figures provided above are estimates and may vary depending on the property, location, and individual circumstances.', 'estatein' ); ?></span>
						</p>
						<div class="pricing">
							<p class="price price--xl pricing__listing">
								<span class="price__label"><?php esc_html_e( 'Listing Price', 'estatein' ); ?></span>
								<span class="price__value"><?php echo esc_html( $estatein_pricing['price'] ); ?></span>
							</p>
							<div class="pricing__groups">
								<?php foreach ( $estatein_pricing['groups'] as $estatein_group ) : ?>
									<section class="card pricing-card">
										<header class="pricing-card__head">
											<h3 class="pricing-card__title"><?php echo esc_html( $estatein_group['title'] ); ?></h3>
											<a class="btn btn--dark" href="<?php echo esc_url( $estatein_contact ); ?>"><?php esc_html_e( 'Learn More', 'estatein' ); ?><span class="visually-hidden"> <?php echo esc_html( $estatein_group['title'] ); ?></span></a>
										</header>
										<?php foreach ( $estatein_group['rows'] as $estatein_row ) : ?>
											<dl class="pricing-card__row">
												<?php foreach ( $estatein_row as $estatein_cell ) : ?>
													<div class="pricing-card__cell">
														<dt><?php echo esc_html( $estatein_cell[0] ); ?></dt>
														<dd>
															<span class="pricing-card__value"><?php echo esc_html( $estatein_cell[1] ); ?></span>
															<?php if ( $estatein_cell[2] ) : ?>
																<span class="chip chip--note"><?php echo esc_html( $estatein_cell[2] ); ?></span>
															<?php endif; ?>
														</dd>
													</div>
												<?php endforeach; ?>
											</dl>
										<?php endforeach; ?>
									</section>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</section>
			<?php endif; ?>
		</article>
		<?php
	endwhile;

	get_template_part( 'template-parts/home/faq' );
	?>
</main>
<?php
get_footer();

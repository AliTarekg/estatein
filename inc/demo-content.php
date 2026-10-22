<?php
/**
 * One-time sample content on theme activation.
 *
 * Creates the pages, menus, properties, testimonials and FAQs shown in the
 * Figma design, so a fresh install looks like the design immediately and
 * editors can see how each content type is used. Runs once (guarded by an
 * option) and never overwrites existing content.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Seed demo content after the theme is activated.
 */
function estatein_seed_demo_content() {
	if ( get_option( 'estatein_demo_seeded' ) ) {
		return;
	}

	// Post types are registered on init; make sure they exist during activation.
	estatein_register_post_types();

	// Replace WordPress's placeholder site name with the brand.
	if ( in_array( get_option( 'blogname' ), array( '', 'My WordPress Website', 'My Blog' ), true ) ) {
		update_option( 'blogname', 'Estatein' );
	}

	estatein_seed_pages();
	estatein_seed_properties();
	estatein_seed_testimonials();
	estatein_seed_faqs();
	estatein_seed_menus();

	// Pretty permalinks are needed for /properties/; only set if none chosen yet.
	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();

	update_option( 'estatein_demo_seeded', time() );
}
add_action( 'after_switch_theme', 'estatein_seed_demo_content' );

/**
 * Insert a post only if no post of that type and slug exists.
 *
 * @param array $args wp_insert_post() args (post_type and post_name required).
 * @return int Post ID (existing or new), 0 on failure.
 */
function estatein_insert_once( $args ) {
	$existing = get_page_by_path( $args['post_name'], OBJECT, $args['post_type'] );
	if ( $existing ) {
		return (int) $existing->ID;
	}

	$id = wp_insert_post( array_merge( array( 'post_status' => 'publish' ), $args ), true );

	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Save a value the way ACF stores it (value + field key reference), so the
 * field shows up filled in the editor.
 *
 * @param int    $post_id Post ID.
 * @param string $name    Field name.
 * @param mixed  $value   Value.
 * @param string $key     ACF field key.
 */
function estatein_seed_meta( $post_id, $name, $value, $key ) {
	update_post_meta( $post_id, $name, $value );
	update_post_meta( $post_id, '_' . $name, $key );
}

/**
 * Pages used by the navigation and footer.
 */
function estatein_seed_pages() {
	$home = estatein_insert_once(
		array(
			'post_type'  => 'page',
			'post_name'  => 'home',
			'post_title' => __( 'Home', 'estatein' ),
		)
	);

	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}

	$pages = array(
		'about-us'             => __( 'About Us', 'estatein' ),
		'services'             => __( 'Services', 'estatein' ),
		'contact-us'           => __( 'Contact Us', 'estatein' ),
		'terms-and-conditions' => __( 'Terms & Conditions', 'estatein' ),
	);

	foreach ( $pages as $slug => $title ) {
		$id = estatein_insert_once(
			array(
				'post_type'    => 'page',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => 'contact-us' === $slug ? '' : '<p>' . esc_html__( 'Content for this page is managed in the editor.', 'estatein' ) . '</p>',
			)
		);

		if ( $id && 'contact-us' === $slug ) {
			update_post_meta( $id, '_wp_page_template', 'page-contact.php' );
		}
	}
}

/**
 * Sample listings (copy from the Figma file).
 */
function estatein_seed_properties() {
	$properties = array(
		array( 'Seaside Serenity Villa', 'A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood.', 550000, 4, 3, 'Villa', 'Malibu, California' ),
		array( 'Metropolitan Haven', 'A chic and fully-furnished 2-bedroom apartment with panoramic city views.', 550000, 2, 2, 'Apartment', 'Downtown, New York' ),
		array( 'Rustic Retreat Cottage', 'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community.', 550000, 3, 3, 'Cottage', 'Asheville, North Carolina' ),
		array( 'Lakeside Modern Residence', 'A bright 5-bedroom family home with private lake access and a large garden.', 875000, 5, 4, 'Villa', 'Lake Tahoe, Nevada' ),
		array( 'Skyline Penthouse', 'A top-floor 3-bedroom penthouse with a wraparound terrace and skyline views.', 1250000, 3, 3, 'Penthouse', 'Miami, Florida' ),
		array( 'Garden Court Townhouse', 'A modern 3-bedroom townhouse steps away from parks, schools and shops.', 465000, 3, 2, 'Townhouse', 'Austin, Texas' ),
	);

	foreach ( $properties as $order => $p ) {
		list( $title, $excerpt, $price, $beds, $baths, $type, $location ) = $p;

		$id = estatein_insert_once(
			array(
				'post_type'    => 'property',
				'post_name'    => sanitize_title( $title ),
				'post_title'   => $title,
				'post_excerpt' => $excerpt,
				'post_content' => '<p>' . esc_html( $excerpt ) . ' ' . esc_html__( 'Contact our team to arrange a private viewing and get the full property details.', 'estatein' ) . '</p>',
				'menu_order'   => $order,
			)
		);

		if ( ! $id ) {
			continue;
		}

		estatein_seed_meta( $id, 'price', $price, 'field_estatein_property_price' );
		estatein_seed_meta( $id, 'bedrooms', $beds, 'field_estatein_property_bedrooms' );
		estatein_seed_meta( $id, 'bathrooms', $baths, 'field_estatein_property_bathrooms' );
		estatein_seed_meta( $id, 'property_type', $type, 'field_estatein_property_property_type' );
		estatein_seed_meta( $id, 'location', $location, 'field_estatein_property_location' );
		estatein_seed_meta( $id, 'featured', 1, 'field_estatein_property_featured' );
	}
}

/**
 * Sample testimonials.
 */
function estatein_seed_testimonials() {
	$testimonials = array(
		array( 'Exceptional Service!', "Our experience with Estatein was outstanding. Their team's dedication and professionalism made finding our dream home a breeze. Highly recommended!", 'Wade Warren', 'USA, California' ),
		array( 'Efficient and Reliable', "Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We couldn't be happier with the results.", 'Emelie Thomson', 'USA, Florida' ),
		array( 'Trusted Advisors', 'The Estatein team guided us through the entire buying process. Their knowledge and commitment to our needs were impressive. Thank you for your support!', 'John Mans', 'USA, Nevada' ),
		array( 'Smooth From Start to Finish', 'From the first viewing to closing day, every step was clear and on time. We always knew what was happening next.', 'Sara Ahmed', 'USA, Texas' ),
		array( 'Great Market Insight', 'Their valuation advice helped us price our home right, and we had strong offers within the first two weeks.', 'Michael Chen', 'USA, Washington' ),
		array( 'Felt Truly Supported', 'As first-time buyers we had a lot of questions. The team answered every one patiently and found us the perfect place.', 'Laura Gomez', 'USA, Arizona' ),
	);

	foreach ( $testimonials as $order => $t ) {
		list( $title, $quote, $name, $location ) = $t;

		$id = estatein_insert_once(
			array(
				'post_type'    => 'testimonial',
				'post_name'    => sanitize_title( $name ),
				'post_title'   => $title,
				'post_content' => $quote,
				'menu_order'   => $order,
			)
		);

		if ( ! $id ) {
			continue;
		}

		estatein_seed_meta( $id, 'rating', 5, 'field_estatein_testimonial_rating' );
		estatein_seed_meta( $id, 'client_name', $name, 'field_estatein_testimonial_client_name' );
		estatein_seed_meta( $id, 'client_location', $location, 'field_estatein_testimonial_client_location' );
	}
}

/**
 * Sample FAQs. Excerpt = short teaser on the card, content = full answer.
 */
function estatein_seed_faqs() {
	$faqs = array(
		array( 'How do I search for properties on Estatein?', 'Learn how to use our user-friendly search tools to find properties that match your criteria.', 'Open the Properties page and filter listings by location, property type, price range and number of bedrooms. Each card links to the full details, photos and pricing for that home.' ),
		array( 'What documents do I need to sell my property through Estatein?', 'Find out about the necessary documentation for listing your property with us.', 'You will typically need proof of ownership (title deed), a valid ID, recent utility bills, any mortgage statements and the property\'s inspection or compliance certificates. Our agents will confirm the exact list for your area.' ),
		array( 'How can I contact an Estatein agent?', 'Discover the different ways you can get in touch with our experienced agents.', 'Use the contact form on our Contact Us page, call our office during business hours, or visit one of our offices. An agent usually replies within one business day.' ),
		array( 'Can Estatein help me value my property?', 'See how our valuation service helps you set the right asking price.', 'Yes. Our Valuation Mastery service combines recent local sales, market trends and a visit to your property to recommend a realistic, competitive asking price.' ),
		array( 'Do you manage rental properties?', 'Learn about our property management services for landlords.', 'Our property management team handles tenant screening, rent collection, maintenance requests and regular inspections, so your investment is looked after.' ),
		array( 'Are there any fees for buyers?', 'Understand what buyers pay when working with Estatein.', 'Searching and viewing properties is free for buyers. Any service fees are explained clearly before you sign anything, with no hidden costs.' ),
	);

	foreach ( $faqs as $order => $f ) {
		list( $question, $teaser, $answer ) = $f;

		estatein_insert_once(
			array(
				'post_type'    => 'faq',
				'post_name'    => sanitize_title( $question ),
				'post_title'   => $question,
				'post_excerpt' => $teaser,
				'post_content' => $answer,
				'menu_order'   => $order,
			)
		);
	}
}

/**
 * Primary menu and footer column menu.
 */
function estatein_seed_menus() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( __( 'Primary', 'estatein' ) );
		if ( ! is_wp_error( $menu_id ) ) {
			$links = array(
				__( 'Home', 'estatein' )       => home_url( '/' ),
				__( 'About Us', 'estatein' )   => home_url( '/about-us/' ),
				__( 'Properties', 'estatein' ) => home_url( '/properties/' ),
				__( 'Services', 'estatein' )   => home_url( '/services/' ),
			);
			foreach ( $links as $title => $url ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $title,
						'menu-item-url'    => $url,
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
					)
				);
			}
			$locations['primary'] = $menu_id;
		}
	}

	if ( empty( $locations['footer'] ) ) {
		$menu_id = wp_create_nav_menu( __( 'Footer Columns', 'estatein' ) );
		if ( ! is_wp_error( $menu_id ) ) {
			foreach ( estatein_footer_default_columns() as $column ) {
				$parent = wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $column['title'],
						'menu-item-url'    => $column['url'],
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
					)
				);
				foreach ( $column['links'] as $title => $url ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $title,
							'menu-item-url'       => $url,
							'menu-item-status'    => 'publish',
							'menu-item-type'      => 'custom',
							'menu-item-parent-id' => $parent,
						)
					);
				}
			}
			$locations['footer'] = $menu_id;
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Footer columns from the design. Used for seeding and as the menu fallback.
 *
 * @return array
 */
function estatein_footer_default_columns() {
	$home       = home_url( '/' );
	$about      = home_url( '/about-us/' );
	$properties = home_url( '/properties/' );
	$services   = home_url( '/services/' );
	$contact    = home_url( '/contact-us/' );

	return array(
		array(
			'title' => __( 'Home', 'estatein' ),
			'url'   => $home,
			'links' => array(
				__( 'Hero Section', 'estatein' ) => $home . '#hero',
				__( 'Features', 'estatein' )     => $home . '#features',
				__( 'Properties', 'estatein' )   => $home . '#properties',
				__( 'Testimonials', 'estatein' ) => $home . '#testimonials',
				__( "FAQ's", 'estatein' )        => $home . '#faq',
			),
		),
		array(
			'title' => __( 'About Us', 'estatein' ),
			'url'   => $about,
			'links' => array(
				__( 'Our Story', 'estatein' )    => $about . '#story',
				__( 'Our Works', 'estatein' )    => $about . '#works',
				__( 'How It Works', 'estatein' ) => $about . '#how-it-works',
				__( 'Our Team', 'estatein' )     => $about . '#team',
				__( 'Our Clients', 'estatein' )  => $about . '#clients',
			),
		),
		array(
			'title' => __( 'Properties', 'estatein' ),
			'url'   => $properties,
			'links' => array(
				__( 'Portfolio', 'estatein' )  => $properties,
				__( 'Categories', 'estatein' ) => $properties . '#categories',
			),
		),
		array(
			'title' => __( 'Services', 'estatein' ),
			'url'   => $services,
			'links' => array(
				__( 'Valuation Mastery', 'estatein' )    => $services . '#valuation',
				__( 'Strategic Marketing', 'estatein' )  => $services . '#marketing',
				__( 'Negotiation Wizardry', 'estatein' ) => $services . '#negotiation',
				__( 'Closing Success', 'estatein' )      => $services . '#closing',
				__( 'Property Management', 'estatein' )  => $services . '#management',
			),
		),
		array(
			'title' => __( 'Contact Us', 'estatein' ),
			'url'   => $contact,
			'links' => array(
				__( 'Contact Form', 'estatein' ) => $contact . '#contact-form',
				__( 'Our Offices', 'estatein' )  => $contact . '#offices',
			),
		),
	);
}

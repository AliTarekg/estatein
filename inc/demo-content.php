<?php
/**
 * Sample content, created once so a fresh install matches the Figma design.
 *
 * Creates the pages (with their templates), menus, properties (with photos,
 * gallery and details), testimonials, FAQs and team members. Images ship in
 * assets/images and are copied into the Media Library, so editors can
 * replace them like any other upload.
 *
 * Safety rules:
 * - Runs on theme activation, and once from the dashboard if activation
 *   hooks never fired (some hosts and installers switch themes directly).
 * - Never overwrites: posts are matched by slug, meta and featured images
 *   are only filled when empty.
 * - Versioned, so a site seeded by an older theme version receives only the
 *   content added since.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

define( 'ESTATEIN_SEED_VERSION', 2 );

/**
 * Seed demo content.
 */
function estatein_seed_demo_content() {
	if ( (int) get_option( 'estatein_seed_version' ) >= ESTATEIN_SEED_VERSION ) {
		return;
	}

	// Post types are registered on init; make sure they exist during activation.
	estatein_register_post_types();

	// Replace WordPress's placeholder site name and tagline with the brand.
	if ( in_array( get_option( 'blogname' ), array( '', 'My WordPress Website', 'My Blog', 'WordPress' ), true ) ) {
		update_option( 'blogname', 'Estatein' );
	}
	// Includes the default taglines set by WordPress and by Softaculous installers.
	if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site', 'My WordPress Blog', 'My WordPress Website' ), true ) ) {
		update_option( 'blogdescription', __( 'Find, buy, sell and manage property with Estatein.', 'estatein' ) );
	}

	estatein_seed_pages();
	estatein_seed_properties();
	estatein_seed_testimonials();
	estatein_seed_faqs();
	estatein_seed_team();
	estatein_seed_menus();

	// Pretty permalinks are needed for /properties/; only set if none chosen yet.
	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();

	update_option( 'estatein_seed_version', ESTATEIN_SEED_VERSION );
}
add_action( 'after_switch_theme', 'estatein_seed_demo_content' );

/**
 * Fallback: seed on the first dashboard visit by an administrator.
 */
function estatein_maybe_seed_from_admin() {
	if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	estatein_seed_demo_content();
}
add_action( 'admin_init', 'estatein_maybe_seed_from_admin' );

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
 * field shows up filled in the editor. Existing values are kept.
 *
 * @param int    $post_id Post ID.
 * @param string $name    Field name.
 * @param mixed  $value   Value.
 * @param string $key     ACF field key.
 */
function estatein_seed_meta( $post_id, $name, $value, $key ) {
	if ( '' !== get_post_meta( $post_id, $name, true ) ) {
		return;
	}
	update_post_meta( $post_id, $name, $value );
	update_post_meta( $post_id, '_' . $name, $key );
}

/**
 * Copy an image from assets/images into the Media Library (once).
 *
 * @param string $file File name in assets/images.
 * @param string $alt  Alternative text.
 * @return int Attachment ID, 0 on failure.
 */
function estatein_seed_image( $file, $alt = '' ) {
	$map = get_option( 'estatein_seed_images', array() );
	if ( ! empty( $map[ $file ] ) && get_post( $map[ $file ] ) ) {
		return (int) $map[ $file ];
	}

	$path = ESTATEIN_DIR . '/assets/images/' . $file;
	if ( ! is_readable( $path ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( $file, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$type = wp_check_filetype( $upload['file'] );
	$id   = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => $alt ? $alt : ucwords( str_replace( '-', ' ', pathinfo( $file, PATHINFO_FILENAME ) ) ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}

	$map[ $file ] = $id;
	update_option( 'estatein_seed_images', $map, false );

	return (int) $id;
}

/**
 * Set a featured image from assets/images if the post has none.
 *
 * @param int    $post_id Post ID.
 * @param string $file    Image file.
 * @param string $alt     Alt text.
 */
function estatein_seed_thumbnail( $post_id, $file, $alt ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return;
	}
	$image = estatein_seed_image( $file, $alt );
	if ( $image ) {
		set_post_thumbnail( $post_id, $image );
	}
}

/**
 * Pages used by the navigation and footer, with their templates.
 */
function estatein_seed_pages() {
	$home = estatein_insert_once(
		array(
			'post_type'  => 'page',
			'post_name'  => 'home',
			'post_title' => __( 'Home', 'estatein' ),
		)
	);

	if ( $home && 'page' !== get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}

	$pages = array(
		'about-us'             => array( __( 'About Us', 'estatein' ), 'page-about.php', '' ),
		'services'             => array( __( 'Services', 'estatein' ), 'page-services.php', '' ),
		'contact-us'           => array( __( 'Contact Us', 'estatein' ), 'page-contact.php', '' ),
		'terms-and-conditions' => array( __( 'Terms & Conditions', 'estatein' ), '', estatein_terms_copy() ),
	);

	foreach ( $pages as $slug => $page ) {
		list( $title, $template, $content ) = $page;

		$id = estatein_insert_once(
			array(
				'post_type'    => 'page',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => $content,
			)
		);

		// Assign the template unless an editor already picked one.
		if ( $id && $template && ! get_page_template_slug( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}

		// Version 1 created these pages with a placeholder sentence; clear it.
		if ( $id && $template && false !== strpos( (string) get_post_field( 'post_content', $id ), 'Content for this page is managed in the editor.' ) ) {
			wp_update_post(
				array(
					'ID'           => $id,
					'post_content' => '',
				)
			);
		}
	}
}

/**
 * Short, generic terms text so the footer link does not lead to an empty page.
 *
 * @return string
 */
function estatein_terms_copy() {
	$paragraphs = array(
		__( 'These terms govern the use of the Estatein website. By using the site you agree to them.', 'estatein' ),
		__( 'Property information is provided in good faith for general guidance. Prices, availability and specifications can change and should be confirmed with an Estatein agent before any decision is made.', 'estatein' ),
		__( 'Messages sent through our forms are used only to answer your enquiry and are stored securely. We do not sell your personal data.', 'estatein' ),
		__( 'Questions about these terms can be sent through the Contact Us page.', 'estatein' ),
	);

	return '<!-- wp:paragraph --><p>' . implode( '</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>', array_map( 'esc_html', $paragraphs ) ) . '</p><!-- /wp:paragraph -->';
}

/**
 * Sample listings. Copy, prices and the first property's details come from the Figma file.
 */
function estatein_seed_properties() {
	$features = array(
		__( 'Expansive oceanfront terrace for outdoor entertaining', 'estatein' ),
		__( 'Gourmet kitchen with top-of-the-line appliances', 'estatein' ),
		__( 'Private beach access for morning strolls and sunset views', 'estatein' ),
		__( 'Master suite with a spa-inspired bathroom and ocean-facing balcony', 'estatein' ),
		__( 'Private garage and ample storage space', 'estatein' ),
	);
	$gallery  = array( 'property-lakeside.webp', 'gallery-living.webp', 'property-garden.webp', 'property-skyline.webp', 'gallery-lounge.webp', 'gallery-family.webp', 'gallery-dining.webp', 'gallery-bedroom.webp' );

	$properties = array(
		array(
			'title'    => 'Seaside Serenity Villa',
			'excerpt'  => 'A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood.',
			'content'  => 'Discover your own piece of paradise with the Seaside Serenity Villa. With an open floor plan, breathtaking ocean views from every room, and direct access to a pristine sandy beach, this property is the epitome of coastal living.',
			'tagline'  => 'Coastal Escapes - Where Waves Beckon',
			'image'    => 'property-seaside.webp',
			'price'    => 1250000,
			'beds'     => 4,
			'baths'    => 3,
			'area'     => 2500,
			'year'     => 2019,
			'type'     => 'Villa',
			'location' => 'Malibu, California',
		),
		array(
			'title'    => 'Metropolitan Haven',
			'excerpt'  => 'A chic and fully-furnished 2-bedroom apartment with panoramic city views.',
			'content'  => 'Immerse yourself in the energy of the city. This modern apartment in the heart of downtown pairs floor-to-ceiling windows and panoramic skyline views with a quiet, fully furnished interior.',
			'tagline'  => 'Urban Oasis - Life in the Heart of the City',
			'image'    => 'property-metropolitan.webp',
			'price'    => 650000,
			'beds'     => 2,
			'baths'    => 2,
			'area'     => 1200,
			'year'     => 2016,
			'type'     => 'Apartment',
			'location' => 'Downtown, New York',
		),
		array(
			'title'    => 'Rustic Retreat Cottage',
			'excerpt'  => 'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community.',
			'content'  => 'Find tranquility in the countryside. This charming cottage is nestled amidst rolling hills, with a wood-burning fireplace, a mature garden and easy access to hiking trails.',
			'tagline'  => "Countryside Charm - Escape to Nature's Embrace",
			'image'    => 'property-rustic.webp',
			'price'    => 350000,
			'beds'     => 3,
			'baths'    => 2.5,
			'area'     => 1650,
			'year'     => 1998,
			'type'     => 'Cottage',
			'location' => 'Asheville, North Carolina',
		),
		array(
			'title'    => 'Lakeside Modern Residence',
			'excerpt'  => 'A bright 5-bedroom family home with private lake access and a large garden.',
			'content'  => 'A bright, architect-designed family home with private lake access, a heated pool and a large landscaped garden. Open-plan living spaces flow onto terraces with views across the water.',
			'tagline'  => 'Lakeside Living - Calm Waters, Modern Lines',
			'image'    => 'property-lakeside.webp',
			'price'    => 875000,
			'beds'     => 5,
			'baths'    => 4,
			'area'     => 3400,
			'year'     => 2021,
			'type'     => 'Villa',
			'location' => 'Lake Tahoe, Nevada',
		),
		array(
			'title'    => 'Skyline Penthouse',
			'excerpt'  => 'A top-floor 3-bedroom penthouse with a wraparound terrace and skyline views.',
			'content'  => 'A top-floor penthouse with a wraparound terrace, private pool deck and skyline views in every direction. Residents enjoy a concierge, spa and a resort-style pool.',
			'tagline'  => 'Elevated Living - Above the City Lights',
			'image'    => 'property-skyline.webp',
			'price'    => 1450000,
			'beds'     => 3,
			'baths'    => 3,
			'area'     => 2800,
			'year'     => 2020,
			'type'     => 'Penthouse',
			'location' => 'Miami, Florida',
		),
		array(
			'title'    => 'Garden Court Townhouse',
			'excerpt'  => 'A modern 3-bedroom townhouse steps away from parks, schools and shops.',
			'content'  => 'A modern townhouse steps away from parks, schools and shops, with a sunny private courtyard, a pool in the shared gardens and a two-car garage.',
			'tagline'  => 'Family Favourites - Space to Grow',
			'image'    => 'property-garden.webp',
			'price'    => 465000,
			'beds'     => 3,
			'baths'    => 2,
			'area'     => 1900,
			'year'     => 2012,
			'type'     => 'Townhouse',
			'location' => 'Austin, Texas',
		),
	);

	foreach ( $properties as $order => $p ) {
		$id = estatein_insert_once(
			array(
				'post_type'    => 'property',
				'post_name'    => sanitize_title( $p['title'] ),
				'post_title'   => $p['title'],
				'post_excerpt' => $p['excerpt'],
				'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $p['content'] ) . '</p><!-- /wp:paragraph -->',
				'menu_order'   => $order,
			)
		);

		if ( ! $id ) {
			continue;
		}

		$fields = array(
			'price'         => $p['price'],
			'bedrooms'      => $p['beds'],
			'bathrooms'     => $p['baths'],
			'area'          => $p['area'],
			'year_built'    => $p['year'],
			'property_type' => $p['type'],
			'location'      => $p['location'],
			'tagline'       => $p['tagline'],
			'featured'      => 1,
			'features'      => implode( "\n", $features ),
		);
		foreach ( $fields as $name => $value ) {
			estatein_seed_meta( $id, $name, $value, 'field_estatein_property_' . $name );
		}

		/* translators: %s: property name. */
		estatein_seed_thumbnail( $id, $p['image'], sprintf( __( 'Exterior of %s', 'estatein' ), $p['title'] ) );

		if ( ! get_post_meta( $id, 'gallery', true ) ) {
			$ids = array( (int) get_post_thumbnail_id( $id ) );
			foreach ( $gallery as $file ) {
				if ( $file !== $p['image'] ) {
					/* translators: %s: property name. */
					$ids[] = estatein_seed_image( $file, sprintf( __( 'Interior and amenities at %s', 'estatein' ), $p['title'] ) );
				}
			}
			update_post_meta( $id, 'gallery', array_values( array_filter( $ids ) ) );
		}
	}
}

/**
 * Sample testimonials. The first three are from the Figma file.
 */
function estatein_seed_testimonials() {
	$testimonials = array(
		array( 'Exceptional Service!', "Our experience with Estatein was outstanding. Their team's dedication and professionalism made finding our dream home a breeze. Highly recommended!", 'Wade Warren', 'USA, California', 'avatar-wade.webp' ),
		array( 'Efficient and Reliable', "Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We couldn't be happier with the results.", 'Emelie Thomson', 'USA, Florida', 'avatar-emelie.webp' ),
		array( 'Trusted Advisors', 'The Estatein team guided us through the entire buying process. Their knowledge and commitment to our needs were impressive. Thank you for your support!', 'John Mans', 'USA, Nevada', 'avatar-john.webp' ),
		array( 'Smooth From Start to Finish', 'From the first viewing to closing day, every step was clear and on time. We always knew what was happening next.', 'Sara Ahmed', 'USA, Texas', '' ),
		array( 'Great Market Insight', 'Their valuation advice helped us price our home right, and we had strong offers within the first two weeks.', 'Michael Chen', 'USA, Washington', '' ),
		array( 'Felt Truly Supported', 'As first-time buyers we had a lot of questions. The team answered every one patiently and found us the perfect place.', 'Laura Gomez', 'USA, Arizona', '' ),
	);

	foreach ( $testimonials as $order => $t ) {
		list( $title, $quote, $name, $location, $photo ) = $t;

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

		if ( $photo ) {
			estatein_seed_thumbnail( $id, $photo, $name );
		}
	}
}

/**
 * Sample FAQs. Excerpt = short teaser on the card, content = full answer.
 */
function estatein_seed_faqs() {
	$faqs = array(
		array( 'How do I search for properties on Estatein?', 'Learn how to use our user-friendly search tools to find properties that match your criteria.', 'Open the Properties page and search by keyword, or filter listings by location, property type, price range, size and build year. Each card links to the full details, photos and pricing for that home.' ),
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
 * Team members from the Figma About page.
 */
function estatein_seed_team() {
	$team = array(
		array( 'Max Mitchell', __( 'Founder', 'estatein' ), 'team-max.webp' ),
		array( 'Sarah Johnson', __( 'Chief Real Estate Officer', 'estatein' ), 'team-sarah.webp' ),
		array( 'David Brown', __( 'Head of Property Management', 'estatein' ), 'team-david.webp' ),
		array( 'Michael Turner', __( 'Legal Counsel', 'estatein' ), 'team-michael.webp' ),
	);

	foreach ( $team as $order => $member ) {
		list( $name, $role, $photo ) = $member;

		$id = estatein_insert_once(
			array(
				'post_type'  => 'team_member',
				'post_name'  => sanitize_title( $name ),
				'post_title' => $name,
				'menu_order' => $order,
			)
		);

		if ( ! $id ) {
			continue;
		}

		estatein_seed_meta( $id, 'role', $role, 'field_estatein_team_role' );
		estatein_seed_thumbnail( $id, $photo, $name );
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
 * Every anchor exists on the target page.
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
				__( 'Portfolio', 'estatein' )  => $properties . '#portfolio',
				__( 'Categories', 'estatein' ) => $properties . '#categories',
			),
		),
		array(
			'title' => __( 'Services', 'estatein' ),
			'url'   => $services,
			'links' => array(
				__( 'Valuation Mastery', 'estatein' )    => $services . '#valuation-mastery',
				__( 'Strategic Marketing', 'estatein' )  => $services . '#strategic-marketing',
				__( 'Negotiation Wizardry', 'estatein' ) => $services . '#negotiation-wizardry',
				__( 'Closing Success', 'estatein' )      => $services . '#closing-success',
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

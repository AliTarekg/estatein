<?php
/**
 * Editable page sections for the About, Services and Contact templates.
 *
 * One definition per section drives three things:
 *  1. the default copy (taken from the Figma file), so the pages look right
 *     on a fresh install and if ACF is deactivated;
 *  2. the ACF field groups (generated, so fields never drift from the templates);
 *  3. the values templates read, through estatein_section().
 *
 * Repeated items use a fixed number of slots, because ACF free has no
 * repeater field; listings that grow (team, properties) are post types instead.
 *
 * Field types are inferred from the key: "image" → image, "text"/"quote" →
 * textarea, "icon" → icon picker, "*_url" → URL, anything else → text.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template file => section definitions.
 *
 * @return array
 */
function estatein_page_sections() {
	static $sections = null;

	if ( null !== $sections ) {
		return $sections;
	}

	$stats = array(
		array(
			'value' => '200+',
			'label' => __( 'Happy Customers', 'estatein' ),
		),
		array(
			'value' => '10k+',
			'label' => __( 'Properties For Clients', 'estatein' ),
		),
		array(
			'value' => '16+',
			'label' => __( 'Years of Experience', 'estatein' ),
		),
	);

	$journey = __( "Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary. Over the years, we've expanded our reach, forged valuable partnerships, and gained the trust of countless clients.", 'estatein' );
	$growth  = __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.', 'estatein' );

	$sections = array(
		'page-about.php'    => array(
			'journey'      => array(
				'label' => __( 'Our Journey', 'estatein' ),
				'title' => __( 'Our Journey', 'estatein' ),
				'text'  => $journey,
				'image' => 'about-house.webp',
				'items' => $stats,
			),
			'values'       => array(
				'label' => __( 'Our Values', 'estatein' ),
				'title' => __( 'Our Values', 'estatein' ),
				'text'  => $growth,
				'items' => array(
					array(
						'icon'  => 'star-solid',
						'title' => __( 'Trust', 'estatein' ),
						'text'  => __( 'Trust is the cornerstone of every successful real estate transaction.', 'estatein' ),
					),
					array(
						'icon'  => 'graduation',
						'title' => __( 'Excellence', 'estatein' ),
						'text'  => __( 'We set the bar high for ourselves. From the properties we list to the services we provide.', 'estatein' ),
					),
					array(
						'icon'  => 'users',
						'title' => __( 'Client-Centric', 'estatein' ),
						'text'  => __( 'Your dreams and needs are at the center of our universe. We listen, understand.', 'estatein' ),
					),
					array(
						'icon'  => 'star-solid',
						'title' => __( 'Our Commitment', 'estatein' ),
						'text'  => __( 'We are dedicated to providing you with the highest level of service, professionalism.', 'estatein' ),
					),
				),
			),
			'achievements' => array(
				'label' => __( 'Our Achievements', 'estatein' ),
				'title' => __( 'Our Achievements', 'estatein' ),
				'text'  => $growth,
				'items' => array(
					array(
						'title' => __( '3+ Years of Excellence', 'estatein' ),
						'text'  => __( "With over 3 years in the industry, we've amassed a wealth of knowledge and experience.", 'estatein' ),
					),
					array(
						'title' => __( 'Happy Clients', 'estatein' ),
						'text'  => __( 'Our greatest achievement is the satisfaction of our clients. Their success stories fuel our passion for what we do.', 'estatein' ),
					),
					array(
						'title' => __( 'Industry Recognition', 'estatein' ),
						'text'  => __( "We've earned the respect of our peers and industry leaders, with accolades and awards that reflect our commitment to excellence.", 'estatein' ),
					),
				),
			),
			'process'      => array(
				'label' => __( 'How It Works', 'estatein' ),
				'title' => __( 'Navigating the Estatein Experience', 'estatein' ),
				'text'  => __( "At Estatein, we've designed a straightforward process to help you find and purchase your dream property with ease. Here's a step-by-step guide to how it all works.", 'estatein' ),
				'items' => array(
					array(
						'title' => __( 'Discover a World of Possibilities', 'estatein' ),
						'text'  => __( 'Your journey begins with exploring our carefully curated property listings. Use our intuitive search tools to filter properties based on your preferences, including location, type, size and budget.', 'estatein' ),
					),
					array(
						'title' => __( 'Narrowing Down Your Choices', 'estatein' ),
						'text'  => __( "Once you've found properties that catch your eye, make a shortlist. This allows you to compare and revisit your favorites as you make your decision.", 'estatein' ),
					),
					array(
						'title' => __( 'Personalized Guidance', 'estatein' ),
						'text'  => __( 'Have questions about a property or need more information? Our dedicated team of real estate experts is just a call or message away.', 'estatein' ),
					),
					array(
						'title' => __( 'See It for Yourself', 'estatein' ),
						'text'  => __( "Arrange viewings of the properties you're interested in. We'll coordinate with the property owners and accompany you to ensure you get a firsthand look at your potential new home.", 'estatein' ),
					),
					array(
						'title' => __( 'Making Informed Decisions', 'estatein' ),
						'text'  => __( 'Before making an offer, our team will assist you with due diligence, including property inspections, legal checks, and market analysis. We want you to be fully informed.', 'estatein' ),
					),
					array(
						'title' => __( 'Getting the Best Deal', 'estatein' ),
						'text'  => __( "We'll help you negotiate the best terms and prepare your offer. Our goal is to secure the property at the right price and on favorable terms.", 'estatein' ),
					),
				),
			),
			'team'         => array(
				'label' => __( 'Our Team', 'estatein' ),
				'title' => __( 'Meet the Estatein Team', 'estatein' ),
				'text'  => __( 'At Estatein, our success is driven by the dedication and expertise of our team. Get to know the people behind our mission to make your real estate dreams a reality.', 'estatein' ),
			),
			'clients'      => array(
				'label' => __( 'Our Clients', 'estatein' ),
				'title' => __( 'Our Valued Clients', 'estatein' ),
				'text'  => __( "At Estatein, we have had the privilege of working with a diverse range of clients across various industries. Here are some of the clients we've had the pleasure of serving", 'estatein' ),
				'items' => array(
					array(
						'since'       => __( 'Since 2019', 'estatein' ),
						'title'       => 'ABC Corporation',
						'website_url' => '',
						'domain'      => __( 'Commercial Real Estate', 'estatein' ),
						'category'    => __( 'Luxury Home Development', 'estatein' ),
						'quote'       => __( "Estatein's expertise in finding the perfect office space for our expanding operations was invaluable. They truly understand our business needs.", 'estatein' ),
					),
					array(
						'since'       => __( 'Since 2018', 'estatein' ),
						'title'       => 'GreenTech Enterprises',
						'website_url' => '',
						'domain'      => __( 'Commercial Real Estate', 'estatein' ),
						'category'    => __( 'Retail Space', 'estatein' ),
						'quote'       => __( "Estatein's ability to identify prime retail locations helped us expand our brand presence. They are a trusted partner in our growth.", 'estatein' ),
					),
					array(
						'since'       => __( 'Since 2020', 'estatein' ),
						'title'       => 'Northwind Holdings',
						'website_url' => '',
						'domain'      => __( 'Residential Portfolio', 'estatein' ),
						'category'    => __( 'Rental Management', 'estatein' ),
						'quote'       => __( 'Estatein manages our rental portfolio with care and clear reporting. Vacancies are down and our tenants are happier.', 'estatein' ),
					),
					array(
						'since'       => __( 'Since 2021', 'estatein' ),
						'title'       => 'Blue Harbor Partners',
						'website_url' => '',
						'domain'      => __( 'Investment Advisory', 'estatein' ),
						'category'    => __( 'Mixed-Use Development', 'estatein' ),
						'quote'       => __( 'Their market insight shaped our acquisition strategy. Every recommendation was backed by solid data.', 'estatein' ),
					),
				),
			),
		),
		'page-services.php' => array(
			'intro'       => array(
				'label' => __( 'Page intro', 'estatein' ),
				'title' => __( 'Elevate Your Real Estate Experience', 'estatein' ),
				'text'  => __( 'Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.', 'estatein' ),
			),
			'valuation'   => array(
				'label'      => __( 'Selling services', 'estatein' ),
				'title'      => __( 'Unlock Property Value', 'estatein' ),
				'text'       => __( 'Selling your property should be a rewarding experience, and at Estatein, we make sure it is. Our Property Selling Service is designed to maximize the value of your property, ensuring you get the best deal possible. Explore the categories below to see how we can help you at every step of your selling journey', 'estatein' ),
				'cta_title'  => __( 'Unlock the Value of Your Property Today', 'estatein' ),
				'cta_text'   => __( 'Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.', 'estatein' ),
				'cta_label'  => __( 'Learn More', 'estatein' ),
				'cta_url'    => '/contact-us/#contact-form',
				'items'      => array(
					array(
						'icon'  => 'chart',
						'title' => __( 'Valuation Mastery', 'estatein' ),
						'text'  => __( 'Discover the true worth of your property with our expert valuation services.', 'estatein' ),
					),
					array(
						'icon'  => 'pie',
						'title' => __( 'Strategic Marketing', 'estatein' ),
						'text'  => __( 'Selling a property requires more than just a listing; it demands a strategic marketing.', 'estatein' ),
					),
					array(
						'icon'  => 'coins',
						'title' => __( 'Negotiation Wizardry', 'estatein' ),
						'text'  => __( 'Negotiating the best deal is an art, and our negotiation experts are masters of it.', 'estatein' ),
					),
					array(
						'icon'  => 'megaphone',
						'title' => __( 'Closing Success', 'estatein' ),
						'text'  => __( 'A successful sale is not complete until the closing. We guide you through the intricate closing process.', 'estatein' ),
					),
				),
			),
			'management'  => array(
				'label'     => __( 'Property management', 'estatein' ),
				'title'     => __( 'Effortless Property Management', 'estatein' ),
				'text'      => __( "Owning a property should be a pleasure, not a hassle. Estatein's Property Management Service takes the stress out of property ownership, offering comprehensive solutions tailored to your needs. Explore the categories below to see how we can make property management effortless for you", 'estatein' ),
				'cta_title' => __( 'Experience Effortless Property Management', 'estatein' ),
				'cta_text'  => __( 'Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.', 'estatein' ),
				'cta_label' => __( 'Learn More', 'estatein' ),
				'cta_url'   => '/contact-us/#contact-form',
				'items'     => array(
					array(
						'icon'  => 'apps',
						'title' => __( 'Tenant Harmony', 'estatein' ),
						'text'  => __( 'Our Tenant Management services ensure that your tenants have a smooth and reducing vacancies.', 'estatein' ),
					),
					array(
						'icon'  => 'swatch',
						'title' => __( 'Maintenance Ease', 'estatein' ),
						'text'  => __( 'Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.', 'estatein' ),
					),
					array(
						'icon'  => 'sparkles',
						'title' => __( 'Financial Peace of Mind', 'estatein' ),
						'text'  => __( 'Managing property finances can be complex. Our financial experts take care of rent collection', 'estatein' ),
					),
					array(
						'icon'  => 'sun',
						'title' => __( 'Legal Guardian', 'estatein' ),
						'text'  => __( 'Stay compliant with property laws and regulations effortlessly.', 'estatein' ),
					),
				),
			),
			'investments' => array(
				'label'     => __( 'Investment advisory', 'estatein' ),
				'title'     => __( 'Smart Investments, Informed Decisions', 'estatein' ),
				'text'      => __( "Building a real estate portfolio requires a strategic approach. Estatein's Investment Advisory Service empowers you to make smart investments and informed decisions.", 'estatein' ),
				'cta_title' => __( 'Unlock Your Investment Potential', 'estatein' ),
				'cta_text'  => __( 'Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.', 'estatein' ),
				'cta_label' => __( 'Learn More', 'estatein' ),
				'cta_url'   => '/contact-us/#contact-form',
				'items'     => array(
					array(
						'icon'  => 'chart',
						'title' => __( 'Market Insight', 'estatein' ),
						'text'  => __( 'Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions', 'estatein' ),
					),
					array(
						'icon'  => 'fire',
						'title' => __( 'ROI Assessment', 'estatein' ),
						'text'  => __( 'Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments', 'estatein' ),
					),
					array(
						'icon'  => 'bulb',
						'title' => __( 'Customized Strategies', 'estatein' ),
						'text'  => __( 'Every investor is unique, and so are their goals. We develop Customized Investment Strategies tailored to your specific needs', 'estatein' ),
					),
					array(
						'icon'  => 'sun',
						'title' => __( 'Diversification Mastery', 'estatein' ),
						'text'  => __( 'Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across various property types and locations', 'estatein' ),
					),
				),
			),
		),
		'page-contact.php'  => array(
			'intro'   => array(
				'label' => __( 'Page intro', 'estatein' ),
				'title' => __( 'Get in Touch with Estatein', 'estatein' ),
				'text'  => __( "Welcome to Estatein's Contact Us page. We're here to assist you with any inquiries, requests, or feedback you may have. Whether you're looking to buy or sell a property, explore investment opportunities, or simply want to connect, we're just a message away. Reach out to us, and let's start a conversation.", 'estatein' ),
			),
			'form'    => array(
				'label' => __( 'Contact form', 'estatein' ),
				'title' => __( "Let's Connect", 'estatein' ),
				'text'  => __( "We're excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein. Whether you're a prospective client, partner, or simply curious about our services, we're here to answer your questions and provide the assistance you need.", 'estatein' ),
			),
			'offices' => array(
				'label' => __( 'Office locations', 'estatein' ),
				'title' => __( 'Discover Our Office Locations', 'estatein' ),
				'text'  => __( "Estatein is here to serve you across multiple locations. Whether you're looking to meet our team, discuss real estate opportunities, or simply drop by for a chat, we have offices conveniently located to serve your needs. Explore the categories below to find the Estatein office nearest to you", 'estatein' ),
				'items' => array(
					array(
						'label'    => __( 'Main Headquarters', 'estatein' ),
						'category' => 'regional',
						'title'    => '123 Estatein Plaza, City Center, Metropolis',
						'text'     => __( 'Our main headquarters serve as the heart of Estatein. Located in the bustling city center, this is where our core team of experts operates, driving the excellence and innovation that define us.', 'estatein' ),
						'email'    => 'info@estatein.com',
						'phone'    => '+1 (123) 456-7890',
						'city'     => 'Metropolis',
					),
					array(
						'label'    => __( 'Regional Offices', 'estatein' ),
						'category' => 'regional',
						'title'    => '456 Urban Avenue, Downtown District, Metropolis',
						'text'     => __( "Estatein's presence extends to multiple regions, each with its own dynamic real estate landscape. Discover our regional offices, staffed by local experts who understand the nuances of their respective markets.", 'estatein' ),
						'email'    => 'regional@estatein.com',
						'phone'    => '+1 (123) 628-7890',
						'city'     => 'Metropolis',
					),
					array(
						'label'    => __( 'International Office', 'estatein' ),
						'category' => 'international',
						'title'    => '78 Harbour Street, Marina District, Dubai',
						'text'     => __( 'Our international office supports overseas buyers and investors, from first enquiry to completion, with local market knowledge and multilingual advisors.', 'estatein' ),
						'email'    => 'global@estatein.com',
						'phone'    => '+971 4 123 4567',
						'city'     => 'Dubai',
					),
				),
			),
			'gallery' => array(
				'label' => __( 'Gallery', 'estatein' ),
				'title' => __( "Explore Estatein's World", 'estatein' ),
				'text'  => __( 'Step inside the world of Estatein, where professionalism meets warmth, and expertise meets passion. Our gallery offers a glimpse into our team and workspaces, inviting you to get to know us better.', 'estatein' ),
				'items' => array(
					array( 'image' => 'office-workspace.webp' ),
					array( 'image' => 'office-team.webp' ),
					array( 'image' => 'office-meeting.webp' ),
					array( 'image' => 'office-leaders.webp' ),
					array( 'image' => 'office-smiles.webp' ),
					array( 'image' => 'office-handshake.webp' ),
				),
			),
		),
	);

	return $sections;
}

/**
 * Icons editors can pick for icon cards.
 *
 * @return array
 */
function estatein_icon_choices() {
	$icons = array( 'chart', 'pie', 'coins', 'megaphone', 'apps', 'swatch', 'sparkles', 'sun', 'fire', 'bulb', 'star-solid', 'graduation', 'users', 'house', 'cash', 'bolt' );

	return array_combine( $icons, array_map( 'ucfirst', str_replace( '-', ' ', $icons ) ) );
}

/**
 * Read a section: saved ACF values merged over the design defaults.
 *
 * @param string   $section  Section key.
 * @param int|null $post_id  Page ID (defaults to the current page).
 * @param string   $template Template file (defaults to the current page's template).
 * @return array Section values, with defaults filled in for empty fields.
 */
function estatein_section( $section, $post_id = null, $template = '' ) {
	$post_id  = $post_id ? $post_id : get_queried_object_id();
	$template = $template ? $template : get_page_template_slug( $post_id );
	$all      = estatein_page_sections();

	if ( ! isset( $all[ $template ][ $section ] ) ) {
		return array();
	}

	$defaults = $all[ $template ][ $section ];
	$saved    = function_exists( 'get_field' ) ? get_field( $section, $post_id ) : null;
	$saved    = is_array( $saved ) ? $saved : array();
	$out      = array();

	foreach ( $defaults as $key => $default ) {
		if ( 'items' === $key ) {
			continue;
		}
		$out[ $key ] = estatein_pick( $saved, $key, $default );
	}

	if ( isset( $defaults['items'] ) ) {
		$out['items'] = array();
		foreach ( $defaults['items'] as $i => $item_default ) {
			$saved_item = isset( $saved[ 'item_' . ( $i + 1 ) ] ) && is_array( $saved[ 'item_' . ( $i + 1 ) ] ) ? $saved[ 'item_' . ( $i + 1 ) ] : array();
			$item       = array();
			foreach ( $item_default as $key => $default ) {
				$item[ $key ] = estatein_pick( $saved_item, $key, $default );
			}
			$out['items'][] = $item;
		}
	}

	foreach ( $out as $key => $value ) {
		if ( is_string( $value ) && '_url' === substr( $key, -4 ) && 0 === strpos( $value, '/' ) ) {
			$out[ $key ] = home_url( $value );
		}
	}

	return $out;
}

/**
 * A saved value, or the default when the saved value is empty.
 *
 * @param array  $saved   Saved values.
 * @param string $key     Key.
 * @param mixed  $default Default.
 * @return mixed
 */
function estatein_pick( $saved, $key, $default ) {
	return isset( $saved[ $key ] ) && '' !== $saved[ $key ] && null !== $saved[ $key ] && false !== $saved[ $key ] ? $saved[ $key ] : $default;
}

/**
 * ACF field definition for one key, inferred from its name.
 *
 * @param string $prefix Unique key prefix.
 * @param string $key    Field name.
 * @param mixed  $value  Default value (shown as placeholder).
 * @return array
 */
function estatein_section_field( $prefix, $key, $value ) {
	$label = ucfirst( str_replace( '_', ' ', $key ) );
	$args  = array();

	if ( 'image' === $key ) {
		$type = 'image';
		$args = array(
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'instructions'  => __( 'Leave empty to use the design image.', 'estatein' ),
		);
	} elseif ( 'icon' === $key ) {
		$type = 'select';
		$args = array(
			'choices'       => estatein_icon_choices(),
			'default_value' => $value,
		);
	} elseif ( in_array( $key, array( 'text', 'quote', 'cta_text' ), true ) ) {
		$type = 'textarea';
		$args = array(
			'rows'        => 3,
			'placeholder' => $value,
		);
	} else {
		// URLs stay plain text so site-relative paths ("/contact-us/") are allowed.
		$type = 'text';
		$args = array( 'placeholder' => $value );
	}

	return estatein_acf( $prefix, $key, $label, $type, $args );
}

/**
 * Register one ACF field group per template, with a tab per section.
 */
function estatein_register_section_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$titles = array(
		'page-about.php'    => __( 'About Page Sections', 'estatein' ),
		'page-services.php' => __( 'Services Page Sections', 'estatein' ),
		'page-contact.php'  => __( 'Contact Page Sections', 'estatein' ),
	);

	foreach ( estatein_page_sections() as $template => $sections ) {
		$slug   = sanitize_key( str_replace( '.php', '', $template ) );
		$fields = array();

		foreach ( $sections as $section => $defaults ) {
			$prefix     = $slug . '_' . $section;
			$fields[]   = estatein_acf( $prefix, 'tab', $defaults['label'], 'tab' );
			$sub_fields = array();

			foreach ( $defaults as $key => $value ) {
				if ( 'label' === $key || 'items' === $key ) {
					continue;
				}
				$sub_fields[] = estatein_section_field( $prefix, $key, $value );
			}

			if ( isset( $defaults['items'] ) ) {
				foreach ( $defaults['items'] as $i => $item ) {
					$item_prefix = $prefix . '_item_' . ( $i + 1 );
					$item_fields = array();
					foreach ( $item as $key => $value ) {
						$item_fields[] = estatein_section_field( $item_prefix, $key, $value );
					}
					$sub_fields[] = estatein_acf(
						$prefix,
						'item_' . ( $i + 1 ),
						/* translators: %d: item number. */
						sprintf( __( 'Item %d', 'estatein' ), $i + 1 ),
						'group',
						array(
							'layout'     => 'row',
							'sub_fields' => $item_fields,
						)
					);
				}
			}

			$fields[] = estatein_acf(
				$slug,
				$section,
				$defaults['label'],
				'group',
				array(
					'layout'       => 'block',
					'instructions' => __( 'Empty fields show the default design copy (shown as placeholder).', 'estatein' ),
					'sub_fields'   => $sub_fields,
				)
			);
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_estatein_' . $slug,
				'title'    => $titles[ $template ],
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => $template,
						),
					),
				),
				'fields'   => $fields,
			)
		);
	}
}
add_action( 'acf/init', 'estatein_register_section_fields' );

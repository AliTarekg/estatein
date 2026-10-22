<?php
/**
 * Home: hero (headline, actions, stats, building image) and the four feature cards.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_front = get_queried_object_id();

$estatein_title     = estatein_field( 'hero.title', __( 'Discover Your Dream Property with Estatein', 'estatein' ), $estatein_front );
$estatein_text      = estatein_field( 'hero.text', __( 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.', 'estatein' ), $estatein_front );
$estatein_secondary = estatein_link( estatein_field( 'hero.secondary_button', array(), $estatein_front ), __( 'Learn More', 'estatein' ), home_url( '/about-us/' ) );
$estatein_primary   = estatein_link( estatein_field( 'hero.primary_button', array(), $estatein_front ), __( 'Browse Properties', 'estatein' ), get_post_type_archive_link( 'property' ) );
$estatein_image_id  = estatein_field( 'hero.image', 0, $estatein_front );

$estatein_stats = array(
	1 => array( '200+', __( 'Happy Customers', 'estatein' ) ),
	2 => array( '10k+', __( 'Properties For Clients', 'estatein' ) ),
	3 => array( '16+', __( 'Years of Experience', 'estatein' ) ),
);

// Hero image is the largest element above the fold: load it eagerly with high priority.
$estatein_image_attrs = array(
	'class'         => 'hero__img',
	'alt'           => __( 'Modern glass tower representing Estatein properties', 'estatein' ),
	'loading'       => 'eager',
	'fetchpriority' => 'high',
	'sizes'         => '(min-width: 1024px) 50vw, 100vw',
);
$estatein_image = $estatein_image_id
	? estatein_image( $estatein_image_id, 'estatein-hero', $estatein_image_attrs )
	: estatein_image( 'hero-building.webp', 'full', $estatein_image_attrs + array( 'width' => 1000, 'height' => 814 ) );

$estatein_features = array(
	1 => array( __( 'Find Your Dream Home', 'estatein' ), 'feature-home', get_post_type_archive_link( 'property' ) ),
	2 => array( __( 'Unlock Property Value', 'estatein' ), 'feature-value', home_url( '/services/#valuation' ) ),
	3 => array( __( 'Effortless Property Management', 'estatein' ), 'feature-management', home_url( '/services/#management' ) ),
	4 => array( __( 'Smart Investments, Informed Decisions', 'estatein' ), 'feature-investment', home_url( '/services/#investments' ) ),
);
?>
<section class="hero" id="hero" aria-labelledby="hero-title">
	<div class="hero__grid">
		<div class="hero__content">
			<div class="hero__heading">
				<h1 class="hero__title" id="hero-title"><?php echo esc_html( $estatein_title ); ?></h1>
				<p class="hero__intro"><?php echo esc_html( $estatein_text ); ?></p>

				<a class="badge-spin" href="<?php echo esc_url( $estatein_primary['url'] ); ?>" aria-label="<?php echo esc_attr( $estatein_primary['title'] ); ?>">
					<svg class="badge-spin__ring" viewBox="0 0 175 175" aria-hidden="true" focusable="false">
						<defs><path id="badge-circle" d="M87.5,87.5 m-64,0 a64,64 0 1,1 128,0 a64,64 0 1,1 -128,0"/></defs>
						<text><textPath href="#badge-circle" textLength="400"><?php esc_html_e( 'Discover Your Dream Property ✨', 'estatein' ); ?></textPath></text>
					</svg>
					<span class="badge-spin__core"><?php estatein_the_icon( 'arrow-up-right' ); ?></span>
				</a>
			</div>

			<div class="hero__actions">
				<a class="btn btn--outline btn--lg" <?php estatein_link_attrs( $estatein_secondary ); ?>><?php echo esc_html( $estatein_secondary['title'] ); ?></a>
				<a class="btn btn--primary btn--lg" <?php estatein_link_attrs( $estatein_primary ); ?>><?php echo esc_html( $estatein_primary['title'] ); ?></a>
			</div>

			<dl class="stats">
				<?php foreach ( $estatein_stats as $estatein_n => $estatein_stat ) : ?>
					<div class="stat">
						<dt class="stat__label"><?php echo esc_html( estatein_field( 'hero.stat_' . $estatein_n . '_label', $estatein_stat[1], $estatein_front ) ); ?></dt>
						<dd class="stat__value" data-count-up><?php echo esc_html( estatein_field( 'hero.stat_' . $estatein_n . '_value', $estatein_stat[0], $estatein_front ) ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>

		<div class="hero__media">
			<span class="hero__pattern" aria-hidden="true"></span>
			<?php echo $estatein_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped in estatein_image(). ?>
		</div>
	</div>
</section>

<section class="features" id="features" aria-label="<?php esc_attr_e( 'What we offer', 'estatein' ); ?>">
	<ul class="features__list">
		<?php foreach ( $estatein_features as $estatein_n => $estatein_feature ) : ?>
			<?php
			$estatein_feature_link = estatein_link(
				estatein_field( 'features.feature_' . $estatein_n . '.link', array(), $estatein_front ),
				'',
				$estatein_feature[2]
			);
			$estatein_feature_title = estatein_field( 'features.feature_' . $estatein_n . '.title', $estatein_feature[0], $estatein_front );
			?>
			<li class="feature">
				<a class="feature__link" <?php estatein_link_attrs( $estatein_feature_link ); ?>>
					<span class="icon-ring"><span class="icon-ring__inner"><?php estatein_the_icon( $estatein_feature[1] ); ?></span></span>
					<span class="feature__title"><?php echo esc_html( $estatein_feature_title ); ?></span>
					<?php estatein_the_icon( 'arrow-up-right', 'feature__arrow' ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

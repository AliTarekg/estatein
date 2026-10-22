<?php
/**
 * Property detail helpers: features list and the pricing breakdown.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Key features, one per line in the "features" field.
 *
 * @param int $post_id Property ID.
 * @return string[]
 */
function estatein_property_features( $post_id ) {
	$raw = (string) estatein_field( 'features', '', $post_id );

	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', $raw ) ) ) );
}

/**
 * Pricing breakdown in the shape of the Figma "Comprehensive Pricing Details"
 * section. Stored values win; empty fields fall back to typical estimates
 * based on the listing price, and every total is computed, so the cards
 * always agree with each other.
 *
 * @param int $post_id Property ID.
 * @return array|null Null when the property has no numeric price.
 */
function estatein_property_pricing( $post_id ) {
	$price = estatein_field( 'price', '', $post_id );
	if ( ! is_numeric( $price ) || (float) $price <= 0 ) {
		return null;
	}
	$price = (float) $price;

	$get = function ( $key, $estimate ) use ( $post_id ) {
		$value = estatein_field( 'pricing_' . $key, '', $post_id );
		return is_numeric( $value ) ? (float) $value : (float) $estimate;
	};

	$transfer   = $get( 'transfer_tax', round( $price * 0.02 ) );
	$legal      = $get( 'legal_fees', 3000 );
	$inspection = $get( 'inspection', 500 );
	$insurance  = $get( 'insurance', 1200 );
	$tax        = $get( 'property_tax', round( $price * 0.012 / 12 ) );
	$hoa        = $get( 'hoa', 300 );
	$down_pct   = $get( 'down_payment', 20 );
	$down       = round( $price * $down_pct / 100 );
	$fees       = $transfer + $legal + $inspection + $insurance;

	$money = 'estatein_format_price';

	return array(
		'price'  => $money( $price ),
		'groups' => array(
			array(
				'title' => __( 'Additional Fees', 'estatein' ),
				'rows'  => array(
					array(
						array( __( 'Property Transfer Tax', 'estatein' ), $money( $transfer ), __( 'Based on the sale price and local regulations', 'estatein' ) ),
						array( __( 'Legal Fees', 'estatein' ), $money( $legal ), __( 'Approximate cost for legal services, including title transfer', 'estatein' ) ),
					),
					array(
						array( __( 'Home Inspection', 'estatein' ), $money( $inspection ), __( 'Recommended for due diligence', 'estatein' ) ),
						array( __( 'Property Insurance', 'estatein' ), $money( $insurance ), __( 'Annual cost for comprehensive property insurance', 'estatein' ) ),
					),
					array(
						array( __( 'Mortgage Fees', 'estatein' ), __( 'Varies', 'estatein' ), __( 'If applicable, consult with your lender for specific details', 'estatein' ) ),
					),
				),
			),
			array(
				'title' => __( 'Monthly Costs', 'estatein' ),
				'rows'  => array(
					array(
						array( __( 'Property Taxes', 'estatein' ), $money( $tax ), __( 'Approximate monthly property tax based on the sale price and local rates', 'estatein' ) ),
					),
					array(
						array( __( "Homeowners' Association Fee", 'estatein' ), $money( $hoa ), __( 'Monthly fee for common area maintenance and security', 'estatein' ) ),
					),
				),
			),
			array(
				'title' => __( 'Total Initial Costs', 'estatein' ),
				'rows'  => array(
					array(
						array( __( 'Listing Price', 'estatein' ), $money( $price ), '' ),
						array( __( 'Additional Fees', 'estatein' ), $money( $fees ), __( 'Property transfer tax, legal fees, inspection, insurance', 'estatein' ) ),
					),
					array(
						/* translators: %s: down payment percentage. */
						array( __( 'Down Payment', 'estatein' ), $money( $down ), sprintf( __( '%s%%', 'estatein' ), number_format_i18n( $down_pct ) ) ),
						array( __( 'Mortgage Amount', 'estatein' ), $money( $price - $down ), __( 'If applicable', 'estatein' ) ),
					),
				),
			),
			array(
				'title' => __( 'Monthly Expenses', 'estatein' ),
				'rows'  => array(
					array(
						array( __( 'Property Taxes', 'estatein' ), $money( $tax ), '' ),
						array( __( "Homeowners' Association Fee", 'estatein' ), $money( $hoa ), '' ),
					),
					array(
						array( __( 'Mortgage Payment', 'estatein' ), __( 'Varies based on terms and interest rate', 'estatein' ), __( 'If applicable', 'estatein' ) ),
						array( __( 'Property Insurance', 'estatein' ), $money( round( $insurance / 12 ) ), __( 'Approximate monthly cost', 'estatein' ) ),
					),
				),
			),
		),
	);
}

<?php
/**
 * Price Method Abstract Method Class
 *
 * @package  MANCA\CoolCA\PriceMethod
 */

namespace MANCA\CoolCA\PriceMethod;

defined( 'ABSPATH' ) || exit;

use MANCA\CoolCA\Helper\Helper;

/**
 * Price Method by Table
 */
class PriceMethodTable extends PriceMethodAbstract implements PriceMethodInterface {

	/**
	 * Calc Cost
	 *
	 * @param WC_CoolCA $shippingMethod Woo Shipping Method.
	 * @param array     $items List of product items of the cart.
	 * @param array     $destination List of Destination data.
	 *
	 * @return array
	 */
	public function calc_cost( $shippingMethod, $items = array(), $destination = array() ) {
		Helper::log( 'Init ' . __CLASS__ . '.' . __FUNCTION__ );
		if ( 'branch' === $shippingMethod->service_type ) {
			$coolca_branch = filter_input(
				INPUT_POST,
				'coolca_branch',
				FILTER_SANITIZE_FULL_SPECIAL_CHARS
			); // phpcs:ignore WordPress.Security.NonceVerification

			if ( $coolca_branch ) {
				if ( isset( helper::get_branches()[ $post_data['coolca_branch'] ] ) ) {
					$branch = helper::get_branches()[ $post_data['coolca_branch'] ];
					$zone   = helper::get_zone( $shippingMethod->pickup_state, $shippingMethod->pickup_city, $shippingMethod->pickup_postcode, $branch['s'], $branch['c'], $branch['pc'] );
				}
			}

			if ( ! isset( $zone ) ) {
				$zone = helper::get_zone( $shippingMethod->pickup_state, $shippingMethod->pickup_city, $shippingMethod->pickup_postcode, $destination['state'], $destination['city'], $destination['postcode'] );
			}
		} else {
			$zone = helper::get_zone( $shippingMethod->pickup_state, $shippingMethod->pickup_city, $shippingMethod->pickup_postcode, $destination['state'], $destination['city'], $destination['postcode'] );
		}

		$has_costs = false;

		Helper::log( 'Zone: ' . $zone );

		// Evaluar por Tarifario Peso Real.
		$WeightRange = Helper::get_weight_ranges();

		// Fill PaqAr packages.
		$PaqArs = $this->get_paqars( $items );
		foreach ( $PaqArs as &$Paq ) {
			// Coeficiente de Aforo: fixed 6000.
			$Paq['volumetric_weight'] = floatval( ( $Paq['height'] * $Paq['width'] * $Paq['length'] ) / 6000 );
			$PaqWeight                = max( $Paq['weight'], $Paq['volumetric_weight'] );
			foreach ( $WeightRange as $value ) {
				if ( floatval( $PaqWeight ) <= floatval( $value ) ) {
					$weight = $value;
					break;
				}
			}
			$Paq['cost'] += Helper::get_price( $shippingMethod->service_type, strVal( $zone ), strVal( $weight ) );
		}

		Helper::log( $PaqArs );
		Helper::log( 'End ' . __CLASS__ . '.' . __FUNCTION__ );
		return $PaqArs;
	}
}

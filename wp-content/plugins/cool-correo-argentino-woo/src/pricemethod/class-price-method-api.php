<?php
/**
 * Price Method Abstract Method Class
 *
 * @package  MANCA\CoolCA\PriceMethod
 */

namespace MANCA\CoolCA\PriceMethod;

defined( 'ABSPATH' ) || exit;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\Sdk\CoolCASdk;

/**
 * Price Method by API
 */
class PriceMethodApi extends PriceMethodAbstract implements PriceMethodInterface {
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
		$PaqArs = $this->get_paqars( $items );
		foreach ( $PaqArs as &$Paq ) {
			$data = array(
				'postalCodeOrigin'      => $shippingMethod->pickup_postcode,
				'postalCodeDestination' => $destination['postcode'],
				'deliveredType'         => $shippingMethod->service_type,
				'weight'                => ceil( $Paq['weight'] * 1000 ), // transform Kg to Grams.
				'height'                => ceil( $Paq['height'] ),
				'width'                 => ceil( $Paq['width'] ),
				'length'                => ceil( $Paq['length'] ),
			);

			$sdk         = new CoolCASdk( Helper::get_option( 'api-key' ) );
			$Paq['cost'] = $sdk->get_prices( $data );
		}

		return ( 0 === count(
			array_filter(
				$PaqArs,
				function ( $paq ) {
					return ! isset( $paq['cost'] ) || empty( $paq['cost'] );
				}
			)
		) ) ? $PaqArs : array();
	}
}

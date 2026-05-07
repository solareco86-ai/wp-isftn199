<?php
/**
 * Price Method Abstract Method Class
 *
 * @package  MANCA\CoolCA\PriceMethod
 */

namespace MANCA\CoolCA\PriceMethod;

defined( 'ABSPATH' ) || exit;

/**
 * Price Method Interface
 */
interface PriceMethodInterface {
	/**
	 * Calc Cost
	 *
	 * @param WC_CoolCA $shippingMethod Woo Shipping Method.
	 * @param array     $package List of product items of the cart.
	 * @param array     $destination List of Destination data.
	 *
	 * @return array
	 */
	public function calc_cost( WC_CoolCA $shippingMethod, $package = array(), $destination = array() );
}

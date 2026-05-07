<?php
/**
 * Woocommerce Trait
 * Version 1.1.2 - Add Product Calculator
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

trait WooTrait {

	/**
	 * Get Array of Products from Package
	 *
	 * @param Array $package Package of items form cart.
	 *
	 * @return Array
	 */
	public static function get_individual_items_from_package( $package = array() ) {
		$products = array();
		if ( isset( $package['contents'] ) && ! empty( $package['contents'] ) ) {
			foreach ( $package['contents'] as $product_item ) {
				$product_id = ( isset( $product_item['variation_id'] ) && ! empty( $product_item['variation_id'] ) ) ? $product_item['variation_id'] : $product_item['product_id'];
				$product    = wc_get_product( $product_id );
				if ( ! ( $product->is_downloadable( 'yes' ) || $product->is_virtual( 'yes' ) ) ) {
					$qty                    = ( ! empty( $product_item['quantity'] ) ) ? $product_item['quantity'] : 1;
					$product_data           = array();
					$product_data['id']     = $product_id;
					$product_data['qty']    = 1;
					$product_data['width']  = ( $product->get_width() ? wc_get_dimension( $product->get_width(), 'cm' ) : '0' );
					$product_data['height'] = ( $product->get_height() ? wc_get_dimension( $product->get_height(), 'cm' ) : '0' );
					$product_data['length'] = ( $product->get_length() ? wc_get_dimension( $product->get_length(), 'cm' ) : '0' );
					$product_data['weight'] = ( $product->has_weight() ? wc_get_weight( $product->get_weight(), 'kg' ) : '0' );
					$product_data['name']   = $product->get_name();
					$product_data['sku']    = $product->get_sku();
					$product_data['volume'] = round( floatval( ( $product_data['width'] * $product_data['height'] * $product_data['length'] ) / 1000000 ), 7 );
					// Total Volume in m3.
					$product_data['total_volume'] = round( floatval( ( $product_data['width'] * $product_data['height'] * $product_data['length'] ) / 1000000 ), 7 );
					$product_data['total_weight'] = $product_data['weight'];
					$product_data['total_price']  = round( floatval( $product_item['line_total'] / $qty ), 7 );
					for ( $i = 1; $i <= $qty; $i++ ) {
						$products[] = $product_data;
					}
				}
			}
		}
		return $products;
	}

	/**
	 * Get Destination Array
	 *
	 * @param Array $package Package of items form cart.
	 * @return Array
	 */
	public static function get_destination_from_package( $package = array() ) {
		$state    = ( isset( $package['destination'] ) && isset( $package['destination']['state'] ) ) ? $package['destination']['state'] : '';
		$city     = ( isset( $package['destination'] ) && isset( $package['destination']['city'] ) ) ? $package['destination']['city'] : '';
		$postcode = ( isset( $package['destination'] ) && isset( $package['destination']['postcode'] ) ) ? $package['destination']['postcode'] : '';
		return array(
			'state'    => $state,
			'city'     => $city,
			'postcode' => $postcode,
		);
	}

	/**
	 * Get Array of Products from Package
	 *
	 * @param Array $package Package of items form cart.
	 *
	 * @return Array
	 */
	public static function get_product_list_from_package( $package = array() ) {
		$settings = self::get_setup_from_settings();
		$products = array();
		if ( isset( $package['contents'] ) && ! empty( $package['contents'] ) ) {
			foreach ( $package['contents'] as $product_item ) {
				$product_id = ( isset( $product_item['variation_id'] ) && ! empty( $product_item['variation_id'] ) ) ? $product_item['variation_id'] : $product_item['product_id'];
				$product    = wc_get_product( $product_id );
				if ( ! ( $product->is_downloadable( 'yes' ) || $product->is_virtual( 'yes' ) ) ) {
					$product_data           = array();
					$product_data['id']     = $product_id;
					$product_data['qty']    = ( ! empty( $product_item['quantity'] ) ) ? $product_item['quantity'] : 1;
					$product_data['width']  = ( $product->get_width() ? wc_get_dimension( $product->get_width(), 'cm' ) : intval( $settings['default-width'] ) );
					$product_data['height'] = ( $product->get_height() ? wc_get_dimension( $product->get_height(), 'cm' ) : intval( $settings['default-height'] ) );
					$product_data['length'] = ( $product->get_length() ? wc_get_dimension( $product->get_length(), 'cm' ) : intval( $settings['default-length'] ) );
					$product_data['weight'] = ( $product->has_weight() ? wc_get_weight( $product->get_weight(), 'kg' ) : floatval( intval( $settings['default-weight'] ) / 1000 ) );
					$product_data['name']   = $product->get_name();
					$product_data['sku']    = $product->get_sku();
					$product_data['volume'] = round( floatval( ( $product_data['width'] * $product_data['height'] * $product_data['length'] ) / 1000000 ), 7 );
					// Total Volume in m3.
					$product_data['total_volume'] = round( floatval( ( $product_data['width'] * $product_data['height'] * $product_data['length'] * $product_data['qty'] ) / 1000000 ), 7 );
					$product_data['total_weight'] = $product_data['weight'] * $product_data['qty'];
					$product_data['total_price']  = $product_item['line_total'];
					$products[]                   = $product_data;
				}
			}
		}
		return $products;
	}

	/**
	 * Split Street Name & Number from Address1
	 *
	 * @param string $value Woo Order Address 1.
	 * @return array
	 */
	public static function split_street_and_number( $value ) {
		// Elimina espacios en blanco al principio y al final.
		$value = trim( $value );

		// Inicializa el número como una cadena vacía.
		$number = '';

		// Busca un número al final de la cadena.
		if ( preg_match( '/\s+(\d+)$/', $value, $matches ) ) {
			$number = $matches[1];
			// Elimina el número encontrado al final.
			$value = rtrim( $value, $number );
		}

		return array(
			'street' => $value,
			'number' => $number,
		);
	}

	/**
	 * Split Floor and Aparment from Address2
	 *
	 * @param string $value Woo Order Address 2.
	 * @return array
	 */
	public static function split_fllor_and_apartment( $value ) {
		$data = preg_split( '/\s(?=[A-Za-z])|(?<=[0-9])\s/', $value, 2 );
		if ( 0 === count( $data ) ) {
			$data[] = '';
		}
		if ( 1 === count( $data ) ) {
			$data[] = '';
		}
		if ( count( $data ) > 2 ) {
			$data[1] = implode( ' ', array_slice( $data, 1 ) );
		}
		return $data;
	}

	/**
	 * Get Argentinean prefix.
	 *
	 * @return array
	 */
	public static function get_arg_phone_prefix() {
		return array( '11', '351', '379', '370', '221', '380', '261', '299', '343', '376', '280', '362', '2966', '387', '383', '264', '266', '381', '388', '342', '2954', '385', '2920', '2901', '11', '220', '221', '223', '230', '236', '237', '249', '260', '261', '263', '264', '266', '280', '291', '294', '297', '298', '299', '336', '341', '342', '343', '345', '348', '351', '353', '358', '362', '364', '370', '376', '379', '380', '381', '383', '385', '387', '388', '2202', '2221', '2223', '2224', '2225', '2226', '2227', '2229', '2241', '2242', '2243', '2244', '2245', '2246', '2252', '2254', '2255', '2257', '2261', '2262', '2264', '2265', '2266', '2267', '2268', '2271', '2272', '2273', '2274', '2281', '2283', '2284', '2285', '2286', '2291', '2292', '2296', '2297', '2302', '2314', '2316', '2317', '2320', '2323', '2324', '2325', '2326', '2331', '2333', '2334', '2335', '2336', '2337', '2338', '2342', '2343', '2344', '2345', '2346', '2352', '2353', '2354', '2355', '2356', '2357', '2358', '2392', '2393', '2394', '2395', '2396', '2473', '2474', '2475', '2477', '2478', '2622', '2624', '2625', '2626', '2646', '2647', '2648', '2651', '2652', '2655', '2656', '2657', '2658', '2901', '2902', '2903', '2920', '2921', '2922', '2923', '2924', '2925', '2926', '2927', '2928', '2929', '2931', '2932', '2933', '2934', '2935', '2936', '2940', '2942', '2945', '2946', '2948', '2952', '2953', '2954', '2962', '2963', '2964', '2966', '2972', '2982', '2983', '3327', '3329', '3382', '3385', '3387', '3388', '3400', '3401', '3402', '3404', '3405', '3406', '3407', '3408', '3409', '3435', '3436', '3437', '3438', '3442', '3444', '3445', '3446', '3447', '3454', '3455', '3456', '3458', '3460', '3462', '3463', '3464', '3465', '3466', '3467', '3468', '3469', '3471', '3472', '3476', '3482', '3483', '3487', '3489', '3491', '3492', '3493', '3496', '3497', '3498', '3521', '3522', '3524', '3525', '3532', '3533', '3537', '3541', '3542', '3543', '3544', '3546', '3547', '3548', '3549', '3562', '3563', '3564', '3571', '3572', '3573', '3574', '3575', '3576', '3582', '3583', '3584', '3585', '3711', '3715', '3716', '3718', '3721', '3725', '3731', '3734', '3735', '3741', '3743', '3751', '3754', '3755', '3756', '3757', '3758', '3772', '3773', '3774', '3775', '3777', '3781', '3782', '3786', '3821', '3825', '3826', '3827', '3832', '3835', '3837', '3838', '3841', '3843', '3844', '3845', '3846', '3854', '3855', '3856', '3857', '3858', '3861', '3862', '3863', '3865', '3867', '3868', '3869', '3873', '3876', '3877', '3878', '3885', '3886', '3889', '3888', '3891', '3892', '3894' );
	}
	/**
	 * Get Argentinean phone number prefix.
	 *
	 * @param string $phone Phone number.
	 * @return string
	 */
	public static function get_phone_prefix_arg( $phone ) {
		if ( ! empty( $phone ) ) {
			$prefixs = self::get_arg_phone_prefix();

			foreach ( $prefixs as $prefix ) {
				if ( substr( $phone, 0, strlen( $prefix ) ) === $prefix ) {
					return $prefix;
				}
			}
		}
		return '';
	}

	/**
	 * Get Argentinean phone number prefix Sanitized.
	 *
	 * @param string $phone Phone number.
	 * @return string
	 */
	public static function get_phone_prefix_sanitized( $phone ) {
		$phoneRet = self::remove_phone_prefix( self::remove_phone_prefix( self::remove_phone_prefix( self::remove_phone_prefix( $phone, '+' ), '549' ), '15' ), '0' );
		$phoneRet = self::get_phone_prefix_arg( $phoneRet );
		return ( ! empty( $phoneRet ) ) ? $phoneRet : '11';
	}

	/**
	 * Remove prefix from String.
	 *
	 * @param string $str input string.
	 * @param string $prefix prefix to be removed from $str..
	 * @return string
	 */
	public static function remove_phone_prefix( $str, $prefix ) {
		if ( ! empty( $str ) ) {
			if ( substr( $str, 0, strlen( $prefix ) ) === $prefix ) {
				return substr( $str, strlen( $prefix ) );
			}
		}
		return $str;
	}

	/**
	 * Sanitize Phone Number.
	 *
	 * @param string $phone Phone number.
	 * @return string
	 */
	public static function get_phone_sanitized( $phone ) {
		$phoneRet = self::remove_phone_prefix( self::remove_phone_prefix( self::remove_phone_prefix( self::remove_phone_prefix( $phone, '+' ), '549' ), '15' ), '0' );
		$phoneRet = self::remove_phone_prefix( $phoneRet, self::get_phone_prefix_arg( $phoneRet ) );
		return $phoneRet;
	}
}

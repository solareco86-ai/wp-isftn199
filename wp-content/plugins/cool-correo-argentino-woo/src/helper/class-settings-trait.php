<?php
/**
 * Class SettingsTrait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

/**
 * Settings Trait
 */
trait SettingsTrait {

	/**
	 * Gets a plugin option
	 *
	 * @param string  $key meta_key.
	 * @param boolean $default_value default value for $key.
	 * @return mixed
	 */
	public static function get_option( string $key, $default_value = false ) {
		return get_option( 'wc-coolca-' . $key, $default_value );
	}

	/**
	 * Gets a plugin option
	 *
	 * @param string  $key meta_key.
	 * @param boolean $value value to set.
	 * @return mixed
	 */
	public static function set_option( string $key, $value = false ) {
		return update_option( 'wc-coolca-' . $key, $value );
	}

	/**
	 * Gets the seller settings
	 *
	 * @return array
	 */
	public static function get_setup_from_settings() {
		return array(
			'api-key'                      => self::get_option( 'api-key' ),
			'vat'                          => self::get_option( 'vat' ),
			'debug'                        => self::get_option( 'debug' ),
			'prices'                       => self::get_option( 'prices' ),
			'default-weight'               => self::get_option( 'default-weight' ),
			// Version 1.1.2 - Add Product Calculator.
			'product-shipping-calculator'  => self::get_option( 'product-shipping-calculator' ),
			'shipping-calculator-title'    => self::get_option( 'shipping-calculator-title' ),
			// Version 1.3.0 - Add Mode & More Defaults.
			'default-height'               => self::get_option( 'default-height', 0 ),
			'default-width'                => self::get_option( 'default-width', 0 ),
			'default-length'               => self::get_option( 'default-length', 0 ),
			'mode'                         => self::get_option( 'mode' ),
			// Version 1.3.8 - Add Master Options.
			'js-primitive'                 => self::get_option( 'js-primitive', false ),
			'timeout'                      => self::get_option( 'timeout', 10 ),
			// Version 1.3.13 - Add Order Prefix.
			'order-prefix'                 => self::get_option( 'order-prefix', '0000' ),
			// Version 1.3.12 - Make product stackable.
			'stack-products'               => self::get_option( 'stack-products' ),
			// Version 1.3.22 - Add Shipping Calculator - location.
			'shipping-calculator-location' => self::get_option( 'shipping-calculator-location', 'before-addtocartbutton' ),
			// Version 1.3.22 - Add Shipping Calculator - style.
			'shipping-calculator-style'    => self::get_option( 'shipping-calculator-style', 'none' ),
			// Version 1.4.0 - Add WCFM compatibility.
			'wcfm-enabled'                 => self::get_option( 'wcfm-enabled', false ),
		);
	}

	/**
	 * Is API Key Setted for use.
	 *
	 * @return bool
	 */
	public static function is_api_key_setted() {
		return ( 'K' === Helper::get_option( 'mode' ) );
	}
}

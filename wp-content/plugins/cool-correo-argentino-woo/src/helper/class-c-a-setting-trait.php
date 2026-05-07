<?php
/**
 * Class CATrait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

/**
 * CA Trait
 */
trait CASettingTrait {
	/**
	 * Get Prices Array
	 *
	 * @return array
	 */
	public static function get_prices_array_init() {
		$ret = array();

		foreach ( self::get_weight_ranges() as $weight ) {
			foreach ( self::get_zones_array_init() as $zone ) {
				$ret['classic'][ $weight ][ $zone ] = 0;
				$ret['branch'][ $weight ][ $zone ]  = 0;
			}
		}
		return $ret;
	}

	/**
	 * Get Zones Array
	 *
	 * @return array
	 */
	public static function get_zones_array_init() {
		return array( '1', '2', '3', '4' );
	}

	/**
	 * Get Weight Ranges
	 *
	 * @return array
	 */
	public static function get_weight_ranges() {
		return array( '0.5', '1', '2', '3', '5', '10', '15', '20', '25', '30', '35', '40', '50', '60', '70', '80', '90', '100', '110', '120', '130', '140', '150' );
	}

	/**
	 * Get Provincias
	 *
	 * @return array
	 */
	public static function get_states_array() {
		return array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
	}
}

<?php
/**
 * Settings Trait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

trait CABranchTrait {

	/**
	 * Gets branch dropdown
	 *
	 * @param string $state Provice to get branches from.
	 * @return array
	 */
	public static function get_branches_dropdown( $state = null ) {
		if ( empty( $state ) ) {
			$branches = self::get_branches();
		} else {
			$branches = self::get_branches_by_state( $state );
		}

		$ret = array();
		if ( is_array( $branches ) ) {
			foreach ( $branches as $key => $value ) {
				$postcode    = isset( $value['pc'] ) ? ', CP ' . $value['pc'] : '';
				$ret[ $key ] = $value['d'] . '(' . $value['a'] . ', ' . $value['c'] . $postcode . ')';
			}
		}

		return $ret;
	}

	/**
	 * Gets branch data structured for checkout selector.
	 *
	 * Returns pre-parsed data to avoid regex parsing in the template.
	 *
	 * @param string $state Province to get branches from.
	 * @return array Array with 'name', 'address', and 'postcode' for each branch.
	 */
	public static function get_branches_dropdown_with_postcode( $state = null ) {
		if ( empty( $state ) ) {
			$branches = self::get_branches();
		} else {
			$branches = self::get_branches_by_state( $state );
		}

		$ret = array();
		if ( is_array( $branches ) ) {
			foreach ( $branches as $key => $value ) {
				$name             = isset( $value['d'] ) ? $value['d'] : '';
				$city             = isset( $value['c'] ) ? $value['c'] : '';
				$city_display     = ! empty( $city ) ? ', ' . $city : '';
				$street           = isset( $value['a'] ) ? $value['a'] : '';
				$postcode         = isset( $value['pc'] ) ? $value['pc'] : '';
				$postcode_display = ! empty( $postcode ) ? ', ' . $postcode : '';

				$ret[ $key ] = array(
					'name'     => $name,
					'address'  => $street . $city_display . $postcode_display,
					'postcode' => $postcode,
				);
			}
		}

		return $ret;
	}

	/**
	 * Gets branch array
	 *
	 * @return array
	 */
	public static function get_branches() {
		$branches = get_option( 'wc-coolca-branches', true );
		return $branches;
	}

	/**
	 * Get branches count
	 *
	 * @return int
	 */
	public static function get_branches_count() {
		$branches = self::get_branches();
		return is_array( $branches ) ? count( $branches ) : 0;
	}

	/**
	 * Get branch array by state
	 *
	 * @param string $state State.
	 * @return array
	 */
	public static function get_branches_by_state( $state ) {
		$branches = get_option( 'wc-coolca-branches-' . $state, true );
		return $branches;
	}
}

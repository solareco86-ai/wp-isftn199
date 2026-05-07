<?php
/**
 * Settings Trait
 *
 * @package  MANCA\CoolCA\Helper
 */

namespace MANCA\CoolCA\Helper;

trait CACostTrait {

	/**
	 * Gets full Zone Map
	 *
	 * @return array
	 */
	public static function get_zone_map() {
		return array(

			// Capital Federal.
			'C' => array(
				'C' => '2',
				'B' => '2',
				'K' => '3',
				'H' => '3',
				'U' => '4',
				'X' => '2',
				'W' => '3',
				'E' => '2',
				'P' => '3',
				'Y' => '4',
				'L' => '2',
				'F' => '3',
				'M' => '3',
				'N' => '3',
				'Q' => '3',
				'R' => '3',
				'A' => '4',
				'J' => '3',
				'D' => '3',
				'Z' => '4',
				'S' => '2',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// Buenos Aires.
			'B' => array(
				'C' => '2',
				'B' => '2',
				'K' => '3',
				'H' => '3',
				'U' => '4',
				'X' => '2',
				'W' => '3',
				'E' => '2',
				'P' => '3',
				'Y' => '4',
				'L' => '2',
				'F' => '3',
				'M' => '3',
				'N' => '3',
				'Q' => '3',
				'R' => '3',
				'A' => '4',
				'J' => '3',
				'D' => '3',
				'Z' => '4',
				'S' => '2',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// Catamarca.
			'K' => array(
				'C' => '3',
				'B' => '3',
				'K' => '2',
				'H' => '3',
				'U' => '4',
				'X' => '2',
				'W' => '3',
				'E' => '3',
				'P' => '3',
				'Y' => '2',
				'L' => '3',
				'F' => '2',
				'M' => '3',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '2',
				'J' => '2',
				'D' => '2',
				'Z' => '4',
				'S' => '3',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),

			// Chaco.
			'H' => array(
				'C' => '3',
				'B' => '3',
				'K' => '3',
				'H' => '2',
				'U' => '4',
				'X' => '3',
				'W' => '2',
				'E' => '2',
				'P' => '2',
				'Y' => '3',
				'L' => '4',
				'F' => '3',
				'M' => '4',
				'N' => '2',
				'Q' => '4',
				'R' => '4',
				'A' => '3',
				'J' => '4',
				'D' => '4',
				'Z' => '4',
				'S' => '2',
				'G' => '2',
				'V' => '4',
				'T' => '3',
			),

			// Chubut.
			'U' => array(
				'C' => '4',
				'B' => '4',
				'K' => '4',
				'H' => '4',
				'U' => '2',
				'X' => '4',
				'W' => '4',
				'E' => '4',
				'P' => '4',
				'Y' => '4',
				'L' => '3',
				'F' => '4',
				'M' => '4',
				'N' => '4',
				'Q' => '3',
				'R' => '2',
				'A' => '4',
				'J' => '4',
				'D' => '4',
				'Z' => '3',
				'S' => '4',
				'G' => '4',
				'V' => '3',
				'T' => '4',
			),

			// Córdoba.
			'X' => array(
				'C' => '2',
				'B' => '2',
				'K' => '2',
				'H' => '3',
				'U' => '4',
				'X' => '2',
				'W' => '3',
				'E' => '2',
				'P' => '3',
				'Y' => '3',
				'L' => '2',
				'F' => '2',
				'M' => '2',
				'N' => '4',
				'Q' => '3',
				'R' => '3',
				'A' => '3',
				'J' => '2',
				'D' => '2',
				'Z' => '4',
				'S' => '2',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),

			// Corrientes.
			'W' => array(
				'C' => '3',
				'B' => '3',
				'K' => '3',
				'H' => '2',
				'U' => '4',
				'X' => '3',
				'W' => '2',
				'E' => '2',
				'P' => '2',
				'Y' => '3',
				'L' => '4',
				'F' => '3',
				'M' => '4',
				'N' => '2',
				'Q' => '4',
				'R' => '4',
				'A' => '3',
				'J' => '4',
				'D' => '4',
				'Z' => '4',
				'S' => '2',
				'G' => '2',
				'V' => '4',
				'T' => '3',
			),

			// Entre Ríos.
			'E' => array(
				'C' => '2',
				'B' => '2',
				'K' => '3',
				'H' => '2',
				'U' => '4',
				'X' => '2',
				'W' => '2',
				'E' => '2',
				'P' => '3',
				'Y' => '3',
				'L' => '3',
				'F' => '3',
				'M' => '3',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '3',
				'J' => '3',
				'D' => '2',
				'Z' => '4',
				'S' => '2',
				'G' => '2',
				'V' => '4',
				'T' => '3',
			),

			// Formosa.
			'P' => array(
				'C' => '3',
				'B' => '3',
				'K' => '3',
				'H' => '2',
				'U' => '4',
				'X' => '3',
				'W' => '2',
				'E' => '3',
				'P' => '2',
				'Y' => '3',
				'L' => '4',
				'F' => '3',
				'M' => '4',
				'N' => '2',
				'Q' => '4',
				'R' => '4',
				'A' => '3',
				'J' => '4',
				'D' => '4',
				'Z' => '4',
				'S' => '2',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// Jujuy.
			'Y' => array(
				'C' => '4',
				'B' => '4',
				'K' => '2',
				'H' => '3',
				'U' => '4',
				'X' => '3',
				'W' => '3',
				'E' => '3',
				'P' => '3',
				'Y' => '2',
				'L' => '4',
				'F' => '3',
				'M' => '4',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '2',
				'J' => '4',
				'D' => '4',
				'Z' => '4',
				'S' => '3',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),

			// La Pampa.
			'L' => array(
				'C' => '2',
				'B' => '2',
				'K' => '3',
				'H' => '4',
				'U' => '3',
				'X' => '2',
				'W' => '4',
				'E' => '3',
				'P' => '4',
				'Y' => '4',
				'L' => '2',
				'F' => '3',
				'M' => '3',
				'N' => '4',
				'Q' => '2',
				'R' => '2',
				'A' => '4',
				'J' => '3',
				'D' => '2',
				'Z' => '4',
				'S' => '3',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// La Rioja.
			'F' => array(
				'C' => '3',
				'B' => '3',
				'K' => '2',
				'H' => '3',
				'U' => '4',
				'X' => '2',
				'W' => '3',
				'E' => '3',
				'P' => '3',
				'Y' => '3',
				'L' => '3',
				'F' => '2',
				'M' => '2',
				'N' => '4',
				'Q' => '4',
				'R' => '4',
				'A' => '2',
				'J' => '2',
				'D' => '2',
				'Z' => '4',
				'S' => '3',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),

			// Mendoza.
			'M' => array(
				'C' => '3',
				'B' => '3',
				'K' => '3',
				'H' => '4',
				'U' => '4',
				'X' => '2',
				'W' => '4',
				'E' => '3',
				'P' => '4',
				'Y' => '4',
				'L' => '3',
				'F' => '2',
				'M' => '2',
				'N' => '4',
				'Q' => '3',
				'R' => '4',
				'A' => '4',
				'J' => '2',
				'D' => '2',
				'Z' => '4',
				'S' => '3',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// Misiones.
			'N' => array(
				'C' => '3',
				'B' => '3',
				'K' => '3',
				'H' => '2',
				'U' => '4',
				'X' => '4',
				'W' => '2',
				'E' => '3',
				'P' => '2',
				'Y' => '3',
				'L' => '4',
				'F' => '4',
				'M' => '4',
				'N' => '2',
				'Q' => '4',
				'R' => '4',
				'A' => '3',
				'J' => '4',
				'D' => '4',
				'Z' => '4',
				'S' => '3',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// Neuquen.
			'Q' => array(
				'C' => '3',
				'B' => '3',
				'K' => '4',
				'H' => '4',
				'U' => '3',
				'X' => '3',
				'W' => '4',
				'E' => '4',
				'P' => '4',
				'Y' => '4',
				'L' => '2',
				'F' => '4',
				'M' => '3',
				'N' => '4',
				'Q' => '2',
				'R' => '2',
				'A' => '4',
				'J' => '3',
				'D' => '3',
				'Z' => '3',
				'S' => '4',
				'G' => '4',
				'V' => '3',
				'T' => '4',
			),

			// Rio Negro.
			'R' => array(
				'C' => '3',
				'B' => '3',
				'K' => '4',
				'H' => '4',
				'U' => '2',
				'X' => '3',
				'W' => '4',
				'E' => '4',
				'P' => '4',
				'Y' => '4',
				'L' => '2',
				'F' => '4',
				'M' => '4',
				'N' => '4',
				'Q' => '2',
				'R' => '2',
				'A' => '4',
				'J' => '4',
				'D' => '3',
				'Z' => '3',
				'S' => '4',
				'G' => '4',
				'V' => '3',
				'T' => '4',
			),

			// Salta.
			'A' => array(
				'C' => '4',
				'B' => '4',
				'K' => '2',
				'H' => '3',
				'U' => '4',
				'X' => '3',
				'W' => '3',
				'E' => '3',
				'P' => '3',
				'Y' => '2',
				'L' => '4',
				'F' => '2',
				'M' => '4',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '2',
				'J' => '3',
				'D' => '4',
				'Z' => '4',
				'S' => '3',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),

			// San Juan.
			'J' => array(
				'C' => '3',
				'B' => '3',
				'K' => '2',
				'H' => '4',
				'U' => '4',
				'X' => '2',
				'W' => '4',
				'E' => '3',
				'P' => '4',
				'Y' => '4',
				'L' => '3',
				'F' => '2',
				'M' => '2',
				'N' => '4',
				'Q' => '3',
				'R' => '4',
				'A' => '3',
				'J' => '2',
				'D' => '2',
				'Z' => '4',
				'S' => '3',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// San Luis.
			'D' => array(
				'C' => '3',
				'B' => '3',
				'K' => '2',
				'H' => '4',
				'U' => '4',
				'X' => '2',
				'W' => '4',
				'E' => '2',
				'P' => '4',
				'Y' => '4',
				'L' => '2',
				'F' => '2',
				'M' => '2',
				'N' => '4',
				'Q' => '3',
				'R' => '3',
				'A' => '4',
				'J' => '2',
				'D' => '2',
				'Z' => '4',
				'S' => '2',
				'G' => '3',
				'V' => '4',
				'T' => '3',
			),

			// Santa Cruz.
			'Z' => array(
				'C' => '4',
				'B' => '4',
				'K' => '4',
				'H' => '4',
				'U' => '3',
				'X' => '4',
				'W' => '4',
				'E' => '4',
				'P' => '4',
				'Y' => '4',
				'L' => '4',
				'F' => '4',
				'M' => '4',
				'N' => '4',
				'Q' => '4',
				'R' => '4',
				'A' => '4',
				'J' => '4',
				'D' => '4',
				'Z' => '2',
				'S' => '4',
				'G' => '4',
				'V' => '2',
				'T' => '4',
			),

			// Santa Fe.
			'S' => array(
				'C' => '2',
				'B' => '2',
				'K' => '3',
				'H' => '2',
				'U' => '4',
				'X' => '2',
				'W' => '2',
				'E' => '2',
				'P' => '2',
				'Y' => '3',
				'L' => '3',
				'F' => '3',
				'M' => '3',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '3',
				'J' => '3',
				'D' => '2',
				'Z' => '4',
				'S' => '2',
				'G' => '2',
				'V' => '4',
				'T' => '3',
			),

			// Santiago del Estero.
			'G' => array(
				'C' => '3',
				'B' => '3',
				'K' => '2',
				'H' => '2',
				'U' => '4',
				'X' => '2',
				'W' => '2',
				'E' => '2',
				'P' => '3',
				'Y' => '2',
				'L' => '3',
				'F' => '2',
				'M' => '3',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '2',
				'J' => '3',
				'D' => '3',
				'Z' => '4',
				'S' => '2',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),

			// Tierra del Fuego.
			'V' => array(
				'C' => '4',
				'B' => '4',
				'K' => '4',
				'H' => '4',
				'U' => '4',
				'X' => '4',
				'W' => '4',
				'E' => '4',
				'P' => '4',
				'Y' => '4',
				'L' => '4',
				'F' => '4',
				'M' => '4',
				'N' => '4',
				'Q' => '4',
				'R' => '4',
				'A' => '4',
				'J' => '4',
				'D' => '4',
				'Z' => '2',
				'S' => '4',
				'G' => '4',
				'V' => '2',
				'T' => '4',
			),

			// Tucumán.
			'T' => array(
				'C' => '3',
				'B' => '3',
				'K' => '2',
				'H' => '3',
				'U' => '4',
				'X' => '2',
				'W' => '3',
				'E' => '3',
				'P' => '3',
				'Y' => '2',
				'L' => '3',
				'F' => '2',
				'M' => '3',
				'N' => '3',
				'Q' => '4',
				'R' => '4',
				'A' => '2',
				'J' => '3',
				'D' => '3',
				'Z' => '4',
				'S' => '3',
				'G' => '2',
				'V' => '4',
				'T' => '2',
			),
		);
	}

	/**
	 * Gets zone by State
	 *
	 * @param string $OriginState Province from.
	 * @param string $TargetState Province to.
	 * @return string
	 */
	public static function get_zone_by_state( $OriginState, $TargetState ) {
		return ( isset( self::get_zone_map()[ $OriginState ][ $TargetState ] ) ) ? self::get_zone_map()[ $OriginState ][ $TargetState ] : '4';
	}

	/**
	 * Gets zone by State, City & PostCode
	 *
	 * @param string $OriginState From Province.
	 * @param string $OriginCity From City.
	 * @param string $OriginPostal From Postal Code.
	 * @param string $TargetState To Province.
	 * @param string $TargetCity To City.
	 * @param string $TargetPostal To Postal Code.
	 * @return string
	 */
	public static function get_zone( $OriginState, $OriginCity, $OriginPostal, $TargetState, $TargetCity, $TargetPostal ) {
		if ( $OriginState === $TargetState && $OriginCity === $TargetCity && $OriginPostal === $TargetPostal ) {
			return '1';
		}

		if ( $OriginState === $TargetState && ( 'C' === $OriginState || 'B' === $OriginState ) ) {
			if (
			( ( $OriginPostal >= '1000' && $OriginPostal <= '1893' ) ||
			'1924' === $OriginPostal ||
			'2752' === $OriginPostal ||
			'2760' === $OriginPostal ||
			'2814' === $OriginPostal ||
			'2930' === $OriginPostal ||
			'2931' === $OriginPostal ||
			'2935' === $OriginPostal ||
			'2953' === $OriginPostal ) &&
			( ( $TargetPostal >= '1000' && $TargetPostal <= '1893' ) ||
			'1924' === $TargetPostal ||
			'2752' === $TargetPostal ||
			'2760' === $TargetPostal ||
			'2814' === $TargetPostal ||
			'2930' === $TargetPostal ||
			'2931' === $TargetPostal ||
			'2935' === $TargetPostal ||
			'2953' === $TargetPostal )
			) {
				return '1';
			}
		}

		return self::get_zone_by_state( $OriginState, $TargetState );
	}

	/**
	 * Gets price Map
	 *
	 * @return array
	 */
	public static function get_price_map() {
		return ! empty( get_option( 'wc-coolca-price' ) ) ? get_option( 'wc-coolca-price' ) : array();
	}

	/**
	 * Gets price by PriceType, Service, Zone & Weight
	 *
	 * @param string $ServiceType Service type.
	 * @param string $Zone Zone.
	 * @param int    $WeightRange Weight Range.
	 * @return float
	 */
	public static function get_price( $ServiceType = '', $Zone = '', $WeightRange = '' ) {
		return ( isset( self::get_price_map()[ $ServiceType ][ $WeightRange ][ $Zone ] ) ) ? self::get_price_map()[ $ServiceType ][ $WeightRange ][ $Zone ] : 0;
	}
}

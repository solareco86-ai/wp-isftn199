<?php
/**
 * Price Method Abstract Class
 *
 * @package  MANCA\CoolCA\PriceMethod
 */

namespace MANCA\CoolCA\PriceMethod;

defined( 'ABSPATH' ) || exit;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\Sdk\CoolCASdk;

/**
 * Price Method Abstract
 */
abstract class PriceMethodAbstract implements PriceMethodInterface {

	// Version 1.3.0.
	// El peso debe ser menor a 50 Kg o 50.000 gramos.
	// El largo debe ser menor o igual a 200 cm.
	// La suma (largo + ancho + altura) debe ser menor o igual a 3 metros.
	const COOLCA_MAX_PAQ_WEIGHT            = 50;
	const COOLCA_MAX_PAQ_DIM               = 300;
	const COOLCA_MAX_PAQ_CM                = 200;
	const COOLCA_MAX_PAQ_VOLUMETRIC_WEIGHT = 166;

	const COOLCA_MAX_PAQ_VOL        = 1;
	const COOLCA_MAX_PAQ_VOL_LENGHT = 100;
	const COOLCA_MAX_PAQ_VOL_WIDTH  = 100;
	const COOLCA_MAX_PAQ_VOL_HEIGHT = 100;
	/**
	 * Get Init Paq
	 *
	 * @return array
	 */
	private function get_init_package() {
		return array(
			'weight' => 0,
			'width'  => 0,
			'height' => 0,
			'length' => 0,
			'items'  => array(),
			'cost'   => 0,
			'volume' => 0,
		);
	}

	/**
	 * Calc Dimensions from Volume.
	 *
	 * @param int $volume min height required.
	 * @param int $minHeight min height required.
	 * @param int $minLenght min Lenght required.
	 * @param int $minWidth min Width required.
	 *
	 * @return array
	 */
	private function get_dimensions( $volume, $minHeight, $minLenght, $minWidth ) {
		$ret = array();
		for ( $auxHeight = round( $minHeight ); $auxHeight <= self::COOLCA_MAX_PAQ_CM; ++$auxHeight ) {
			for ( $auxLenght = round( $minLenght ); $auxLenght <= self::COOLCA_MAX_PAQ_CM; ++$auxLenght ) {
				for ( $auxWidth = round( $minWidth ); $auxWidth <= self::COOLCA_MAX_PAQ_CM; ++$auxWidth ) {
					if ( self::COOLCA_MAX_PAQ_DIM <= ( $auxWidth + $auxLenght + $auxHeight ) ) {
						break 2;
					}
					$auxVolume = round( floatval( ( $auxHeight * $auxLenght * $auxWidth ) / 1000000 ), 7 );
					if ( abs( $auxVolume ) > abs( $volume ) ) {
						return array(
							'width'  => $auxWidth,
							'height' => $auxHeight,
							'length' => $auxLenght,
						);
					}
				}
			}
		}

		return array(
			'width'  => self::COOLCA_MAX_PAQ_VOL_WIDTH,
			'height' => self::COOLCA_MAX_PAQ_VOL_HEIGHT,
			'length' => self::COOLCA_MAX_PAQ_VOL_LENGHT,
		);
	}

	/**
	 * Get Packages when products are stackables.
	 *
	 * @param array $product_list Cart items.
	 *
	 * @return array
	 */
	private function get_paqars_when_stackables( $product_list = array() ) {
		Helper::log( 'Init ' . __CLASS__ . '.' . __FUNCTION__ );
		$PaqArs = array();

		// Init package.
		$PaqAr = self::get_init_package();

		foreach ( $product_list as $product_item ) {
			for ( $i = 1;  $i <= $product_item['qty']; $i++ ) {
				$NEED_NEW_PAQ = false;
				$auxVolume    = $PaqAr['volume'] + $product_item['volume'];
				$auxWeight    = $PaqAr['weight'] + $product_item['weight'];

				$NEED_NEW_PAQ = $NEED_NEW_PAQ
								|| ( $auxWeight > self::COOLCA_MAX_PAQ_WEIGHT )
								|| ( $auxVolume > self::COOLCA_MAX_PAQ_VOL );

				if ( $NEED_NEW_PAQ ) {
					$PaqArs[] = $PaqAr;
					$PaqAr    = array(
						'weight' => $product_item['weight'],
						'volume' => $product_item['volume'],
						'width'  => max( $PaqAr['width'], $product_item['width'] ),
						'height' => max( $PaqAr['height'], $product_item['height'] ),
						'length' => max( $PaqAr['length'], $product_item['length'] ),
						'items'  => array( $product_item['id'] ),
						'cost'   => 0,
					);
				} else {
					$PaqAr['weight']  = $auxWeight;
					$PaqAr['volume']  = $auxVolume;
					$PaqAr['width']   = $product_item['width'];
					$PaqAr['length']  = $product_item['length'];
					$PaqAr['height']  = $product_item['height'];
					$PaqAr['items'][] = $product_item['id'];
				}
			}
		}
		$PaqArs[]  = $PaqAr;
		$PaqArsRet = array();
		// Get Dimensions from volume.
		foreach ( $PaqArs as $PaqAr ) {
			$newDimentions   = self::get_dimensions( $PaqAr['volume'], $PaqAr['height'], $PaqAr['length'], $PaqAr['width'] );
			$PaqAr['width']  = $newDimentions['width'];
			$PaqAr['length'] = $newDimentions['length'];
			$PaqAr['height'] = $newDimentions['height'];
			$PaqArsRet[]     = $PaqAr;
		}

		Helper::log( 'PaqArs: ' );
		Helper::log( $PaqArsRet );
		Helper::log( 'End ' . __CLASS__ . '.' . __FUNCTION__ );
		return $PaqArsRet;
	}

	/**
	 * Get Packages when producst are not staclables.
	 *
	 * @param array $product_list Cart items.
	 *
	 * @return array
	 */
	private function get_paqars_when_non_stackables( $product_list = array() ) {
		Helper::log( 'Init ' . __CLASS__ . '.' . __FUNCTION__ );
		$PaqArs = array();

		// Init package.
		$PaqAr = self::get_init_package();

		foreach ( $product_list as $product_item ) {
			for ( $i = 1;  $i <= $product_item['qty']; $i++ ) {
				$NEED_NEW_PAQ = false;
				$auxWeight    = $PaqAr['weight'] + $product_item['weight'];
				$auxHeight    = max( $PaqAr['height'], $product_item['height'] );
				if ( $PaqAr['length'] + $product_item['length'] < self::COOLCA_MAX_PAQ_CM ) {
					$auxLenght = $PaqAr['length'] + $product_item['length'];
					$auxWidth  = max( $PaqAr['width'], $product_item['width'] );
				} elseif ( $PaqAr['width'] + $product_item['width'] < self::COOLCA_MAX_PAQ_CM ) {
					$auxLenght = max( $PaqAr['length'], $product_item['length'] );
					$auxWidth  = $PaqAr['width'] + $product_item['width'];
				} else {
					$NEED_NEW_PAQ = true;
				}
				$NEED_NEW_PAQ = $NEED_NEW_PAQ
								|| ( $auxWeight > self::COOLCA_MAX_PAQ_WEIGHT )
								|| ( $auxHeight > self::COOLCA_MAX_PAQ_CM )
								|| ( $auxLenght > self::COOLCA_MAX_PAQ_CM )
								|| ( $auxWidth > self::COOLCA_MAX_PAQ_CM )
								|| ( $auxHeight + $auxLenght + $auxWidth > self::COOLCA_MAX_PAQ_DIM );
				if ( $NEED_NEW_PAQ ) {
					$PaqArs[] = $PaqAr;
					$PaqAr    = array(
						'weight' => $product_item['weight'],
						'width'  => $product_item['width'],
						'height' => $product_item['height'],
						'length' => $product_item['length'],
						'items'  => array( $product_item['id'] ),
						'cost'   => 0,
					);
				} else {
					$PaqAr['weight']  = $auxWeight;
					$PaqAr['width']   = $auxWidth;
					$PaqAr['length']  = $auxLenght;
					$PaqAr['height']  = $auxHeight;
					$PaqAr['items'][] = $product_item['id'];
				}
			}
		}
		$PaqArs[] = $PaqAr;

		Helper::log( 'PaqArs: ' );
		Helper::log( $PaqArs );
		Helper::log( 'End ' . __CLASS__ . '.' . __FUNCTION__ );
		return $PaqArs;
	}
	/**
	 * Get Packages
	 *
	 * @param array $product_list Cart items.
	 *
	 * @return array
	 */
	public function get_paqars( $product_list = array() ) {
		Helper::log( 'Init ' . __CLASS__ . '.' . __FUNCTION__ );
		return ( 'yes' === Helper::get_option( 'stack-products' ) ) ? self::get_paqars_when_stackables( $product_list ) : self::get_paqars_when_non_stackables( $product_list );
	}

	/**
	 * Calc Cost
	 *
	 * @param WC_CoolCA $shippingMethod Woo Shipping Method.
	 * @param array     $package List of product items of the cart.
	 * @param array     $destination List of Destination data.
	 *
	 * @return array
	 */
	abstract public function calc_cost( WC_CoolCA $shippingMethod, $package = array(), $destination = array() );
}

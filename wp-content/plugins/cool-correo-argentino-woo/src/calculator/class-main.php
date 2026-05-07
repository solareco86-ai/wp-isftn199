<?php
/**
 * Calculator Class
 *
 * Version 1.1.2 - Add Product Calculator
 *
 * @package  MANCA\CoolCA\Calculator
 */

namespace MANCA\CoolCA\Calculator;

defined( 'ABSPATH' ) || exit;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\ShippingMethod\WC_CoolCA;
use WC_Shipping;

/**
 * Main Plugin Process Class
 */
class Main {

	/**
	 * Show Calculator
	 *
	 * @param string $product_id Woo Product Id.
	 *
	 * @return void
	 */
	public static function show_calculator( $product_id = '' ) {

		// Check if the product shipping calculator is enabled.
		if ( 'yes' !== Helper::get_option( 'product-shipping-calculator' ) ) {
			return;
		}

		// Check if the product is virtual.
		$product_id = empty( $product_id ) ? get_the_ID() : $product_id;
		$product    = wc_get_product( $product_id );
		if ( $product->is_virtual() ) {
			return;
		}

		// Check if the location is correct.
		$current_action   = current_action();
		$setting_location = Helper::get_option( 'shipping-calculator-location', 'before-addtocartbutton' );
		if ( 'before-addtocartbutton' === $setting_location && 'woocommerce_before_add_to_cart_button' !== $current_action ) {
			return;
		}
		if ( 'after-addtocartbutton' === $setting_location && 'woocommerce_after_add_to_cart_button' !== $current_action ) {
			return;
		}
		if ( 'before-addtocartform' === $setting_location && 'woocommerce_before_add_to_cart_form' !== $current_action ) {
			return;
		}
		if ( 'after-addtocartform' === $setting_location && 'woocommerce_after_add_to_cart_form' !== $current_action ) {
			return;
		}

		$data                    = array();
		$data['rates']           = self::run_calculator_for_product( $product_id );
		$data['product_id']      = $product_id;
		$data['current_address'] = self::get_current_shipping_address();
		Helper::get_template_part( 'product', 'shipping-cost', $data );
		$setting_style = Helper::get_option( 'shipping-calculator-style', 'none' );
		wp_enqueue_style( 'coolca-product-shipping-cost-' . $setting_style );
		wp_enqueue_script( 'coolca-product-shipping-cost' );
	}

	/**
	 * Get Current Address
	 *
	 * @return string
	 */
	public static function get_current_shipping_address() {
		$country  = 'AR';
		$state    = WC()->customer->get_shipping_state();
		$postcode = WC()->customer->get_shipping_postcode();
		return 'Argentina, ' . WC()->countries->get_states( $country )[ $state ] . ', ' . $postcode;
	}

	/**
	 * Show Calculator
	 *
	 * @param string $product_id Woo Product Id.
	 * @param bool   $include_cart Include cart.
	 * @return array
	 */
	public static function run_calculator_for_product( $product_id, $include_cart = true ) {
		$product              = wc_get_product( $product_id );
		$variation_id         = 0;
		$variation_attributes = array();

		if ( $product->is_type( 'variable' ) ) {
			foreach ( $product->get_children() as $child_id ) {
				$variation = wc_get_product( $child_id );
				if ( $variation && $variation->is_type( 'variation' ) ) {
					$eligible = $variation->managing_stock()
						? ( (int) $variation->get_stock_quantity() > 0 )
						: $variation->is_in_stock();

					if ( $eligible ) {
						$variation_id         = (int) $child_id;
						$variation_attributes = $variation->get_variation_attributes();
						break;
					}
				}
			}
			if ( ! $variation_id ) {
				return array();
			}
		} elseif ( $product->is_virtual() ) {
				return array();
		}

		$packages = $include_cart
			? self::create_package_merged_with_cart( $product_id, 1, $variation_id, $variation_attributes )
			: array( self::create_product_package( $product_id, 1, $variation_id, $variation_attributes ) );

		$rates = self::exec_shipping_calc_without_cart( $packages );

		$ret = array();
		foreach ( $rates as $rate ) {
			if ( 'coolca' === $rate->get_method_id() ) {
				$ret[] = array(
					'id'   => $rate->get_method_id(),
					'name' => $rate->get_label(),
					'cost' => wc_price( $rate->get_cost() ),
				);
			}
		}

		return $ret;
	}

	/**
	 * Create Product Package
	 *
	 * @param int   $product_id Product Id.
	 * @param int   $qty Quantity.
	 * @param int   $variation_id Variation Id.
	 * @param array $variation_attributes Variation Attributes.
	 * @return array
	 */
	protected static function create_product_package( $product_id, $qty = 1, $variation_id = 0, $variation_attributes = array() ) {
		// Destino desde la dirección que ya guardás en la sesión del cliente.
		$destination = array(
			'country'   => 'AR', // ISO-2 correcto para Argentina.
			'state'     => WC()->customer->get_shipping_state(),
			'postcode'  => WC()->customer->get_shipping_postcode(),
			'city'      => '', // opcional.
			'address'   => '', // opcional.
			'address_2' => '', // opcional.
		);

		// Producto/variación.
		$_product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );

		// Precios: usá el precio sin impuestos para contents_cost (igual que hace WC_Cart).
		$line_total = wc_get_price_excluding_tax( $_product, array( 'qty' => $qty ) );

		$cart_item = array(
			'key'               => 'coolca_temp_calc',  // identificador cualquiera.
			'product_id'        => (int) $product_id,
			'variation_id'      => (int) $variation_id,
			'variation'         => is_array( $variation_attributes ) ? $variation_attributes : array(),
			'quantity'          => (int) $qty,
			'data'              => $_product, // instancia de WC_Product.
			'data_hash'         => 'coolca_temp_calc',
			'line_total'        => (float) $line_total,
			'line_tax'          => 0,
			'line_subtotal'     => (float) $line_total,
			'line_subtotal_tax' => 0,
		);

		return array(
			'contents'        => array( $cart_item['key'] => $cart_item ),
			'contents_cost'   => (float) $line_total,
			'applied_coupons' => array(),
			'user'            => array( 'ID' => get_current_user_id() ),
			'destination'     => $destination,
		);
	}

	/**
	 * Exec Shipping Calc Without Cart
	 *
	 * @param array $packages Packages.
	 * @return array
	 */
	protected static function exec_shipping_calc_without_cart( $packages ) {
		$shipping = new \WC_Shipping();
		$shipping->load_shipping_methods();

		// Limpia cualquier “cache” previa de paquetes.
		if ( method_exists( $shipping, 'reset' ) ) {
			$shipping->reset();
		}

		// Calcula.
		$shipping->calculate_shipping( $packages );

		// Recogemos las tarifas del primer package (en este caso solo hay uno).
		$calculated = $shipping->get_packages();
		if ( empty( $calculated ) || empty( $calculated[0]['rates'] ) ) {
			return array();
		}

		return $calculated[0]['rates'];
	}

	/**
	 * Create Package Merged With Cart
	 *
	 * @param int   $product_id Product Id.
	 * @param int   $qty Quantity.
	 * @param int   $variation_id Variation Id.
	 * @param array $variation_attributes Variation Attributes.
	 * @return array
	 */
	protected static function create_package_merged_with_cart( $product_id, $qty = 1, $variation_id = 0, $variation_attributes = array() ) {
		// 1) Traer packages actuales del carrito (sin calcular nada global).
		$existing_packages = ( WC()->cart ) ? WC()->cart->get_shipping_packages() : array();

		// 2) Base: si no hay packages (carrito vacío), usar un esqueleto.
		if ( empty( $existing_packages ) ) {
			$base              = array(
				'contents'        => array(),
				'contents_cost'   => 0,
				'applied_coupons' => array(),
				'user'            => array( 'ID' => get_current_user_id() ),
				'destination'     => array(
					'country'   => 'AR',
					'state'     => WC()->customer->get_shipping_state() ?? '',
					'postcode'  => WC()->customer->get_shipping_postcode() ?? '',
					'city'      => '',
					'address'   => '',
					'address_2' => '',
				),
			);
			$existing_packages = array( $base );
		}

		// 3) Construir el cart_item sintético del producto candidato.
		$_product   = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
		$line_total = wc_get_price_excluding_tax( $_product, array( 'qty' => $qty ) );

		$temp_key  = 'coolca_temp_calc_' . wp_generate_uuid4();
		$cart_item = array(
			'key'               => $temp_key,
			'product_id'        => (int) $product_id,
			'variation_id'      => (int) $variation_id,
			'variation'         => is_array( $variation_attributes ) ? $variation_attributes : array(),
			'quantity'          => (int) $qty,
			'data'              => $_product,         // WC_Product requerido.
			// 'data_hash'       => (omitido a propósito).
			'line_total'        => (float) $line_total,
			'line_tax'          => 0,
			'line_subtotal'     => (float) $line_total,
			'line_subtotal_tax' => 0,
		);

		// 4) Clonar el primer package y sumar el ítem sintético.
		$merged                             = $existing_packages; // no tocar el original por referencia.
		$merged[0]['contents']              = isset( $merged[0]['contents'] ) ? $merged[0]['contents'] : array();
		$merged[0]['contents'][ $temp_key ] = $cart_item;

		// contents_cost del package suele ser el subtotal (sin impuestos) de los items.
		$current_contents_cost = isset( $merged[0]['contents_cost'] ) ? (float) $merged[0]['contents_cost'] : 0;

		// Por si el package original no trae contents_cost fiable, lo recalculamos rápido.
		if ( $current_contents_cost <= 0 && ! empty( $merged[0]['contents'] ) ) {
			$current_contents_cost = 0;
			foreach ( $merged[0]['contents'] as $ci ) {
				$current_contents_cost += isset( $ci['line_total'] ) ? (float) $ci['line_total'] : 0;
			}
		}

		$merged[0]['contents_cost'] = $current_contents_cost + (float) $line_total;

		// 5) Devolver el set completo de packages (si había más de uno, se conservan tal cual).
		return $merged;
	}

	/**
	 * Ajax Update Product Shipping Calculator
	 *
	 * @return void
	 */
	public static function ajax_callback_wp() {
		if ( ! isset( $_REQUEST['coolca_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['coolca_nonce'] ) ), 'cool-ca' ) ) {
			wp_send_json_error();
		}

		// Save selected point to session.
		WC()->customer->set_shipping_state( isset( $_REQUEST['calc_shipping_state'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['calc_shipping_state'] ) ) : null );
		WC()->customer->set_shipping_postcode( isset( $_REQUEST['calc_shipping_postcode'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['calc_shipping_postcode'] ) ) : null );
		$data = array();
		if ( isset( $_REQUEST['product_id'] ) && ! empty( $_REQUEST['product_id'] ) ) {
			$data['rates'] = self::run_calculator_for_product( sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) ) );
		}
		$data['current_address'] = self::get_current_shipping_address();
		wp_send_json_success( $data );
	}
}

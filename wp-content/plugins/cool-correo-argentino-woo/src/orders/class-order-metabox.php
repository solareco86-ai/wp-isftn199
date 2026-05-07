<?php
/**
 * Branch Field Class
 *
 * @package  MANCA\CoolCA\Orders
 */

namespace MANCA\CoolCA\Orders;

defined( 'ABSPATH' ) || exit();

use MANCA\CoolCA\ShippingMethod\WC_CoolCA;
use MANCA\CoolCA\Helper\Helper;

/**
 * Checkout Class to mantain new custom fields for checkout.
 */
class OrderMetabox {
	/**
	 * Adds the meta box container.
	 *
	 * @param string $post_type current post type.
	 */
	public static function add_meta_box( $post_type ) {
		// Limit meta box to certain post types.
		$post_types = array( 'woocommerce_page_wc-orders' );

		if ( in_array( $post_type, $post_types, true ) ) {
			add_meta_box(
				'cool',
				__( 'Cool Correo Argentino', 'cool-ca' ),
				function ( $post ) use ( $post_type ) {
					static::render_meta_box_content( $post );
				},
				null,
				'side',
				'high'
			);
		}
	}

	/**
	 * Rendes Metabox Content.
	 *
	 * @param WC_Order $order Woo Order Object.
	 */
	public static function render_meta_box_content( $order ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
				return;
		}
		?>
		<?php
		$shipping_methods = $order->get_shipping_methods();
		$shipping_method  = array_shift( $shipping_methods );

		$data              = array(
			'is_coolca'      => false,
			'service_type'   => '',
			'branch'         => '',
			'branch_name'    => '',
			'branch_address' => '',
			'paqs'           => '',
			'exported'       => false,
		);
		$data['is_coolca'] = ( ! empty( $shipping_method ) && 'coolca' === $shipping_method->get_method_id() );
		if ( $data['is_coolca'] ) {
			if ( method_exists( $shipping_method, 'get_instance_id' ) ) {
				$instance_id = $shipping_method->get_instance_id();
			} else {
				$instance_id = substr( $shipping_method, strpos( $shipping_method, ':' ) + 1 );
			}
			$CoolCAShippingMethod = new WC_CoolCA( $instance_id );
			$data['service_type'] = $CoolCAShippingMethod->service_type;

			if ( 'branch' === $data['service_type'] ) {
				$data['branch']         = trim( wc_get_order_item_meta( $shipping_method->get_id(), 'Sucursal' ) );
				$data['branch_name']    = trim( wc_get_order_item_meta( $shipping_method->get_id(), 'Nombre Sucursal' ) );
				$data['branch_address'] = trim( wc_get_order_item_meta( $shipping_method->get_id(), 'Dirección Sucursal' ) );
			}
				$PaqArsEncoded = wc_get_order_item_meta( $shipping_method->get_id(), '_paqars' );
			if ( $PaqArsEncoded ) {
				$PaqArs = Helper::decode_array( $PaqArsEncoded );
				foreach ( $PaqArs as &$PaqAr ) {
					$PaqAr['item_count'] = array_count_values( $PaqAr['items'] );
				}
				$data['paqs'] = $PaqArs;
			}
		}

			$data['exported'] = $order->get_meta( '_coolca_already_exported' );

		Helper::get_template_part( 'order', 'metabox', $data );
	}
}

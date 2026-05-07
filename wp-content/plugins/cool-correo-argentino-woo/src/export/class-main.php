<?php
/**
 * Export Page Class
 *
 * @package  MANCA\CoolCA\Export
 */

namespace MANCA\CoolCA\Export;

defined( 'ABSPATH' ) || exit;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\ShippingMethod\WC_CoolCA;
/**
 * Main Plugin Process Class
 */
class Main {
	/**
	 * Creates Process Page
	 *
	 * @return void
	 */
	public static function create_menu_option() {
		add_submenu_page(
			'woocommerce',
			__( 'Exporta Pedidos Correo Argentino', 'cool-ca' ),
			__( 'Exporta Pedidos Correo Argentino', 'cool-ca' ),
			'manage_woocommerce', // phpcs:ignore WordPress.WP.Capabilities.Unknown
			'coolca-export', // phpcs:ignore WordPress.WP.Capabilities.Unknown
			array( __CLASS__, 'page_content' )
		);
	}

	/**
	 * Convert Orders into Array Data to export
	 *
	 * @param array $orders Woo Orders.
	 * @return array
	 */
	public static function prepare_orders_to_export( $orders = array() ) {
		$data_arr     = array();
		$total_orders = 1;
		foreach ( $orders as $order ) {
			$shipping_methods   = $order->get_shipping_methods();
			$shipping_method    = array_shift( $shipping_methods );
			$PaqArs             = array(
				array(
					'weight' => 0,
					'width'  => 0,
					'height' => 0,
					'length' => 0,
					'items'  => array(),
					'cost'   => 0,
				),
			);
			$is_express_service = false;
			$branch             = '';
			$is_branch_service  = false;
			if ( 'coolca' === $shipping_method->get_method_id() ) {
				if ( method_exists( $shipping_method, 'get_instance_id' ) ) {
					$instance_id = $shipping_method->get_instance_id();
				} else {
					$instance_id = substr( $shipping_method, strpos( $shipping_method, ':' ) + 1 );
				}
				$CoolCAShippingMethod = new WC_CoolCA( $instance_id );

				$is_express_service = ( 'branch-express' === $CoolCAShippingMethod->service_type || 'express' === $CoolCAShippingMethod->service_type );
				// Check if shipping method is branch.
				$is_branch_service = ( 'branch' === $CoolCAShippingMethod->service_type || 'branch-express' === $CoolCAShippingMethod->service_type );
				if ( $is_branch_service ) {
					$branch_state = ( ! $order->get_shipping_state() ) ? $order->get_billing_state() : $order->get_shipping_state();
					$branch       = trim( wc_get_order_item_meta( $shipping_method->get_id(), 'Sucursal' ) );
				}
				$PaqArsEncoded = wc_get_order_item_meta( $shipping_method->get_id(), '_paqars' );
				if ( $PaqArsEncoded ) {
					$PaqArs = Helper::decode_array( $PaqArsEncoded );
				}
			}

			if ( count( $PaqArs ) + $total_orders > 90 ) {
				return $data_arr;
			}

			$state      = ( ! $order->get_shipping_state() ) ? $order->get_billing_state() : $order->get_shipping_state();
			$city       = ( ! $order->get_shipping_city() ) ? $order->get_billing_city() : $order->get_shipping_city();
			$address1   = Helper::split_street_and_number( ( ! $order->get_shipping_address_1() ) ? $order->get_billing_address_1() : $order->get_shipping_address_1() );
			$street     = $address1['street'];
			$number     = $address1['number'];
			$address2   = Helper::split_fllor_and_apartment( ( ! $order->get_shipping_address_2() ) ? $order->get_billing_address_2() : $order->get_shipping_address_2() );
			$floor      = array_shift( $address2 );
			$apartment  = array_shift( $address2 );
			$postcode   = ( ! $order->get_shipping_postcode() ) ? $order->get_billing_postcode() : $order->get_shipping_postcode();
			$phone_area = ( $order->get_billing_phone() ) ? Helper::get_phone_prefix_sanitized( $order->get_billing_phone() ) : '';
			$phone      = ( $order->get_billing_phone() ) ? Helper::get_phone_sanitized( $order->get_billing_phone() ) : '';

			$cPaqAr = 0;
			foreach ( $PaqArs as $PaqAr ) {
				$cPaqAr     = 1 + $cPaqAr;
				$data_arr[] = array(
					'id'                => $order->get_id(),
					'coolca_type'       => ( $is_express_service ) ? 'EP' : 'CP',
					'length'            => $PaqAr['length'],
					'width'             => $PaqAr['width'],
					'height'            => $PaqAr['height'],
					'weight'            => $PaqAr['weight'],
					'value'             => $order->get_subtotal() / count( $PaqArs ),
					'state'             => $state,
					'branch'            => ( $is_branch_service ) ? $branch : '',
					'city'              => ( $is_branch_service ) ? '' : $city,
					'street'            => ( $is_branch_service ) ? '' : $street,
					'street_nbr'        => ( $is_branch_service ) ? '' : $number,
					'floor'             => ( $is_branch_service ) ? '' : $floor,
					'apartment'         => ( $is_branch_service ) ? '' : $apartment,
					'postcode'          => ( $is_branch_service ) ? '' : $postcode,
					'fullname'          => $order->get_formatted_shipping_full_name(),
					'email'             => $order->get_billing_email(),
					'codarea_tel'       => '',
					'tel'               => '',
					'codarea_cellphone' => $phone_area,
					'cellphone'         => $phone,
					'external_id'       => Helper::get_option( 'order-prefix' ) . $order->get_id() . $cPaqAr,
				);
				++$total_orders;
			}
		}
		return $data_arr;
	}

	/**
	 * Process Data to Template
	 *
	 * @param array $args Filters.
	 * @return array
	 */
	public static function get_order_data( $args = array() ) {
		$search_args = array(
			'limit' => 90,
		);
		if ( 'ALL' !== strtoupper( $args['curr_status'] ) ) {
			$search_args['status'] = array( $args['curr_status'] );
		}
		if ( isset( $args['from_dt_dflt'] ) && isset( $args['to_dt_dflt'] ) ) {
			$search_args['date_created'] = $args['from_dt_dflt'] . '...' . $args['to_dt_dflt'];
		}

		$search_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => '_coolca_already_exported',
				'compare' => '!=',
				'value'   => 1,
			),
			array(
				'key'     => '_coolca_already_exported',
				'compare' => 'NOT EXISTS',
			),
		);

		if ( \CoolCA::$WCFM_ENABLED ) {
			global $wpdb;
			$current_vendor_id  = get_user_meta( get_current_user_id(), '_wcfmmp_profile_id', true );
			$vendor_orders_list = $wpdb->get_results( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE `vendor_id` = %d", absint( $current_vendor_id ) ) );
			if ( ! empty( $vendor_orders_list ) ) {
				$vendor_orders = array();
				foreach ( $vendor_orders_list as $vendor_order_list ) {
					$vendor_orders[] = $vendor_order_list->order_id;
				}
				$search_args['post__in'] = $vendor_orders;
			} else {
				$search_args['post__in'] = array( 0 );
			}
		}

		$free_shipping_method = ( $args['inc_free_shipping'] ) ? 'free' : 'none';
		$orders               = array_values(
			array_filter(
				wc_get_orders( $search_args ),
				function ( $order ) use ( $free_shipping_method ) {
					return ( $order->has_shipping_method( 'coolca' ) || $order->has_shipping_method( $free_shipping_method ) );
				}
			)
		);

		return $orders;
	}

	/**
	 * Displays process page
	 *
	 * @return void
	 */
	public static function page_content() {
		if ( ! \CoolCA::$WCFM_ENABLED && ! is_admin() && ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_woocommerce' ) ) { //phpcs:ignore WordPress.WP.Capabilities.Unknown
			die( esc_html__( 'what are you doing here?', 'cool-ca' ) );
		}

		if ( \CoolCA::$WCFM_ENABLED && Helper::get_option( 'wcfm-enabled' ) === 'yes' && ! wcfm_is_vendor() ) { //phpcs:ignore WordPress.WP.Capabilities.Unknown
			die( esc_html__( 'what are you doing here?', 'cool-ca' ) );
		}

		$search      = false;
		$form_values = array(
			'from_dt_dflt'         => gmdate( 'Y-m-d' ),
			'to_dt_dflt'           => gmdate( 'Y-m-d' ),
			'curr_status'          => 'all',
			'cool-ca-export-nonce' => wp_create_nonce( 'cool-ca-export' ),
			'inc_free_shipping'    => '',
		);
		// phpcs:ignore Generic.Commenting.Todo.TaskFound
		// TODO: Add Nonce Validation.
		$nonce_value    = wc_get_var( $_REQUEST['cool-ca-export-nonce'], '' ); // phpcs:ignore
		if (
			! empty( $nonce_value ) &&
			wp_verify_nonce(
				$nonce_value,
				'cool-ca-export'
			)
		) {
			$search = ( isset( $_POST['coolca_search'] ) ) ? true : false;

			$form_values['from_dt_dflt'] = filter_input(
				INPUT_POST,
				'coolca_from_dt',
				FILTER_SANITIZE_FULL_SPECIAL_CHARS
			);

			$form_values['to_dt_dflt'] = filter_input(
				INPUT_POST,
				'coolca_to_dt',
				FILTER_SANITIZE_FULL_SPECIAL_CHARS
			);

			$form_values['curr_status'] = filter_input(
				INPUT_POST,
				'coolca_status',
				FILTER_SANITIZE_FULL_SPECIAL_CHARS
			);

			$form_values['inc_free_shipping'] = ( isset( $_POST['coolca_include_free-shipping'] ) ) ? true : false;

		}

		Helper::get_template_part( 'export', 'header' );
		Helper::get_template_part( 'export', 'form', $form_values );
		if ( $search ) {
			$form_values['orders'] = self::prepare_orders_to_export( self::get_order_data( $form_values ) );
			Helper::get_template_part( 'export', 'results', $form_values );
		}
		wp_enqueue_style( 'coolca-export' );
		wp_enqueue_script( 'jquery' );
	}
}

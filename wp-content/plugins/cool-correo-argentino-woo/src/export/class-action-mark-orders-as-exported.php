<?php
/**
 * Ajax Mark Orders as Exported
 *
 * @package  MANCA\CoolCA\SDK
 */

namespace MANCA\CoolCA\Export;

defined( 'ABSPATH' ) || exit;

use MANCA\CoolCA\Helper\Helper;

/**
 * Cron Processor's Main Class
 */
class ActionMarkOrdersAsExported {

	/**
	 * Run Action
	 *
	 * @param array $orders List of exported orders.
	 *
	 * @return bool
	 */
	public static function execute( array $orders = array() ) {
		Helper::log( 'Orders > ' );
		Helper::log( $orders );
		if ( ! empty( $orders ) ) {
			foreach ( $orders as $order_id ) {
				if ( ! empty( $order_id ) ) {
					$order = wc_get_order( $order_id );
					if ( $order ) {
						$order->update_meta_data( '_coolca_already_exported', 1 );
						$order->save();
					}
				}
			}
		}
		return true;
	}

	/**
	 * Handle Ajax Request
	 *
	 * @return void
	 */
	public static function ajax_callback_wp() {
		Helper::log( 'ajax ' . __CLASS__ . '.' . __FUNCTION__ );
		if ( ! isset( $_POST['coolca_nonce'] ) || ! isset( $_POST['orders'] ) ) {
			wp_send_json_error();
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coolca_nonce'] ) ), 'coolca-orders-update' ) ) {
			wp_send_json_error();
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$ret = static::execute( array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['orders'] ) ) );
		if ( $ret ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_send_json_success();
	}
}

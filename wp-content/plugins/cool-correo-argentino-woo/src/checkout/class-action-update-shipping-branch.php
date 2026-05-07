<?php
/**
 * Update Shipping after Branch Selection
 *
 * @package  MANCA\CoolCA\CheckOut
 */

namespace MANCA\CoolCA\CheckOut;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\ShippingMethod\WC_CoolCA;

defined( 'ABSPATH' ) || exit();

/**
 * Checkout Class to mantain new custom fields for checkout.
 */
class ActionUpdateShippingBranch {

	/**
	 * Handle Ajax request
	 *
	 * @return void
	 */
	public static function ajax_callback_wp() {
		if ( ! isset( $_POST['coolca_nonce'] ) || ! isset( $_POST['coolca_branch'] ) ) {
			wp_send_json_error();
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['coolca_nonce'] ) ), 'coolca-branch-selection' ) ) {
			wp_send_json_error();
		}

		// Get current state from shipping packages.
		$packages      = WC()->cart->get_shipping_packages();
		$current_state = isset( $packages[0]['destination']['state'] ) ? $packages[0]['destination']['state'] : '';

		// Save selected point to session.
		WC()->session->set( 'coolca_branch_selected', isset( $_POST['coolca_branch'] ) ? sanitize_text_field( wp_unslash( $_POST['coolca_branch'] ) ) : null );
		WC()->session->set( 'coolca_branch_name_selected', isset( $_POST['coolca_branch_name'] ) ? sanitize_text_field( wp_unslash( $_POST['coolca_branch_name'] ) ) : null );
		WC()->session->set( 'coolca_branch_address_selected', isset( $_POST['coolca_branch_address'] ) ? sanitize_text_field( wp_unslash( $_POST['coolca_branch_address'] ) ) : null );
		// Save the state when the branch was selected to detect state changes later.
		WC()->session->set( 'coolca_branch_state', $current_state );

		// Force to Recalculate Shippings.
		foreach ( $packages as $package_key => $package ) {
			WC()->session->set( 'shipping_for_package_' . $package_key, false );
		}

		wp_send_json_success();
	}

	/**
	 * Clear Branch Selection on State Change
	 *
	 * Only clears the branch selection if the shipping state has changed
	 * since the branch was selected. This prevents losing the selection
	 * when unrelated checkout updates occur (e.g., coupon changes).
	 *
	 * @param string $posted_data URL-encoded form data from checkout.
	 * @return void
	 */
	public static function clear_branch_selection( $posted_data ) {
		// If no branch was selected, nothing to clear.
		$saved_state = WC()->session->get( 'coolca_branch_state' );
		if ( empty( $saved_state ) ) {
			return;
		}

		// Parse the posted data to get current state.
		$posted = array();
		parse_str( $posted_data, $posted );

		// Determine current state: use shipping state if shipping to different address, otherwise billing state.
		$ship_to_different = isset( $posted['ship_to_different_address'] ) && '1' === $posted['ship_to_different_address'];
		if ( $ship_to_different && ! empty( $posted['shipping_state'] ) ) {
			$current_state = sanitize_text_field( $posted['shipping_state'] );
		} else {
			$current_state = isset( $posted['billing_state'] ) ? sanitize_text_field( $posted['billing_state'] ) : '';
		}

		// Only clear the selection if the state has changed.
		if ( $current_state !== $saved_state ) {
			WC()->session->set( 'coolca_branch_selected', null );
			WC()->session->set( 'coolca_branch_name_selected', null );
			WC()->session->set( 'coolca_branch_address_selected', null );
			WC()->session->set( 'coolca_branch_state', null );
		}
	}
}

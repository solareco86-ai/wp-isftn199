<?php
/**
 * Branch Field Class
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
class BranchSelectorField {
	/**
	 * Show Branch Select Field
	 *
	 * @param string $method Shipping Method Selected.
	 * @param array  $index Shipping Method Index.
	 */
	public static function shipping_point_field( $method, $index = 0 ) { //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! is_checkout() ) {
			return; // Only on checkout page.
		}

		$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		if ( empty( $shipping_methods ) ) {
			return; }
		$shipping_method = $shipping_methods[0];

		// If shipping method is the choosen one.
		if ( $method->id === $shipping_method ) {
			// If shipping method choosen is from Cool Correo Argentino.
			if ( false !== strpos( $shipping_method, 'coolca' ) ) {
				$instance_id = substr(
					$shipping_method,
					strpos( $shipping_method, ':' ) + 1
				);

				$CoolCAShippingMethod = new WC_CoolCA( $instance_id );
				// Check if shipping method is branch.
				if ( 'branch' === $CoolCAShippingMethod->service_type || 'branch-express' === $CoolCAShippingMethod->service_type ) {

					global $woocommerce;
					$package       = $woocommerce->cart->get_shipping_packages();
					$state         = $package[0]['destination']['state'];
					$defaultBranch = '';

					$post_data_str = filter_input(
						INPUT_POST,
						'post_data',
						FILTER_SANITIZE_FULL_SPECIAL_CHARS
					);
					if ( ! empty( $post_data_str ) ) {
						parse_str( $post_data_str, $post_data );
						// phpcs:ignore Generic.Commenting.Todo.TaskFound
						// TODO: Add new nonce Validation.
						if ( isset( $post_data['woocommerce-process-checkout-nonce'] ) ) {
							$nonce_value =
								$post_data['woocommerce-process-checkout-nonce'];
							if (
								empty( $nonce_value ) ||
								! wp_verify_nonce(
									$nonce_value,
									'woocommerce-process_checkout'
								)
							) {
								return;
							}
						}
						if ( isset( $post_data['coolca_branch'] ) ) {
							$defaultBranch = $post_data['coolca_branch'];
						}
					}

					/**
					 * Version 1.5.0 - New Branch Selector
					 * Version 1.5.6 - Branches with postcode for frontend sorting
					 */
					$branches = Helper::get_branches_dropdown_with_postcode( $state );

					/**
					 * Version 1.3.8 - Add Js Primitive Option
					 */
					$js_primitive = Helper::get_option( 'js-primitive' );

					$branch_selected         = WC()->session->get( 'coolca_branch_selected' );
					$branch_name_selected    = WC()->session->get( 'coolca_branch_name_selected' );
					$branch_address_selected = WC()->session->get( 'coolca_branch_address_selected' );

					$data = array(
						'state'                   => $state,
						'branches'                => $branches,
						'method-id'               => $instance_id,
						'branch'                  => $defaultBranch,
						'branch_selected'         => $branch_selected,
						'branch_name_selected'    => $branch_name_selected,
						'branch_address_selected' => $branch_address_selected,
						'js_primitive'            => $js_primitive,
					);
					Helper::get_template_part( 'checkout', 'branch-selector', $data );
				}
			}
		}
	}

	/**
	 * Show Point Select Field
	 *
	 * @param array $fields Checkout Fields.
	 */
	public static function override_checkout_fields( $fields ) {
		array_push(
			$fields['billing']['billing_state']['class'],
			'update_totals_on_change'
		);
		// check if it's setting up.
		array_push(
			$fields['shipping']['shipping_state']['class'],
			'update_totals_on_change'
		);
		return $fields;
	}

	/**
	 * Save checkout fields
	 *
	 * @deprecated
	 *
	 * @param string $order_id Order ID.
	 * @param bool   $post_data Posted data.
	 */
	public static function checkout_update_order_meta( $order_id, $post_data = null ) { //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// This function is deprecated.
		trigger_error( 'Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		$order            = wc_get_order( $order_id );
		$shipping_methods = $order->get_shipping_methods();
		$shipping_method  = array_shift( $shipping_methods );

		$nonce_value    = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // phpcs:ignore
		if (
			empty( $nonce_value ) ||
			! wp_verify_nonce(
				$nonce_value,
				'woocommerce-process_checkout'
			)
		) {
			return;
		}

		$coolca_branch = filter_input(
			INPUT_POST,
			'coolca_branch',
			FILTER_SANITIZE_FULL_SPECIAL_CHARS
		);

		$coolca_branch_name = filter_input(
			INPUT_POST,
			'coolca_branch_name',
			FILTER_SANITIZE_FULL_SPECIAL_CHARS
		);

		$coolca_branch_address = filter_input(
			INPUT_POST,
			'coolca_branch_address',
			FILTER_SANITIZE_FULL_SPECIAL_CHARS
		);

		if ( $coolca_branch ) {
			wc_add_order_item_meta(
				$shipping_method->get_id(),
				'Sucursal',
				sanitize_text_field( $coolca_branch )
			);

			/**
			 *  Version 1.3.0 - Add Shipping Meta Data "Branch Description"
			 */
			wc_add_order_item_meta(
				$shipping_method->get_id(),
				'Nombre Sucursal',
				sanitize_text_field( $coolca_branch_name )
			);
			wc_add_order_item_meta(
				$shipping_method->get_id(),
				'Dirección Sucursal',
				sanitize_text_field( $coolca_branch_address )
			);
		}
	}

	/**
	 * Display New fields on Order
	 *
	 * @param WC_ORDER $order Woo Order Object.
	 */
	public static function display_fields( $order ) {
		$shipping_methods = $order->get_shipping_methods();
		$shipping_method  = array_shift( $shipping_methods );
		if ( ! empty( $shipping_method ) ) {
			if ( 'coolca' === $shipping_method->get_method_id() ) {
				$instance_id          = substr(
					$shipping_method,
					strpos( $shipping_method, ':' ) + 1
				);
				$CoolCAShippingMethod = new WC_CoolCA( $instance_id );

				// Check if shipping method is branch.
				if ( 'branch' === $CoolCAShippingMethod->service_type || 'branch-express' === $CoolCAShippingMethod->service_type ) {
					?>
					<h2 class="woocommerce-order-coolca"> 
						<?php
						__(
							'Envío por Correo Argentino',
							'cool-ca'
						);
						?>
					</h2>
					<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
					<li class="woocommerce-order-overview__order order">
						<?php __( 'Método Envío', 'cool-ca' ); ?>
						<strong><?php echo esc_html( $shipping_method->get_method_title() ); ?></strong>
					</li>
					<?php
					if (
					! empty( wc_get_order_item_meta( $shipping_method->get_id(), 'Sucursal' ) )
					) {
						?>
					<li class="woocommerce-order-overview__order order">
									<?php __( 'Sucursal', 'cool-ca' ); ?>
						<strong>
						<?php
						echo esc_html(
							wc_get_order_item_meta(
								$shipping_method->get_id(),
								'Nombre Sucursal'
							)
						);
						?>
	</strong> 			
						<?php
						echo esc_html(
							wc_get_order_item_meta(
								$shipping_method->get_id(),
								'Dirección Sucursal'
							)
						);
						?>
					</li>
					<?php } ?>
				</ul>
					<?php
				}
			}
		}
	}

	/**
	 * Checkout Validation for Shipping Method
	 *
	 * @param array    $fields An array of posted data.
	 * @param WP_Error $errors Validation errors.
	 */
	public static function checkout_validation( $fields, $errors ) {
		$nonce_value    = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // phpcs:ignore
		if (
			empty( $nonce_value ) ||
			! wp_verify_nonce(
				$nonce_value,
				'woocommerce-process_checkout'
			)
		) {
			return;
		}

		$coolca_branch = filter_input(
			INPUT_POST,
			'coolca_branch',
			FILTER_SANITIZE_FULL_SPECIAL_CHARS
		);

		// If Branch should be selected, but it's not there.
		if ( isset( $_POST['coolca_branch'] ) && ( 'none' === $coolca_branch || empty( $coolca_branch ) ) ) {
			$errors->add( 'coolca_branch_required', __( 'Debe seleccionar una sucursal de retiro.', 'cool-ca' ) );
		}
	}
}

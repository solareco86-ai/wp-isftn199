<?php
/**
 * Cool CA WCFM Class
 *
 * @package  MANCA\CoolCA\WCFM
 */

namespace MANCA\CoolCA\WCFM;

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\WCFM\CoolCAExport;

/**
 * WCFM Class
 */
class WCFM {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( \CoolCA::$WCFM_ENABLED && Helper::get_option( 'wcfm-enabled' ) === 'no' ) {
			// do not show coolca shipping method.
			add_filter(
				'woocommerce_package_rates',
				function ( $rates ) {
					foreach ( $rates as $rate_id => $rate ) {
						if ( 'coolca' === $rate->method_id ) {
							unset( $rates[ $rate_id ] );
						}
					}
					return $rates;
				}
			);
		}
		if ( \CoolCA::$WCFM_ENABLED && Helper::get_option( 'wcfm-enabled' ) === 'yes' ) {

			// Add Correo Argentino Settings Tab.
			add_action( 'end_wcfm_vendor_settings', array( $this, 'wcfm_correo_argentino_settings' ), 15 );

			// Update Correo Argentino Settings.
			add_action( 'wcfm_vendor_settings_update', array( $this, 'wcfm_correo_argentino_settings_update' ), 20, 2 );

			// Set origin from vendor.
			add_action(
				'wc_coolca_before_calculate_shipping',
				function ( $shippingMethod, $product_list ) {
					$this->set_origin_WCFMmp( $shippingMethod, $product_list );
				},
				10,
				2
			);

			// do not show coolca shipping method.
			add_filter(
				'woocommerce_package_rates',
				function ( $rates, $package ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
					foreach ( $rates as $rate_id => $rate ) {
						$vendor_id = '';
						try {
							$vendor_id = $rate->meta_data['_vendor_id'];
						} catch ( Exception $e ) {
							Helper::log( 'Error getting vendor id: ' . $e->getMessage() );
						}
						if ( ! $this->is_vendor_has_coolca_shipping_method( $vendor_id ) ) {
							if ( 'coolca' === $rate->method_id ) {
								unset( $rates[ $rate_id ] );
							}
						}
					}
					return $rates;
				},
				10,
				2
			);

			// Avoid hide admin shipping for vendor shipping.
			add_filter( 'wcfm_is_allow_hide_admin_shipping_for_vendor_shipping', '__return_false' );

			// Add Export Menu.
			new CoolCAExport();
		}
	}

	/**
	 * Check if Vendor has CoolCA Shipping Method enabled.
	 *
	 * @param int $vendor_id Vendor ID.
	 * @return bool
	 */
	private function is_vendor_has_coolca_shipping_method( $vendor_id ) {
		$enable_coolca = get_user_meta( $vendor_id, '_wcfm_coolca_enable', true );
		return ( 'yes' === $enable_coolca );
	}


	/**
	 * Integration with WCFM - WooCommerce Multivendor Marketplace
	 *
	 * @param WC_CoolCA $shippingMethod Woo Shipping Method.
	 * @param Array     $product_list Woo Items.
	 * @return void
	 */
	private function set_origin_WCFMmp( $shippingMethod, $product_list = array() ) {
		$shippingMethod->pickup_state    = '';
		$shippingMethod->pickup_city     = '';
		$shippingMethod->pickup_postcode = '';
		$shippingMethod->vendor_id       = '';

		// if WCFM - WooCommerce Multivendor Marketplace is activated.
		if ( class_exists( 'WCFMmp' ) && function_exists( 'wcfm_get_vendor_id_by_post' ) ) {
			$item       = $product_list[0];
			$product_id = $item['id'];
			$vendor_id  = wcfm_get_vendor_id_by_post( $product_id );

			$vendor_state    = get_user_meta( $vendor_id, '_wcfm_coolca_state', true );
			$vendor_city     = get_user_meta( $vendor_id, '_wcfm_coolca_city', true );
			$vendor_postcode = get_user_meta( $vendor_id, '_wcfm_coolca_postcode', true );

			Helper::log( 'WCFMmp vendor >' . $vendor_id );
			Helper::log( 'WCFMmp vendor state >' . $vendor_state );
			Helper::log( 'WCFMmp vendor city >' . $vendor_city );
			Helper::log( 'WCFMmp vendor postcode >' . $vendor_postcode );

			$shippingMethod->pickup_state    = ( ! empty( $vendor_state ) ) ? $vendor_state : '';
			$shippingMethod->pickup_city     = ( ! empty( $vendor_city ) ) ? $vendor_city : '';
			$shippingMethod->pickup_postcode = ( ! empty( $vendor_postcode ) ) ? $vendor_postcode : '';
			$shippingMethod->vendor_id       = ( ! empty( $vendor_id ) ) ? $vendor_id : '';
		}
	}

	/**
	 * Correo Argentino Settings
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function wcfm_correo_argentino_settings( $user_id ) {
		global $WCFM;

		$enable_coolca   = get_user_meta( $user_id, '_wcfm_coolca_enable', true );
		$enable_options  = array(
			'no'  => 'Desactivado',
			'yes' => 'Activado',
		);
		$coolca_state    = get_user_meta( $user_id, '_wcfm_coolca_state', true );
		$coolca_city     = get_user_meta( $user_id, '_wcfm_coolca_city', true );
		$coolca_postcode = get_user_meta( $user_id, '_wcfm_coolca_postcode', true );

		global $woocommerce;
		$countries_obj = new \WC_Countries();
		$countries     = $countries_obj->__get( 'countries' );
		$states        = $countries_obj->get_states( 'AR' );

		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_correo_argentino_head">
			<label class="wcfmfa fa-truck"></label>
			<?php esc_html_e( 'Correo Argentino', 'coolca' ); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_correo_argentino_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field(
					/**
					 * Filter: wcfm_vendors_settings_fields_coolca
					 *
					 * @since 1.4.0
					 * @param array $fields Fields.
					 * @return array
					 */
					apply_filters(
						'wcfm_vendors_settings_fields_coolca',
						array(
							'wcfm_coolca_enable'   => array(
								'label'       => __( 'Estado Correo Argentino', 'coolca' ),
								'type'        => 'select',
								'class'       => 'wcfm-select wcfm_ele',
								'label_class' => 'wcfm_title wcfm_ele',
								'options'     => $enable_options,
								'value'       => ! empty( $enable_coolca ) ? $enable_coolca : 'no',
							),
							'wcfm_coolca_state'    => array(
								'label'       => __( 'Provincia', 'coolca' ),
								'type'        => 'select',
								'class'       => 'wcfm-select wcfm_ele',
								'label_class' => 'wcfm_title wcfm_ele',
								'options'     => $states,
								'value'       => $coolca_state,
							),
							'wcfm_coolca_city'     => array(
								'label'       => __( 'Ciudad', 'coolca' ),
								'type'        => 'text',
								'class'       => 'wcfm-text wcfm_ele',
								'label_class' => 'wcfm_title wcfm_ele',
								'value'       => $coolca_city,
							),
							'wcfm_coolca_postcode' => array(
								'label'       => __( 'Código Postal', 'coolca' ),
								'type'        => 'text',
								'class'       => 'wcfm-text wcfm_ele',
								'label_class' => 'wcfm_title wcfm_ele',
								'value'       => $coolca_postcode,
							),
						),
						$user_id
					)
				);
				?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		<?php
	}

	/**
	 * Correo Argentino Settings Update
	 *
	 * @param int   $user_id User ID.
	 * @param array $wcfm_settings_form WCFM Settings Form.
	 * @return void
	 */
	public function wcfm_correo_argentino_settings_update( $user_id, $wcfm_settings_form ) {
		global $WCFM;

		if ( isset( $wcfm_settings_form['wcfm_coolca_enable'] ) ) {
			update_user_meta( $user_id, '_wcfm_coolca_enable', $wcfm_settings_form['wcfm_coolca_enable'] );
		}

		if ( isset( $wcfm_settings_form['wcfm_coolca_state'] ) ) {
			update_user_meta( $user_id, '_wcfm_coolca_state', $wcfm_settings_form['wcfm_coolca_state'] );
		}

		if ( isset( $wcfm_settings_form['wcfm_coolca_city'] ) ) {
			update_user_meta( $user_id, '_wcfm_coolca_city', $wcfm_settings_form['wcfm_coolca_city'] );
		}

		if ( isset( $wcfm_settings_form['wcfm_coolca_postcode'] ) ) {
			update_user_meta( $user_id, '_wcfm_coolca_postcode', $wcfm_settings_form['wcfm_coolca_postcode'] );
		}
	}


	/**
	 * Add Export Menu
	 *
	 * @param array $menus Menus.
	 * @return array
	 */
	public function wcfm_correo_argentino_export_menu( $menus ) {
		global $WCFM;

		$menus = array_slice( $menus, 0, count( $menus ) - 1, true ) +
				array(
					'wcfm-coolca-export' => array(
						'label'    => __( 'Correo Argentino', 'coolca' ),
						'url'      => get_wcfm_url() . 'coolca-export',
						'icon'     => 'truck',
						'priority' => 5.1,
					),
				) +
				array_slice( $menus, count( $menus ) - 1, null, true );

		return $menus;
	}

	/**
	 * Load Export Views
	 *
	 * @param string $end_point End Point.
	 * @return void
	 */
	public function wcfm_correo_argentino_export_load_views( $end_point ) {
		global $WCFM;
		Helper::log( 'end_point: ' . $end_point );
		switch ( $end_point ) {
			case 'wcfm-coolca-export':
				?>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_correo_argentino_expander" class="wcfm-content">
						<h2>Exportación Correo Argentino</h2>
					</div>
				</div>
				<?php
				break;
		}
	}
}

<?php
/**
 * Settings Main Class
 *
 * @package  MANCA\CoolCA\Settings
 */

namespace MANCA\CoolCA\Settings;

use MANCA\CoolCA\Settings\Section;
use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\Process\ActionBranchesUpdate;
use MANCA\CoolCA\SDK\CoolCASdk;
defined( 'ABSPATH' ) || exit;

/**
 * A main class that holds all our settings logic
 */
class Main {
	/**
	 * Add CoolCa Setting Tab
	 *
	 * @param Array $settings_tab Shipping Methods.
	 * @return Array Shipping Methods
	 */
	public static function add_tab_settings( $settings_tab ) {
		$settings_tab['coolca_shipping_options'] = __( 'Cool Correo Argentino' );
		return $settings_tab;
	}

	/**
	 * Get CoolCa Setting Tab
	 *
	 * @param Array  $settings Shipping Methods.
	 * @param string $current_section Section which is beaing processing.
	 * @return Array Shipping Method Settings
	 */
	public static function get_tab_settings( $settings, $current_section ) {
		if ( 'coolca_shipping_options' === $current_section ) {
			add_action( 'admin_footer', array( __CLASS__, 'enqueue_admin_js' ), 10 ); // Priority needs to be higher than wc_print_js (25).
			return Section::get();
		} else {
			return $settings;
		}
	}

	/**
	 * Get CoolCa Settings
	 *
	 * @return Array Shipping Methods
	 */
	public static function get_settings() {
		/**
		 * Get Coolca General Settings.
		 *
		 * @since 2023.07.13
		 *
		 * @return Array
		 */
		return apply_filters( 'wc_settings_coolca_shipping_options', Section::get() );
	}

	/**
	 * Update CoolCa Settings
	 *
	 * @return void
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Save Coolca Settings
	 *
	 * @return void
	 */
	public static function save() {

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'woocommerce-settings' ) ) {
			echo '<div class="updated error"><p>' . esc_html__( 'Edit failed. Please try again.', 'woocommerce' ) . '</p></div>';
		}
		global $current_section;
		if ( 'coolca_shipping_options' === $current_section ) {
			$price_arr = filter_input( INPUT_POST, 'wc-coolca-price', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			update_option( 'wc-coolca-price', $price_arr );

			// version 1.5.0 - Sync Branches.
			ActionBranchesUpdate::execute();

			if ( wp_next_scheduled( 'coolca-branches-cron-updater' ) ) {
				wp_clear_scheduled_hook( 'coolca-branches-cron-updater' );
			}
			wp_schedule_event( time(), 'coolca_branches', 'coolca-branches-cron-updater' );

			// Force flush rewrite rules WCFM.
			update_option( 'coolca_wcfm_updated_end_point_coolca_export', 0 );
		}
	}

	/**
	 * Enqueue_admin_js
	 */
	public static function enqueue_admin_js() {
		wc_enqueue_js(
			"jQuery( function( $ ) {
                function wcCoolCAModeFieldChange( el ) {
					var form = $( el ).closest( 'form' );
					if ( 'K' === $( el ).val() ) {
						$( '.coolca-mode-m', form).each( function( ){
							$( this ).hide();	
							$( this ).closest( 'tr' ).hide();
						} );
						$( '.coolca-mode-k', form).each( function( ){
							$( this ).show();
							$( this ).closest( 'tr' ).show();		
						} );
					} else {
						$( '.coolca-mode-m', form).each( function( ){
							$( this ).show();	
							$( this ).closest( 'tr' ).show();	
						} );
						$( '.coolca-mode-k', form).each( function( ){
							$( this ).hide();	
							$( this ).closest( 'tr' ).hide();
						} );
					}
				}				             

				$( document.body ).on( 'change', '#wc-coolca-mode', function() {
					wcCoolCAModeFieldChange( this );
				});

				// Change while load.
				$( '#wc-coolca-mode' ).trigger( 'change' );	
				
				// Update branches last sync.
				$( '.coolca-disabled' ).attr('disabled', true);
			});"
		);
	}
}

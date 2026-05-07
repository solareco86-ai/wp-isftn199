<?php
/**
 * Plugin Name: Cool Correo Argentino for WooCommerce
 * Description: Método de Envío de WooCommerce para Correo Argentino.
 * Version: 1.5.6
 * Requires PHP: 7.0
 * Author: MANCA
 * Author URI: https://manca.com.ar
 * Text Domain: cool-ca
 * WC requires at least: 5.4
 * WC tested up to: 9.0
 *
 * @package MANCA\CoolCA;
 */

use MANCA\CoolCA\Helper\Helper;
use MANCA\CoolCA\WCFM\WCFM;

defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', array( 'CoolCA', 'init' ) );
add_action( 'deactivated_plugin', array( 'CoolCA', 'deactivation' ) );

/**
 * Plugin's base Class
 */
class CoolCA {

	const VERSION     = '1.5.6';
	const PLUGIN_NAME = 'cool-correo-argentino-for-woocommerce';
	const MAIN_FILE   = __FILE__;
	const MAIN_DIR    = __DIR__;

	/**
	 * Flag to indicate if WCFM plugin is enabled
	 *
	 * @var bool
	 */
	public static $WCFM_ENABLED = false;

	/**
	 * Flag to indicate if YITH Auctions plugin is enabled
	 *
	 * @var bool
	 */
	public static $YITH_AUCTIONS_ENABLED = false;

	/**
	 * Checks system requirements
	 *
	 * @return bool
	 */
	public static function check_system() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$system = self::check_components();

		if ( $system['flag'] ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			echo '<div class="notice notice-error is-dismissible">';
			/* translators: %s: System Flag */
			echo '<p>' . sprintf( esc_html__( '<strong>%1$s/strong> Requiere al menos %2$s versión %3$s o superior.', 'cool-ca' ), esc_html( self::PLUGIN_NAME ), esc_html( $system['flag'] ), esc_html( $system['version'] ) ) . '</p>';
			echo '</div>';
			return false;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			echo '<div class="notice notice-error is-dismissible">';
			/* translators: %s: Plugin Name */
			echo '<p>' . sprintf( esc_html__( 'WooCommerce debe estar activo antes de usar <strong>%s</strong>', 'cool-ca' ), esc_html( self::PLUGIN_NAME ) ) . '</p>';
			echo '</div>';
			return false;
		}
		return true;
	}

	/**
	 * Check the components required for the plugin to work (PHP, WordPress and WooCommerce)
	 *
	 * @return array
	 */
	private static function check_components() {
		global $wp_version;
		$flag    = false;
		$version = false;

		if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
			$flag    = 'PHP';
			$version = '7.0';
		} elseif ( version_compare( $wp_version, '5.4', '<' ) ) {
			$flag    = 'WordPress';
			$version = '5.4';
		} elseif ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '4.3', '<' ) ) {
			$flag    = 'WooCommerce';
			$version = '4.3';
		}

		return array(
			'flag'    => $flag,
			'version' => $version,
		);
	}

	/**
	 * Inits our plugin
	 *
	 * @return void|bool
	 */
	public static function init() {
		if ( ! self::check_system() ) {
			return false;
		}

		spl_autoload_register(
			function ( $required_class ) {
				// Plugin base Namespace.
				if ( strpos( $required_class, 'CoolCA' ) === false ) {
					return;
				}
				$required_class = str_replace( '\\', '/', $required_class );
				$parts          = explode( '/', $required_class );
				$classname      = array_pop( $parts );

				$filename = $classname;
				$filename = str_replace( 'WooCommerce', 'Woocommerce', $filename );
				$filename = str_replace( 'WC_', 'Wc', $filename );
				$filename = str_replace( 'WC', 'Wc', $filename );
				$filename = preg_replace( '/([A-Z])/', '-$1', $filename );
				$filename = 'class' . $filename;
				$filename = strtolower( $filename );
				$folder   = strtolower( array_pop( $parts ) );
				if ( 'class-coolca' === $filename ) {
					return;
				}
				require_once plugin_dir_path( __FILE__ ) . 'src/' . $folder . '/' . $filename . '.php';
			}
		);
		include_once __DIR__ . '/hooks.php';
		Helper::init();
		self::load_textdomain();

		// Check if WCFM Marketplace is enabled.
		self::$WCFM_ENABLED = ( class_exists( 'WCFMmp' ) );
		if ( self::$WCFM_ENABLED ) {
			new WCFM();
		}

		// Check if YITH Auctions is enabled.
		self::$YITH_AUCTIONS_ENABLED = ( class_exists( 'YITH_Auctions' ) );
	}

	/**
	 * Create a link to the settings page, in the plugins page
	 *
	 * @param array $links Array of Links.
	 * @return array
	 */
	public static function create_settings_link( array $links ) {
		$link = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=wc-settings&tab=shipping&section=coolca_shipping_options' ) ) . '">' . __( 'Ajustes', 'cool-ca' ) . '</a>';
		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Adds our shipping method to WooCommerce
	 *
	 * @param array $shipping_methods Array of Shipping Methods.
	 * @return array
	 */
	public static function add_shipping_method( $shipping_methods ) {
		$shipping_methods['coolca'] = '\MANCA\CoolCA\ShippingMethod\WC_CoolCA';
		return $shipping_methods;
	}

	/**
	 * Loads the plugin text domain
	 *
	 * @return void
	 */
	public static function load_textdomain() {
		load_plugin_textdomain( 'cool-ca', false, basename( __DIR__ ) . '/i18n/languages' );
	}

	/**
	 * Agregar CSS + JS
	 *
	 * @return void
	 */
	public static function add_scripts() {
		wp_register_style( 'coolca-export', Helper::get_assets_folder_url() . '/css/export.css', array(), self::VERSION );
		// Version 1.1.2 - Add Product Calculator.
		wp_register_style( 'coolca-product-shipping-cost-none', Helper::get_assets_folder_url() . '/css/product-shipping-cost.none.css', array(), self::VERSION );
		wp_register_style( 'coolca-product-shipping-cost-basic', Helper::get_assets_folder_url() . '/css/product-shipping-cost.basic.css', array(), self::VERSION );
		wp_register_style( 'coolca-product-shipping-cost-rounded', Helper::get_assets_folder_url() . '/css/product-shipping-cost.rounded.css', array(), self::VERSION );
		wp_register_script( 'coolca-product-shipping-cost', Helper::get_assets_folder_url() . '/js/product-shipping-cost.js', array( 'jquery' ), true, 'in_footer', self::VERSION );

		// Version 1.3.0 - Add Checkout Branch Selector.
		wp_register_style( 'coolca-branch-checkout', Helper::get_assets_folder_url() . '/css/checkout-branch.css', array(), self::VERSION );
		if ( is_checkout() ) {
			wp_enqueue_style( 'coolca-branch-checkout' );
			wp_enqueue_style( 'dashicons' );
		}
	}

	/**
	 * DeActivation Plugin Actions
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 * @return void
	 */
	public static function deactivation( $plugin ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ( wp_next_scheduled( 'coolca-branches-cron-updater' ) ) {
			wp_clear_scheduled_hook( 'coolca-branches-cron-updater' );
		}
	}
}

// --- HPOS WooCommerce Compatibility
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

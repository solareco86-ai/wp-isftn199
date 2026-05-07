<?php
/**
 * PHP version 7
 *
 * @package  Hooks
 */

defined( 'ABSPATH' ) || exit;

// --- Settings
add_filter( 'woocommerce_get_sections_shipping', array( '\MANCA\CoolCA\Settings\Main', 'add_tab_settings' ) );
add_filter( 'woocommerce_get_settings_shipping', array( '\MANCA\CoolCA\Settings\Main', 'get_tab_settings' ), 10, 2 );
add_action( 'woocommerce_update_options_coolca_shipping_options', array( '\MANCA\CoolCA\Settings\Main', 'update_settings' ) );

// --- Settings - Prices
add_action( 'woocommerce_admin_field_coolca-prices', array( '\MANCA\CoolCA\Settings\PriceForm', 'render' ) );
add_action( 'woocommerce_settings_save_shipping', array( '\MANCA\CoolCA\Settings\Main', 'save' ) );

// --- Shipment Method
add_filter( 'woocommerce_shipping_methods', array( 'CoolCA', 'add_shipping_method' ) );

// --- CheckOut Fields
// Version 1.3.0 - New Class to handle Branchs in Checkout.
add_filter( 'woocommerce_order_details_before_order_table', array( '\MANCA\CoolCA\CheckOut\BranchSelectorField', 'display_fields' ) );
add_action( 'woocommerce_after_shipping_rate', array( '\MANCA\CoolCA\CheckOut\BranchSelectorField', 'shipping_point_field' ) );
add_filter( 'woocommerce_checkout_fields', array( '\MANCA\CoolCA\CheckOut\BranchSelectorField', 'override_checkout_fields' ) );
add_action( 'woocommerce_after_checkout_validation', array( '\MANCA\CoolCA\CheckOut\BranchSelectorField', 'checkout_validation' ), 9999, 2 );

// Version 1.5.1 - Clear Branch Selection on Shipping Method or State Change.
add_action( 'woocommerce_checkout_update_order_review', array( '\MANCA\CoolCA\CheckOut\ActionUpdateShippingBranch', 'clear_branch_selection' ) );

// --- Plugin Links
add_filter( 'plugin_action_links_' . plugin_basename( CoolCA::MAIN_FILE ), array( 'CoolCA', 'create_settings_link' ) );

// --- Run Import Manually
add_action( 'admin_menu', array( '\MANCA\CoolCA\Export\Main', 'create_menu_option' ) );
// Version 1.3.0 - Mark orders as exported ajax.
add_action( 'wp_ajax_coolca_action_mark_orders_as_exported', array( '\MANCA\CoolCA\Export\ActionMarkOrdersAsExported', 'ajax_callback_wp' ) );
add_action( 'wp_ajax_nopriv_coolca_action_mark_orders_as_exported', array( '\MANCA\CoolCA\Export\ActionMarkOrdersAsExported', 'ajax_callback_wp' ) );

// --- Add Scripts
add_action( 'admin_enqueue_scripts', array( 'CoolCA', 'add_scripts' ) );
// Version 1.1.2 - Add Product Calculator.
add_action( 'wp_enqueue_scripts', array( 'CoolCA', 'add_scripts' ) );

// --- Show Calculator
// Version 1.1.2 - Add Product Calculator.
add_action( 'woocommerce_before_add_to_cart_button', array( '\MANCA\CoolCA\Calculator\Main', 'show_calculator' ) );
add_action( 'woocommerce_after_add_to_cart_button', array( '\MANCA\CoolCA\Calculator\Main', 'show_calculator' ) );
add_action( 'woocommerce_before_add_to_cart_form', array( '\MANCA\CoolCA\Calculator\Main', 'show_calculator' ) );
add_action( 'woocommerce_after_add_to_cart_form', array( '\MANCA\CoolCA\Calculator\Main', 'show_calculator' ) );
add_action( 'wp_ajax_coolca_update_shipping_data', array( '\MANCA\CoolCA\Calculator\Main', 'ajax_callback_wp' ) );
add_action( 'wp_ajax_nopriv_coolca_update_shipping_data', array( '\MANCA\CoolCA\Calculator\Main', 'ajax_callback_wp' ) );

// --- Cron to update Branches
// Version 1.3.0
add_action( 'coolca-branches-cron-updater', array( '\MANCA\CoolCA\Process\CronBranches', 'run_cron' ) );
add_filter( 'cron_schedules', array( '\MANCA\CoolCA\Process\CronBranches', 'add_schedule' ) ); // phpcs:ignore WordPress.WP.CronInterval.ChangeDetected
add_action( 'wp_ajax_coolca_action_update_branches', array( '\MANCA\CoolCA\Process\ActionBranchesUpdate', 'ajax_callback_wp' ) );
add_action( 'wp_ajax_nopriv_coolca_action_update_branches', array( '\MANCA\CoolCA\Process\ActionBranchesUpdate', 'ajax_callback_wp' ) );
add_action( 'wp_ajax_coolca_action_update_shipping_branch', array( '\MANCA\CoolCA\CheckOut\ActionUpdateShippingBranch', 'ajax_callback_wp' ) );
add_action( 'wp_ajax_nopriv_coolca_action_update_shipping_branch', array( '\MANCA\CoolCA\CheckOut\ActionUpdateShippingBranch', 'ajax_callback_wp' ) );


// --- Order Metabox
// Version 1.3.19
add_action( 'add_meta_boxes', array( '\MANCA\CoolCA\Orders\OrderMetabox', 'add_meta_box' ) );

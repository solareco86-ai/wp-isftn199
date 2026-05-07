<?php
/**
 * Cool CA Woo Shipping Method Class
 *
 * @package  MANCA\CoolCA\ShippingMethod
 */

namespace MANCA\CoolCA\ShippingMethod;

use MANCA\CoolCA\Helper\Helper;
use WC_Shipping_Method;
use MANCA\CoolCA\PriceMethod\PriceMethodTable;
use MANCA\CoolCA\PriceMethod\PriceMethodApi;

defined( 'ABSPATH' ) || class_exists( '\WC_Shipping_Method' ) || exit;

/**
 * Our main payment method class
 */
class WC_CoolCA extends \WC_Shipping_Method {

	/**
	 * Aditional Description for Shipping Method.
	 *
	 * @var string
	 */
	public $additional_description;

	/**
	 * Service Type: Brach or Classic, Express or Express-Branch.
	 *
	 * @var string
	 */
	public $service_type;

	/**
	 * Pickup Country.
	 *
	 * @var string
	 */
	public $pickup_country;

	/**
	 * Pickup State.
	 *
	 * @var string
	 */
	public $pickup_state;

	/**
	 * Pickup City.
	 *
	 * @var string
	 */
	public $pickup_city;

	/**
	 * Pickup PostCode.
	 *
	 * @var string
	 */
	public $pickup_postcode;

	/**
	 * Pickup Address1.
	 *
	 * @var string
	 */
	public $pickup_address1;

	/**
	 * Pickup Address2.
	 *
	 * @var string
	 */
	public $pickup_address2;

	/**
	 * Free Delivery: 'yes', 'no'.
	 *
	 * @var string
	 */
	public $free_delivery;

	/**
	 * Free Delivery From Amount.
	 *
	 * @var float
	 */
	public $free_delivery_from;

	/**
	 * Charge or Discount Flag: 'yes', 'no'..
	 *
	 * @var string
	 */
	public $charge_discount;

	/**
	 * Charge or Discount Type: 'DISCOUNT', 'CHARGE' .
	 *
	 * @var string
	 */
	public $charge_discount_type;

	/**
	 * Charge or Discount Percentage.
	 *
	 * @var float
	 */
	public $charge_discount_pct;

	/**
	 * Charge or Discount From Amount.
	 *
	 * @var float
	 */
	public $charge_discount_from;

	/**
	 * Vendor ID.
	 *
	 * @var string
	 */
	public $vendor_id;

	/**
	 * Default constructor
	 *
	 * @param int $instance_id Shipping Method Instance from Order.
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'coolca';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Cool Correo Argentino', 'cool-ca' );
		$this->method_description = __( 'Permite a tus clientes calcular el costo del envío por Correo Argentino (paq.ar).', 'cool-ca' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);
		$this->init();
	}

	/**
	 * Init user set variables.
	 *
	 * @return void
	 */
	public function init() {
		$this->instance_form_fields = include 'settings-coolca.php';
		$this->title                = $this->get_option( 'title' );

		// Custom Settings.
		$this->additional_description = $this->get_option( 'coolca-additional-description' );
		$this->service_type           = $this->get_option( 'coolca-service-type' );
		$this->pickup_country         = $this->get_option( 'coolca-pickup-country' );
		$this->pickup_state           = $this->get_option( 'coolca-pickup-state' );
		$this->pickup_city            = $this->get_option( 'coolca-pickup-city' );
		$this->pickup_postcode        = $this->get_option( 'coolca-pickup-postcode' );
		$this->pickup_address1        = $this->get_option( 'coolca-pickup-address1' );
		$this->pickup_address2        = $this->get_option( 'coolca-pickup-address2' );
		$this->free_delivery          = $this->get_option( 'coolca-free-delivery' );
		$this->free_delivery_from     = $this->get_option( 'coolca-free-delivery-from' );
		$this->charge_discount        = $this->get_option( 'coolca-charge-discount-cb' );
		$this->charge_discount_type   = $this->get_option( 'coolca-charge-discount-type' );
		$this->charge_discount_pct    = $this->get_option( 'coolca-charge-discount-pct' );
		$this->charge_discount_from   = $this->get_option( 'coolca-charge-discount-from' );
		$this->vendor_id              = '';

		// Save settings in admin if you have any defined.
		add_action(
			'woocommerce_update_options_shipping_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);
		add_action( 'admin_footer', array( 'MANCA\CoolCA\ShippingMethod\WC_CoolCA', 'enqueue_admin_js' ), 10 ); // Priority needs to be higher than wc_print_js (25).
	}

	/**
	 * Sanitize the cost field.
	 *
	 * @param string $value value to be zanitized.
	 * @return string
	 */
	public function sanitize_cost( $value ) {
		$value = filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		return $value;
	}

	/**
	 * Validates product list
	 *
	 * @param Array $product_list List of Products from Cart Package.
	 *
	 * @return bool
	 */
	private function validate_product_list( $product_list = array() ) {
		$ret = true;
		if ( empty( $product_list ) ) {
			Helper::log( 'No items in Product List from Cart()' );
			$ret = false;
		}
		$MaxWeight           = 50;
		$MaxDim              = 300;
		$MaxCm               = 200;
		$MaxVolumetricWeight = 160;

		/** Version 1.3.0
		 * El peso debe ser menor a 50 Kg
		 * El largo debe ser menor o igual a 200 cm.
		 * La suma (largo + ancho + altura) debe ser menor o igual a 3 metros.
		 */
		foreach ( $product_list as $product_item ) {
			if ( empty( $product_item['width'] ) || empty( $product_item['length'] ) || empty( $product_item['height'] ) ) {
				Helper::log( 'Products without dimentions -> ' . $product_item['id'] );
				$ret = false;
			}

			if ( empty( $product_item['weight'] ) ) {
				Helper::log( 'Product without weight -> ' . $product_item['id'] );
				$ret = false;
			}

			if ( $product_item['weight'] > $MaxWeight
			|| $product_item['height'] + $product_item['width'] + $product_item['length'] > $MaxDim
			|| $product_item['height'] > $MaxCm
			|| $product_item['height'] > $MaxCm
			|| $product_item['height'] > $MaxCm ) {
				// Producto no puede ser enviado.
				Helper::log( 'Producto no cumple con las restricciones de tamaño / peso -> ' . $product_item['id'] );
				$ret = false;
			}

			// V1.1.1 - Now dimentions are required!
			if ( empty( $product_item['height'] ) || empty( $product_item['width'] ) || empty( $product_item['length'] ) ) {
				Helper::log( 'Error obteniendo el tamaño del paquete, los productos deben tener sus dimensiones cargadas.  -> ' . $product_item['id'] );
				$ret = false;
			}

			// Coeficiente de Aforo: fixed 6000.
			$currProductVolumetricWeight = floatval( ( $product_item['height'] * $product_item['width'] * $product_item['length'] ) / 6000 );
			if ( $currProductVolumetricWeight > $MaxVolumetricWeight ) {
				Helper::log( 'Error peso volumetrico del Producto supera máximo.-> ' . $product_item['id'] );
				$ret = false;
			}
		}

		return $ret;
	}

	/**
	 * Calculate the shipping costs by price table.
	 *
	 * @param array $package Package of items from cart.
	 * @param array $rate Rate object array of data.
	 *
	 * @return array
	 */
	private function generate_rate( $package = array(), $rate = array() ) {
		Helper::log( str_repeat( '-', 40 ) );
		Helper::log( 'Package:' );
		Helper::log( wp_json_encode( $package ) );

		// Version 1.1.2 - Add Product Calculator.
		// $items = Helper::get_individual_items_from_package( $package );
		// Version 1.3.0 - New Function.
		$items = Helper::get_product_list_from_package( $package );

		Helper::log( 'Items:' );
		Helper::log( wp_json_encode( $items ) );

		if ( false === $this->validate_product_list( $items ) ) {
			return;
		}

		$destination = Helper::get_destination_from_package( $package );
		if ( 'branch' === $this->service_type || 'branch-express' === $this->service_type ) {
			$branch_selected = WC()->session->get( 'coolca_branch_selected' );

			if ( ! empty( $branch_selected ) ) {
				Helper::log( 'Branch Selected ' . $branch_selected );

				// v1.3.17 - Change the way to save this data.
				$branch_selected_name    = WC()->session->get( 'coolca_branch_name_selected' );
				$branch_selected_address = WC()->session->get( 'coolca_branch_address_selected' );

				$rate['meta_data']['Sucursal']           = $branch_selected;
				$rate['meta_data']['Nombre Sucursal']    = $branch_selected_name;
				$rate['meta_data']['Dirección Sucursal'] = $branch_selected_address;

				$state = $destination['state'];

				// v1.5.0 - New Branch Selector.
				$branches = Helper::get_branches_by_state( $state );
				if ( isset( $branches[ $branch_selected ] ) ) {
					$city     = $branches[ $branch_selected ]['c'];
					$postcode = $branches[ $branch_selected ]['pc'];
				}
				$destination = array(
					'state'    => $state,
					'city'     => $city,
					'postcode' => $postcode,
				);
			}
		}

		/**
		 * Hook to allow developers to modify the shipping rate before calculating the shipping cost.
		 *
		 * @since 1.4.0
		 *
		 * @param WC_Shipping_Method $this The shipping method instance.
		 * @param array $items The list of items from cart.
		 */
		do_action( 'wc_coolca_before_calculate_shipping', $this, $items );

		if ( empty( $this->pickup_state ) || empty( $this->pickup_postcode ) ) {
			Helper::log( 'No se puede calcular el envío, el estado o el código postal de la sucursal no están configurados.' );
			return;
		}

		$totalCost = 0;

		if ( Helper::is_api_key_setted() ) {
			$priceMethod = new PriceMethodApi();
		} else {
			$priceMethod = new PriceMethodTable();
		}

		$Paqs = $priceMethod->calc_cost( $this, $items, $destination );

		if ( empty( $Paqs ) ) {
			Helper::log( 'No es posible realizar el envío-' );
			return;
		}

		$total_cost = 0;
		foreach ( $Paqs as $paq ) {
			$total_cost += $paq['cost'];
		}

		$rate['meta_data']['_paqars'] = Helper::encode_array( $Paqs );

		if ( ! empty( $this->vendor_id ) ) {
			$rate['meta_data']['_vendor_id'] = $this->vendor_id;
		}

		if ( empty( $total_cost ) ) {
			Helper::log( 'No es posible realizar el envío-' );
			return;
		}
		$rate['cost'] = $total_cost;

		return $rate;
	}


	/**
	 * Calculate the shipping costs.
	 *
	 * @param array $package Package of items from cart.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {

		$rate = array(
			'label'    => $this->get_option( 'title' ), // Label for the rate.
			'cost'     => '0', // Amount for shipping or an array of costs (for per item shipping).
			'taxes'    => '', // Pass an array of taxes, or pass nothing to have it calculated for you, or pass 'false' to calculate no tax for this method.
			'calc_tax' => 'per_order', // Calc tax per_order or per_item. Per item needs an array of costs passed via 'cost'.
			'package'  => $package,
			'term'     => '',
		);

		$rate = self::generate_rate( $package, $rate );

		if ( $rate ) {

			if ( 'CF' === Helper::get_option( 'vat' ) ) {
				$rate['cost'] = $rate['cost'] * 1.21;
			}

			if ( 'yes' === $this->charge_discount && floatval( $this->charge_discount_from ) <= floatval( $package['contents_cost'] ) ) {
				$charge_discount_pct = abs( floatval( $this->charge_discount_pct ) );
				if ( 'DISCOUNT' === $this->charge_discount_type ) {
					$charge_discount_pct = $charge_discount_pct * -1;
				}
				$rate['cost'] = $rate['cost'] * ( 1 + $charge_discount_pct / 100 );
			}

			if ( 'yes' === $this->free_delivery && floatval( $this->free_delivery_from ) <= floatval( $package['contents_cost'] ) ) {
				$rate['cost']  = 0;
				$rate['label'] = $this->get_option( 'title' ) . ' ' . __( ' - Gratis', 'cool-ca' );
			}

			// Register the rate.
			$this->add_rate( $rate );
		}
	}

	/**
	 * Enqueue admin JS for settings
	 *
	 * @return void
	 */
	public static function enqueue_admin_js() {
		wc_enqueue_js(
			"jQuery( function( $ ) {
				function wcCoolcaFree( el ) {
					var form = $( el ).closest( 'form' );
					var freeDeliveryFrom = $( '#woocommerce_coolca_coolca-free-delivery-from', form ).closest( 'tr' );            
					if ( $( el ).prop('checked') ) {
						freeDeliveryFrom.show();             
					} else {         
						freeDeliveryFrom.hide();
					}                  
				}            
				$( document.body ).on( 'change', '#woocommerce_coolca_coolca-free-delivery', function() {
					wcCoolcaFree( this );
				});  
				// Change while load.
				$( '#woocommerce_coolca_coolca-free-delivery' ).trigger( 'change' );
				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
						wcCoolcaFree( $( '#wc-backbone-modal-dialog #woocommerce_coolca_coolca-free-delivery', evt.currentTarget ) );
					}
				} );								
				function wcCoolcaChargeDiscount( el ) {
					var form = $( el ).closest( 'form' );
					var chargeDiscountType = $( '#woocommerce_coolca_coolca-charge-discount-type', form ).closest( 'tr' );            
					var chargeDiscountPct = $( '#woocommerce_coolca_coolca-charge-discount-pct', form ).closest( 'tr' );            
					if ( $( el ).prop('checked') ) {
						chargeDiscountType.show();  
						chargeDiscountPct.show();           
					} else {         
						chargeDiscountType.hide();  
						chargeDiscountPct.hide();    
					}                  
				}
				$( document.body ).on( 'change', '#woocommerce_coolca_coolca-charge-discount-cb', function() {
					wcCoolcaChargeDiscount( this );
				});  
				// Change while load.
				$( '#woocommerce_coolca_coolca-charge-discount-cb' ).trigger( 'change' );
				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
						wcCoolcaChargeDiscount( $( '#wc-backbone-modal-dialog #woocommerce_coolca_coolca-charge-discount-cb', evt.currentTarget ) );
					}
				} );
				

			});
        "
		);
	}
}

<?php
/**
 * Settings Includes
 *
 * @package  MANCA\CoolCA\ShippingMethod
 */

namespace MANCA\CoolCA\ShippingMethod;

use MANCA\CoolCA\Helper\Helper;

/**
 * Settings for Cool Correo Argentino rate shipping.
 */

defined( 'ABSPATH' ) || exit;
use WC_Countries;

global $woocommerce;
$countries_obj = new WC_Countries();
$countries     = $countries_obj->__get( 'countries' );
$states        = $countries_obj->get_states( 'AR' );


$settings = array(
	'title'                          => array(
		'title'       => __( 'Nombre Método Envío', 'cool-ca' ),
		'type'        => 'text',
		'description' => __( 'Nombre con el que aparecerá el tipo de envío en tu tienda.', 'cool-ca' ),
		'default'     => __( 'Cool Correo Argentino', 'cool-ca' ),
		'desc_tip'    => true,
	),
	'coolca-additional-description'  => array(
		'title'       => __( 'Descripción Adicional', 'cool-ca' ),
		'type'        => 'text',
		'description' => __( 'Indicaciones extras a mostrar al seleccionar el método de envío.', 'cool-ca' ),
		'desc_tip'    => true,
	),

	'coolca-service-type'            => array(
		'title'             => __( 'Tipo Envío', 'cool-ca' ),
		'type'              => 'select',
		'custom_attributes' => array( 'required' => 'required' ),
		'desc_tip'          => true,
		'description'       => __( 'Tipo de Envío', 'cool-ca' ),
		'default'           => 'branch',
		'options'           => array(
			'classic'        => __( 'Paq.ar Clásico a Domicilio', 'cool-ca' ),
			'branch'         => __( 'Paq.ar Clásico a Sucursal', 'cool-ca' ),
			'express'        => __( 'Paq.ar Express a Domicilio (solo con ApiKey)', 'cool-ca' ),
			'branch-express' => __( 'Paq.ar Express a Sucursal (solo con ApiKey)', 'cool-ca' ),
		),
	),

	// Sección Dirección.
	'coolca_pickup_address'          => array(
		'title'       => __( 'Dirección de Despacho', 'cool-ca' ),
		'description' => __( 'Ingresá la dirección y datos de contacto por dónde Correo Argentino retirará tus paquetes, o bien la dirección de la sucursal o locker donde dejarás tus paquetes', 'cool-ca' ),
		'type'        => 'title',
	),

	'coolca-pickup-country'          => array(
		'title'             => __( 'País', 'cool-ca' ),
		'type'              => 'select',
		'custom_attributes' => array(
			'required' => 'required',
			'disabled' => 'disabled',
		),
		'desc_tip'          => true,
		'description'       => __( 'Nombre del País', 'cool-ca' ),
		'options'           => array( 'AR' => __( 'Argentina', 'cool-ca' ) ),
		'default'           => 'AR',

	),

	'coolca-pickup-state'            => array(
		'title'   => __( 'Provincia', 'cool-ca' ),
		'type'    => 'select',
		'options' => $states,

	),

	'coolca-pickup-city'             => array(
		'title'             => __( 'Ciudad/Localidad', 'cool-ca' ),
		'type'              => 'text',
		'custom_attributes' => array( 'required' => 'required' ),
		'desc_tip'          => true,
		'description'       => __( 'Ciudad/Localidad', 'cool-ca' ),
	),

	'coolca-pickup-postcode'         => array(
		'title'             => __( 'Código Postal', 'cool-ca' ),
		'type'              => 'text',
		'custom_attributes' => array( 'required' => 'required' ),
		'desc_tip'          => true,
		'description'       => __( 'Código Postal', 'cool-ca' ),
	),

	'coolca-pickup-address1'         => array(
		'title'             => __( 'Calle y Número', 'cool-ca' ),
		'type'              => 'text',
		'custom_attributes' => array( 'required' => 'required' ),
	),

	'coolca-pickup-address2'         => array(
		'title' => __( 'Piso y Departamento', 'cool-ca' ),
		'type'  => 'text',
	),

	// Sección Envío Gratuito.
	'coolca_free_delivey_section'    => array(
		'title'       => __( 'Envíos Gratis', 'cool-ca' ),
		'description' => __( 'El envío será gratuito siempre que el monto del mismo supere el valor configurado.', 'cool-ca' ),
		'type'        => 'title',
	),

	'coolca-free-delivery'           => array(
		'title'   => __( 'Activar', 'cool-ca' ),
		'type'    => 'checkbox',
		'default' => 'no',
	),

	'coolca-free-delivery-from'      => array(
		'title'             => __( 'Monto Mínimo de Pedido', 'cool-ca' ),
		'type'              => 'number',
		'default'           => 0,
		'custom_attributes' => array( 'required' => 'required' ),
		'desc_tip'          => true,
		'sanitize_callback' => array( $this, 'sanitize_cost' ),
	),

	// Sección Cargo / Descuento.
	'coolca_charge_discount_section' => array(
		'title'       => __( 'Cargo / Descuento', 'cool-ca' ),
		'description' => __( 'Puede aplicar un cargo o descuento porcentual sobre el costo del envío.', 'cool-ca' ),
		'type'        => 'title',
	),

	'coolca-charge-discount-cb'      => array(
		'title'   => __( 'Aplicar Cargo / Descuento', 'cool-ca' ),
		'type'    => 'checkbox',
		'default' => 'no',
	),
	'coolca-charge-discount-type'    => array(
		'title'   => __( 'Aplicar', 'cool-ca' ),
		'label'   => __( 'Activar', 'cool-ca' ),
		'type'    => 'select',
		'options' => array(
			'CHARGE'   => 'Cargo',
			'DISCOUNT' => 'Descuento',
		),
		'default' => 'CHARGE',
	),
	'coolca-charge-discount-pct'     => array(
		'title'             => __( '%', 'cool-ca' ),
		'type'              => 'number',
		'default'           => 0,
		'custom_attributes' => array(
			'required' => 'required',
			'step'     => 2,
		),
	),
	'coolca-charge-discount-from'    => array(
		'title'             => __( 'Monto Mínimo de Pedido', 'cool-ca' ),
		'type'              => 'number',
		'default'           => 0,
		'custom_attributes' => array( 'required' => 'required' ),
		'desc_tip'          => true,
		'sanitize_callback' => array( $this, 'sanitize_cost' ),
	),

);

return $settings;

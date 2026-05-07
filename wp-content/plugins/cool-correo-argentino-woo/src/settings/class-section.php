<?php
/**
 * Setting Section Main Class
 *
 * @package  MANCA\CoolCA\Settings
 */

namespace MANCA\CoolCA\Settings;

use MANCA\CoolCA\Helper\Helper;
/**
 * CoolCA Setting Section Main
 */
class Section {

	/**
	 * Checks system requirements
	 *
	 * @return Array Fields Settings for CoolCA
	 */
	public static function get() {
		$logo_url = Helper::get_assets_folder_url() . '/img/banner-settings-1920x240.webp';

		$settings = array(
			array(
				/* translators: %s: logo url */
				'desc' => sprintf( __( '<img style="max-width:80vw; border-radius:20px;" src="%1$s" ><p>Versión %2$s</p>', 'cool-ca' ), $logo_url, \CoolCA::VERSION ),
				'type' => 'title',
				'id'   => 'wc-coolca_shipping_options',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options',
			),

			array(
				'title' => __( 'Precios', 'cool-ca' ),
				'type'  => 'title',
				'desc'  => __( 'En la siguiente sección deberá definir cómo el plugin tomará los precios. ', 'cool-ca' )
				. '<br>'
				. __( 'Si posee una API Key, puede utilizarla para completar automáticamente estos valores.', 'cool-ca' )
				. '<br>'
				. __( 'De lo contrario, deberá completar el tarifario. Lamentablemente Correo Argentino ya no comparte públicamente este tarifario con lo cual, será su responsabilidad solicitarlo al correo.', 'cool-ca' ),
				'id'    => 'wc-coolca_prices_options',
			),
			array(
				'name'    => __( 'Condición frente al IVA', 'cool-ca' ),
				'type'    => 'select',
				'id'      => 'wc-coolca-vat',
				'options' => array(
					'CF' => 'Consumidor Final',
					'RI' => 'Responsable Inscripto',
				),
				'default' => 'CF',
				'desc'    => __( 'Tanto el tarifario como la API calculan los costos sin IVA, seleccione "Consumidor Final" para incluir el IVA en el costo expresado a su cliente.', 'cool-ca' ),
			),
			array(
				'name'    => __( 'Modo de funcionamiento', 'cool-ca' ),
				'type'    => 'select',
				'id'      => 'wc-coolca-mode',
				'options' => array(
					'K' => 'Tengo una API Key',
					'M' => 'Cargaré el tarifario manualmente',
				),
				'default' => 'M',
			),
			array(
				'name'  => __( 'API Key', 'cool-ca' ),
				'type'  => 'text',
				'id'    => 'wc-coolca-api-key',
				'class' => 'coolca-mode-k',
			),
			array(
				'name'  => '',
				'id'    => 'wc-coolca-prices',
				'type'  => 'coolca-prices',
				'class' => 'coolca-mode-m',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_prices_options',
			),
			array(
				'title' => __( 'Opciones Generales', 'cool-ca' ),
				'type'  => 'title',
				'id'    => 'wc-coolca_shipping_options_general',
			),
			array(
				'name'    => __( 'Prefijo ID Orden', 'cool-ca' ),
				'type'    => 'text',
				'default' => __( '0000', 'cool-ca' ),
				'id'      => 'wc-coolca-order-prefix',
				'desc'    => __( 'Utiliza un prefijo numérico para identificar tus órdenes en el Correo (gana relevancia siempre que tengas más de una tienda asociada a tu cuenta de Correo Argentino). El formato del número de orden será XXXXYYYYYYJ donde XXXX será el prefijo, YYYYYY el número de orden de WooCommerce, y J el número de Paquete (para el caso en que un pedido tenga más de un paquete).', 'cool-ca' ),
			),
			array(
				'name'    => __( 'Permitir apilar productos', 'cool-ca' ),
				'id'      => 'wc-coolca-stack-products',
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Marca esta opción si tus productos pueden apilarse dentro de una caja de envío, esto permitirá optimizar el espacio de los paquetes que envías.', 'cool-ca' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options_general',
			),
			array(
				'title' => __( 'Calculadora de Costo de Envío', 'cool-ca' ),
				'desc'  => __(
					'Mostrar el calculador de costos de envío en la página de producto. Puede afectar la performance de tu sitio, ya que recalculará el costo de envío en cada página de producto.',
					'wc-coolca'
				),
				'type'  => 'title',
				'id'    => 'wc-coolca_shipping_calculator',
			),
			array(
				'name'    => '',
				'id'      => 'wc-coolca-product-shipping-calculator',
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Habilitar Calculadora', 'cool-ca' ),
			),
			array(
				'name'    => __( 'Título', 'cool-ca' ),
				'type'    => 'text',
				'default' => __( 'Costo de envío', 'cool-ca' ),
				'id'      => 'wc-coolca-shipping-calculator-title',
			),
			array(
				'name'    => __( 'Ubicación', 'cool-ca' ),
				'type'    => 'select',
				'default' => 'before-addtocartform',
				'id'      => 'wc-coolca-shipping-calculator-location',
				'options' => array(
					'before-addtocartbutton' => 'Antes del botón de agregar al carrito',
					'after-addtocartbutton'  => 'Después del botón de agregar al carrito',
					'before-addtocartform'   => 'Antes del formulario de agregar al carrito',
					'after-addtocartform'    => 'Después del formulario de agregar al carrito',
				),
				'desc'    => __( 'Define la ubicación de la calculadora de costos de envío en la página de producto.', 'cool-ca' ),
			),
			array(
				'name'    => __( 'Estilo', 'cool-ca' ),
				'type'    => 'select',
				'default' => 'default',
				'id'      => 'wc-coolca-shipping-calculator-style',
				'options' => array(
					'none'    => 'Ninguno',
					'basic'   => 'Básico',
					'rounded' => 'Redondeado',
				),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_calculator',
			),
			array(
				'title' => __( 'Valores por Defecto', 'cool-ca' ),
				'desc'  => __(
					'Correo Argentino precisa contar con el peso y dimensiones de los productos a enviar. Si no tiene cargado esos datos en sus productos, puede utilizar esta funcionalidad para definir un valor por defecto. Esto significa que siempre que el producto no tenga un peso definido, va a utilizar los valores indicados a continuación.',
					'wc-coolca'
				),
				'type'  => 'title',
				'id'    => 'wc-coolca_shipping_options_defaults',
			),

			array(
				'name' => __( 'Peso por defecto', 'cool-ca' ),
				'type' => 'number',
				'id'   => 'wc-coolca-default-weight',
				'desc' => __( 'Expresar el peso en gramos.', 'cool-ca' ),
			),
			array(
				'name' => __( 'Alto por defecto', 'cool-ca' ),
				'type' => 'number',
				'id'   => 'wc-coolca-default-height',
				'desc' => __( 'Expresar la longitud en cm.', 'cool-ca' ),
			),
			array(
				'name' => __( 'Ancho por defecto', 'cool-ca' ),
				'type' => 'number',
				'id'   => 'wc-coolca-default-width',
				'desc' => __( 'Expresar la longitud en cm.', 'cool-ca' ),
			),
			array(
				'name' => __( 'Largo por defecto', 'cool-ca' ),
				'type' => 'number',
				'id'   => 'wc-coolca-default-length',
				'desc' => __( 'Expresar la longitud en cm.', 'cool-ca' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options_defaults',
			),
			array(
				'title' => __( 'Opciones Multivendor', 'cool-ca' ),
				'desc'  => __( 'Configuración para el soporte de Multivendor/Marketplace en WooCommerce.', 'cool-ca' ),
				'type'  => 'title',
				'id'    => 'wc-coolca_shipping_options_multivendor',
			),
			array(
				'name'    => 'Habilitar compatibilidad con WCFM Marketplace',
				'id'      => 'wc-coolca-wcfm-enabled',
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Si habilitas la compatibilidad, los métodos de envío de Cool Correo Argentino se mostrarán solo si cada vendedor lo activa. Si no activas la compatibilidad, pero utilizas el plugin de Multivendor, los métodos de envío de Cool Correo Argentino no se mostrarán.', 'cool-ca' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options_multivendor',
			),

			array(
				'title' => __( 'Sincronización de Sucursales', 'cool-ca' ),
				'type'  => 'title',
				'desc'  => __( 'La sincronización de sucursales se realiza automáticamente cada 24 horas. Adicionalmente, se realiza una sincronización manual cada vez que se guarde esta configuración. Para que la configuración automática funcione correctamente es necesario que tenga activado el cron de WordPress. Para más información sobre WP Cron por favor visite <a href="https://developer.wordpress.org/plugins/cron/">https://developer.wordpress.org/plugins/cron/</a>.', 'cool-ca' ),
				'id'    => 'wc-coolca_shipping_options_branches',
			),
			array(
				'name'  => __( 'Última sincronización', 'cool-ca' ),
				'type'  => 'text',
				'desc'  => __( 'Fecha y hora de la última sincronización. Si no se muestra una fecha, puede significar que no se ha realizado ninguna sincronización. Puede realizar una sincronización manual presionando el botón "Guardar" de esta página.', 'cool-ca' ),
				'value' => get_option( 'coolca-branches-last-run', '' ),
				'class' => 'coolca-disabled',
			),
			array(
				'name'  => __( 'Última sincronización automática (CRON)', 'cool-ca' ),
				'type'  => 'text',
				'desc'  => __( 'Fecha y hora de la última sincronización automática. Si no se muestra una fecha, puede significar que no se ha realizado ninguna sincronización automática o que el cron no está activo.', 'cool-ca' ),
				'value' => get_option( 'coolca-branches-cron-last-run', '' ),
				'class' => 'coolca-disabled',
			),
			array(
				'name'  => __( 'Cantidad de sucursales sincronizadas', 'cool-ca' ),
				'type'  => 'text',
				'value' => Helper::get_branches_count(),
				'class' => 'coolca-disabled',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options_branches',
			),
			array(
				'title' => __( 'Debug', 'cool-ca' ),
				'desc'  => sprintf(
											/* translators: %s: logs url */
					__(
						'Puede habilitar el debug del plugin para realizar un seguimiento de la comunicación efectuada entre el plugin y la API de CoolCA. Podrá ver el registro desde el menú <a href="%s">WooCommerce > Estado > Registros</a>.',
						'cool-ca'
					),
					esc_url(
						get_admin_url( null, 'admin.php?page=wc-status&tab=logs' )
					)
				),
				'type'  => 'title',
				'id'    => 'wc-coolca_shipping_options_debug',
			),
			array(
				'name'    => '',
				'id'      => 'wc-coolca-debug',
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Habilitar Debug', 'cool-ca' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options_debug',
			),
			array(
				'title' => __( 'Opciones Avanzadas/Maestras', 'cool-ca' ),
				'desc'  => __( 'Modifique las opciones de esta sección solo si sabe lo que está haciendo.', 'cool-ca' ),
				'type'  => 'title',
				'id'    => 'wc-coolca_shipping_options_advanced',
			),
			array(
				'name'    => '',
				'id'      => 'wc-coolca-js-primitive',
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Habilitar JS Primitivo', 'cool-ca' ),
			),
			array(
				'name'    => __( 'Timeout', 'cool-ca' ),
				'type'    => 'number',
				'default' => 10,
				'id'      => 'wc-coolca-timeout',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wc-coolca_shipping_options_advanced',
			),
		);

		return $settings;
	}
}

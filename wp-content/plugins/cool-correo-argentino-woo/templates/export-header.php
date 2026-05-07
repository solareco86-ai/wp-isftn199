<?php
/**
 * PHP version 7
 *
 * @package  Templates
 */

use MANCA\CoolCA\Helper\Helper;
$logo_url = Helper::get_assets_folder_url() . '/img/banner-settings-1920x240.webp';

?>
<img src="<?php echo esc_url( $logo_url ); ?>" class="coolca-export-header-image" />
<h1> <?php esc_html_e( 'Exportar Pedidos para Correo Argentino', 'cool-ca' ); ?> </h1>
<p><?php esc_html_e( 'El buscador traerá hasta 90 órdenes (el máximo soportado por el proceso de Lotes de Correo Argentino). Una vez que realice la exportación, las órdenes exportadas no volverán a aparecer en la búsqueda.', 'cool-ca' ); ?></p>

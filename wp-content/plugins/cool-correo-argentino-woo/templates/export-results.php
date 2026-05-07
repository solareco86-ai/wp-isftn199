<?php
/**
 * PHP version 7
 *
 * @package  Templates
 */

use MANCA\CoolCA\Helper\Helper;

$from_dt_dflt      = $args['from_dt_dflt'];
$to_dt_dflt        = $args['to_dt_dflt'];
$curr_status       = $args['curr_status'];
$inc_free_shipping = $args['inc_free_shipping'];

$orders = $args['orders'];
?>

<h3><?php esc_html_e( 'Resultados', 'cool-ca' ); ?></h3>

<?php if ( ! empty( $orders ) ) { ?>
	<div class="coolca-table-content">
		<div class="coolca-table-row header">
			<div scope="col" class="coolca-table-cell small-column"> <?php esc_html_e( 'Acciones', 'cool-ca' ); ?></div>
			<div scope="col" class="coolca-table-cell small-column"> <?php esc_html_e( 'Orden#', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Tipo Producto', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Largo', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Ancho', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Altura', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Peso', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Valor del contenido', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Provincia Destino', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Sucursal Destino', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable medium-column"> <?php esc_html_e( 'Localidad Destino', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable medium-column"> <?php esc_html_e( 'Calle Destino', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xsmall-column"> <?php esc_html_e( 'Altura Destino', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xsmall-column"> <?php esc_html_e( 'Piso', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xsmall-column"> <?php esc_html_e( 'Depto', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable small-column"> <?php esc_html_e( 'Codigo Postal', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable large-column"> <?php esc_html_e( 'Destino Nombre', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xlarge-column"> <?php esc_html_e( 'Destino Email', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xsmall-column"> <?php esc_html_e( 'Cod Area Tel', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xsmall-column"> <?php esc_html_e( 'Tel', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable xsmall-column"> <?php esc_html_e( 'Cod Area Cel', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable medium-column"> <?php esc_html_e( 'Cel', 'cool-ca' ); ?> </div>
			<div scope="col" class="coolca-table-cell coolca-exportable medium-column"> <?php esc_html_e( 'Número de Orden', 'cool-ca' ); ?> </div>
		</div>
		<?php foreach ( $orders as $order_data ) { ?>			
		<div class="coolca-table-row data" data-order-id="<?php echo esc_attr( $order_data['id'] ); ?>">
			<div class="coolca-table-cell small-column">				
				<span class="dashicons-before dashicons-trash coolca-btn coolca-delete-btn" aria-hidden="true"></span>				
				<span class="dashicons-before dashicons-plus coolca-btn coolca-add-btn" aria-hidden="true"></span>				
				<a class="coolca-btn" href="<?php echo esc_url( get_admin_url( null, 'post.php?post=' . $order_data['id'] . '&action=edit' ) ); ?>" target='_blank'><span class="btn dashicons-before dashicons-search" aria-hidden="true"></span></a>
			</div>
			<div class="coolca-table-cell small-column"> <?php echo esc_html( $order_data['id'] ); ?> </div>
			<div class="coolca-table-cell small-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['coolca_type'] ) ); ?> </div>
			<div class="coolca-table-cell small-column coolca-exportable"> <div><span class="btn dashicons-before dashicons-edit coolca-btn coolca-edit-span" aria-hidden="true"></div><div class="coolca-span"><?php echo esc_html( Helper::remove_special_chars( $order_data['length'] ) ); ?></span></div></div>
			<div class="coolca-table-cell small-column coolca-exportable"> <div><span class="btn dashicons-before dashicons-edit coolca-btn coolca-edit-span" aria-hidden="true"></div><div class="coolca-span"><?php echo esc_html( Helper::remove_special_chars( $order_data['width'] ) ); ?></span></div></div>
			<div class="coolca-table-cell small-column coolca-exportable"> <div><span class="btn dashicons-before dashicons-edit coolca-btn coolca-edit-span" aria-hidden="true"></div><div class="coolca-span"><?php echo esc_html( Helper::remove_special_chars( $order_data['height'] ) ); ?></span></div></div>
			<div class="coolca-table-cell small-column coolca-exportable"> <div><span class="btn dashicons-before dashicons-edit coolca-btn coolca-edit-span" aria-hidden="true"></div><div class="coolca-span"><?php echo esc_html( Helper::remove_special_chars( $order_data['weight'] ) ); ?> </span></div></div>
			<div class="coolca-table-cell small-column coolca-exportable"> <div><span class="btn dashicons-before dashicons-edit coolca-btn coolca-edit-span" aria-hidden="true"></div><div class="coolca-span"><?php echo esc_html( Helper::remove_special_chars( $order_data['value'] ) ); ?> </span></div></div>
			<div class="coolca-table-cell small-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['state'] ) ); ?> </div>
			<div class="coolca-table-cell small-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['branch'] ) ); ?> </div>
			<div class="coolca-table-cell medium-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['city'] ) ); ?> </div>
			<div class="coolca-table-cell medium-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['street'] ) ); ?> </div>
			<div class="coolca-table-cell xsmall-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['street_nbr'] ) ); ?> </div>
			<div class="coolca-table-cell xsmall-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['floor'] ) ); ?> </div>
			<div class="coolca-table-cell xsmall-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['apartment'] ) ); ?> </div>
			<div class="coolca-table-cell small-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['postcode'] ) ); ?> </div>
			<div class="coolca-table-cell large-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['fullname'] ) ); ?> </div>
			<div class="coolca-table-cell xlarge-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['email'] ) ); ?> </div>
			<div class="coolca-table-cell xsmall-column coolca-exportable"> </div>
			<div class="coolca-table-cell xsmall-column coolca-exportable"> </div>
			<div class="coolca-table-cell xsmall-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['codarea_cellphone'] ) ); ?> </div>
			<div class="coolca-table-cell medium-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['cellphone'] ) ); ?>  </div>
			<div class="coolca-table-cell medium-column coolca-exportable"> <?php echo esc_html( helper::remove_special_chars( $order_data['external_id'] ) ); ?>  </div>
		</div>				
		<?php } ?>	
	</div>
	<div class="coolca-action">
		<button id='cool-ca-export2csv' class="coolca-btn btn btn-primary"> <?php esc_html_e( 'Exportar a CSV', 'cool-ca' ); ?></button>
	</div>

	<script>
	var coolca_export_setting = {
		nonce: "<?php echo esc_html( wp_create_nonce( 'coolca-orders-update' ) ); ?>",
		ajax_url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
	};
	</script>

	<?php
	wp_enqueue_script( 'coolca-export-app', Helper::get_assets_folder_url() . '/js/export-app.js', array( 'jquery' ), true, 'in_footer' );
} else {
	?>
	<p> <?php esc_html_e( 'No hay pedidos en el rango de fechas indicado', 'cool-ca' ); ?></p>
	<?php
}
?>

<?php
/**
 * PHP version 7.
 *
 * @package  Templates
 */

$is_coolca    = $args['is_coolca'];
$service_type = $args['service_type'];
$service      = '';
if ( 'branch' === $service_type ) {
	$service = __( 'a Sucursal', 'coolca' );
} elseif ( 'branch-express' === $service_type ) {
	$service = __( 'a Sucursal Express', 'coolca' );
} elseif ( 'express' === $service_type ) {
	$service = __( 'Express', 'coolca' );
} else {
	$service = __( 'a Domicilio', 'coolca' );
}
$branch         = $args['branch'];
$branch_name    = $args['branch_name'];
$branch_address = $args['branch_address'];
$paqs           = $args['paqs'];
$exported       = $args['exported'];
?>
<div class="coolca order-attribution-metabox">
	<?php
	if ( $is_coolca ) {
		?>

		<h4> <?php echo esc_html_e( 'Servicio', 'coolca' ); ?></h4>
		<span> <?php echo esc_html( $service ); ?></span>
		<?php
		if ( 'branch' === $service_type || 'branch-express' === $service_type ) {
			?>
		<h4> <?php echo esc_html_e( 'Sucursal', 'coolca' ); ?></h4>
		<span> <?php echo esc_html( $branch_name . ' ( ' . $branch . ' ) ' ); ?></span>
		<br/>
		<span> <?php echo esc_html( $branch_address ); ?></span>
		
			<?php
		} else {
			?>

			<?php
		}
		?>

		<?php
		foreach ( $paqs as $paq ) {
			?>
			<h4> <?php echo esc_html_e( 'Paquete', 'coolca' ); ?></h4>
			<span> <?php echo esc_html_e( 'Dimensiones (cm): ', 'coolca' ); ?> <?php echo esc_html( $paq['width'] . 'x' . $paq['height'] . 'x' . $paq['length'] ); ?></span>
			<br/>
			<span> <?php echo esc_html_e( 'Peso  (kg): ', 'coolca' ); ?><?php echo esc_html( $paq['weight'] ); ?></span>
			<br/>           
			<strong> <?php echo esc_html_e( 'Items: ', 'coolca' ); ?></strong>
			<?php
			foreach ( $paq['item_count'] as $key => $count ) {
				?>
				<br/>   
				<span><?php echo esc_html( $key . ' x ' . $count ); ?></span>
				<?php
			}
		}

		?>
		<h4> <?php echo esc_html_e( 'Estado', 'coolca' ); ?></h4>
		<?php if ( $exported ) { ?>
			<span> <?php echo esc_html_e( 'Exportado', 'coolca' ); ?></span>
		<?php } else { ?>
			<span> <?php echo esc_html_e( 'No exportado', 'coolca' ); ?></span>
		<?php } ?>
		
		
		<?php
	} else {
		echo esc_html_e( 'Pedido no procesado por Correo Argentino', 'cool-ca' );
	}
	?>
</div>

<?php
/**
 * Price Form Main Class
 *
 * @package  MANCA\CoolCA\Settings
 */

namespace MANCA\CoolCA\Settings;

use MANCA\CoolCA\Helper\Helper;

/**
 * Price Form Setting Section Main
 */
class PriceForm {

	/**
	 * Draw Table
	 *
	 * @param string $type Name of Table.
	 * @param array  $value Array of Values comes from Setting Field.
	 * @param array  $data Array of Data.
	 * @return void
	 */
	public static function draw_table( $type = '', $value = array(), $data = array() ) {
		$zones   = Helper::get_zones_array_init();
		$weights = Helper::get_weight_ranges();
		$name    = ( 'classic' === $type ) ? __( 'Clásico', 'cool-ca' ) : __( 'Sucursal', 'cool-ca' );
		?>
		<table class="<?php echo esc_attr( $value['class'] ); ?>">            
			<thead>
				<tr>
					<th rowspan=2 colspan=2><?php echo esc_html( $name ); ?></th>
					<th colspan=4><?php esc_html_e( 'ZONA', 'cool-ca' ); ?></th>
				</tr>
				<tr>
					<?php foreach ( $weights as $weight ) { ?>
						<th><?php esc_html( $weight ); ?></th>
					<?php } ?>					
				</tr>
			</thead>
			<tbody>
				<?php
				$first_column = false;
				foreach ( $weights as $weight ) {
					?>
					<tr>
						<?php if ( false === $first_column ) { ?>
							<th rowspan=<?php echo esc_attr( count( $weights ) ); ?> ><?php esc_html_e( 'PESO', 'cool-ca' ); ?></th>
							<?php
							$first_column = true;
						}
						?>
						<th><?php echo esc_html( $weight ); ?></th>      
						<?php foreach ( $zones as $zone ) { ?>
							<td><input type="number" name="wc-coolca-price[<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $weight ); ?>][<?php echo esc_attr( $zone ); ?>]" value="<?php echo esc_html( $data[ $weight ][ $zone ] ); ?>" step=".01"></td>
						<?php } ?>
					</tr>
				<?php } ?>															 
			</tbody>
		</table> 
		<?php
	}

	/**
	 * Render Price Form.
	 *
	 * @param string $value Nothing.
	 * @return void
	 */
	public static function render( $value ) {
		$saved_values = ! empty( get_option( 'wc-coolca-price' ) )
			? get_option( 'wc-coolca-price' )
			: array();

		$saved_values = array_merge( Helper::get_prices_array_init(), $saved_values );

		?>
		<br>
		<?php self::draw_table( 'classic', $value, $saved_values['classic'] ); ?>
		<br>
		<?php self::draw_table( 'branch', $value, $saved_values['branch'] ); ?>	
		<?php
	}
}

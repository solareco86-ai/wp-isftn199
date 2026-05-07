<?php
/**
 * PHP version 7
 *
 * Version 1.1.2 - Add Product Calculator
 *
 * @package  Templates
 */

use MANCA\CoolCA\Helper\Helper;

$rates           = isset( $args['rates'] ) ? $args['rates'] : array();
$product_id      = isset( $args['product_id'] ) ? $args['product_id'] : '';
$current_address = isset( $args['current_address'] ) ? $args['current_address'] : '';
?>
<div class="coolca-product-cost-calculator">
	<div class="custom-loader"></div>
	<h3><?php echo esc_html( Helper::get_option( 'shipping-calculator-title' ) ); ?></h3>
		<ul class="coolca-rates">           
			<?php foreach ( $rates as $rate ) { ?>
				<li><?php echo esc_html( $rate['name'] . ' - ' ) . wp_kses_post( $rate['cost'] ); ?></li>
			<?php } ?>               
		</ul>
		<?php $rates_check = ! empty( $rates ) ? 'display:none' : ''; ?>
		<p class="coolca-no-rates-message" style="<?php echo esc_attr( $rates_check ); ?>"><?php echo esc_html_e( 'Complete su dirección para calcular el envío', 'cool-ca' ); ?></p>
		<p class="coolca-current-address"><?php echo esc_html( $current_address ); ?></p>
	<div>        
		<?php printf( '<a class="shipping-calculator-button" style="cursor:pointer;">%s <img src="%s" alt="Shipping Cart" /></a>', esc_html( ! empty( $button_text ) ? $button_text : __( 'Calculate shipping', 'woocommerce' ) ), esc_url( Helper::get_assets_folder_url() . '/img/8542234_shipping_fast_icon.png' ) ); ?>
		<section class="shipping-calculator-form" style="display:none;">
			<p class="coolca-form-instructions"><?php echo esc_html_e( 'Complete Provincia y Código Postal.', 'cool-ca' ); ?></p>
			<div class="coolca-form-fields">
				<?php
				/**
				 * Check if WooCommerce State is enable for Shipping Calculator.
				 *
				 * @since 2023.07.13
				 *
				 * @param bool  $enabled Enabled.
				 * @return bool
				 */
				if ( apply_filters( 'woocommerce_shipping_calculator_enable_state', true ) ) :
					?>
					<?php
						$current_cc = 'AR';
						$current_r  = WC()->customer->get_shipping_state();
						$states     = WC()->countries->get_states( $current_cc );
					if ( is_array( $states ) ) {
						?>
							<p>
								<select name="calc_shipping_state" class="state_select" id="calc_shipping_state" data-placeholder="<?php echo esc_attr_e( 'State / County', 'woocommerce' ); ?>">
									<option value=""><?php echo esc_html_e( 'Select an option&hellip;', 'woocommerce' ); ?></option>
								<?php
								foreach ( $states as $ckey => $cvalue ) {
									if ( 'C' === $ckey ) {
										$cvalue = 'Capital Federal';
									}
									echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
								}
								?>
								</select>
							</p>
							<?php
					}
					?>
				<?php endif; ?>
				<?php
				/**
				 * Check if WooCommerce Postcode is enable for Shipping Calculator.
				 *
				 * @since 2023.07.13
				 *
				 * @param bool  $enabled Enabled.
				 * @return bool
				 */
				if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) :
					?>
					<p>
						<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php echo esc_attr_e( 'Postcode / ZIP', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />                    
					</p>
				<?php endif; ?>
				<p><a  name="calc_shipping" value="1" class="button"><?php echo esc_html_e( 'Update', 'woocommerce' ); ?></a></p>              
			</div>		
		</section>
	</div>
</div>
<script>
var coolca_settings = {
	nonce: "<?php echo esc_html( wp_create_nonce( 'cool-ca' ) ); ?>",
	product_id: "<?php echo esc_attr( $product_id ); ?>",
	ajax_url: "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
};
</script>

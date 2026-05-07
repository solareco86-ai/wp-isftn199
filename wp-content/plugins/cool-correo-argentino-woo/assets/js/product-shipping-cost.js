/**
 * Product Shipping App
 *
 * @package  MANCA\CoolCA\Calculator
 */

jQuery( document ).ready(
	function( $ ) {
		$( ".shipping-calculator-button" ).click(
			function() {
				if ( $( ".shipping-calculator-form" ).css( 'display' ) == 'none') {
					$( ".shipping-calculator-form" ).show();
				} else {
					$( ".shipping-calculator-form" ).hide();
				}
			}
		);
		$( "a[name='calc_shipping'" ).dblclick(
			function(e){
				e.preventDefault();
			}
		);
		$( "a[name='calc_shipping'" ).click(
			function() {
				$( ".coolca-product-cost-calculator" ).addClass( "processing" );
				var coolca_state    = $( "#calc_shipping_state" ).val();
				var coolca_postcode = $( "#calc_shipping_postcode" ).val();
				var dataToSend      = {
					action: "coolca_update_shipping_data",
					coolca_nonce: coolca_settings.nonce,
					calc_shipping_state: coolca_state,
					calc_shipping_postcode: coolca_postcode,
					product_id: coolca_settings.product_id
				}

				jQuery.post(
					coolca_settings.ajax_url,
					dataToSend,
					function (data) {
						if (data.success) {
							$( "ul.coolca-rates" ).html( '' );
							if ( data.data.rates ) {
								$( "ul.coolca-rates" ).html( '' );
								var count_rates = data.data.rates.length;
								for (var i = 0; i < count_rates; i++) {
									$( "ul.coolca-rates" ).append( '<li>' + data.data.rates[i].name + ' - ' + data.data.rates[i].cost + '</li>' );
								}
								$( ".coolca-current-address" ).html( data.data.current_address );
								$( ".coolca-no-rates-message" ).css( "display","none" );
								$( ".shipping-calculator-form" ).hide();
							} else {
								$( ".coolca-no-rates-message" ).css( "display","inherit" );
							}
						} else {
							console.log( data );
						}
						$( ".coolca-product-cost-calculator" ).removeClass( "processing" );
					}
				);
			}
		);
	}
);

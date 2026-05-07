<?php
/**
 * PHP version 7
 *
 * @package  Templates
 */

use MANCA\CoolCA\Helper\Helper;

$branches                = $args['branches'];
$method_id               = 'coolca-branch-selection-' . $args['method-id'];
$branch_selected         = $args['branch_selected'];
$branch_name_selected    = $args['branch_name_selected'];
$branch_address_selected = $args['branch_address_selected'];
$js_primitive            = $args['js_primitive'];
$has_branch_selected     = ! empty( $branch_selected );
$button_text             = $has_branch_selected ? __( 'Cambiar Sucursal', 'cool-ca' ) : __( 'Seleccionar Sucursal', 'cool-ca' );
?>
<div id="<?php echo esc_attr( $method_id ); ?>">
	<div class="modal coolca-branch-selection">  
	<div class="modal-content">
		<span class="close">&times;</span>
		<div class="modal-header">
			<h2><?php echo esc_html_e( 'Seleccione la sucursal de retiro', 'cool-ca' ); ?></h2>
		</div>        
		<div class="search-content">
			<div class="search-btn">
				<input type="text" name="coolca-branch-search">
			</div>
		</div>
		<div class="three-col">
		<?php
		foreach ( $branches as $code => $branch ) {
			$name     = isset( $branch['name'] ) ? $branch['name'] : '';
			$address  = isset( $branch['address'] ) ? $branch['address'] : '';
			$postcode = isset( $branch['postcode'] ) ? $branch['postcode'] : '';
			?>
		<div class="row" data-address="<?php echo esc_attr( $name . ' ' . $address ); ?>" data-postcode="<?php echo esc_attr( $postcode ); ?>">
			<div class="column"><?php echo esc_html( $name ); ?></div>
			<div class="column"><?php echo esc_html( $address ); ?></div>
			<div class="column">
				<a class="button coolca-branch-select-act" data-id="<?php echo esc_attr( $code ); ?>" data-address="<?php echo esc_attr( $address ); ?>" data-name="<?php echo esc_attr( $name ); ?>"><?php echo esc_html_e( 'Seleccionar', 'cool-ca' ); ?></a>
			</div>
		</div>
		<?php } ?>
		</div>
	</div>
	</div>
	<p>
		<span class="coolca-branch-label"><?php echo esc_html( $branch_address_selected ); ?></span>
	</p>
	<a class="button coolca-branch-selection-btn">
		<span>
			<?php echo esc_html( $button_text ); ?>
		</span>
	</a>
	<p class="validate-required" id="coolca_branch_field">
		<span class="woocommerce-input-wrapper">
			<input type="hidden" name="coolca_branch" id="coolca_branch" value="<?php echo esc_attr( $branch_selected ); ?>">  
			<input type="hidden" name="coolca_branch_name" id="coolca_branch_name" value="<?php echo esc_attr( $branch_name_selected ); ?>">  
			<input type="hidden" name="coolca_branch_address" id="coolca_branch_address" value="<?php echo esc_attr( $branch_address_selected ); ?>">  
		</span>
	</p>
</div>

<?php if ( 'no' !== $js_primitive ) { ?>
	<script>
	jQuery(document).ready( function( $ ){
		var ctx = "#<?php echo esc_attr( $method_id ); ?> ";      

		// Extract numeric part from Argentine postcode (handles both 1615 and B1615ZAB formats).
		var extractPostcodeNumber = function(postcode) {
			if (!postcode) return 0;
			var numeric = postcode.replace(/[^0-9]/g, '');
			return parseInt(numeric, 10) || 0;
		};

		// Sort branch rows by proximity to customer postcode.
		var sortRowsByPostcode = function() {
			var customerPostcode = $('#billing_postcode').val() || $('#shipping_postcode').val() || '';
			var customerPostcodeNum = extractPostcodeNumber(customerPostcode);
			
			if (customerPostcodeNum === 0) return; // No postcode to sort by.
			
			var $container = $( ctx + '.three-col');
			var $rows = $container.find('.row').get();
			
			$rows.sort(function(a, b) {
				var aPostcode = extractPostcodeNumber($(a).attr('data-postcode'));
				var bPostcode = extractPostcodeNumber($(b).attr('data-postcode'));
				var aDiff = Math.abs(customerPostcodeNum - aPostcode);
				var bDiff = Math.abs(customerPostcodeNum - bPostcode);
				return aDiff - bDiff;
			});
			
			$.each($rows, function(idx, row) {
				$container.append(row);
			});
		};

		// Sort on page load.
		sortRowsByPostcode();

		//Filter Branch Rows.
		var filterRows = function(value){
			$( ctx + '.row').hide();
			if( value.length > 3 ){
				$( ctx + '.row').filter(function() {
					return $(this).attr('data-address').toUpperCase().indexOf(value.toUpperCase()) >= 0;
				}).show();                
			}
			if( value.length == 0) {
				$( ctx + '.row').show();
			}
		}
		// When the user clicks on <span> (x), close the modal.
		$( ctx + ".coolca-branch-selection " + "span.close").click( function() {
			$( ctx + ".coolca-branch-selection").css("display", 'none'); 
		});      
		$( ctx + ".coolca-branch-selection-btn").click(function(){
			sortRowsByPostcode(); // Re-sort in case postcode changed.
			$( ctx + ".coolca-branch-selection").css("display", 'inherit'); 
		});

		$( ctx + ".coolca-branch-select-act").click(function(){
			var id = $(this).attr('data-id');
			var address = $(this).attr('data-address');
			var name = $(this).attr('data-name');
			$( ctx + 'input[name="coolca_branch"]' ).attr('value', id);
			$( ctx + 'input[name="coolca_branch_address"]' ).attr('value', address);
			$( ctx + 'input[name="coolca_branch_name"]' ).attr('value', name);

			$( ctx + 'span.coolca-branch-label' ).html(address);
			$( ctx + '.coolca-branch-selection-btn span' ).text('<?php echo esc_js( __( 'Cambiar Sucursal', 'cool-ca' ) ); ?>');

			var dataToSend = {
				action: "coolca_action_update_shipping_branch",           
				coolca_nonce: "<?php echo esc_attr( wp_create_nonce( 'coolca-branch-selection' ) ); ?>",
				coolca_branch: id,
				coolca_branch_address: address,
				coolca_branch_name: name,
			}
			$( ctx + ".coolca-branch-selection .modal-content" ).css("display", 'none'); 

			$.post("<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>", dataToSend, function (data) {
				if (data.success) {                       
					$('body').trigger('update_checkout');
					$( ctx + ".coolca-branch-selection" ).css("display", 'none'); 
					$( ctx + ".coolca-branch-selection .modal-content" ).css("display", '');	
				} else {
					console.log(data);
					$( ctx + ".coolca-branch-selection" ).css("display", 'none'); 
					$( ctx + ".coolca-branch-selection .modal-content" ).css("display", '');	
				}             
			});					 
		});
   
		$( ctx + ".search-btn " + "input" ).bind("keypress", function (e) {                                     
			if (e.keyCode == 13) {  
				return false;  
			}
		});  
		$( ctx + ".search-btn " + "input" ).bind("keyup", function (e) {                                     
			filterRows(  $( ctx + ".search-btn " + "input" ).val() );            
		});     
	});
</script>
<?php } else { ?>	
<script>	
	jQuery(document).ready( function( $ ){
		var ctx = $("#<?php echo esc_attr( $method_id ); ?>");      

		// Extract numeric part from Argentine postcode (handles both 1615 and B1615ZAB formats).
		var extractPostcodeNumber = function(postcode) {
			if (!postcode) return 0;
			var numeric = postcode.replace(/[^0-9]/g, '');
			return parseInt(numeric, 10) || 0;
		};

		// Sort branch rows by proximity to customer postcode.
		var sortRowsByPostcode = function() {
			var customerPostcode = $('#billing_postcode').val() || $('#shipping_postcode').val() || '';
			var customerPostcodeNum = extractPostcodeNumber(customerPostcode);
			
			if (customerPostcodeNum === 0) return; // No postcode to sort by.
			
			var $container = $('.three-col', ctx);
			var $rows = $container.find('.row').get();
			
			$rows.sort(function(a, b) {
				var aPostcode = extractPostcodeNumber($(a).attr('data-postcode'));
				var bPostcode = extractPostcodeNumber($(b).attr('data-postcode'));
				var aDiff = Math.abs(customerPostcodeNum - aPostcode);
				var bDiff = Math.abs(customerPostcodeNum - bPostcode);
				return aDiff - bDiff;
			});
			
			$.each($rows, function(idx, row) {
				$container.append(row);
			});
		};

		// Sort on page load.
		sortRowsByPostcode();

		//Filter Branch Rows.
		var filterRows = function(value){
			$('.row', ctx).hide();
			if( value.length > 3 ){
				$('.row', ctx).filter(function() {
					return $(this).attr('data-address').toUpperCase().indexOf(value.toUpperCase()) >= 0;
				}).show();                
			}
			if( value.length == 0) {
				$('.row', ctx).show();
			}
		}
		// When the user clicks on <span> (x), close the modal.
		$("span.close", $(".coolca-branch-selection", ctx)).click( function() {
			$(".coolca-branch-selection", ctx).css("display", 'none'); 
		});      
		$(".coolca-branch-selection-btn", ctx).click(function(){
			sortRowsByPostcode(); // Re-sort in case postcode changed.
			$(".coolca-branch-selection", ctx).css("display", 'inherit'); 
		});

		$(".coolca-branch-select-act", ctx).click(function(){
			var id = $(this).attr('data-id');
			var address = $(this).attr('data-address');
			var name = $(this).attr('data-name');
			$('input[name="coolca_branch"]' , ctx ).attr('value', id);
			$('input[name="coolca_branch_address"]' , ctx ).attr('value', address);
			$('input[name="coolca_branch_name"]' , ctx ).attr('value', name);

			$('span.coolca-branch-label', ctx ).html(address);
			$('.coolca-branch-selection-btn span', ctx ).text('<?php echo esc_js( __( 'Cambiar Sucursal', 'cool-ca' ) ); ?>');

			var dataToSend = {
				action: "coolca_action_update_shipping_branch",           
				coolca_nonce: "<?php echo esc_attr( wp_create_nonce( 'coolca-branch-selection' ) ); ?>",
				coolca_branch: id,
				coolca_branch_address: address,
				coolca_branch_name: name,
			}
			$(".coolca-branch-selection .modal-content" , ctx).css("display", 'none'); 

			$.post("<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>", dataToSend, function (data) {
				if (data.success) {                       
					$('body').trigger('update_checkout');
					$(".coolca-branch-selection" , ctx).css("display", 'none'); 
					$(".coolca-branch-selection .modal-content" , ctx).css("display", '');	
				} else {
					console.log(data);
					$(".coolca-branch-selection" , ctx).css("display", 'none'); 
					$(".coolca-branch-selection .modal-content" , ctx).css("display", '');	
				}             
			});					 
		});
   
		$("input", $(".search-btn", ctx)).bind("keypress", function (e) {                                     
			if (e.keyCode == 13) {  
				return false;  
			}
		});  
		$("input", $(".search-btn", ctx)).bind("keyup", function (e) {                                     
			filterRows(  $("input", $(".search-btn", ctx)).val() );            
		});     
	});
</script>
<?php } ?>

/**
 * Export App
 *
 * @package  MANCA\CoolCA\Export
 */

jQuery( document ).ready(
	function( ) {
		function downloadCSV( csv, filename ) {
			var csvFile;
			var downloadLink;

			// CSV file.
			csvFile = new Blob( [csv], { type: "text/csv" } );

			// Download link.
			downloadLink = document.createElement( "a" );

			// File name.
			downloadLink.download = filename;

			// Create a link to the file.
			downloadLink.href = window.URL.createObjectURL( csvFile );

			// Hide download link.
			downloadLink.style.display = "none";

			// Add the link to DOM.
			document.body.appendChild( downloadLink );

			// Click download link.
			downloadLink.click();
		}

		function exportTableToCSV( filename ) {

			var csv        = [ ];
			var rows       = document.querySelectorAll( ".coolca-table-content .coolca-table-row" );
			var count_rows = rows.length;
			var order_ids  = [ ];
			for ( var i = 0; i < count_rows; i++ ) {
				var row        = [ ], cols = rows[ i ].querySelectorAll( ".coolca-exportable" );
				var count_cols = cols.length;
				for ( var j = 0; j < count_cols; j++ ) {
					row.push( cols[ j ].innerText );
				}

				csv.push( row.join( ";" ) );

				order_ids.push( rows[ i ].getAttribute( 'data-order-id' ) );
			}

			// Download CSV file.
			downloadCSV( csv.join( "\n" ), filename );

			var dataToSend = {
				action: "coolca_action_mark_orders_as_exported",
				coolca_nonce: coolca_export_setting.nonce,
				orders: order_ids
			};

			jQuery.post(
				coolca_export_setting.ajax_url,
				dataToSend,
				function (data) {
					if (data.success) {

					} else {
						console.log( data );
					}
				}
			);

		}

		jQuery( "#cool-ca-export2csv" ).click(
			function( ) {
				exportTableToCSV( "coolca.csv" );
			}
		);

		function setBtnEvents( ) {
			jQuery( ".coolca-edit-span" ).off( "click" );
			jQuery( ".coolca-edit-span" ).click(
				function( ) {
					newVal = prompt( "", jQuery( this ).parent().parent().find( ".coolca-span" ).html() );
					jQuery( this ).parent().parent().find( ".coolca-span" ).html( newVal );
				}
			);

			jQuery( ".coolca-delete-btn" ).off( "click" );
			jQuery( ".coolca-delete-btn" ).click(
				function( ) {
					if ( confirm( "¿Está segur@ que desea eliminar la fila? " ) ) {
						jQuery( this ).closest( "div.coolca-table-row" ).remove();
					}
				}
			);

			jQuery( ".coolca-add-btn" ).off( "click" );
			jQuery( ".coolca-add-btn" ).click(
				function( ) {
					if ( confirm( "¿Está segur@ que desea duplicar la fila? " ) ) {
						jQuery( this ).closest( 'div.coolca-table-row' ).after( jQuery( this ).closest( 'div.coolca-table-row' ).clone() );
						setBtnEvents();
					}
				}
			);

		}

		setBtnEvents();
	}
);

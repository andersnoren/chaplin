( function( $ ) {

	/*	-----------------------------------------------------------------------------------------------
		Helper functions
	--------------------------------------------------------------------------------------------------- */

	/* Output AJAX errors -------------------------------- */

	function ajaxErrors( jqXHR, exception ) {
		var message = '';
		if ( jqXHR.status === 0 ) {
			message = 'Not connect.n Verify Network.';
		} else if ( jqXHR.status == 404 ) {
			message = 'Requested page not found. [404]';
		} else if ( jqXHR.status == 500 ) {
			message = 'Internal Server Error [500].';
		} else if ( exception === 'parsererror' ) {
			message = 'Requested JSON parse failed.';
		} else if ( exception === 'timeout' ) {
			message = 'Time out error.';
		} else if ( exception === 'abort' ) {
			message = 'Ajax request aborted.';
		} else {
			message = 'Uncaught Error.n' + jqXHR.responseText;
		}
		console.log( 'AJAX ERROR:' + message );
	}

	/*	-----------------------------------------------------------------------------------------------
		Multiple Checkboxes
		Add the values of the checked checkboxes to the hidden input
	--------------------------------------------------------------------------------------------------- */

	$( document ).on( 'change', '.customize-control-checkbox-multiple input[type="checkbox"]', function() {

		// Get the values of all of the checkboxes into a comma seperated variable
		checkbox_values = $( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(
			function() {
				return this.value;
			}
		).get().join( ',' );

		// If there are no values, make that explicit in the variable so we know whether the default output is needed
		if ( ! checkbox_values ) {
			checkbox_values = 'empty';
		}

		// Update the hidden input with the variable
		$( this ).parents( '.customize-control' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );

	} );

	/*	-----------------------------------------------------------------------------------------------
		Color Schemes
		Update the color pickers when a new color scheme is selected
	--------------------------------------------------------------------------------------------------- */

	$( document ).on( 'change', '#customize-control-chaplin_color_schemes_selector .chaplin-color-scheme-control input', function() {

		if ( $( this ).is( ':checked' ) ) {

			var colorScheme = this.value;

			$.ajax( {
			url: 	chaplin_ajax_get_color_scheme_colors.ajaxurl,
			type: 	'post',
			data: {
				action: 		'chaplin_ajax_get_color_scheme_colors',
				color_scheme:	colorScheme
			},
			success: function( result ) {

				// Get the list of settings to update, and their colors
				var colors = JSON.parse( result );

				// Loop over them
				for ( var color in colors ) {
					if ( ! colors.hasOwnProperty( color ) ) {
						continue;
					}

					var colorName = color,
						colorValue = colors[color];

					// Update the color settings
					wp.customize( colorName, function( colorSetting ) {
						colorSetting.set( colorValue );
					} );

				}

			},

			error: function( jqXHR, exception ) {
				ajaxErrors( jqXHR, exception );
			}
		} );

		}

	} );

} )( jQuery );

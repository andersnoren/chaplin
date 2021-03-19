<?php

/* ---------------------------------------------------------------------------------------------
   CUSTOM CSS CLASS
   Handle custom CSS output
------------------------------------------------------------------------------------------------ */

if ( ! class_exists( 'Chaplin_Custom_CSS' ) ) :
	class Chaplin_Custom_CSS {


		/*	-----------------------------------------------------------------------------------------------
			GENERATE CSS
		--------------------------------------------------------------------------------------------------- */

		public static function generate_css( $selector, $style, $value, $prefix = '', $suffix = '', $echo = false ) {

			$return = '';
			if ( ! $value ) {
				return;
			}
			$return = sprintf( '%s { %s: %s; }', $selector, $style, $prefix . $value . $suffix );
			if ( $echo ) {
				echo $return;
			}
			return $return;
		}


		/*	-----------------------------------------------------------------------------------------------
			HEX TO RGB
			Convert hex colors to RGB colors
		--------------------------------------------------------------------------------------------------- */

		public static function hex_to_rgb( $hex_color ) {

			$values = str_replace( '#', '', $hex_color );
			$rgb_color = array();
			switch ( strlen( $values ) ) {
				case 3 :
					list( $r, $g, $b ) = sscanf( $values, "%1s%1s%1s" );
					return [ hexdec( "$r$r" ), hexdec( "$g$g" ), hexdec( "$b$b" ) ];
				case 6 :
					return array_map( 'hexdec', sscanf( $values, "%2s%2s%2s" ) );
				default :
					return false;
			}

		}


		/*	-----------------------------------------------------------------------------------------------
			HEX TO P3
			Convert hex colors to the P3 color gamut
		--------------------------------------------------------------------------------------------------- */

		public static function hex_to_p3( $hex_color ) {

			$rgb_color = self::hex_to_rgb( $hex_color );

			return array(
				'red'	=> round( $rgb_color[0] / 255, 3 ),
				'green'	=> round( $rgb_color[1] / 255, 3 ),
				'blue'	=> round( $rgb_color[2] / 255, 3 ),
			);

		}


		/*	-----------------------------------------------------------------------------------------------
			FORMAT P3
			Format P3 colors
		--------------------------------------------------------------------------------------------------- */

		public static function format_p3( $p3_colors ) {
			return 'color( display-p3 ' . $p3_colors['red'] . ' ' . $p3_colors['green'] . ' ' . $p3_colors['blue'] . ' / 1 )';
		}


		/*	-----------------------------------------------------------------------------------------------
			MINIFY CSS
			Helper function for reducing the size of the line styles output by Chaplin.
			Based on a original script by @webgefrickel: https://gist.github.com/webgefrickel/3339063
		--------------------------------------------------------------------------------------------------- */

		public static function minify_css( $css ) {

			$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

			// Backup values within single or double quotes
			preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);

			for ( $i = 0; $i < count( $hit[1] ); $i++ ) {
				$css = str_replace( $hit[1][$i], '##########' . $i . '##########', $css );
			}

			// Remove traoling semicolon of selector's last property
			$css = preg_replace( '/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css );

			// Remove any whitespace between semicolon and property-name
			$css = preg_replace( '/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css );

			// Remove any whitespace surrounding property-colon
			$css = preg_replace( '/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css );

			// Remove any whitespace surrounding selector-comma
			$css = preg_replace( '/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css );

			// Remove any whitespace surrounding opening parenthesis
			$css = preg_replace( '/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css );

			// Remove any whitespace between numbers and units
			$css = preg_replace( '/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css );

			// Shorten zero-values
			$css = preg_replace( '/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css );

			// Constrain multiple whitespaces
			$css = preg_replace( '/\p{Zs}+/ims',' ', $css );

			// Remove newlines
			$css = str_replace( array( "\r\n", "\r", "\n" ), '', $css );

			// Restore backed up values within single or double quotes
			for ( $i = 0; $i < count( $hit[1] ); $i++ ) {
				$css = str_replace( '##########' . $i . '##########', $hit[1][$i], $css );
			}

			return $css;

		}

		/*	-----------------------------------------------------------------------------------------------
			GET CSS FOR CUSTOMIZER OPTIONS
			Build CSS reflecting colors, fonts and other options set in the Customizer settings, and return them for output

			@param		$type string	Whether to return CSS for 'front-end', 'block-editor', or 'classic-editor'
		--------------------------------------------------------------------------------------------------- */

		public static function get_customizer_css( $type = 'front-end' ) {

			$css = '';

			/* Font Options ------------------ */

			$body_font = 			esc_attr( get_theme_mod( 'chaplin_body_font', Chaplin_Google_Fonts::$default_body_font ) );
			$headings_font = 		esc_attr( get_theme_mod( 'chaplin_headings_font', Chaplin_Google_Fonts::$default_headings_font ) );
			$headings_weight =		esc_attr( get_theme_mod( 'chaplin_headings_weight' ) );
			$headings_case =		esc_attr( get_theme_mod( 'chaplin_headings_letter_case' ) );
			$headings_spacing =		get_theme_mod( 'chaplin_headings_letterspacing' ) ? str_replace( '_', '.', esc_attr( get_theme_mod( 'chaplin_headings_letterspacing' ) ) ) : ''; // Replace underscores with dots

			// Combine the chosen fonts with the appropriate fallback font stack
			if ( $body_font ) {
				$body_font_stack = 	Chaplin_Google_Fonts::get_font_fallbacks( $body_font, 'body' );
				$body_font = 		$body_font . ', '. $body_font_stack;
			}

			if ( $headings_font ) {
				$headings_font_stack = 	Chaplin_Google_Fonts::get_font_fallbacks( $headings_font, 'headings' );
				$headings_font = 		$headings_font . ', ' . $headings_font_stack;
			}

			/* Color Options ----------------- */

			$background = 			get_theme_mod( 'background_color' ) ? '#' . get_theme_mod( 'background_color' ) : false;
			$primary = 				get_theme_mod( 'chaplin_primary_text_color' );
			$headings = 			get_theme_mod( 'chaplin_headings_text_color' );
			$buttons_background = 	get_theme_mod( 'chaplin_buttons_background_color' );
			$buttons_text = 		get_theme_mod( 'chaplin_buttons_text_color' );
			$secondary = 			get_theme_mod( 'chaplin_secondary_text_color' );
			$accent = 				get_theme_mod( 'chaplin_accent_color' );
			$border = 				get_theme_mod( 'chaplin_border_color' );
			$light_background = 	get_theme_mod( 'chaplin_light_background_color' );
			$overlay_text = 		get_theme_mod( 'chaplin_cover_template_overlay_text_color' );

			// The default buttons background color is conditional.
			// If an accent color is set, the default is the accent color.
			// If an accent color is not set, the default is the default accent color.
			$default_accent = '#007c89';
			$buttons_background_default = ( $accent && $accent !== $default_accent ) ? $accent : $default_accent;

			/* Properties -------------------- */

			// Make the list of CSS properties filterable
			$properties = apply_filters( 'chaplin_css_properties', array(
				'body_font'				=> array(
					'default'				=> '',
					'value'					=> $body_font,
					'type'					=> 'font',
				),
				'headings_font'			=> array(
					'default'				=> '',
					'value'					=> $headings_font,
					'type'					=> 'font',
				),
				'headings_weight'		=> array(
					'default'				=> '700',
					'value'					=> $headings_weight,
					'type'					=> 'font',
				),
				'headings_case'			=> array(
					'default'				=> 'normal',
					'value'					=> $headings_case,
					'type'					=> 'font',
				),
				'headings_spacing'			=> array(
					'default'				=> 'normal',
					'value'					=> $headings_spacing,
					'suffix'				=> 'em',
					'type'					=> 'font',
				),
				'background'			=> array(
					'default'				=> '#ffffff',
					'value'					=> $background,
					'type'					=> 'color',
				),
				'primary'				=> array(
					'default'				=> '#1a1b1f',
					'value'					=> $primary,
					'type'					=> 'color',
				),
				'headings'				=> array(
					'default'				=> '#1a1b1f',
					'value'					=> $headings,
					'type'					=> 'color',
				),
				'buttons_background'	=> array(
					'default'				=> $buttons_background_default,
					'value'					=> $buttons_background,
					'type'					=> 'color',
				),
				'buttons_text'			=> array(
					'default'				=> $background,
					'value'					=> $buttons_text,
					'type'					=> 'color',
				),
				'secondary'				=> array(
					'default'				=> '#747579',
					'value'					=> $secondary,
					'type'					=> 'color',
				),
				'accent'				=> array(
					'default'				=> '#007c89',
					'value'					=> $accent,
					'type'					=> 'color',
				),
				'border'				=> array(
					'default'				=> '#e1e1e3',
					'value'					=> $border,
					'type'					=> 'color',
				),
				'light_background'		=> array(
					'default'				=> '#f1f1f3',
					'value'					=> $light_background,
					'type'					=> 'color',
				),
				'overlay_text'			=> array(
					'default'				=> '#ffffff',
					'value'					=> $overlay_text,
					'type'					=> 'color',
				),
			) );

			/* P3 Colors --------------------- */

			// Filter for whether to output P3 colors
			$output_p3 = apply_filters( 'chaplin_custom_css_output_p3_colors', true );

			// Default value
			$p3_value = '';

			// P3 media query opening and closing
			$p3_open =	'@supports ( color: color( display-p3 0 0 0 / 1 ) ) {';
			$p3_close = '}';

			/* CSS Variables ----------------- */

			// Filter for whether to output CSS variables
			$output_css_variables = apply_filters( 'chaplin_custom_css_output_variables', true );

			if ( $output_css_variables ) {

				$css_variables_string = '';

				foreach ( $properties as $name => $data ) {

					// Skip if we're missing a value, or if it's the same as the default
					if ( ! $data['value'] || $data['value'] == $data['default'] ) continue;

					$variable_name = '--' . str_replace( '_', '-', $name );

					if ( $data['type'] == 'color' ) {
						$variable_name .= '-color';
					}

					$variable_value = isset( $data['prefix'] ) ? $data['prefix'] : '';
					$variable_value .= $data['value'];
					$variable_value .= isset( $data['suffix'] ) ? $data['suffix'] : '';
					
					$css_variables_string .= $variable_name . ': ' . $variable_value . ';';

				}

				// Only output the wrapping scope if we have variables to output
				if ( $css_variables_string ) {
					$css .= ':root {' . $css_variables_string . '}';
				}

			}

			/* CSS Elements ------------------ */

			$css_elements = self::get_css_elements_array( $type );

			/* Loop over the CSS elements ---- */

			foreach ( $css_elements as $key => $definitions ) {

				$property = $properties[ $key ];

				// Only proceeed if the value is set and not the default one
				if ( ! $property['value'] || ( $property['default'] && $property['default'] == $property['value'] ) ) {
					continue;
				}

				// Get the P3 color, if they're enabled and we're outputting a color property
				if ( $output_p3 && isset( $property['type'] ) && $property['type'] == 'color' ) {
					$p3_value = self::format_p3( self::hex_to_p3( $property['value'] ) );
				}

				// Add the specified prefix and/or suffix to the value
				$value = $property['value'];
				$value = isset( $property['prefix'] ) && $property['prefix'] ? $property['prefix'] . $value : $value;
				$value = isset( $property['suffix'] ) && $property['suffix'] ? $value . $property['suffix'] : $value;

				foreach ( $definitions as $elements_property => $elements ) {
					
					// No elements, no output
					if ( empty( $elements ) ) {
						continue;
					}

					// Convert to array, to support multiple sets of elements for each property. This gets 
					// us around edgecases where browsers will break if it hits an urecognized selector. 
					// For example, vendor specific ::placeholder selectors need to be styled separately, 
					// or the browser will skip the entire CSS rule.
					if ( ! is_array( $elements ) ) $elements = array( $elements );

					foreach ( $elements as $elements_set ) {
						// Generate CSS for the elements
						$css .= self::generate_css( $elements_set, $elements_property, $value );

						// Generate P3 color CSS, if available and enabled
						if ( $output_p3 && isset( $property['type'] ) && $property['type'] == 'color' && $p3_value ) {
							$css .= $p3_open . self::generate_css( $elements_set, $elements_property, $p3_value ) . $p3_close;
						}
					}

				}
			}

			/* Minify the results ------------ */

			$css = self::minify_css( $css );

			/* Return the results ------------ */

			return $css;
			
		}

		/*	-----------------------------------------------------------------------------------------------
			GET THE CSS ELEMENTS
			Stores an array of all elements to apply custom CSS to.
			
			@param		$type string	Whether to return elements for 'front-end', 'block-editor', or 'classic-editor'
		--------------------------------------------------------------------------------------------------- */

		public static function get_css_elements_array( $type = 'front-end' ) {

			/* Helper Variables -------------- */

			// Type specific helper variables
			switch ( $type ) {
				case 'front-end' :
					$headings_targets = apply_filters( 'chaplin_headings_targets_front_end', 'h1, h2, h3, h4, h5, h6, .faux-heading' );
					$buttons_targets = apply_filters( 'chaplin_buttons_targets_front_end', 'button, .button, .faux-button, .wp-block-button__link, :root .wp-block-file a.wp-block-file__button, input[type=\'button\'], input[type=\'reset\'], input[type=\'submit\'], :root .woocommerce #respond input#submit, :root .woocommerce a.button, :root .woocommerce button.button, :root .woocommerce input.button' );
					break;
				case 'block-editor' : 
					$headings_targets = apply_filters( 'chaplin_headings_targets_block_editor', ':root .wp-block h1, :root h1.wp-block, :root .wp-block h2, :root h2.wp-block, :root .wp-block h3, :root h3.wp-block, :root .wp-block h4, :root h4.wp-block, :root .wp-block h5, :root h5.wp-block, :root .wp-block h6, :root h6.wp-block, .editor-post-title__block .editor-post-title__input, .editor-post-title__block .editor-post-title__input:focus' );
					$buttons_targets = apply_filters( 'chaplin_buttons_targets_block_editor', '.editor-styles-wrapper .faux-button, .wp-block-button__link, .editor-styles-wrapper :root .wp-block-file a.wp-block-file__button' );
					break;
				case 'classic-editor' : 
					$headings_targets = apply_filters( 'chaplin_headings_targets_classic_editor', 'body#tinymce.wp-editor h1, body#tinymce.wp-editor h2, body#tinymce.wp-editor h3, body#tinymce.wp-editor h4, body#tinymce.wp-editor h5, body#tinymce.wp-editor h6' );
					$buttons_targets = apply_filters( 'chaplin_buttons_targets_classic_editor', 'body#tinymce.wp-editor button, body#tinymce.wp-editor .button, body#tinymce.wp-editor .faux-button, body#tinymce.wp-editor input[type=\'button\'], body#tinymce.wp-editor input[type=\'reset\'], body#tinymce.wp-editor input[type=\'submit\']' );
					break;
			}

			/* Build the array --------------- */

			$elements = array(
				'front-end'			=> array(
					// Typography
					'body_font'				=> array(
						'font-family'			=> 'body, .ff-body',
					),
					'headings_font'			=> array(
						'font-family'			=> $headings_targets . ', .ff-headings',
					),
					'headings_weight'		=> array(
						'font-weight'			=> $headings_targets . ', .fw-headings',
					),
					'headings_case'			=> array(
						'text-transform'		=> $headings_targets . ', .tt-headings',
					),
					'headings_spacing'		=> array(
						'letter-spacing'		=> $headings_targets . ', .ls-headings',
					),
					// Colors
					'background'			=> array(
						'background-color'		=> '.bg-body-background, .bg-body-background-hover:hover, :root .has-background-background-color, body, :root body.custom-background, .menu-modal, .header-inner.is-sticky',
						'border-color'			=> '.border-color-body-background, .border-color-body-background-hover:hover',
						'border-top-color'		=> '#pagination .loader.same-primary-border-color',
						'color'					=> '.color-body-background, .color-body-background-hover:hover, :root .has-background-color, ' . $buttons_targets,
						'fill'					=> '.fill-children-body-background, .fill-children-body-background *'
					),
					'primary'				=> array(
						'background-color'		=> '.bg-primary, .bg-primary-hover:hover, :root .has-primary-background-color',
						'border-color'			=> '.border-color-primary, .border-color-primary-hover:hover',
						'color'					=> '.color-primary, .color-primary-hover:hover, :root .has-primary-color, body, .main-menu-alt ul li',
						'fill'					=> '.fill-children-primary, .fill-children-primary *',
					),
					'headings'				=> array(
						'color'					=> $headings_targets,
					),
					'secondary'				=> array(
						'background-color'		=> '.bg-secondary, .bg-secondary-hover:hover, :root .has-secondary-background-color',
						'border-color'			=> '.border-color-secondary, .border-color-secondary-hover:hover',
						'color'					=> array( 
													'.color-secondary, .color-secondary-hover:hover, :root .has-secondary-color, .wp-block-latest-comments time, .wp-block-latest-posts time', 
													'::-webkit-input-placeholder', 
													'::-moz-placeholder',
													':-moz-placeholder',
													':-ms-input-placeholder',
													'::placeholder' 
												),
						'fill'					=> '.fill-children-secondary, .fill-children-secondary *',
					),
					'accent'				=> array(
						'background-color'		=> '.bg-accent, .bg-accent-hover:hover, :root .has-accent-background-color, ' . $buttons_targets,
						'border-color'			=> '.border-color-accent, .border-color-accent-hover:hover, blockquote',
						'color'					=> '.color-accent, .color-accent-hover:hover, :root .has-accent-color, a, .is-style-outline .wp-block-button__link:not(.has-text-color), .wp-block-button__link.is-style-outline',
						'fill'					=> '.fill-children-accent, .fill-children-accent *',
					),
					'buttons_background'	=> array(
						'background-color'		=> $buttons_targets . ', :root .has-buttons-background-background-color',
						'color'					=> ':root .has-buttons-background-color, .is-style-outline .wp-block-button__link:not(.has-text-color), .wp-block-button__link.is-style-outline',
					),
					'buttons_text'			=> array(
						'background-color'		=> ':root .has-buttons-text-background-color',
						'color'					=> $buttons_targets . ', :root .has-buttons-text-color',
					),
					'border'				=> array(
						'background-color'		=> '.bg-border, .bg-border-hover:hover, :root .has-border-background-color, caption',
						'border-color'			=> '.border-color-border, .border-color-border-hover:hover, pre, th, td, input, textarea, fieldset, .main-menu li, button.sub-menu-toggle, .wp-block-latest-posts.is-grid li, .wp-block-calendar, .footer-menu li, .comment .comment, .post-navigation, .related-posts, .widget, .select2-container .select2-selection--single',
						'color'					=> '.color-border, .color-border-hover:hover, :root .has-border-color, hr',
						'fill'					=> '.fill-children-border, .fill-children-border *',
					),
					'light_background'		=> array(
						'background-color'		=> '.bg-light-background, .bg-light-background-hover:hover, :root .has-light-background-background-color, code, kbd, samp, table.is-style-stripes tr:nth-child( odd )',
						'border-color'			=> '.border-color-light-background, .border-color-light-background-hover:hover',
						'color'					=> '.color-light-background, .color-light-background-hover:hover, :root .has-light-background-color, .main-menu-alt ul',
						'fill'					=> '.fill-children-light-background, .fill-children-light-background *',
					),
					'overlay_text'			=> array(
						'color'					=> '.cover-header .entry-header, .overlay-header .header-inner:not(.is-sticky)',
					),
				),

				'block-editor'		=> array(
					// Typography
					'body_font'				=> array(
						'font-family'			=> '.editor-styles-wrapper > *, .editor-post-title__block .editor-post-title__input',
					),
					'headings_font'			=> array(
						'font-family'			=> $headings_targets,
					),
					'headings_weight'		=> array(
						'font-weight'			=> $headings_targets,
					),
					'headings_case'			=> array(
						'text-transform'		=> $headings_targets,
					),
					'headings_spacing'		=> array(
						'letter-spacing'		=> $headings_targets,
					),
					// Colors
					'background'			=> array(
						'background-color'		=> ':root .has-background-background-color, .editor-styles-wrapper, .editor-styles-wrapper > .editor-writing-flow, .editor-styles-wrapper > .editor-writing-flow > div',
						'color'					=> ':root .has-background-color, ' . $buttons_targets,
					),
					'primary'				=> array(
						'background-color'		=> ':root .has-primary-background-color',
						'color'					=> ':root .has-primary-color, .editor-styles-wrapper > *',
					),
					'headings'				=> array(
						'color'					=> $headings_targets,
					),
					'secondary'				=> array(
						'background-color'		=> ':root .has-secondary-background-color',
						'color'					=> ':root .has-secondary-color, .editor-styles-wrapper .wp-block-latest-comments time, .editor-styles-wrapper .wp-block-latest-posts time, .block-editor-default-block-appender textarea.block-editor-default-block-appender__content, .editor-post-title__block .editor-post-title__input::placeholder, .block-editor-default-block-appender textarea.block-editor-default-block-appender__content .editor-post-title__input::placeholder, .components-modal__frame input::placeholder, .components-modal__frame textarea::placeholder, .components-popover input::placeholder, .components-popover textarea::placeholder, .edit-post-header input::placeholder, .edit-post-header textarea::placeholder, .edit-post-sidebar input::placeholder, .edit-post-sidebar textarea::placeholder, .edit-post-text-editor input::placeholder, .edit-post-text-editor textarea::placeholder, .edit-post-visual-editor input::placeholder, .edit-post-visual-editor textarea::placeholder, .editor-post-publish-panel input::placeholder, .editor-post-publish-panel textarea::placeholder',
					),
					'accent'				=> array(
						'background-color'		=> ':root .has-accent-background-color, ' . $buttons_targets,
						'border-color'			=> '.editor-styles-wrapper blockquote, .editor-styles-wrapper .wp-block-quote',
						'color'					=> ':root .has-accent-color, .editor-styles-wrapper .editor-block-list__layout a, .editor-styles-wrapper .block-editor-block-list__layout a, .editor-styles-wrapper .wp-block-file .wp-block-file__textlink, .wp-block-button.is-style-outline, .wp-block-button__link.is-style-outline',
					),
					'buttons_background'	=> array(
						'background-color'		=> $buttons_targets,
						'color'					=> '.wp-block-button.is-style-outline, .wp-block-button__link.is-style-outline',
					),
					'buttons_text'			=> array(
						'color'					=> $buttons_targets,
					),
					'border'				=> array(
						'background-color'		=> ':root .has-border-background-color, .editor-styles-wrapper caption',
						'border-color'			=> '.editor-styles-wrapper hr, .editor-styles-wrapper pre, .editor-styles-wrapper th, .editor-styles-wrapper td, .editor-styles-wrapper fieldset, .editor-styles-wrapper .wp-block-latest-posts.is-grid li, .editor-styles-wrapper table.wp-block-table',
						'color'					=> ':root .has-border-color, .editor-styles-wrapper hr.wp-block-separator',
					),
					'light_background'		=> array(
						'background-color'		=> ':root .has-light-background-background-color, code, kbd, samp, table.is-style-stripes tbody tr:nth-child( odd ), .wp-block-table.is-style-stripes tbody tr:nth-child(odd), .wp-block-shortcode',
						'color'					=> ':root .has-light-background-color',
					),
				),

				'classic-editor'	=> array(
					// Typography
					'body_font'				=> array(
						'font-family'			=> 'body#tinymce.wp-editor',
					),
					'headings_font'			=> array(
						'font-family'			=> $headings_targets,
					),
					'headings_weight'		=> array(
						'font-weight'			=> $headings_targets,
					),
					'headings_case'			=> array(
						'text-transform'		=> $headings_targets,
					),
					'headings_spacing'		=> array(
						'letter-spacing'		=> $headings_targets,
					),
					// Colors
					'background'			=> array(
						'background-color'		=> 'body#tinymce.wp-editor',
						'color'					=> $buttons_targets,
					),
					'primary'				=> array(
						'color'					=> 'body#tinymce.wp-editor',
					),
					'headings'				=> array(
						'color'					=> $headings_targets,
					),
					'accent'				=> array(
						'background-color'		=> $buttons_targets,
						'border-color'			=> 'body#tinymce.wp-editor blockquote, body#tinymce.wp-editor .wp-block-quote',
						'color'					=> 'body#tinymce.wp-editor a',
					),
					'buttons_background'	=> array(
						'background-color'		=> $buttons_targets,
					),
					'buttons_text'			=> array(
						'color'					=> $buttons_targets,
					),
					'border'				=> array(
						'background-color'		=> 'body#tinymce.wp-editor caption',
						'border-color'			=> 'body#tinymce.wp-editor hr, body#tinymce.wp-editor pre, body#tinymce.wp-editor th, body#tinymce.wp-editor td, body#tinymce.wp-editor input, body#tinymce.wp-editor textarea, body#tinymce.wp-editor select, body#tinymce.wp-editor fieldset',
					),
					'light_background'		=> array(
						'background-color'		=> 'body#tinymce.wp-editor code, body#tinymce.wp-editor kbd, body#tinymce.wp-editor samp, body#tinymce.wp-editor table tbody > tr:nth-child(odd)',
					),
				),
			);

			/**
			 * Filter the array of elements and return the array
			 * 
			 * @param array Array of elements
			 * @param string The type of elements selected (front-end, block-editor or classic-editor)
			 */

			return apply_filters( 'chaplin_get_css_elements_array', $elements[$type], $type );

		}

	}
endif;

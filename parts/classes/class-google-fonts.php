<?php

/* ---------------------------------------------------------------------------------------------
   FONT CLASS
   Handle Google Fonts options and URL enqueue construction
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'Chaplin_Google_Fonts' ) ) :
	class Chaplin_Google_Fonts {


		/* --------------------------------------------------------------------
		   SET DEFAULT FONTS
		   -------------------------------------------------------------------- */

		public static $default_headings_font = 'Merriweather';
		public static $default_body_font = '';


		/* --------------------------------------------------------------------
		   GET GOOGLE FONTS ENQUEUE URL
		   Get the enqueue URL for the fonts selected
		   -------------------------------------------------------------------- */

		public static function get_google_fonts_url() {

			// Get the fonts set in the Customizer
			$headings_font = get_theme_mod( 'chaplin_headings_font', self::$default_headings_font );
			$body_font = get_theme_mod( 'chaplin_body_font', self::$default_body_font );

			// Get the languages set in the customizer
			$font_languages = get_theme_mod( 'chaplin_font_languages', array( 'latin' ) );

			// Check for web safe fonts, since they don't require enqueues
			$web_safe_fonts = array( 'web-safe-sans-serif', 'web-safe-serif' );

			// Prepare the font options for looping
			$font_options = array(
				'headings'		=> $headings_font,
				'body'			=> $body_font,
			);

			$google_fonts_families = array();

			// Loop over the fonts and get the enqueue values (name:styles)
			foreach ( $font_options as $font_option => $font_name ) {

				// Continue if the font name is empty, or matches one of the web safe fonts
				if ( ! $font_name || in_array( $font_name, self::$web_safe_fonts ) ) {
					continue;
				}

				// Get the font value (name|styles) from the font name
				$font_value = self::get_font_value_from_name( $font_name, $font_option );
				if ( $font_value && ! in_array( $font_value, $web_safe_fonts ) ) {
					$google_fonts_families[] = urlencode( $font_value );
				}

			}

			// If we have font families set, construct an URL from them and return it
			if ( $google_fonts_families ) {
				
				$google_fonts_url = '//fonts.googleapis.com/css?family=';
				foreach ( $google_fonts_families as $family ) {
					$google_fonts_url .= $family . '|';
				}
				$google_fonts_url = rtrim( $google_fonts_url, '|' );

				// If font languages are set, and they're not "empty", add them
				if ( $font_languages && ! in_array( $font_languages[0], array( 'empty', 'latin' ) ) ) {
					$font_languages_str = implode( ',', $font_languages );
					$google_fonts_url = add_query_arg( 'subset', $font_languages_str, $google_fonts_url );
				}

				return $google_fonts_url;
			}

			return;

		}


		/* --------------------------------------------------------------------
		   GET FONT VALUE
		   Get the font value from a font name
		   -------------------------------------------------------------------- */

		public static function get_font_value_from_name( $font_name, $font_option ) {

			// Different styles for body and headings
			if ( $font_option == 'body' ) {
				$styles = apply_filters( 'chaplin_google_font_body_styles', ':400,500,600,700,400italic,700italic' );
			} else if ( $font_option == 'headings' ) {
				$styles = ':400,700,400italic,700italic';

				// If the headings weight is set to a different weight than the default ones, add the weight to the styles
				$extra_weights = array( '100', '200', '300', '500', '600', '800', '900' );
				$headings_weight = get_theme_mod( 'chaplin_headings_weight' );

				if ( $headings_weight && in_array( $headings_weight, $extra_weights ) ) {
					$styles .=  ',' . $headings_weight . ',' . $headings_weight . 'italic';
				}

				$styles = apply_filters( 'chaplin_google_font_headings_styles', $styles );
			}

			return $font_name . $styles;

		}


		/* --------------------------------------------------------------------
		   GET FONT FALLBACKS
		   Get the font fallback stack
		   -------------------------------------------------------------------- */

		public static function get_font_fallbacks( $font ) {

			$sans_serif_stack = '-apple-system, BlinkMacSystemFont, \'Helvetica Neue\', Helvetica, sans-serif';
			$serif_stack = 'Georgia, \'Times New Roman\', Times, serif';
			$mono_stack = 'Menlo, monospace';

			// Start with the simple checks
			if ( strpos( $font, ' Mono' ) !== false ) {
				return $mono_stack;
			} else if ( strpos( $font, ' Sans' ) !== false ) {
				return $sans_serif_stack;
			} else if ( strpos( $font, ' Serif' ) !== false || strpos( $font, ' Slab' ) !== false ) {
				return $serif_stack;
			}

			// Continue with font-specific checks for common serif/mono font families without serif/mono in their name
			$serif_fonts = array( 'Merriweather', 'Literata', 'Slabo 27px', 'Playfair Display', 'Lora', 'Crimson Text', 'Libre Baskerville', 'Bitter', 'Arvo', 'EB Garamond', 'Domine', 'Amiri', 'Vollkorn', 'Noticia Text', 'Alegreya', 'Martel', 'Cardo', 'Neuton', 'Gentium Book Basic' );
			$mono_fonts = array( 'Inconsolata', 'Source Code Pro', 'Cousine', 'Nanum Gothic Coding', 'Anonymous Pro' );

			if ( in_array( $font, $serif_fonts ) ) {
				return $serif_stack;
			} else if ( in_array( $font, $mono_fonts ) ) {
				return $mono_stack;
			}

			// Finally, default to sans-serif
			return $sans_serif_stack;

		}
		

		/* --------------------------------------------------------------------
		   WEB SAFE FONTS
		   Store a list of web safe fonts that don't need Google Fonts
		   -------------------------------------------------------------------- */

		public static $web_safe_fonts = array( '--apple-system', 'Arial', 'Comic Sans', 'Courier New', 'Courier', 'Garamond', 'Georgia', 'Helvetica', 'Impact', 'Palatino', 'Times New Roman', 'Times', 'Trebuchet', 'Verdana' );


		/* --------------------------------------------------------------------
		   GET SUGGESTED FONTS
		   Get suggested fonts for autocomplete in the Customizer
		   -------------------------------------------------------------------- */

		public static function get_suggested_fonts( $font_option ) {

			$suggested_fonts = array(
				'Alegreya Sans',
				'Alegreya',
				'Archivo',
				'Arial',
				'Cabin',
				'Catamaran',
				'DM Sans',
				'EB Garamond',
				'Exo 2',
				'Fira Sans',
				'Georgia',
				'Helvetica',
				'IBM Plex Sans',
				'IBM Plex Serif',
				'Inter',
				'Josefin Sans',
				'Lato',
				'Libre Baskerville',
				'Libre Franklin',
				'Literata',
				'Lora',
				'Merriweather Sans',
				'Merriweather',
				'Montserrat',
				'Muli',
				'Neuton',
				'Noto Sans',
				'Noto Serif',
				'Nunito Sans',
				'Nunito',
				'Open Sans',
				'PT Sans Caption',
				'PT Sans',
				'PT Serif Caption',
				'PT Serif',
				'Playfair Display',
				'Quattrocento Sans',
				'Quattrocento',
				'Roboto Condensed',
				'Roboto Mono',
				'Roboto Slab',
				'Roboto',
				'Rubik',
				'Source Sans Pro',
				'Source Serif Pro',
				'Times New Roman',
				'Titillium Web',
				'Ubuntu',
				'Vollkorn',
				'Work Sans',
			);

			// Font families suitable for headings, but not body text
			if ( $font_option == 'headings' ) {
				$suggested_fonts = array_merge( $suggested_fonts, array(
					'Abril Fatface',
					'Anton',
					'Bitter',
					'Bree Serif',
					'Domine',
					'Fjalla One',
					'Josefin Slab',
					'Patua One',
					'Playfair Display SC',
					'Playfair Display',
					'Questrial',
					'Righteous',
					'Teko',
					'Zilla Slab',
				) );
			}

			return apply_filters( 'chaplin_suggested_fonts', $suggested_fonts, $font_option );
		}

	}
endif;

<?php

/* ---------------------------------------------------------------------------------------------
   FONT CLASS
   Handle Google Fonts options and URL enqueue construction
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'Chaplin_Google_Fonts' ) ) :
	class Chaplin_Google_Fonts {

		// Set default fonts
		public static $default_headings_font = 'Merriweather';
		public static $default_body_font = '';

		// Get the enqueue URL for the fonts selected
		public static function get_google_fonts_url() {

			// Get the fonts set in the Customizer
			$headings_font = get_theme_mod( 'chaplin_headings_font', self::$default_headings_font );
			$body_font = get_theme_mod( 'chaplin_body_font', self::$default_body_font );

			// Get the languages set in the customizer
			$font_languages = get_theme_mod( 'chaplin_font_languages' );

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

				// If font languages are set, and they're not "empty" or "latin" (= default in Google Fonts), add them
				if ( $font_languages && ! in_array( $font_languages[0], array( 'empty', 'latin' ) ) ) {
					$font_languages_str = implode( ',', $font_languages );
					$google_fonts_url = add_query_arg( 'subset', $font_languages_str, $google_fonts_url );
				}

				return $google_fonts_url;
			}

			return;

		}

		// Get the font value from a font name
		public static function get_font_value_from_name( $font_name, $font_option ) {

			// Different styles for body and headings
			if ( $font_option == 'body' ) {
				$styles = apply_filters( 'chaplin_google_font_body_styles', ':400,500,600,700,400italic,700italic' );
			} else {
				$styles = apply_filters( 'chaplin_google_font_headings_styles', ':400,700,400italic,700italic' );
			}

			return $font_name . $styles;
		}

		// Get the font fallback stack
		public static function get_font_fallbacks() {
			$sans_serif = '-apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif';
			$serif = 'Georgia, "Times New Roman", Times, serif';
			$mono = 'Menlo, monospace';

			return $sans_serif;
		}

		// Store a list of web safe fonts that don't need Google Fonts
		static $web_safe_fonts = array(
			'--apple-system',
			'Arial',
			'Comic Sans',
			'Courier New',
			'Courier',
			'Garamond',
			'Georgia',
			'Helvetica',
			'Impact',
			'Palatino',
			'Times New Roman',
			'Times',
			'Trebuchet',
			'Verdana',
		);

	}
endif;

?>
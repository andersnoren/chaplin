<?php

/* ---------------------------------------------------------------------------------------------
   FONT CLASS
   Handle Google Fonts options and URL enqueue construction
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'Chaplin_Google_Fonts' ) ) :
	class Chaplin_Google_Fonts {

		// Set default fonts
		static $default_body_font = 'web-safe-sans-serif';
		static $default_headings_font = 'merriweather';

		// Get the enqueue URL for the fonts selected
		public static function get_google_fonts_url() {

			// Get the fonts set in the Customizer
			$headings_font = get_theme_mod( 'chaplin_headings_font', Chaplin_Google_Fonts::$default_headings_font );
			$body_font = get_theme_mod( 'chaplin_body_font', Chaplin_Google_Fonts::$default_body_font );

			// Check for web safe fonts, since they don't require enqueues
			$web_safe_fonts = array( 'web-safe-sans-serif', 'web-safe-serif' );

			// Prepare the font options for looping
			$font_options = array(
				'headings'		=> $headings_font,
				'body'			=> $body_font,
			);

			$google_fonts_families = array();

			// Loop over the fonts and get the enqueue values (name:styles)
			foreach ( $font_options as $font_option => $font_slug ) {
				$font_value = self::get_font_value_from_slug( $font_slug, $font_option );
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
				return $google_fonts_url;
			}

			return;

		}
		
		// Return an array of Google Fonts values used by the customizer settings and validation
		public static function google_fonts_values() {
			$google_fonts_list = self::google_fonts_list();
			$font_values = array();

			foreach ( $google_fonts_list as $font_slug => $font_data ) {
				$font_values[$font_slug] = $font_data['name'];
			}

			return $font_values;

		}

		// Complete list of Google Fonts to make available, including fallback font stack and other details
		public static function google_fonts_list() {
			return array(
				'web-safe-sans-serif' => array(
					'name'			=> __( 'Web Safe Sans-Serif', 'chaplin' ),
					'fallback_type'	=> 'sans-serif',
					'styles'		=> 'web-safe-sans-serif',
				),
				'web-safe-serif' => array(
					'name'			=> __( 'Web Safe Serif', 'chaplin' ),
					'fallback_type'	=> 'serif',
					'styles'		=> 'web-safe-serif',
				),
				'arimo' => array(
					'name'			=> 'Arimo',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'arvo' => array(
					'name'			=> 'Arvo',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'bitter' => array(
					'name'			=> 'Bitter',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,700,400italic',
				),
				'cabin' => array(
					'name'			=> 'Cabin',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic',
				),
				'droid-sans' => array(
					'name'			=> 'Droid Sans',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700',
				),
				'droid-serif' => array(
					'name'			=> 'Droid Serif',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'fjalla-one' => array(
					'name'			=> 'Fjalla One',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400',
				),
				'francois-one' => array(
					'name'			=> 'Francois One',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400',
				),
				'josefin-sans' => array(
					'name'			=> 'Josefin Sans',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,300,600,700',
				),
				'lato' => array(
					'name'			=> 'Lato',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'libre-baskerville' => array(
					'name'			=> 'Libre Baskerville',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,400italic,700',
				),
				'lora' => array(
					'name'			=> 'Lora',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'merriweather' => array(
					'name'			=> 'Merriweather',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,300italic,300,400italic,700,700italic',
				),
				'montserrat' => array(
					'name'			=> 'Montserrat',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700',
				),
				'open-sans-condensed' => array(
					'name'			=> 'Open Sans Condensed',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> 'Open Sans Condensed:700,300italic,300',
				),
				'open-sans' => array(
					'name'			=> 'Open Sans',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400italic,700italic,400,700',
				),
				'oswald' => array(
					'name'			=> 'Oswald',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700',
				),
				'oxygen' => array(
					'name'			=> 'Oxygen',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,300,700',
				),
				'pt-sans-narrow' => array(
					'name'			=> 'PT Sans Narrow',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700',
				),
				'pt-sans' => array(
					'name'			=> 'PT Sans',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'pt-serif' => array(
					'name'			=> 'PT Serif',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,700',
				),
				'playfair-display' => array(
					'name'			=> 'Playfair Display',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,700,400italic',
				),
				'raleway' => array(
					'name'			=> 'Raleway',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700',
				),
				'roboto-condensed' => array(
					'name'			=> 'Roboto Condensed',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400italic,700italic,400,700',
				),
				'roboto-slab' => array(
					'name'			=> 'Roboto Slab',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,700',
				),
				'roboto' => array(
					'name'			=> 'Roboto',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,400italic,500,700,700italic',
				),
				'rokkitt' => array(
					'name'			=> 'Rokkitt',
					'fallback_type'	=> 'serif',
					'styles'		=> '400,500,600,700',
				),
				'source-sans-pro' => array(
					'name'			=> 'Source Sans Pro',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'ubuntu' => array(
					'name'			=> 'Ubuntu',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,700,400italic,700italic',
				),
				'work-sans' => array(
					'name'			=> 'Work Sans',
					'fallback_type'	=> 'sans-serif',
					'styles'		=> '400,500,600,700,400italic,700italic',
				),
				'other'		=> array(
					'name'			=> __( 'Other', 'chaplin' ),
				),
			);
		}

		// Get the font value from a font slug
		public static function get_font_value_from_slug( $font_slug, $font_option ) {
			if ( $font_slug == 'other' ) {
				$other_font_name = get_theme_mod( 'chaplin_' . $font_option . '_font_other' );
				$font_value = $other_font_name . ':400,500,600,700,400italic,700italic';
				return $font_value;
			}

			$complete_font_list = self::google_fonts_list();
			
			$font = $complete_font_list[$font_slug];
			$font_value = $font['name'] . ':' . $font['styles'];
			return $font_value;
		}

		// Get the font name from a font slug
		public static function get_font_name_from_slug( $font_slug, $font_option ) {
			if ( $font_slug == 'other' ) {
				$other_font_name = get_theme_mod( 'chaplin_' . $font_option . '_font_other' );
				return $other_font_name;
			}

			$complete_font_list = self::google_fonts_list();
			
			$font = $complete_font_list[$font_slug];
			return $font['name'];
		}

		// Get the front fallback stack from a font slug
		public static function get_font_fallbacks_from_slug( $font_slug ) {
			$sans_serif = '-apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, sans-serif';
			$serif = 'Georgia, "Times New Roman", Times, serif';
			$mono = 'Menlo, monospace';

			if ( $font_slug == 'other' ) {
				return $sans_serif;
			}

			$complete_font_list = self::google_fonts_list();
			
			$font_fallback_type = $complete_font_list[$font_slug]['fallback_type'];
			switch ( $font_fallback_type ) {
				case 'sans-serif' :
					return $sans_serif;
					break;
				case 'serif' :
					return $serif;
					break;
				case 'mono' :
					return $mono;
					break;
			}
		}

	}
endif;

?>
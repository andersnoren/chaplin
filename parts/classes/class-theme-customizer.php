<?php 

/* ---------------------------------------------------------------------------------------------
   CUSTOMIZER SETTINGS
   --------------------------------------------------------------------------------------------- */

if ( ! class_exists( 'Chaplin_Customize' ) ) :
	class Chaplin_Customize {

		public static function chaplin_register( $wp_customize ) {

			/* ------------------------------------------------------------------------
			 * Site Identity
			 * ------------------------------------------------------------------------ */

			/* 2X Header Logo ---------------- */

			$wp_customize->add_setting( 'chaplin_retina_logo', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
				'transport'			=> 'postMessage',
			) );

			$wp_customize->add_control( 'chaplin_retina_logo', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'title_tagline',
				'priority'		=> 10,
				'label' 		=> __( 'Retina logo', 'chaplin' ),
				'description' 	=> __( 'Scales the logo to half its uploaded size, making it sharp on high-res screens.', 'chaplin' ),
			) );

			/* ------------------------------------------------------------------------
			 * Colors
			 * ------------------------------------------------------------------------ */

			$chaplin_accent_color_options = self::chaplin_get_color_options();

			// Loop over the color options and add them to the customizer
			foreach ( $chaplin_accent_color_options as $color_option_name => $color_option ) {

				$wp_customize->add_setting( $color_option_name, array(
					'default' 			=> $color_option['default'],
					'type' 				=> 'theme_mod',
					'sanitize_callback' => 'sanitize_hex_color',
				) );

				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $color_option_name, array(
					'label' 		=> $color_option['label'],
					'section' 		=> 'colors',
					'settings' 		=> $color_option_name,
					'priority' 		=> 10,
				) ) );

			}

			// Update background color with postMessage, so inline CSS output is updated as well
			$wp_customize->get_setting( 'background_color' )->transport = 'refresh';

			/* ------------------------------------------------------------------------
			 * Fonts
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_fonts_options', array(
				'title' 		=> __( 'Fonts', 'chaplin' ),
				'priority' 		=> 40,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Specify which fonts to use. Chaplin supports all fonts on <a href="https://fonts.google.com" target="_blank">Google Fonts</a> and all <a href="https://www.w3schools.com/cssref/css_websafe_fonts.asp" target="_blank">web safe fonts</a>.', 'chaplin' ),
			) );

			/* Font Options ------------------ */

			$chaplin_font_options = apply_filters( 'chaplin_font_options', array(
				'chaplin_body_font' => array(
					'default'	=> '',
					'label'		=> __( 'Body Font', 'chaplin' ),
					'slug'		=> 'body'
				),
				'chaplin_headings_font' => array(
					'default'	=> 'Merriweather',
					'label'		=> __( 'Headings Font', 'chaplin' ),
					'slug'		=> 'headings'
				),
			) );

			// Loop over the font options and add them to the customizer
			foreach ( $chaplin_font_options as $font_option_name => $font_option ) {
				$wp_customize->add_setting( $font_option_name, array(
					'default' 			=> $font_option['default'],
					'sanitize_callback' => 'wp_filter_nohtml_kses',
					'type'				=> 'theme_mod',
				) );

				$wp_customize->add_control( $font_option_name, array(
					'type'			=> 'text',
					'label' 		=> $font_option['label'],
					'description'	=> self::chaplin_suggested_fonts_data_list( $font_option['slug'] ),
					'section' 		=> 'chaplin_fonts_options',
					'input_attrs' 	=> array(
						'autocapitalize'	=> 'off',
						'autocomplete'		=> 'off',
						'autocorrect'		=> 'off',
						'class'				=> 'font-suggestions',
						'list'  			=> 'chaplin-suggested-fonts-list-' . $font_option['slug'],
						'placeholder' 		=> __( 'Enter the font name', 'chaplin' ),
						'spellcheck'		=> 'false',
					),
				) );
			}

			/* Separator --------------------- */

			$wp_customize->add_setting( 'chaplin_fonts_separator_1', array(
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new Chaplin_Separator_Control( $wp_customize, 'chaplin_fonts_separator_1', array(
				'section'		=> 'chaplin_fonts_options',
			) ) );

			/* Headings Weight --------------- */

			$wp_customize->add_setting( 'chaplin_headings_weight', array(
				'default' 			=> '700',
				'sanitize_callback' => 'chaplin_sanitize_select',
			) );

			$wp_customize->add_control( 'chaplin_headings_weight', array(
				'label' 		=> __( 'Headings Weight', 'chaplin' ),
				'description'	=> __( 'Note: All fonts do not support all weights.', 'chaplin' ),
				'section' 		=> 'chaplin_fonts_options',
				'settings' 		=> 'chaplin_headings_weight',
				'type' 			=> 'select',
				'choices' 		=> array(
					'100' 			=> __( 'Thin (100)', 'chaplin' ),
					'200' 			=> __( 'Ultra Light (200)', 'chaplin' ),
					'300' 			=> __( 'Light (300)', 'chaplin' ),
					'400' 			=> __( 'Normal (400)', 'chaplin' ),
					'500' 			=> __( 'Medium (500)', 'chaplin' ),
					'600' 			=> __( 'Semi Bold (600)', 'chaplin' ),
					'700' 			=> __( 'Bold (700)', 'chaplin' ),
					'800' 			=> __( 'Extra Bold (800)', 'chaplin' ),
					'900' 			=> __( 'Black (900)', 'chaplin' ),
				),
			) );

			/* Headings Text Case ------------ */

			$wp_customize->add_setting( 'chaplin_headings_letter_case', array(
				'default' 			=> 'normal',
				'sanitize_callback' => 'chaplin_sanitize_select',
			) );

			$wp_customize->add_control( 'chaplin_headings_letter_case', array(
				'label' 		=> __( 'Headings Case', 'chaplin' ),
				'section' 		=> 'chaplin_fonts_options',
				'settings' 		=> 'chaplin_headings_letter_case',
				'type' 			=> 'select',
				'choices' 		=> array(
					'normal' 		=> __( 'Normal', 'chaplin' ),
					'uppercase' 	=> __( 'Uppercase', 'chaplin' ),
					'lowercase' 	=> __( 'Lowercase', 'chaplin' ),
				),
			) );

			/* Headings Letter Spacing ------- */

			$wp_customize->add_setting( 'chaplin_headings_letterspacing', array(
				'default' 			=> 'normal',
				'sanitize_callback' => 'chaplin_sanitize_select',
			) );

			$wp_customize->add_control( 'chaplin_headings_letterspacing', array(
				'label' 		=> __( 'Headings Letterspacing', 'chaplin' ),
				'section' 		=> 'chaplin_fonts_options',
				'settings' 		=> 'chaplin_headings_letterspacing',
				'type' 			=> 'select',
				'choices' 		=> array(
					'-0_3125' 		=> __( '-50%', 'chaplin' ),
					'-0_28125' 		=> __( '-45%', 'chaplin' ),
					'-0_25' 		=> __( '-40%', 'chaplin' ),
					'-0_21875' 		=> __( '-35%', 'chaplin' ),
					'-0_1875' 		=> __( '-30%', 'chaplin' ),
					'-0_15625' 		=> __( '-25%', 'chaplin' ),
					'-0_125' 		=> __( '-20%', 'chaplin' ),
					'-0_09375' 		=> __( '-15%', 'chaplin' ),
					'-0_0625' 		=> __( '-10%', 'chaplin' ),
					'-0_03125' 		=> __( '-5%', 'chaplin' ),
					'normal' 		=> __( 'Normal', 'chaplin' ),
					'0_03125' 		=> __( '5%', 'chaplin' ),
					'0_0625' 		=> __( '10%', 'chaplin' ),
					'0_09375' 		=> __( '15%', 'chaplin' ),
					'0_125' 		=> __( '20%', 'chaplin' ),
					'0_15625' 		=> __( '25%', 'chaplin' ),
					'0_1875' 		=> __( '30%', 'chaplin' ),
					'0_21875' 		=> __( '35%', 'chaplin' ),
					'0_25' 			=> __( '40%', 'chaplin' ),
					'0_28125' 		=> __( '45%', 'chaplin' ),
					'0_3125' 		=> __( '50%', 'chaplin' ),
				),
			) );

			/* Separator --------------------- */

			$wp_customize->add_setting( 'chaplin_fonts_separator_2', array(
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new Chaplin_Separator_Control( $wp_customize, 'chaplin_fonts_separator_2', array(
				'section'		=> 'chaplin_fonts_options',
			) ) );

			/* Languages --------------------- */

			$wp_customize->add_setting( 'chaplin_font_languages', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'latin' ),
				'sanitize_callback' => 'chaplin_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Chaplin_Customize_Control_Checkbox_Multiple( $wp_customize, 'chaplin_font_languages', array(
				'section' 		=> 'chaplin_fonts_options',
				'label'   		=> __( 'Languages', 'chaplin' ),
				'description'	=> __( 'Note: All fonts do not support all languages. Check Google Fonts to make sure.', 'chaplin' ),
				'choices' 		=> apply_filters( 'chaplin_font_languages', array(
					'latin'			=> __( 'Latin', 'chaplin' ),
					'latin-ext'		=> __( 'Latin Extended', 'chaplin' ),
					'cyrillic'		=> __( 'Cyrillic', 'chaplin' ),
					'cyrillic-ext'	=> __( 'Cyrillic Extended', 'chaplin' ),
					'greek'			=> __( 'Greek', 'chaplin' ),
					'greek-ext'		=> __( 'Greek Extended', 'chaplin' ),
					'vietnamese'	=> __( 'Vietnamese', 'chaplin' ),
				) ),
			) ) );


			/* ------------------------------------------------------------------------
			 * Fallback Image Options
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_image_options', array(
				'title' 		=> __( 'Images', 'chaplin' ),
				'priority' 		=> 40,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for images in Chaplin.', 'chaplin' ),
			) );

			// Activate low-resolution images setting
			$wp_customize->add_setting( 'chaplin_activate_low_resolution_images', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> false,
				'sanitize_callback' => 'chaplin_sanitize_checkbox'
			) );

			$wp_customize->add_control( 'chaplin_activate_low_resolution_images', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_image_options',
				'priority'		=> 5,
				'label' 		=> __( 'Use Low-Resolution Images', 'chaplin' ),
				'description'	=> __( 'Checking this will decrease load times, but also make images look less sharp on high-resolution screens.', 'chaplin' ),
			) );

			// Fallback image setting
			$wp_customize->add_setting( 'chaplin_fallback_image', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'absint'
			) );

			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'chaplin_fallback_image', array(
				'label'			=> __( 'Fallback Image', 'chaplin' ),
				'description'	=> __( 'The selected image will be used when a post is missing a featured image. A default fallback image included in the theme will be used if no image is set.', 'chaplin' ),
				'priority'		=> 10,
				'mime_type'		=> 'image',
				'section' 		=> 'chaplin_image_options',
			) ) );

			// Disable fallback image setting
			$wp_customize->add_setting( 'chaplin_disable_fallback_image', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> false,
				'sanitize_callback' => 'chaplin_sanitize_checkbox'
			) );

			$wp_customize->add_control( 'chaplin_disable_fallback_image', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_image_options',
				'priority'		=> 15,
				'label' 		=> __( 'Disable Fallback Image', 'chaplin' )
			) );

			/* ------------------------------------------------------------------------
			 * Site Header Options
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_site_header_options', array(
				'title' 		=> __( 'Site Header', 'chaplin' ),
				'priority' 		=> 40,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for the site header.', 'chaplin' ),
			) );

			/* Sticky Header ----------------- */

			$wp_customize->add_setting( 'chaplin_sticky_header', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> false,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_sticky_header', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_site_header_options',
				'priority'		=> 10,
				'label' 		=> __( 'Sticky Header', 'chaplin' ),
				'description' 	=> __( 'Stick the header to the top of the window when the visitor scrolls.', 'chaplin' ),
			) );

			/* Disable Header Search --------- */

			$wp_customize->add_setting( 'chaplin_disable_header_search', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> false,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_disable_header_search', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_site_header_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Search Button', 'chaplin' ),
				'description' 	=> __( 'Check to disable the search button in the header.', 'chaplin' ),
			) );

			/* Disable Menu Modal on Desktop - */

			$wp_customize->add_setting( 'chaplin_disable_menu_modal_on_desktop', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> false,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_disable_menu_modal_on_desktop', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_site_header_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Menu Modal on Desktop', 'chaplin' ),
				'description' 	=> __( 'Check to display a regular menu on desktop screens, instead of the search and menu toggles.', 'chaplin' ),
			) );

			/* ------------------------------------------------------------------------
			 * Posts
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_single_post_options', array(
				'title' 		=> __( 'Posts', 'chaplin' ),
				'priority' 		=> 41,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for what to display in the blog and on single posts.', 'chaplin' ),
			) );

			/* Enable Related Posts --------- */

			$wp_customize->add_setting( 'chaplin_enable_related_posts', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> true,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_enable_related_posts', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_single_post_options',
				'label' 		=> __( 'Show Related Posts', 'chaplin' ),
				'description' 	=> __( 'Check to show related posts on single posts.', 'chaplin' ),
			) );

			/* Enable Excerpts --------------- */

			$wp_customize->add_setting( 'chaplin_display_excerpts', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> false,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_display_excerpts', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_single_post_options',
				'label' 		=> __( 'Show Excerpts', 'chaplin' ),
				'description' 	=> __( 'Check to display excerpts in post previews.', 'chaplin' ),
			) );

			/* Separator --------------------- */

			$wp_customize->add_setting( 'chaplin_posts_separator_1', array(
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new Chaplin_Separator_Control( $wp_customize, 'chaplin_posts_separator_1', array(
				'section'		=> 'chaplin_single_post_options',
			) ) );

			/* Post Meta --------------------- */

			$post_meta_choices = array(
				'author'		=> __( 'Author', 'chaplin' ),
				'categories'	=> __( 'Categories', 'chaplin' ),
				'comments'		=> __( 'Comments', 'chaplin' ),
				'edit-link'		=> __( 'Edit link (for logged in users)', 'chaplin' ),
				'post-date'		=> __( 'Post date', 'chaplin' ),
				'sticky'		=> __( 'Sticky status', 'chaplin' ),
				'tags'			=> __( 'Tags', 'chaplin' ),
			);

			if ( post_type_exists( 'jetpack-portfolio' ) ) {
				if ( taxonomy_exists( 'jetpack-portfolio-type' ) ) {
					$post_meta_choices['jetpack-portfolio-type'] = __( 'Portfolio types', 'chaplin' );
				}
				if ( taxonomy_exists( 'jetpack-portfolio-tag' ) ) {
					$post_meta_choices['jetpack-portfolio-tag'] = __( 'Project tags', 'chaplin' );
				}
			}

			$post_meta_choices = apply_filters( 'chaplin_post_meta_choices_in_the_customizer', $post_meta_choices );

			// Post Meta Single Top Setting
			$wp_customize->add_setting( 'chaplin_post_meta_single_top', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'post-date', 'categories' ),
				'sanitize_callback' => 'chaplin_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Chaplin_Customize_Control_Checkbox_Multiple( $wp_customize, 'chaplin_post_meta_single_top', array(
				'section' 		=> 'chaplin_single_post_options',
				'label'   		=> __( 'Top Post Meta:', 'chaplin' ),
				'description'	=> __( 'Select post meta to display above the content.', 'chaplin' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			// Post Meta Single Bottom Setting
			$wp_customize->add_setting( 'chaplin_post_meta_single_bottom', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'tags' ),
				'sanitize_callback' => 'chaplin_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Chaplin_Customize_Control_Checkbox_Multiple( $wp_customize, 'chaplin_post_meta_single_bottom', array(
				'section' 		=> 'chaplin_single_post_options',
				'label'   		=> __( 'Bottom Post Meta:', 'chaplin' ),
				'description'	=> __( 'Select post meta to display below the content.', 'chaplin' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			// Post Meta Archive Setting
			$wp_customize->add_setting( 'chaplin_post_meta_archive', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'post-date' ),
				'sanitize_callback' => 'chaplin_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Chaplin_Customize_Control_Checkbox_Multiple( $wp_customize, 'chaplin_post_meta_archive', array(
				'section' 		=> 'chaplin_single_post_options',
				'label'   		=> __( 'Archive Post Meta:', 'chaplin' ),
				'description'	=> __( 'Select post meta to display on archive pages.', 'chaplin' ),
				'choices' 		=> $post_meta_choices,
			) ) );

			/* ------------------------------------------------------------------------
			 * Pagination Options
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_pagination_options', array(
				'title' 		=> __( 'Archive Pagination', 'chaplin' ),
				'priority' 		=> 45,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Choose which type of pagination to use on archive pages.', 'chaplin' ),
			) );

			/* Pagination Type Setting ----------------------------- */

			$wp_customize->add_setting( 'chaplin_pagination_type', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => 'button',
				'sanitize_callback' => 'chaplin_sanitize_radio',
			) );

			$wp_customize->add_control( 'chaplin_pagination_type', array(
				'type'			=> 'radio',
				'section' 		=> 'chaplin_pagination_options',
				'label'   		=> __( 'Pagination Type:', 'chaplin' ),
				'choices' 		=> array(
					'button'		=> __( 'Load more on button click', 'chaplin' ),
					'scroll'		=> __( 'Load more on scroll', 'chaplin' ),
					'links'			=> __( 'Previous and next page links', 'chaplin' ),
				),
			) );

			/* ------------------------------------------------------------------------
			 * Template: Cover Template
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_cover_template_options', array(
				'title' 		=> __( 'Cover Template', 'chaplin' ),
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for the "Cover Template" page template.', 'chaplin' ),
			) );

			/* Overlay Fixed Background ------ */

			$wp_customize->add_setting( 'chaplin_cover_template_fixed_background', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> true,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_cover_template_fixed_background', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_cover_template_options',
				'label' 		=> __( 'Fixed Background Image', 'chaplin' ),
				'description' 	=> __( 'Creates a parallax effect when the visitor scrolls.', 'chaplin' ),
			) );

			/* Overlay Fade Text ------------- */

			$wp_customize->add_setting( 'chaplin_cover_template_fade_text', array(
				'capability' 		=> 'edit_theme_options',
				'default'			=> true,
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_cover_template_fade_text', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_cover_template_options',
				'label' 		=> __( 'Fade Text On Scroll', 'chaplin' ),
				'description' 	=> __( 'Fade out the text in the header as the visitor scrolls down the page.', 'chaplin' ),
			) );

			/* Separator --------------------- */

			$wp_customize->add_setting( 'chaplin_cover_template_separator_1', array(
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new Chaplin_Separator_Control( $wp_customize, 'chaplin_cover_template_separator_1', array(
				'section'		=> 'chaplin_cover_template_options',
			) ) );

			/* Overlay Background Color ------ */

			$wp_customize->add_setting( 'chaplin_cover_template_overlay_background_color', array(
				'default' 			=> get_theme_mod( 'chaplin_accent_color', '#007C89' ),
				'type' 				=> 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'chaplin_cover_template_overlay_background_color', array(
				'label' 		=> __( 'Image Overlay Background Color', 'chaplin' ),
				'description'	=> __( 'The color used for the featured image overlay. Defaults to the accent color.', 'chaplin' ),
				'section' 		=> 'chaplin_cover_template_options',
				'settings' 		=> 'chaplin_cover_template_overlay_background_color',
			) ) );

			/* Overlay Text Color ------------ */

			$wp_customize->add_setting( 'chaplin_cover_template_overlay_text_color', array(
				'default' 			=> '#FFFFFF',
				'type' 				=> 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'chaplin_cover_template_overlay_text_color', array(
				'label' 		=> __( 'Image Overlay Text Color', 'chaplin' ),
				'description'	=> __( 'The color used for the text in the featured image overlay.', 'chaplin' ),
				'section' 		=> 'chaplin_cover_template_options',
				'settings' 		=> 'chaplin_cover_template_overlay_text_color',
			) ) );

			/* Overlay Blend Mode ------------ */

			$wp_customize->add_setting( 'chaplin_cover_template_overlay_blend_mode', array(
				'default' 			=> 'multiply',
				'sanitize_callback' => 'chaplin_sanitize_select',
			) );

			$wp_customize->add_control( 'chaplin_cover_template_overlay_blend_mode', array(
				'label' 		=> __( 'Image Overlay Blend Mode', 'chaplin' ),
				'description'	=> __( 'How the overlay color will blend with the image. Some browsers, like Internet Explorer and Edge, only support the "Normal" mode.', 'chaplin' ),
				'section' 		=> 'chaplin_cover_template_options',
				'settings' 		=> 'chaplin_cover_template_overlay_blend_mode',
				'type' 			=> 'select',
				'choices' 		=> array(
					'normal' 			=> __( 'Normal', 'chaplin' ),
					'multiply' 			=> __( 'Multiply', 'chaplin' ),
					'screen' 			=> __( 'Screen', 'chaplin' ),
					'overlay' 			=> __( 'Overlay', 'chaplin' ),
					'darken' 			=> __( 'Darken', 'chaplin' ),
					'lighten' 			=> __( 'Lighten', 'chaplin' ),
					'color-dodge' 		=> __( 'Color Dodge', 'chaplin' ),
					'color-burn' 		=> __( 'Color Burn', 'chaplin' ),
					'hard-light' 		=> __( 'Hard Light', 'chaplin' ),
					'soft-light' 		=> __( 'Soft Light', 'chaplin' ),
					'difference' 		=> __( 'Difference', 'chaplin' ),
					'exclusion' 		=> __( 'Exclusion', 'chaplin' ),
					'hue' 				=> __( 'Hue', 'chaplin' ),
					'saturation' 		=> __( 'Saturation', 'chaplin' ),
					'color' 			=> __( 'Color', 'chaplin' ),
					'luminosity' 		=> __( 'Luminosity', 'chaplin' ),
				),
			) );

			/* Overlay Color Opacity --------- */

			$wp_customize->add_setting( 'chaplin_cover_template_overlay_opacity', array(
				'default' 			=> '80',
				'sanitize_callback' => 'chaplin_sanitize_select',
			) );

			$wp_customize->add_control( 'chaplin_cover_template_overlay_opacity', array(
				'label' 		=> __( 'Image Overlay Opacity', 'chaplin' ),
				'description'	=> __( 'Make sure that the value is high enough that the text is readable.', 'chaplin' ),
				'section' 		=> 'chaplin_cover_template_options',
				'settings' 		=> 'chaplin_cover_template_overlay_opacity',
				'type' 			=> 'select',
				'choices' 		=> array(
					'0' 			=> __( '0%', 'chaplin' ),
					'10' 			=> __( '10%', 'chaplin' ),
					'20' 			=> __( '20%', 'chaplin' ),
					'30' 			=> __( '30%', 'chaplin' ),
					'40' 			=> __( '40%', 'chaplin' ),
					'50' 			=> __( '50%', 'chaplin' ),
					'60' 			=> __( '60%', 'chaplin' ),
					'70' 			=> __( '70%', 'chaplin' ),
					'80' 			=> __( '80%', 'chaplin' ),
					'90' 			=> __( '90%', 'chaplin' ),
					'100' 			=> __( '100%', 'chaplin' ),
				),
			) );


			/* Sanitation Functions ---------- */

			// Sanitize boolean for checkbox
			function chaplin_sanitize_checkbox( $checked ) {
				return ( ( isset( $checked ) && true == $checked ) ? true : false );
			}

			// Sanitize booleans for multiple checkboxes
			function chaplin_sanitize_multiple_checkboxes( $values ) {
				$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;
				return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
			}

			// Sanitize radio
			function chaplin_sanitize_radio( $input, $setting ) {
				$input = sanitize_key( $input );
				$choices = $setting->manager->get_control( $setting->id )->choices;
				return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
			}

			// Sanitize select
			function chaplin_sanitize_select( $input, $setting ) {
				$input = sanitize_key( $input );
				$choices = $setting->manager->get_control( $setting->id )->choices;
				return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
			}

		}

		// Return an array of suggested fonts
		public static function chaplin_suggested_fonts_data_list( $font_option ) {

			$suggested_fonts = Chaplin_Google_Fonts::get_suggested_fonts( $font_option );

			$list = '<datalist id="chaplin-suggested-fonts-list-' . esc_attr( $font_option ) . '">';
			foreach ( $suggested_fonts as $font ) {
				$list .= '<option value="' . esc_attr( $font ) . '">';
			}
			$list .= '</datalist>';

			return $list;
		}

		public static function chaplin_get_color_options() {
			return apply_filters( 'chaplin_accent_color_options', array(
				'chaplin_accent_color' => array(
					'default'	=> '#007C89',
					'label'		=> __( 'Accent Color', 'chaplin' ),
					'slug'		=> 'accent',
				),
				'chaplin_primary_text_color' => array(
					'default'	=> '#1A1B1F',
					'label'		=> __( 'Primary Text Color', 'chaplin' ),
					'slug'		=> 'primary',
				),
				'chaplin_headings_text_color' => array(
					'default'	=> '#1A1B1F',
					'label'		=> __( 'Headings Text Color', 'chaplin' ),
					'slug'		=> 'headings',
				),
				'chaplin_secondary_text_color' => array(
					'default'	=> '#747579',
					'label'		=> __( 'Secondary Text Color', 'chaplin' ),
					'slug'		=> 'secondary',
				),
				'chaplin_border_color' => array(
					'default'	=> '#E1E1E3',
					'label'		=> __( 'Border Color', 'chaplin' ),
					'slug'		=> 'border',
				),
				'chaplin_light_background_color' => array(
					'default'	=> '#F1F1F3',
					'label'		=> __( 'Light Background Color', 'chaplin' ),
					'slug'		=> 'light-background',
				),
			) );
		}

		// Initiate the customize controls js
		public static function chaplin_customize_controls() {
			wp_enqueue_script( 'chaplin-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array( 'jquery', 'customize-controls' ), '', true );
		}

	}

	// Setup the Theme Customizer settings and controls
	add_action( 'customize_register', array( 'Chaplin_Customize', 'chaplin_register' ) );

	// Enqueue customize controls javascript in Theme Customizer admin screen
	add_action( 'customize_controls_init', array( 'Chaplin_Customize', 'chaplin_customize_controls' ) );

endif;


/* ---------------------------------------------------------------------------------------------
   CUSTOM CONTROLS
   --------------------------------------------------------------------------------------------- */


if ( class_exists( 'WP_Customize_Control' ) ) :

	/* Separator Control ------------------------- */

	if ( ! class_exists( 'Chaplin_Separator_Control' ) ) :
		class Chaplin_Separator_Control extends WP_Customize_Control {

			public function render_content() {
				echo '<hr/>';
			}

		}
	endif;

endif; 
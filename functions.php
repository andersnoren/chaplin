<?php


/*	-----------------------------------------------------------------------------------------------
	THEME SUPPORTS
	Default setup, some features excluded
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_theme_support' ) ) :
    function chaplin_theme_support()  {

        // Automatic feed
		add_theme_support( 'automatic-feed-links' );

		// Custom background color
		add_theme_support( 'custom-background' );

		// Set content-width
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 580;
		}

		// Post thumbnails
		add_theme_support( 'post-thumbnails' );

		// Set post thumbnail size
		set_post_thumbnail_size( 1120, 9999 );

		// Add image sizes
		add_image_size( 'chaplin_preview_image_low_resolution', 540, 9999 );
		add_image_size( 'chaplin_preview_image_high_resolution', 1080, 9999 );
		add_image_size( 'chaplin_fullscreen', 1980, 9999 );

		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => 240,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );

		// Title tag
		add_theme_support( 'title-tag' );

		// HTML5 semantic markup
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		// Make the theme translation ready
		load_theme_textdomain( 'chaplin', get_template_directory() . '/languages' );

		// Alignwide and alignfull classes in the block editor
		add_theme_support( 'align-wide' );
        
    }
    add_action( 'after_setup_theme', 'chaplin_theme_support' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REQUIRED FILES
	Add required files
--------------------------------------------------------------------------------------------------- */

// Handle Google Fonts
require get_template_directory() . '/parts/classes/google-fonts.php';

// Handle SVG icons
require get_template_directory() . '/parts/classes/svg-icons.php';


/*	-----------------------------------------------------------------------------------------------
	REGISTER STYLES
	Register and enqueue CSS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_register_styles' ) ) :
	function chaplin_register_styles() {

		$theme_version = wp_get_theme()->get( 'Version' );
		$css_dependencies = array();

		// Retrieve and enqueue the URL for Google Fonts
		$google_fonts_url = Chaplin_Google_Fonts::get_google_fonts_url();

		if ( $google_fonts_url ) {
			wp_register_style( 'chaplin_google_fonts', $google_fonts_url, false, 1.0, 'all' );
			$css_dependencies[] = 'chaplin_google_fonts';
		}
		
		wp_enqueue_style( 'chaplin_style', get_template_directory_uri() . '/style.css', $css_dependencies, $theme_version );

	}
	add_action( 'wp_enqueue_scripts', 'chaplin_register_styles' );
endif;


/*	-----------------------------------------------------------------------------------------------
	DEQUEUE STYLES
	Dequeue the block library styles, as we provide our own
--------------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'chaplin_deregister_styles' ) ) :
	function chaplin_deregister_styles() {

		wp_dequeue_style( 'wp-block-library' );

	}
	add_action( 'wp_print_styles', 'chaplin_deregister_styles', 100 );
endif;


/*	-----------------------------------------------------------------------------------------------
	REGISTER SCRIPTS
	Register and enqueue JavaScript
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_register_scripts' ) ) :
	function chaplin_register_scripts() {

		$theme_version = wp_get_theme()->get( 'Version' );
		
		$js_dependencies = array( 'jquery', 'imagesloaded' );

		wp_enqueue_script( 'chaplin_construct', get_template_directory_uri() . '/assets/js/construct.js', $js_dependencies, $theme_version );

		// Setup AJAX
		$ajax_url = admin_url( 'admin-ajax.php' );

		// AJAX Load More
		wp_localize_script( 'chaplin_construct', 'chaplin_ajax_load_more', array(
			'ajaxurl'   => esc_url( $ajax_url ),
		) );

	}
	add_action( 'wp_enqueue_scripts', 'chaplin_register_scripts' );
endif;


/*	-----------------------------------------------------------------------------------------------
	MENUS
	Register navigational menus (wp_nav_menu)
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_menus' ) ) :
    function chaplin_menus() {

        // Register menus
        $locations = array(
            'main-menu' 	=> __( 'Main menu', 'chaplin' ),
            'footer-menu' 	=> __( 'Footer Menu', 'chaplin' ),
            'social-menu' 	=> __( 'Social Menu', 'chaplin' ),
		);
		
        register_nav_menus( $locations );

    }
    add_action( 'init', 'chaplin_menus' );
endif;


/*	-----------------------------------------------------------------------------------------------
	BODY CLASSES
	Conditional addition of classes to the body element
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_body_classes' ) ) :
	function chaplin_body_classes( $classes ) {

		global $post;

		// Determine type of infinite scroll
		$pagination_type = get_theme_mod( 'miyazaki_pagination_type' ) ? get_theme_mod( 'miyazaki_pagination_type' ) : 'button';
		switch ( $pagination_type ) {
			case 'button' :
				$classes[] = 'pagination-type-button';
				break;
			case 'scroll' :
				$classes[] = 'pagination-type-scroll';
				break;
			case 'links' :
				$classes[] = 'pagination-type-links';
				break;
		}

		// Check whether the current page should have an overlay header
		if ( is_page_template( 'template-cover.php' ) ) {
			$classes[] = 'overlay-header';
		}

		// Check for sticky header
		if ( true === true ) {
			$classes[] = 'has-sticky-header';
		}

		// Check for post thumbnail
		if ( is_singular() && has_post_thumbnail() ) {
			$classes[] = 'has-post-thumbnail';
		} elseif ( is_singular() ) {
			$classes[] = 'missing-post-thumbnail';
		}

		// Check whether we're in the customizer preview
		if ( is_customize_preview() ) {
			$classes[] = 'customizer-preview';
		}

		// Slim page template class names (class = name - file suffix)
		if ( is_page_template() ) {
			$classes[] = basename( get_page_template_slug(), '.php' );
		}

		return $classes;

	}
	add_action( 'body_class', 'chaplin_body_classes' );
endif;


/*	-----------------------------------------------------------------------------------------------
	NO-JS CLASS
	If we're missing JavaScript support, the HTML element will have a no-js class
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_no_js_class' ) ) :
	function chaplin_no_js_class() {

		?>
		<script>document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>
		<?php

	}
	add_action( 'wp_head', 'chaplin_no_js_class' );
endif;


/* ------------------------------------------------------------------------------------------------
   CUSTOM LOGO OUTPUT
   ------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'chaplin_the_custom_logo' ) ) :
	function chaplin_the_custom_logo( $logo_theme_mod = 'custom_logo' ) {

		echo chaplin_get_custom_logo( $logo_theme_mod );

	}
endif;

if ( ! function_exists( 'chaplin_get_custom_logo' ) ) :
	function chaplin_get_custom_logo( $logo_theme_mod = 'custom_logo' ) {

		// Get the attachment for the specified logo
		$logo_id = get_theme_mod( $logo_theme_mod );
		$logo = wp_get_attachment_image_src( $logo_id, 'full' );

		if ( ! $logo ) {
			return;
		}

		// For clarity
		$logo_url = esc_url( $logo[0] );
		$logo_width = esc_attr( $logo[1] );
		$logo_height = esc_attr( $logo[2] );

		// If the retina logo setting is active, reduce the width/height by half
		if ( get_theme_mod( 'chaplin_retina_logo' ) ) {
			$logo_width = floor( $logo_width / 2 );
			$logo_height = floor( $logo_height / 2 );
		}

		// CSS friendly class
		$logo_theme_mod_class = str_replace( '_', '-', $logo_theme_mod );

		// Record our output
		ob_start();

		?>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>" class="custom-logo-link <?php echo $logo_theme_mod_class; ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" width="<?php echo esc_attr( $logo_width ); ?>" height="<?php echo esc_attr( $logo_height ); ?>" />
		</a>

		<?php

		// Return our output
		return ob_get_clean();

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	GET PAGE ID FROM TEMPLATE
	Get the ID of the first occurance of a page using the specified page template

	@param		$template_name string	Page template file name for which to retrieve the post ID
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_id_from_template' ) ) :
	function chaplin_get_id_from_template( $template_name ) {

		// Query all pages with the specified page template
		$pages = get_posts( array(
			'meta_key'    	=> '_wp_page_template',
			'meta_value'  	=> $template_name,
			'post_status'   => 'publish',
			'post_type'		=> 'page'
		) );

		// Get the ID of the first occurance of that page
		if ( isset( $pages[0] ) ) {
			$id = $pages[0]->ID;
			return $id;
		}

		return false;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	ADD EXCERPT SUPPORT TO PAGES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_add_excerpt_support_to_pages' ) ) :
	function chaplin_add_excerpt_support_to_pages() {

		add_post_type_support( 'page', 'excerpt' );
		
	}
	add_action( 'init', 'chaplin_add_excerpt_support_to_pages' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE TITLE

	@param	$title string		The initial title
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_archive_title' ) ) :
	function chaplin_filter_archive_title( $title ) {

		// Use the blog page title on home
		$blog_page_id = get_option( 'page_for_posts' );
		if ( is_home() && $blog_page_id && get_the_title( $blog_page_id ) ) {
			$title = get_the_title( $blog_page_id );
		}

		return $title;
		
	}
	add_filter( 'get_the_archive_title', 'chaplin_filter_archive_title' );
endif;

/*	-----------------------------------------------------------------------------------------------
	FILTER ARCHIVE DESCRIPTION

	@param	$description string		The initial description
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_archive_description' ) ) :
	function chaplin_filter_archive_description( $description ) {

		// Use the blog page manual excerpt on home
		$blog_page_id = get_option( 'page_for_posts' );
		if ( is_home() && $blog_page_id && has_excerpt( $blog_page_id ) ) {
			$description = get_the_excerpt( $blog_page_id );
		}

		return $description;
		
	}
	add_filter( 'get_the_archive_description', 'chaplin_filter_archive_description' );
endif;

	
/* ---------------------------------------------------------------------------------------------
   GET FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_fallback_image_url' ) ) :
	function chaplin_get_fallback_image_url() {

		$disable_fallback_image = get_theme_mod( 'chaplin_disable_fallback_image' );

		if ( $disable_fallback_image ) {
			return '';
		}

		$fallback_image_id = get_theme_mod( 'chaplin_fallback_image' );

		if ( $fallback_image_id ) {
			$fallback_image = wp_get_attachment_image_src( $fallback_image_id, 'full' );
		}

		$fallback_image_url = isset( $fallback_image ) ? esc_url( $fallback_image[0] ) : get_template_directory_uri() . '/assets/images/default-fallback-image.png';

		return $fallback_image_url;

	}
endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_fallback_image' ) ) :
	function chaplin_the_fallback_image() {

		$fallback_image_url = chaplin_get_fallback_image_url();

		if ( ! $fallback_image_url ) {
			return;
		}

		echo '<img src="' . $fallback_image_url . '" class="fallback-featured-image" />';

	}
endif;


/* ---------------------------------------------------------------------------------------------
   GET THE IMAGE SIZE OF PREVIEWS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_preview_image_size' ) ) :
	function chaplin_get_preview_image_size() {

		// If the grid is set to one column, use the post thumbnail size
		$max_columns = get_theme_mod( 'chaplin_grid_max_columns' ) ? get_theme_mod( 'chaplin_grid_max_columns' ) : 3;
		if ( $max_columns === 1 ) {
			return 'post-thumbnail';
		}

		// Check if low-resolution images are activated in the customizer
		$low_res_images = get_theme_mod( 'chaplin_activate_low_resolution_images' );

		// If they are, we're using the low resolution image size
		if ( $low_res_images ) {
			return 'chaplin_preview_image_low_resolution';

		// If not, we're using the high resolution image size
		} else {
			return 'chaplin_preview_image_high_resolution';
		}

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT AND GET THEME SVG
	Output and get the SVG markup for a icon in the Chaplin_SVG_Icons class
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_theme_svg' ) ) :
	function chaplin_the_theme_svg( $svg_name, $color = '' ) {

		echo chaplin_get_theme_svg( $svg_name, $color );

	}
endif;

if ( ! function_exists( 'chaplin_get_theme_svg' ) ) :
	function chaplin_get_theme_svg( $svg_name, $color = '' ) {

		$svg = Chaplin_SVG_Icons::get_svg( $svg_name, $color );

		if ( ! $svg ) {
			return false;
		}

		return $svg;

	}
endif;


/* ------------------------------------------------------------------------------------------------
   OUTPUT & GET POST META
   If it's a single post, output the post meta values specified in the Customizer settings.

   @param	$post_id int		The ID of the post for which the post meta should be output
   @param	$location string	Which post meta location to output – single or preview
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_post_meta' ) ) :
	function chaplin_the_post_meta( $post_id = null, $location = 'single-top' ) {

		echo chaplin_get_post_meta( $post_id, $location );

	}
endif;

if ( ! function_exists( 'chaplin_get_post_meta' ) ) :
	function chaplin_get_post_meta( $post_id = null, $location = 'single' ) {

		// Require post ID
		if ( ! $post_id ) {
			return;
		}

		$page_template = get_page_template_slug( $post_id );

		// Check that the post type should be able to output post meta
		$allowed_post_types = apply_filters( 'chaplin_allowed_post_types_for_meta_output', array( 'post' ) );
		if ( ! in_array( get_post_type( $post_id ), $allowed_post_types ) ) {
			return;
		}

		$post_meta_wrapper_classes = '';
		$post_meta_classes = '';

		// Get the post meta settings for the location specified
		if ( 'single-top' === $location ) {

			$post_meta = get_theme_mod( 'chaplin_post_meta_single_top' );
			$post_meta_wrapper_classes = ' post-meta-single post-meta-single-top';

			// Empty = use a fallback
			if ( ! $post_meta ) {
				$post_meta = array(
					'post-date',
					'categories',
				);
			}

		} elseif ( 'single-bottom' === $location ) {

			$post_meta = get_theme_mod( 'chaplin_post_meta_single_bottom' );
			$post_meta_wrapper_classes = ' post-meta-single post-meta-single-bottom';

		}

		// If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output
		if ( $post_meta && ! in_array( 'empty', $post_meta ) ) :

			// Make sure the right color is used for the post meta
			if ( $page_template == 'template-cover.php' && $location == 'single-top' ) {
				$post_meta_classes .= ' color-inherit';
			} else {
				$post_meta_classes .= ' color-accent';
			}

			// Make sure we don't output an empty container
			$has_meta = false;

			global $post;
			$post = get_post( $post_id );
			setup_postdata( $post );

			ob_start();

			?>

			<div class="post-meta-wrapper<?php echo $post_meta_wrapper_classes; ?>">

				<ul class="post-meta<?php echo $post_meta_classes; ?>">

					<?php

					// Post date
					if ( in_array( 'post-date', $post_meta ) ) : 
						$has_meta = true;
						?>
						<li class="post-date">
							<a class="meta-wrapper" href="<?php the_permalink(); ?>">
								<span class="meta-icon">
									<span class="screen-reader-text"><?php _e( 'Post date', 'chaplin' ); ?></span>
									<?php chaplin_the_theme_svg( 'calendar' ); ?>
								</span>
								<span class="meta-text">
									<?php the_time( get_option( 'date_format' ) ); ?>
								</span>
							</a>
						</li>
					<?php endif;

					// Author
					if ( in_array( 'author', $post_meta ) ) : 
						$has_meta = true;
						?>
						<li class="post-author meta-wrapper">
							<span class="meta-icon">
								<span class="screen-reader-text"><?php _e( 'Post author', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'user' ); ?>
							</span>
							<span class="meta-text">
								<?php printf( _x( 'By %s', '%s = author name', 'chaplin' ), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author_meta( 'nickname' ) . '</a>' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Categories
					if ( in_array( 'categories', $post_meta ) ) : 
						$has_meta = true;
						?>
						<li class="post-categories meta-wrapper">
							<span class="meta-icon">
								<span class="screen-reader-text"><?php _e( 'Post categories', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'folder' ); ?>
							</span>
							<span class="meta-text">
								<?php _e( 'In', 'chaplin' ); ?> <?php the_category( ', ' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Tags
					if ( in_array( 'tags', $post_meta ) && has_tag() ) : 
						$has_meta = true;
						?>
						<li class="post-tags meta-wrapper">
							<span class="meta-icon">
								<span class="screen-reader-text"><?php _e( 'Tags', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'tag' ); ?>
							</span>
							<span class="meta-text">
								<?php the_tags( '', ', ', '' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Comments link
					if ( in_array( 'comments', $post_meta ) && comments_open() ) : 
						$has_meta = true; 
						?>
						<li class="post-comment-link meta-wrapper">
							<span class="meta-icon">
								<?php chaplin_the_theme_svg( 'comment' ); ?>
							</span>
							<span class="meta-text">
								<?php comments_popup_link(); ?>
							</span>
						</li>
						<?php
					endif;

					// Sticky
					if ( in_array( 'sticky', $post_meta ) && is_sticky() ) : 
						$has_meta = true; 
						?>
						<li class="post-sticky meta-wrapper">
							<span class="meta-icon">
								<?php chaplin_the_theme_svg( 'bookmark' ); ?>
							</span>
							<span class="meta-text">
								<?php _e( 'Sticky post', 'chaplin' ); ?>
							</span>
						</li>
					<?php endif;

					// Edit link
					if ( in_array( 'edit-link', $post_meta ) && current_user_can( 'edit_post', get_the_ID() ) ) : 
						$has_meta = true; 
						?>
						<li class="post-edit">
							
							<?php
							// Make sure we display something in the customizer, as edit_post_link() doesn't output anything there
							if ( is_customize_preview() ) : ?>
								<a href="#" class="meta-wrapper">
									<span class="meta-icon">
										<?php chaplin_the_theme_svg( 'edit' ); ?>
									</span>
									<span class="meta-text">
										<?php _e( 'Edit', 'chaplin' ); ?>
									</span>
								</a>
							<?php else : ?>
								<a href="<?php echo esc_url( get_edit_post_link() ); ?>" class="meta-wrapper">
									<span class="meta-icon">
										<?php chaplin_the_theme_svg( 'edit' ); ?>
									</span>
									<span class="meta-text">
										<?php _e( 'Edit', 'chaplin' ); ?>
									</span>
								</a>
							<?php endif; ?>

						</li>
					<?php endif; ?>

				</ul><!-- .post-meta -->

			</div><!-- .post-meta-wrapper -->

			<?php

			wp_reset_postdata();

			$meta_output = ob_get_clean();

			// If there is meta to output, return the markup
			if ( $has_meta && $meta_output ) {
				return $meta_output;
			}

		endif;

		// If we've reached this point, there's nothing to return, so we return nothing
		return;

	}
endif;


/* 	-----------------------------------------------------------------------------------------------
	FILTER COMMENT TEXT TO OUTPUT "BY POST AUTHOR" TEXT
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_loading_indicator' ) ) :
	function chaplin_filter_comment_text( $comment_text, $comment, $args ) {

		$comment_author_user_id = $comment->user_id;
		$post_author_user_id = get_post_field( 'post_author', $comment->comment_post_ID );

		if ( $comment_author_user_id === $post_author_user_id ) {
			$comment_text .= '<div class="by-post-author-wrapper">' . __( 'By Post Author', 'chaplin' ) . '</div>';
		}

		return $comment_text;

	}
	add_filter( 'comment_text', 'chaplin_filter_comment_text', 10, 3 );
endif;


/* 	-----------------------------------------------------------------------------------------------
	ADD A SUB NAV TOGGLE TO THE MAIN MENU
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_add_sub_toggles_to_main_menu' ) ) :
	function chaplin_add_sub_toggles_to_main_menu( $args, $item, $depth ) {

		if ( $args->theme_location == 'main-menu' ) {

			// Wrap the menu item link contents in a div, used for positioning
			$args->before = '<div class="ancestor-wrapper">';
			$args->after = '';

			// Add a toggle to items with children
			if ( in_array( 'menu-item-has-children', $item->classes ) ) {

				$toggle_target_string = '.menu-item-' . $item->ID . ' > .sub-menu';

				// Add the sub menu toggle
				$args->after .= '<button class="toggle sub-menu-toggle fill-children-current-color" data-toggle-target="' . $toggle_target_string . '" data-toggle-type="slidetoggle" data-toggle-duration="250"><span class="screen-reader-text">' . __( 'Visa undersidor', 'chaplin' ) . '</span>' . chaplin_get_theme_svg( 'chevron-down' ) . '</button>';

			}

			// Close the wrapper
			$args->after .= '</div><!-- .ancestor-wrapper -->';

		}

		return $args;

	}
	add_filter( 'nav_menu_item_args', 'chaplin_add_sub_toggles_to_main_menu', 10, 3 );
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT LOADING INDICATOR
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_loading_indicator' ) ) :

	function chaplin_loading_indicator() {
		echo '<div class="loader border-color-border"></div>';
	}

endif;


/*	-----------------------------------------------------------------------------------------------
	AJAX LOAD MORE
	Called in construct.js when the user has clicked the load more button
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_ajax_load_more' ) ) :
	function chaplin_ajax_load_more() {

		$query_args = json_decode( wp_unslash( $_POST['json_data'] ), true );

		$ajax_query = new WP_Query( $query_args );

		// Determine which preview to use based on the post_type
		$post_type = $ajax_query->get( 'post_type' );

		// Default to the "post" post type for previews
		if ( ! $post_type || is_array( $post_type ) ) {
			$post_type = 'post';
		}

		if ( $ajax_query->have_posts() ) :

			while ( $ajax_query->have_posts() ) : $ajax_query->the_post();

				?>

				<div class="grid-item">

					<?php get_template_part( 'parts/preview', $post_type ); ?>

				</div><!-- .grid-item -->

				<?php

			endwhile;

		endif;

		wp_die();

	}
	add_action( 'wp_ajax_nopriv_chaplin_ajax_load_more', 'chaplin_ajax_load_more' );
	add_action( 'wp_ajax_chaplin_ajax_load_more', 'chaplin_ajax_load_more' );
endif;


/*	-----------------------------------------------------------------------------------------------
	EDITOR STYLES FOR THE BLOCK EDITOR
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_block_editor_styles' ) ) :
	function chaplin_block_editor_styles() {

		// Enqueue the editor styles
		wp_enqueue_style( 'chaplin-block-editor-styles', get_theme_file_uri( '/editor-style-block-editor.css' ), array(), wp_get_theme()->get( 'Version' ), 'all' );

	}
	add_action( 'enqueue_block_editor_assets', 'chaplin_block_editor_styles', 1 );
endif;


/*	-----------------------------------------------------------------------------------------------
	EDITOR STYLES FOR THE BLOCK EDITOR
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_generate_css' ) ) :
	function chaplin_generate_css( $selector, $style, $value, $prefix = '', $suffix = '', $echo = true ) {
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
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT CSS DEPENDENT ON CUSTOMIZER OPTIONS
	Retrieve the custom colors and fonts set in the Customizer, and output CSS accordingly
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_customizer_css_output' ) ) :
	function chaplin_customizer_css_output() {

		/* Get variables --------------------- */

		$background = 			get_theme_mod( 'background_color' ) ? '#' . get_theme_mod( 'background_color' ) : false;
		$primary = 				get_theme_mod( 'chaplin_primary_text_color' );
		$secondary = 			get_theme_mod( 'chaplin_secondary_text_color' );
		$accent = 				get_theme_mod( 'chaplin_accent_color' );
		$border = 				get_theme_mod( 'chaplin_border_color' );
		$light_background = 	get_theme_mod( 'chaplin_light_background_color' );

		$body_font = 			get_theme_mod( 'chaplin_body_font', Chaplin_Google_Fonts::$default_body_font );
		$headings_font = 		get_theme_mod( 'chaplin_headings_font', Chaplin_Google_Fonts::$default_headings_font );

		// Output CSS for headings if they aren't the defaults
		if ( $body_font ) {
			$body_font_stack = 	Chaplin_Google_Fonts::get_font_fallbacks( $body_font, 'body' );
			$body_font = 		$body_font . ', '. $body_font_stack;
		}

		if ( $headings_font ) {
			$headings_font_stack = 	Chaplin_Google_Fonts::get_font_fallbacks( $headings_font, 'headings' );
			$headings_font = 		$headings_font . ', ' . $headings_font_stack;
		}
		
		ob_start();

		/* Colors ---------------------------- */

		// Background color
		if ( $background ) : 
			chaplin_generate_css( 'button, .button, .faux-button, .wp-block-button__link, .wp-block-file__button, input[type="button"], input[type="reset"], input[type="submit"]', 'color', $background );

			chaplin_generate_css( '.color-body-background, .color-body-background-hover:hover', 'color', $background );
			chaplin_generate_css( '.bg-body-background, .bg-body-background-hover:hover', 'background-color', $background );
			chaplin_generate_css( '.border-color-body-background, .border-color-body-background-hover:hover', 'border-color', $background );
			chaplin_generate_css( '.fill-children-body-background, .fill-children-body-background *', 'fill', $background );
		endif;

		// Primary color
		if ( $primary ) : 
			chaplin_generate_css( 'select', 'background-image', 'url( \'data:image/svg+xml;utf8,' . chaplin_get_theme_svg( 'chevron-down', $primary ) . '\');' );

			chaplin_generate_css( 'body', 'color', $primary );
			chaplin_generate_css( 'button, .button, .faux-button, .wp-block-button__link, .wp-block-file__button, input[type="button"], input[type="reset"], input[type="submit"]', 'background-color', $primary );

			chaplin_generate_css( '.color-primary, .color-primary-hover:hover', 'color', $primary );
			chaplin_generate_css( '.bg-primary, .bg-primary-hover:hover', 'background-color', $primary );
			chaplin_generate_css( '.border-color-primary, .border-color-primary-hover:hover', 'border-color', $primary );
			chaplin_generate_css( '.fill-children-primary, .fill-children-primary *', 'fill', $primary );
		endif;

		// Secondary color
		if ( $secondary ) :
			chaplin_generate_css( '.color-secondary, .color-secondary-hover:hover', 'color', $secondary );
			chaplin_generate_css( '.bg-secondary, .bg-secondary-hover:hover', 'background-color', $secondary );
			chaplin_generate_css( '.border-color-secondary, .border-color-secondary-hover:hover', 'border-color', $secondary );
			chaplin_generate_css( '.fill-children-secondary, .fill-children-secondary *', 'fill', $secondary );
		endif;

		// Accent color
		if ( $accent ) : 
			chaplin_generate_css( 'a', 'color', $accent );
			chaplin_generate_css( 'blockquote', 'border-color', $accent );

			chaplin_generate_css( '.color-accent, .color-accent-hover:hover', 'color', $accent );
			chaplin_generate_css( '.bg-accent, .bg-accent-hover:hover', 'background-color', $accent );
			chaplin_generate_css( '.border-color-accent, .border-color-accent-hover:hover', 'border-color', $accent );
			chaplin_generate_css( '.fill-children-accent, .fill-children-accent *', 'fill', $accent );
		endif;
		
		// Border color
		if ( $border ) : 
			chaplin_generate_css( 'hr, pre, th, td, input, textarea, select', 'border-color', $border );
			chaplin_generate_css( 'caption', 'background', $border );

			chaplin_generate_css( '.main-menu li', 'border-color', $border );
			chaplin_generate_css( 'button.sub-menu-toggle', 'border-color', $border );
			chaplin_generate_css( '.wp-block-latest-posts.is-grid li', 'border-color', $border );
			chaplin_generate_css( '.footer-menu li', 'border-color', $border );

			chaplin_generate_css( '.color-border, .color-border-hover:hover', 'color', $border );
			chaplin_generate_css( '.bg-border, .bg-border-hover:hover', 'background-color', $border );
			chaplin_generate_css( '.border-color-border, .border-color-border-hover:hover', 'border-color', $border );
			chaplin_generate_css( '.fill-children-border, .fill-children-border *', 'fill', $border );
		endif;

		// Light background color
		if ( $light_background ) : 
			chaplin_generate_css( 'code, kbd, samp', 'background-color', $light_background );
			chaplin_generate_css( 'table.is-style-stripes tr:nth-child( odd )', 'background-color', $light_background );

			chaplin_generate_css( '.color-light-background, .color-light-background-hover:hover', 'color', $light_background );
			chaplin_generate_css( '.bg-light-background, .bg-light-background-hover:hover', 'background-color', $light_background );
			chaplin_generate_css( '.border-color-light-background, .border-color-light-background-hover:hover', 'border-color', $light_background );
			chaplin_generate_css( '.fill-children-light-background, .fill-children-light-background *', 'fill', $light_background );
		endif;

		/* Fonts ----------------------------- */

		// Body font
		if ( $body_font ) :
			chaplin_generate_css( 'body', 'font-family', $body_font );
		endif;

		// Headings font
		if ( $headings_font ) :
			chaplin_generate_css( 'h1, h2, h3, h4, h5, h6, .faux-heading', 'font-family', $headings_font );
		endif;

		/* Return the results ---------------- */

		wp_add_inline_style( 'chaplin_style', ob_get_clean() );

	}
	add_action( 'wp_enqueue_scripts', 'chaplin_customizer_css_output' );
endif;


/* ---------------------------------------------------------------------------------------------
   	CUSTOM CUSTOMIZER CONTROLS
   --------------------------------------------------------------------------------------------- */

if ( class_exists( 'WP_Customize_Control' ) ) :
	if ( ! class_exists( 'Chaplin_Customize_Control_Checkbox_Multiple' ) ) :

		// Custom Customizer control that outputs a specified number of checkboxes
		// Based on a solution by Justin Tadlock: http://justintadlock.com/archives/2015/05/26/multiple-checkbox-customizer-control
		class Chaplin_Customize_Control_Checkbox_Multiple extends WP_Customize_Control {

			public $type = 'checkbox-multiple';

			public function render_content() {

				if ( empty( $this->choices ) ) :
					return;
				endif;

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;

				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif;

				$multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

				<ul>
					<?php foreach ( $this->choices as $value => $label ) : ?>

						<li>
							<label>
								<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
								<?php echo esc_html( $label ); ?>
							</label>
						</li>

					<?php endforeach; ?>
				</ul>

				<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />
				<?php
			}
		}

	endif;
endif;


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

			$chaplin_accent_color_options = apply_filters( 'chaplin_accent_color_options', array(
				'chaplin_accent_color' => array(
					'default'	=> '#007C89',
					'label'		=> __( 'Accent Color', 'chaplin' )
				),
				'chaplin_primary_text_color' => array(
					'default'	=> '#1A1B1F',
					'label'		=> __( 'Primary Text Color', 'chaplin' )
				),
				'chaplin_secondary_text_color' => array(
					'default'	=> '#747579',
					'label'		=> __( 'Secondary Text Color', 'chaplin' )
				),
				'chaplin_border_color' => array(
					'default'	=> '#E1E1E3',
					'label'		=> __( 'Border Color', 'chaplin' )
				),
				'chaplin_light_background_color' => array(
					'default'	=> '#F1F1F3',
					'label'		=> __( 'Light Background Color', 'chaplin' )
				),
			) );

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

			// Based on a solution by Claudio Schwarz (@purzlbaum on GitHub)

			$wp_customize->add_section( 'chaplin_fonts_options', array(
				'title' 		=> __( 'Fonts', 'chaplin' ),
				'priority' 		=> 40,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Specify which fonts to use. The theme supports all fonts on <a href="https://fonts.google.com" target="_blank">Google Fonts</a> and all <a href="https://www.w3schools.com/cssref/css_websafe_fonts.asp" target="_blank">web safe fonts</a>.', 'chaplin' ),
			) );

			/* Font Options ------------------ */

			// Body font
			$wp_customize->add_setting( 'chaplin_body_font', array(
				'default' 			=> '',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				'type'				=> 'theme_mod',
			) );

			$wp_customize->add_control( 'chaplin_body_font', array(
				'type'			=> 'text',
				'label' 		=> __( 'Body Font', 'chaplin' ),
				'section' 		=> 'chaplin_fonts_options',
				'input_attrs' 	=> array(
					'placeholder' 	=> __( 'Enter the font name', 'chaplin' ),
				),
			) );

			// Headings font
			$wp_customize->add_setting( 'chaplin_headings_font', array(
				'default' 			=> 'Merriweather',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				'type'				=> 'theme_mod',
			) );

			$wp_customize->add_control( 'chaplin_headings_font', array(
				'type'			=> 'text',
				'label' 		=> __( 'Headings Font', 'chaplin' ),
				'section' 		=> 'chaplin_fonts_options',
				'input_attrs' 	=> array(
					'placeholder' 	=> __( 'Enter the font name', 'chaplin' ),
				),
			) );

			// Languages
			$wp_customize->add_setting( 'chaplin_font_languages', array(
				'capability' 		=> 'edit_theme_options',
				'default'           => array( 'latin' ),
				'sanitize_callback' => 'chaplin_sanitize_multiple_checkboxes',
			) );

			$wp_customize->add_control( new Chaplin_Customize_Control_Checkbox_Multiple( $wp_customize, 'chaplin_font_languages', array(
				'section' 		=> 'chaplin_fonts_options',
				'label'   		=> __( 'Languages', 'chaplin' ),
				'description'	=> __( 'All fonts do not support all languages. Check your font on Google Fonts to make sure.', 'chaplin' ),
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
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_sticky_header', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_site_header_options',
				'priority'		=> 10,
				'label' 		=> __( 'Sticky Header', 'chaplin' ),
				'description' 	=> __( 'Stick the header to the top of the window on scroll.', 'chaplin' ),
			) );

			/* Header Search ------------------- */

			$wp_customize->add_setting( 'chaplin_header_search', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_header_search', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_site_header_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Search Button', 'chaplin' ),
				'description' 	=> __( 'Check to disable the search button in the header.', 'chaplin' ),
			) );

			/* ------------------------------------------------------------------------
			 * Posts
			 * ------------------------------------------------------------------------ */

			$wp_customize->add_section( 'chaplin_single_post_options', array(
				'title' 		=> __( 'Posts', 'chaplin' ),
				'priority' 		=> 41,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for what to display on single posts.', 'chaplin' ),
			) );

			/* Post Meta Setting ------------- */

			$post_meta_choices = apply_filters( 'chaplin_post_meta_choices_in_the_customizer', array(
				'author'		=> __( 'Author', 'chaplin' ),
				'categories'	=> __( 'Categories', 'chaplin' ),
				'comments'		=> __( 'Comments', 'chaplin' ),
				'edit-link'		=> __( 'Edit link (for logged in users)', 'chaplin' ),
				'post-date'		=> __( 'Post date', 'chaplin' ),
				'sticky'		=> __( 'Sticky status', 'chaplin' ),
				'tags'			=> __( 'Tags', 'chaplin' ),
			) );

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

			/* Disable Related Posts Setting - */

			$wp_customize->add_setting( 'chaplin_disable_related_posts', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_disable_related_posts', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_single_post_options',
				'priority'		=> 10,
				'label' 		=> __( 'Disable Related Posts', 'chaplin' ),
				'description' 	=> __( 'Check to hide the related posts section.', 'chaplin' ),
			) );

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
				'priority' 		=> 45,
				'capability' 	=> 'edit_theme_options',
				'description' 	=> __( 'Settings for the "Cover Template" page template.', 'chaplin' ),
			) );

			/* Overlay Color Setting ---------- */

			$wp_customize->add_setting( 'chaplin_cover_template_overlay_color', array(
				'default' 			=> '#007C89',
				'type' 				=> 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'chaplin_cover_template_overlay_color', array(
				'label' 		=> __( 'Image Overlay Color', 'chaplin' ),
				'description'	=> __( 'The color used for the featured image overlay. Defaults to the accent color.', 'chaplin' ),
				'section' 		=> 'chaplin_cover_template_options',
				'settings' 		=> 'chaplin_cover_template_overlay_color',
				'priority' 		=> 10,
			) ) );

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
				'priority' 		=> 20,
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

			/* Fixed Background -------------- */

			$wp_customize->add_setting( 'chaplin_cover_template_fixed_background', array(
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'chaplin_sanitize_checkbox',
			) );

			$wp_customize->add_control( 'chaplin_cover_template_fixed_background', array(
				'type' 			=> 'checkbox',
				'section' 		=> 'chaplin_cover_template_options',
				'priority'		=> 30,
				'label' 		=> __( 'Fixed Background Image', 'chaplin' ),
				'description' 	=> __( 'Creates a parallax effect when the visitor scrolls.', 'chaplin' ),
			) );

			/* Sanitation functions ----------------------------- */

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


?>
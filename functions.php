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
		add_theme_support( 'custom-background', array(
			'default-color'	=> 'FFFFFF'
		) );

		// Set content-width
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 580;
		}

		// Post thumbnails
		add_theme_support( 'post-thumbnails' );

		// Set post thumbnail size
		$low_res_images = get_theme_mod( 'chaplin_activate_low_resolution_images', false );
		if ( $low_res_images ) {
			set_post_thumbnail_size( 1120, 9999 );
		} else {
			set_post_thumbnail_size( 2240, 9999 );
		}

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
	Include required files
--------------------------------------------------------------------------------------------------- */

// Handle Google Fonts
require get_template_directory() . '/parts/classes/class-google-fonts.php';

// Handle SVG icons
require get_template_directory() . '/parts/classes/class-svg-icons.php';

// Handle Customizer settings
require get_template_directory() . '/parts/classes/class-theme-customizer.php';

// Custom comment walker
require get_template_directory() . '/parts/classes/class-comment-walker.php';


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

		if ( ( ! is_admin() ) && is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		
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
		$pagination_type = get_theme_mod( 'chaplin_pagination_type', 'button' );
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
		if ( is_page_template( array( 'template-cover.php', 'template-full-width-cover.php' ) ) ) {
			$classes[] = 'overlay-header';

			// Check if we're fading
			if ( get_theme_mod( 'chaplin_cover_template_fade_text', true ) ) {
				$classes[] = 'overlay-header-fade-text';
			}

			// Check if it has a custom text color
			if ( get_theme_mod( 'chaplin_cover_template_overlay_text_color' ) ) {
				$classes[] = 'overlay-header-has-text-color';
			}
		}

		// Check whether the current page should have an overlay header
		if ( is_page_template( array( 'template-full-width-only-content.php', 'template-only-content.php' ) ) ) {
			$classes[] = 'has-only-content';
		}

		// Check whether the current page should have an overlay header
		if ( is_page_template( array( 'template-full-width-only-content.php', 'template-full-width.php', 'template-full-width-cover.php' ) ) ) {
			$classes[] = 'has-full-width-content';
		}

		// Check for sticky header
		if ( get_theme_mod( 'chaplin_sticky_header' ) ) {
			$classes[] = 'has-sticky-header';
		}

		// Check for disabled menu modal on desktop
		if ( get_theme_mod( 'chaplin_disable_menu_modal_on_desktop', false ) ) {
			$classes[] = 'disable-menu-modal-on-desktop';
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

		echo esc_html( chaplin_get_custom_logo( $logo_theme_mod ) );

	}
endif;

if ( ! function_exists( 'chaplin_get_custom_logo' ) ) :
	function chaplin_get_custom_logo( $logo_theme_mod = 'custom_logo' ) {

		// Get the attachment for the specified logo
		$logo_id = get_theme_mod( $logo_theme_mod );
		
		if ( ! $logo_id ) {
			return;
		}

		$logo = wp_get_attachment_image_src( $logo_id, 'full' );

		if ( ! $logo ) {
			return;
		}

		// For clarity
		$logo_url = esc_url( $logo[0] );
		$logo_width = esc_attr( $logo[1] );
		$logo_height = esc_attr( $logo[2] );

		// If the retina logo setting is active, reduce the width/height by half
		if ( get_theme_mod( 'chaplin_retina_logo', false ) ) {
			$logo_width = floor( $logo_width / 2 );
			$logo_height = floor( $logo_height / 2 );
		}

		// CSS friendly class
		$logo_theme_mod_class = str_replace( '_', '-', $logo_theme_mod );

		// Record our output
		ob_start();

		?>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>" class="custom-logo-link <?php echo esc_attr( $logo_theme_mod_class ); ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" width="<?php echo esc_attr( $logo_width ); ?>" height="<?php echo esc_attr( $logo_height ); ?>" />
		</a>

		<?php

		// Return our output
		return ob_get_clean();

	}
endif;


/* ---------------------------------------------------------------------------------------------
   REGISTER SIDEBAR
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_sidebar_registration' ) ) :
	function chaplin_sidebar_registration() {

		// Arguments used in all register_sidebar() calls
		$shared_args = array(
			'before_title' 	=> '<h3 class="widget-title subheading">',
			'after_title' 	=> '</h3>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget' 	=> '</div></div>',
		);

		// Footer #1
		register_sidebar( array_merge( $shared_args, array(
			'name' 			=> __( 'Footer #1', 'chaplin' ),
			'id' 			=> 'footer-one',
			'description' 	=> __( 'Widgets in this area will be displayed in the first column in the footer.', 'chaplin' ),
		) ) );

		// Footer #2
		register_sidebar( array_merge( $shared_args, array(
			'name' 			=> __( 'Footer #2', 'chaplin' ),
			'id' 			=> 'footer-two',
			'description' 	=> __( 'Widgets in this area will be displayed in the second column in the footer.', 'chaplin' ),
		) ) );

	}
	add_action( 'widgets_init', 'chaplin_sidebar_registration' );
endif;


/* ---------------------------------------------------------------------------------------------
   INCLUDE THEME WIDGETS
   --------------------------------------------------------------------------------------------- */

require_once( get_template_directory() . '/parts/widgets/recent-comments.php' );
require_once( get_template_directory() . '/parts/widgets/recent-posts.php' );


/* ---------------------------------------------------------------------------------------------
   REGISTER THEME WIDGETS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_register_widgets' ) ) :
	function chaplin_register_widgets() {

		register_widget( 'Chaplin_Recent_Comments' );
		register_widget( 'Chaplin_Recent_Posts' );

	}
	add_action( 'widgets_init', 'chaplin_register_widgets' );
endif;


/* ---------------------------------------------------------------------------------------------
	DELIST DEFAULT WIDGETS REPLACE BY THEME ONES
	--------------------------------------------------------------------------------------------- */


if ( ! function_exists( 'chaplin_unregister_default_widgets' ) ) {
	function chaplin_unregister_default_widgets() {

		unregister_widget( 'WP_Widget_Recent_Comments' );
		unregister_widget( 'WP_Widget_Recent_Posts' );

	}
	add_action( 'widgets_init', 'chaplin_unregister_default_widgets', 11 );
}


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

		$disable_fallback_image = get_theme_mod( 'chaplin_disable_fallback_image', false );

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

		echo '<img src="' . esc_attr( $fallback_image_url ) . '" class="fallback-featured-image" />';

	}
endif;


/* ---------------------------------------------------------------------------------------------
   GET THE IMAGE SIZE OF PREVIEWS
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_preview_image_size' ) ) :
	function chaplin_get_preview_image_size() {

		// Check if low-resolution images are activated in the customizer
		$low_res_images = get_theme_mod( 'chaplin_activate_low_resolution_images', false );

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

		// Escaped in chaplin_get_theme_svg();
		echo chaplin_get_theme_svg( $svg_name, $color );

	}
endif;

if ( ! function_exists( 'chaplin_get_theme_svg' ) ) :
	function chaplin_get_theme_svg( $svg_name, $color = '' ) {

		// Make sure that only our allowed tags and attributes are included
		$svg = wp_kses( Chaplin_SVG_Icons::get_svg( $svg_name, $color ), array(
			'svg' => array(
				'class' 	=> true,
				'xmlns' 	=> true,
				'width' 	=> true,
				'height' 	=> true,
				'viewbox' 	=> true,
			),
			'path' => array(
				'fill' 		=> true,
				'fill-rule' => true,
				'd' 		=> true,
				'transform' => true,
			),
			'polygon' => array(
				'fill' 		=> true,
				'fill-rule' => true,
				'points'	=> true,
				'transform' => true,
			),
		) );

		if ( ! $svg ) {
			return false;
		}

		return $svg;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	IS COMMENT BY POST AUTHOR?
	Check if the specified comment is written by the author of the post commented on.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_is_comment_by_post_author' ) ) :
	function chaplin_is_comment_by_post_author( $comment = null ) {

		if ( is_object( $comment ) && $comment->user_id > 0 ) {
			$user = get_userdata( $comment->user_id );
			$post = get_post( $comment->comment_post_ID );
			if ( ! empty( $user ) && ! empty( $post ) ) {
				return $comment->user_id === $post->post_author;
			}
		}
		return false;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER COMMENT REPLY LINK TO NOT JS SCROLL
	Filter the comment reply link to add a class indicating it should not use JS slow-scroll, as it
	makes it scroll to the wrong position on the page
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_comment_reply_link' ) ) :
	function chaplin_filter_comment_reply_link( $link ) {

		$link = str_replace( 'class=\'', 'class=\'do-not-scroll ', $link );
		return $link;

	}
	add_filter( 'comment_reply_link', 'chaplin_filter_comment_reply_link' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER CLASSES OF WP_LIST_PAGES ITEMS TO MATCH MENU ITEMS
	Filter the class applied to wp_list_pages() items with children to match the menu class, to simplify 
	styling of sub levels in the fallback. Only applied if the match_menu_classes argument is set.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_wp_list_pages_item_classes' ) ) :
	function chaplin_filter_wp_list_pages_item_classes( $css_class, $item, $depth, $args, $current_page ) {

		// Only apply to wp_list_pages() calls with match_menu_classes set to true
		$match_menu_classes = isset( $args['match_menu_classes'] );

		if ( ! $match_menu_classes ) {
			return $css_class;
		}

		// Add current menu item class
		if ( in_array( 'current_page_item', $css_class ) ) {
			$css_class[] = 'current-menu-item';
		}

		// Add menu item has children class
		if ( in_array( 'page_item_has_children', $css_class ) ) {
			$css_class[] = 'menu-item-has-children';
		}

		return $css_class;

	}
	add_filter( 'page_css_class', 'chaplin_filter_wp_list_pages_item_classes', 10, 5 );
endif;


/* ------------------------------------------------------------------------------------------------
   OUTPUT & GET POST META
   If it's a single post, output the post meta values specified in the Customizer settings.

   @param	$post_id int		The ID of the post for which the post meta should be output
   @param	$location string	Which post meta location to output – single or preview
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_post_meta' ) ) :
	function chaplin_the_post_meta( $post_id = null, $location = 'single-top' ) {

		// Escaped in chaplin_get_post_meta()
		echo chaplin_get_post_meta( $post_id, $location );

	}
endif;

if ( ! function_exists( 'chaplin_get_post_meta' ) ) :
	function chaplin_get_post_meta( $post_id = null, $location = 'single-top' ) {

		// Require post ID
		if ( ! $post_id ) {
			return;
		}

		$page_template = get_page_template_slug( $post_id );

		// Check that the post type should be able to output post meta
		$allowed_post_types = apply_filters( 'chaplin_allowed_post_types_for_meta_output', array( 'post', 'jetpack-portfolio' ) );
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

			// Empty = use a fallback
			if ( ! $post_meta ) {
				$post_meta = array(
					'tags',
				);
			}

		} elseif ( 'archive' === $location ) {

			$post_meta = get_theme_mod( 'chaplin_post_meta_archive' );
			$post_meta_wrapper_classes = ' post-meta-archive';

			// Empty = use a fallback
			if ( ! $post_meta ) {
				$post_meta = array(
					'post-date',
				);
			}

		}

		// If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output
		if ( $post_meta && ! in_array( 'empty', $post_meta ) ) :

			// Make sure the right color is used for the post meta
			if ( in_array( $page_template, array( 'template-cover.php', 'template-full-width-cover.php' ) ) && $location == 'single-top' ) {
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

			<div class="post-meta-wrapper<?php echo esc_attr( $post_meta_wrapper_classes ); ?>">

				<ul class="post-meta<?php echo esc_attr( $post_meta_classes ); ?>">

					<?php

					// Allow output of additional meta items to be added by child themes and plugins
					do_action( 'chaplin_start_of_post_meta_list', $post_meta, $post_id );

					// Post date
					if ( in_array( 'post-date', $post_meta ) ) : 
						$has_meta = true;
						?>
						<li class="post-date">
							<a class="meta-wrapper" href="<?php the_permalink(); ?>">
								<span class="meta-icon">
									<span class="screen-reader-text"><?php esc_html_e( 'Post date', 'chaplin' ); ?></span>
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
								<span class="screen-reader-text"><?php esc_html_e( 'Post author', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'user' ); ?>
							</span>
							<span class="meta-text">
								<?php 
								// Translators: %s = the author name
								printf( esc_html_x( 'By %s', '%s = author name', 'chaplin' ), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author_meta( 'nickname' ) ) . '</a>' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Categories
					if ( in_array( 'categories', $post_meta ) && has_category() ) : 
						$has_meta = true;
						?>
						<li class="post-categories meta-wrapper">
							<span class="meta-icon">
								<span class="screen-reader-text"><?php esc_html_e( 'Post categories', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'folder' ); ?>
							</span>
							<span class="meta-text">
								<?php esc_html_e( 'In', 'chaplin' ); ?> <?php the_category( ', ' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Jetpack Portfolio Type
					if ( in_array( 'jetpack-portfolio-type', $post_meta ) && has_term( '', 'jetpack-portfolio-type', $post_id ) ) : 
						$has_meta = true;
						?>
						<li class="post-jetpack-portfolio-type meta-wrapper">
							<span class="meta-icon">
								<span class="screen-reader-text"><?php esc_html_e( 'Portfolio types', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'folder' ); ?>
							</span>
							<span class="meta-text">
								<?php the_terms( $post_id, 'jetpack-portfolio-type', __( 'In', 'chaplin' ) . ' ', ', ' ); ?>
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
								<span class="screen-reader-text"><?php esc_html_e( 'Tags', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'tag' ); ?>
							</span>
							<span class="meta-text">
								<?php the_tags( '', ', ', '' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Jetpack Portfolio Tags
					if ( in_array( 'jetpack-portfolio-tag', $post_meta ) && has_term( '', 'jetpack-portfolio-tag', $post_id ) ) : 
						$has_meta = true;
						?>
						<li class="post-jetpack-portfolio-tag meta-wrapper">
							<span class="meta-icon">
								<span class="screen-reader-text"><?php esc_html_e( 'Portfolio tags', 'chaplin' ); ?></span>
								<?php chaplin_the_theme_svg( 'tag' ); ?>
							</span>
							<span class="meta-text">
								<?php the_terms( $post_id, 'jetpack-portfolio-tag', '', ', ' ); ?>
							</span>
						</li>
						<?php
					endif;

					// Comments link
					if ( in_array( 'comments', $post_meta ) && comments_open() || get_comments_number() ) : 
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
								<?php esc_html_e( 'Sticky post', 'chaplin' ); ?>
							</span>
						</li>
					<?php endif;

					// Edit link
					if ( in_array( 'edit-link', $post_meta ) && current_user_can( 'edit_post', $post_id ) ) : 
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
										<?php esc_html_e( 'Edit', 'chaplin' ); ?>
									</span>
								</a>
							<?php else : ?>
								<a href="<?php echo esc_url( get_edit_post_link() ); ?>" class="meta-wrapper">
									<span class="meta-icon">
										<?php chaplin_the_theme_svg( 'edit' ); ?>
									</span>
									<span class="meta-text">
										<?php esc_html_e( 'Edit', 'chaplin' ); ?>
									</span>
								</a>
							<?php endif; ?>

						</li>
						<?php 

						// Allow output of additional post meta types to be added by child themes and plugins
						do_action( 'chaplin_end_of_post_meta_list', $post_meta, $post_id );

					endif; ?>

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


/* ------------------------------------------------------------------------------------------------
   GET POST GRID COLUMN CLASSES
   Gets the number of columns set in the Customizer, and returns the classes that should be used to
   set the post grid to the number of columns specified
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_post_grid_column_classes' ) ) :
	function chaplin_get_post_grid_column_classes() {

		$number_of_columns = get_theme_mod( 'chaplin_post_grid_columns', '2' );

		switch ( $number_of_columns ) {
			case '1' :
				$classes = 'mcols-1';
				break;
			case '2' :
				$classes = 'mcols-1 tcols-2';
				break;
			case '3' :
				$classes = 'mcols-1 tcols-2 tlcols-3';
				break;
			case '4' :
				$classes = 'mcols-1 tcols-2 tlcols-3 dcols-4';
				break;
			default :
				$classes = 'mcols-1 tcols-2';
		}

		return apply_filters( 'chaplin_post_grid_column_classes', $classes );

	}
endif;


/* 	-----------------------------------------------------------------------------------------------
	ADD A SUB NAV TOGGLE TO THE MAIN MENU
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_add_sub_toggles_to_main_menu' ) ) :
	function chaplin_add_sub_toggles_to_main_menu( $args, $item, $depth ) {

		// Add sub menu toggles to the main menu with toggles
		if ( $args->theme_location == 'main-menu' && isset( $args->show_toggles ) ) {

			// Wrap the menu item link contents in a div, used for positioning
			$args->before = '<div class="ancestor-wrapper">';
			$args->after = '';

			// Add a toggle to items with children
			if ( in_array( 'menu-item-has-children', $item->classes ) ) {

				$toggle_target_string = '.menu-item-' . $item->ID . ' > .sub-menu';

				// Add the sub menu toggle
				$args->after .= '<button class="toggle sub-menu-toggle fill-children-current-color" data-toggle-target="' . $toggle_target_string . '" data-toggle-type="slidetoggle" data-toggle-duration="250"><span class="screen-reader-text">' . __( 'Show sub menu', 'chaplin' ) . '</span>' . chaplin_get_theme_svg( 'chevron-down' ) . '</button>';

			}

			// Close the wrapper
			$args->after .= '</div><!-- .ancestor-wrapper -->';

		// Add sub menu icons to the main menu without toggles (the fallback menu)
		} elseif ( $args->theme_location == 'main-menu' ) {
			if ( in_array( 'menu-item-has-children', $item->classes ) ) {
				$args->before = '<div class="link-icon-wrapper fill-children-current-color">';
				$args->after = chaplin_get_theme_svg( 'chevron-down' ) . '</div>';
			} else {
				$args->before = '';
				$args->after = '';
			}
		}

		return $args;

	}
	add_filter( 'nav_menu_item_args', 'chaplin_add_sub_toggles_to_main_menu', 10, 3 );
endif;


/* 	-----------------------------------------------------------------------------------------------
	FILTER THE EXCERPT LENGTH
	Modify the length of automated excerpts to better fit the Chaplin previews
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_excerpt_length' ) ) :
	function chaplin_excerpt_length() {

		return 28;

	}
	add_filter( 'excerpt_length', 'chaplin_excerpt_length' );
endif;


/* 	-----------------------------------------------------------------------------------------------
	FILTER THE EXCERPT SUFFIX
	Replaces the default [...] with a &hellip; (three dots)
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_excerpt_more' ) ) :
	function chaplin_excerpt_more() {

		return '&hellip;';

	}
	add_filter( 'excerpt_more', 'chaplin_excerpt_more' );
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

		$css_dependencies = array();

		// Retrieve and enqueue the URL for Google Fonts
		$google_fonts_url = Chaplin_Google_Fonts::get_google_fonts_url();

		if ( $google_fonts_url ) {
			wp_register_style( 'chaplin_google_fonts', $google_fonts_url, false, 1.0, 'all' );
			$css_dependencies[] = 'chaplin_google_fonts';
		}
		
		// Enqueue the editor styles
		wp_enqueue_style( 'chaplin_block_editor_styles', get_theme_file_uri( '/chaplin-editor-style-block-editor.css' ), $css_dependencies, wp_get_theme()->get( 'Version' ), 'all' );

		// Add inline style from the Customizer
		wp_add_inline_style( 'chaplin_block_editor_styles', chaplin_get_customizer_css( 'block-editor' ) );

	}
	add_action( 'enqueue_block_editor_assets', 'chaplin_block_editor_styles', 1, 1 );
endif;


/* ---------------------------------------------------------------------------------------------
   EDITOR STYLES FOR THE CLASSIC EDITOR
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_classic_editor_styles' ) ) :
	function chaplin_classic_editor_styles() {

		$classic_editor_styles = array(
			'chaplin-editor-style-classic-editor.css',
		);

		// Retrieve the Google Fonts URL and add it as a dependency
		$google_fonts_url = Chaplin_Google_Fonts::get_google_fonts_url();

		if ( $google_fonts_url ) {
			$classic_editor_styles[] = $google_fonts_url;
		}

		add_editor_style( $classic_editor_styles );

	}
	add_action( 'init', 'chaplin_classic_editor_styles' );
endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT OF CUSTOMIZER SETTINGS IN THE CLASSIC EDITOR
   Adds styles to the head of the TinyMCE iframe. Kudos to @Otto42 for the original solution.
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_add_classic_editor_customizer_styles' ) ) :
	function chaplin_add_classic_editor_customizer_styles( $mce_init ) {
		
		$styles = chaplin_get_customizer_css( 'classic-editor' );

		if ( ! isset( $mce_init['content_style'] ) ) {
			$mce_init['content_style'] = $styles . ' ';
		} else {
			$mce_init['content_style'] .= ' ' . $styles . ' ';
		}

		return $mce_init;

	}
	add_filter( 'tiny_mce_before_init', 'chaplin_add_classic_editor_customizer_styles' );
endif;


/* ---------------------------------------------------------------------------------------------
   BLOCK EDITOR SETTINGS
   Add custom colors and font sizes to the block editor
------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'chaplin_block_editor_settings' ) ) :
	function chaplin_block_editor_settings() {

		/* Block Editor Palette --------------------------------------- */

		$editor_color_palette = array();

		// Get the color options
		$chaplin_accent_color_options = Chaplin_Customize::chaplin_get_color_options();

		// Loop over them and construct an array for the editor-color-palette
		if ( $chaplin_accent_color_options ) {
			foreach( $chaplin_accent_color_options as $color_option_name => $color_option ) {
				$editor_color_palette[] = array(
					'name' 	=> $color_option['label'],
					'slug' 	=> $color_option['slug'],
					'color' => get_theme_mod( $color_option_name, $color_option['default'] ),
				);
			}
		}

		// Add the background option
		$background_color = get_theme_mod( 'background_color' );
		if ( ! $background_color ) {
			$background_color_arr = get_theme_support( 'custom-background' );
			$background_color = $background_color_arr[0]['default-color'];
		}
		$editor_color_palette[] = array(
			'name' 	=> __( 'Background Color', 'chaplin' ),
			'slug' 	=> 'background',
			'color' => '#' . $background_color,
		);

		// If we have accent colors, add them to the block editor palette
		if ( $editor_color_palette ) {
			add_theme_support( 'editor-color-palette', $editor_color_palette );
		}

		/* Gutenberg Font Sizes --------------------------------------- */

		add_theme_support( 'editor-font-sizes', array(
			array(
				'name' 		=> _x( 'Small', 'Name of the small font size in Gutenberg', 'chaplin' ),
				'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'chaplin' ),
				'size' 		=> 16,
				'slug' 		=> 'small',
			),
			array(
				'name' 		=> _x( 'Regular', 'Name of the regular font size in Gutenberg', 'chaplin' ),
				'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'chaplin' ),
				'size' 		=> 18,
				'slug' 		=> 'regular',
			),
			array(
				'name' 		=> _x( 'Large', 'Name of the large font size in Gutenberg', 'chaplin' ),
				'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'chaplin' ),
				'size' 		=> 24,
				'slug' 		=> 'large',
			),
			array(
				'name' 		=> _x( 'Larger', 'Name of the larger font size in Gutenberg', 'chaplin' ),
				'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'chaplin' ),
				'size' 		=> 32,
				'slug' 		=> 'larger',
			),
		) );

	}
	add_action( 'after_setup_theme', 'chaplin_block_editor_settings' );
endif;


/*	-----------------------------------------------------------------------------------------------
	GENERATE CSS
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
	HEX TO RGB
	Convert hex colors to RGB colors
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_hex_to_rgb' ) ) :
	function chaplin_hex_to_rgb( $hex_color ) {

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
endif;


/*	-----------------------------------------------------------------------------------------------
	HEX TO P3
	Convert hex colors to the P3 color gamut
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_hex_to_p3' ) ) :
	function chaplin_hex_to_p3( $hex_color ) {

		$rgb_color = chaplin_hex_to_rgb( $hex_color );

		return array(
			'red'	=> round( $rgb_color[0] / 255, 3 ),
			'green'	=> round( $rgb_color[1] / 255, 3 ),
			'blue'	=> round( $rgb_color[2] / 255, 3 ),
		);

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	FORMAT P3 OUTPUT
	Format an array of P3 color gamut values to a CSS friendly property
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_format_p3' ) ) :
	function chaplin_format_p3( $p3_colors ) {

		return 'color( display-p3 ' . $p3_colors['red'] . ' ' . $p3_colors['green'] . ' ' . $p3_colors['blue'] . ' / 1 )';

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	MINIFY CSS
	Helper function for reducing the size of the line styles output by Chaplin.
	Based on a original script by @webgefrickel: https://gist.github.com/webgefrickel/3339063
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_minify_css' ) ) :
	function chaplin_minify_css( $css ) {

		// some of the following functions to minimize the css-output are directly taken
		// from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
		// all credits to Christian Schaefer: http://twitter.com/derSchepp
		// remove comments
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		// backup values within single or double quotes
		preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);

		for ( $i = 0; $i < count( $hit[1] ); $i++ ) {
			$css = str_replace( $hit[1][$i], '##########' . $i . '##########', $css );
		}

		// remove traling semicolon of selector's last property
		$css = preg_replace( '/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css );

		// remove any whitespace between semicolon and property-name
		$css = preg_replace( '/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css );

		// remove any whitespace surrounding property-colon
		$css = preg_replace( '/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css );

		// remove any whitespace surrounding selector-comma
		$css = preg_replace( '/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css );

		// remove any whitespace surrounding opening parenthesis
		$css = preg_replace( '/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css );

		// remove any whitespace between numbers and units
		$css = preg_replace( '/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css );

		// shorten zero-values
		$css = preg_replace( '/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css );

		// constrain multiple whitespaces
		$css = preg_replace( '/\p{Zs}+/ims',' ', $css );

		// remove newlines
		$css = str_replace( array( "\r\n", "\r", "\n" ), '', $css );

		// Restore backupped values within single or double quotes
		for ( $i = 0; $i < count( $hit[1] ); $i++ ) {
			$css = str_replace( '##########' . $i . '##########', $hit[1][$i], $css );
		}

		return $css;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	GET CSS BUILT FROM CUSTOMIZER OPTIONS
	Build CSS reflecting colors, fonts and other options set in the Customizer, and return them for output

	@param		$type string	Whether to return CSS for the "front-end", "block-editor" or "classic-editor"
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_customizer_css' ) ) :
	function chaplin_get_customizer_css( $type = 'front-end' ) {

		/* Get variables --------------------- */
	

		// Colors
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

		// P3 colors
		$p3_background = 			chaplin_format_p3( chaplin_hex_to_p3( $background ) );
		$p3_primary = 				chaplin_format_p3( chaplin_hex_to_p3( $primary ) );
		$p3_headings = 				chaplin_format_p3( chaplin_hex_to_p3( $headings ) );
		$p3_buttons_background =	chaplin_format_p3( chaplin_hex_to_p3( $buttons_background ) );
		$p3_buttons_text = 			chaplin_format_p3( chaplin_hex_to_p3( $buttons_text ) );
		$p3_secondary = 			chaplin_format_p3( chaplin_hex_to_p3( $secondary ) );
		$p3_accent = 				chaplin_format_p3( chaplin_hex_to_p3( $accent ) );
		$p3_border = 				chaplin_format_p3( chaplin_hex_to_p3( $border ) );
		$p3_light_background = 		chaplin_format_p3( chaplin_hex_to_p3( $light_background ) );
		$p3_overlay_text = 			chaplin_format_p3( chaplin_hex_to_p3( $overlay_text ) );

		// Default colors
		$background_default 		= '#ffffff';
		$primary_default 			= '#1a1b1f';
		$headings_default 			= '#1a1b1f';
		$buttons_background_default = $accent;
		$buttons_text_default 		= $background;
		$secondary_default 			= '#747579';
		$accent_default 			= '#007c89';
		$border_default 			= '#e1e1e3';
		$light_background_default 	= '#f1f1f3';

		// Fonts and font settings
		$body_font = 			esc_attr( get_theme_mod( 'chaplin_body_font', Chaplin_Google_Fonts::$default_body_font ) );
		$headings_font = 		esc_attr( get_theme_mod( 'chaplin_headings_font', Chaplin_Google_Fonts::$default_headings_font ) );
		$headings_weight =		esc_attr( get_theme_mod( 'chaplin_headings_weight' ) );
		$headings_case =		esc_attr( get_theme_mod( 'chaplin_headings_letter_case' ) );
		$headings_spacing =		get_theme_mod( 'chaplin_headings_letterspacing' ) ? str_replace( '_', '.', esc_attr( get_theme_mod( 'chaplin_headings_letterspacing' ) ) ) : ''; // Replace underscores with dots

		// P3 media query opening and closing
		$p3_supports_open =		'@supports ( color: color( display-p3 0 0 0 / 1 ) ) {';
		$p3_supports_close =	'}';

		// Combine the chosen fonts with the appropriate fallback font stack
		if ( $body_font ) {
			$body_font_stack = 	Chaplin_Google_Fonts::get_font_fallbacks( $body_font, 'body' );
			$body_font = 		$body_font . ', '. $body_font_stack;
		}

		if ( $headings_font ) {
			$headings_font_stack = 	Chaplin_Google_Fonts::get_font_fallbacks( $headings_font, 'headings' );
			$headings_font = 		$headings_font . ', ' . $headings_font_stack;
		}
		
		ob_start();

		/* 	Note – Styles are applied in this order: 
				1. Element specific 
				2. Helper classes 

			This enables all helper classes to overwrite base element styles, 
			meaning that any color classes applied in the block editor will 
			have a higher priority than the base element styles
		*/

		/* FRONT-END STYLES */

		if ( $type == 'front-end' ) {

			/* Helper Variables ------------------ */

			$headings_targets = apply_filters( 'chaplin_headings_targets_front_end', 'h1, h2, h3, h4, h5, h6, .faux-heading' );
			$buttons_targets = apply_filters( 'chaplin_buttons_targets_front_end', 'button, .button, .faux-button, .wp-block-button__link, .wp-block-file__button, input[type=\'button\'], input[type=\'reset\'], input[type=\'submit\']' );

			/* Fonts ----------------------------- */

			// Body font
			if ( $body_font ) :
				chaplin_generate_css( 'body', 'font-family', $body_font );
			endif;

			// Headings font
			if ( $headings_font ) :
				chaplin_generate_css( $headings_targets, 'font-family', $headings_font );
			endif;

			// Headings weight
			if ( $headings_weight ) {
				chaplin_generate_css( $headings_targets, 'font-weight', $headings_weight );
			}

			// Headings case
			if ( $headings_case !== 'normal' ) {
				chaplin_generate_css( $headings_targets, 'text-transform', $headings_case );
			}

			// Headings letterspacking
			if ( $headings_spacing !== 'normal' ) {
				chaplin_generate_css( $headings_targets, 'letter-spacing', $headings_spacing . 'em' );
			}

			/* Colors ---------------------------- */

			/* ELEMENT SPECIFIC */

			// Background color
			if ( $background && $background !== $background_default ) : 
				chaplin_generate_css( $buttons_targets, 'color', $background );
				chaplin_generate_css( '.header-inner.is-sticky', 'background-color', $background );
				chaplin_generate_css( 'body', 'background-color', $background, '', '!important' );
				echo '@media( max-width: 999px ) {';
				chaplin_generate_css( '.menu-modal', 'background-color', $background, '', ' !important' );
				echo '}';

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( $buttons_targets, 'color', $p3_background );
				chaplin_generate_css( 'body', 'background-color', $p3_background, '', '!important' );
				echo $p3_supports_close;
			endif;

			// Primary color
			if ( $primary && $primary !== $primary_default ) : 
				chaplin_generate_css( 'body', 'color', $primary );
				chaplin_generate_css( 'select', 'background-image', 'url( \'data:image/svg+xml;utf8,' . chaplin_get_theme_svg( 'chevron-down', $primary ) . '\');' );
				chaplin_generate_css( '.main-menu-alt ul li,', 'color', $primary );
				chaplin_generate_css( '.overlay-header .header-inner.is-sticky', 'color', $primary, '', ' !important' );
				echo '@media( max-width: 999px ) {';
				chaplin_generate_css( '.overlay-header.showing-menu-modal .header-inner', 'color', $primary, '', ' !important' );
				echo '}';

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( 'select', 'background-image', 'url( \'data:image/svg+xml;utf8,' . chaplin_get_theme_svg( 'chevron-down', $p3_primary ) . '\');' );
				chaplin_generate_css( '.main-menu-alt ul li', 'color', $p3_primary );
				chaplin_generate_css( 'body', 'color', $p3_primary );
				echo $p3_supports_close;
			endif;

			// Headings color (only if different than default and primary)
			if ( $headings && $headings !== $headings_default && $headings !== $primary ) : 
				chaplin_generate_css( $headings_targets, 'color', $headings );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( $headings_targets, 'color', $p3_headings );
				echo $p3_supports_close;
			endif;

			// Secondary color
			if ( $secondary && $secondary !== $secondary_default ) :
				chaplin_generate_css( '.wp-block-latest-comments time, .wp-block-latest-posts time', 'color', $secondary );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.wp-block-latest-comments time, .wp-block-latest-posts time', 'color', $p3_secondary );
				echo $p3_supports_close;
			endif;

			// Accent color
			if ( $accent && $accent !== $accent_default ) : 
				chaplin_generate_css( 'a, .wp-block-button.is-style-outline', 'color', $accent );
				chaplin_generate_css( 'blockquote, .wp-block-button.is-style-outline', 'border-color', $accent );
				chaplin_generate_css( $buttons_targets, 'background-color', $accent );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( 'a, .wp-block-button.is-style-outline', 'color', $p3_accent );
				chaplin_generate_css( 'blockquote, .wp-block-button.is-style-outline', 'border-color', $p3_accent );
				chaplin_generate_css( $buttons_targets, 'background-color', $p3_accent );
				echo $p3_supports_close;
			endif;

			// Buttons background color
			if ( $buttons_background && $buttons_background !== $buttons_background_default && $buttons_background !== $accent ) : 
				chaplin_generate_css( $buttons_targets, 'background-color', $buttons_background );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( $buttons_targets, 'background-color', $p3_buttons_background );
				echo $p3_supports_close;
			endif;

			// Buttons text color
			if ( $buttons_text && $buttons_text !== $buttons_text_default && $buttons_text !== $background ) : 
				chaplin_generate_css( $buttons_targets, 'color', $buttons_text );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( $buttons_targets, 'color', $p3_buttons_text );
				echo $p3_supports_close;
			endif;
			
			// Border color
			if ( $border && $border !== $border_default ) : 
				chaplin_generate_css( 'hr, pre, th, td, input, textarea, select, fieldset', 'border-color', $border );
				chaplin_generate_css( '.main-menu li, button.sub-menu-toggle, .wp-block-latest-posts.is-grid li, .footer-menu li, .comment .comment, .related-posts, .widget, .select2-container .select2-selection--single', 'border-color', $border );
				chaplin_generate_css( 'caption', 'background', $border );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( 'hr, pre, th, td, input, textarea, select, fieldset', 'border-color', $p3_border );
				chaplin_generate_css( '.main-menu li, button.sub-menu-toggle, .wp-block-latest-posts.is-grid li, .footer-menu li, .comment .comment, .related-posts, .widget, .select2-container .select2-selection--single', 'border-color', $p3_border );
				chaplin_generate_css( 'caption', 'background', $p3_border );
				echo $p3_supports_close;
			endif;

			// Light background color
			if ( $light_background && $light_background !== $light_background_default ) : 
				chaplin_generate_css( 'code, kbd, samp, .main-menu-alt ul, table.is-style-stripes tr:nth-child( odd )', 'background-color', $light_background );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( 'code, kbd, samp, .main-menu-alt ul, table.is-style-stripes tr:nth-child( odd )', 'background-color', $p3_light_background );
				echo $p3_supports_close;
			endif;

			// Cover Template overlay text color
			if ( $overlay_text ) : 

				// Safari has issues with animating the P3 colors, so don't set the overlay header colors to the P3 gamut
				chaplin_generate_css( '.cover-header .entry-header, .overlay-header .header-inner', 'color', $overlay_text );
				echo '@media( max-width: 999px ) {';
				chaplin_generate_css( '.overlay-header:not(.showing-menu-modal) .header-inner:not(.is-sticky) .site-title', 'color', $overlay_text );
				echo '}';
				echo '@media( min-width: 999px ) {';
				chaplin_generate_css( '.overlay-header .header-inner:not(.is-sticky) .site-title', 'color', $overlay_text );
				echo '}';

			endif;

			/* HELPER CLASSES */

			// Default colors
			// These are non-theme-specific color classes that can be set in the Block Editor. They need to be included here in order 
			// to overwrite element specific colors. For example, a button with the .has-white-background-color class would get the 
			// accent color, set a couple of lines up, unless the helper class is redefined here.
			chaplin_generate_css( '.has-pale-pink-background-color', 'background-color', '#f78da7' );
			chaplin_generate_css( '.has-vivid-red-background-color', 'background-color', '#cf2e2e' );
			chaplin_generate_css( '.has-luminous-vivid-orange-background-color', 'background-color', '#ff6900' );
			chaplin_generate_css( '.has-luminous-vivid-amber-background-color', 'background-color', '#fcb900' );
			chaplin_generate_css( '.has-light-green-cyan-background-color', 'background-color', '#7bdcb5' );
			chaplin_generate_css( '.has-vivid-green-cyan-background-color', 'background-color', '#00d084' );
			chaplin_generate_css( '.has-pale-cyan-blue-background-color', 'background-color', '#8ed1fc' );
			chaplin_generate_css( '.has-vivid-cyan-blue-background-color', 'background-color', '#0693e3' );
			chaplin_generate_css( '.has-very-light-gray-background-color', 'background-color', '#eee' );
			chaplin_generate_css( '.has-cyan-bluish-gray-background-color', 'background-color', '#abb8c3' );
			chaplin_generate_css( '.has-very-dark-gray-background-color', 'background-color', '#313131' );
			chaplin_generate_css( '.has-black-background-color', 'background-color', '#000' );
			chaplin_generate_css( '.has-white-background-color', 'background-color', '#fff' );
			chaplin_generate_css( '.has-pale-pink-color', 'color', '#f78da7' );
			chaplin_generate_css( '.has-vivid-red-color', 'color', '#cf2e2e' );
			chaplin_generate_css( '.has-luminous-vivid-orange-color', 'color', '#ff6900' );
			chaplin_generate_css( '.has-luminous-vivid-amber-color', 'color', '#fcb900' );
			chaplin_generate_css( '.has-light-green-cyan-color', 'color', '#7bdcb5' );
			chaplin_generate_css( '.has-vivid-green-cyan-color', 'color', '#00d084' );
			chaplin_generate_css( '.has-pale-cyan-blue-color', 'color', '#8ed1fc' );
			chaplin_generate_css( '.has-vivid-cyan-blue-color', 'color', '#0693e3' );
			chaplin_generate_css( '.has-very-light-gray-color', 'color', '#eee' );
			chaplin_generate_css( '.has-cyan-bluish-gray-color', 'color', '#abb8c3' );
			chaplin_generate_css( '.has-very-dark-gray-color', 'color', '#313131' );
			chaplin_generate_css( '.has-black-color', 'color', '#000' );
			chaplin_generate_css( '.has-white-color', 'color', '#fff' );


			// Background color
			if ( $background && $background !== $background_default ) : 
				chaplin_generate_css( '.color-body-background, .color-body-background-hover:hover, .has-background-color', 'color', $background );
				chaplin_generate_css( '.bg-body-background, .bg-body-background-hover:hover, .has-background-background-color', 'background-color', $background );
				chaplin_generate_css( '.border-color-body-background, .border-color-body-background-hover:hover', 'border-color', $background );
				chaplin_generate_css( '.fill-children-body-background, .fill-children-body-background *', 'fill', $background );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.color-body-background, .color-body-background-hover:hover, .has-background-color', 'color', $p3_background );
				chaplin_generate_css( '.bg-body-background, .bg-body-background-hover:hover, .has-background-background-color', 'background-color', $p3_background );
				chaplin_generate_css( '.border-color-body-background, .border-color-body-background-hover:hover', 'border-color', $p3_background );
				chaplin_generate_css( '.fill-children-body-background, .fill-children-body-background *', 'fill', $p3_background );
				echo $p3_supports_close;
			endif;

			// Primary color
			if ( $primary && $primary !== $primary_default ) : 
				chaplin_generate_css( '.color-primary, .color-primary-hover:hover, .has-primary-color', 'color', $primary );
				chaplin_generate_css( '.bg-primary, .bg-primary-hover:hover, .has-primary-background-color', 'background-color', $primary );
				chaplin_generate_css( '.border-color-primary, .border-color-primary-hover:hover', 'border-color', $primary );
				chaplin_generate_css( '.fill-children-primary, .fill-children-primary *', 'fill', $primary );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.color-primary, .color-primary-hover:hover, .has-primary-color', 'color', $p3_primary );
				chaplin_generate_css( '.bg-primary, .bg-primary-hover:hover, .has-primary-background-color', 'background-color', $p3_primary );
				chaplin_generate_css( '.border-color-primary, .border-color-primary-hover:hover', 'border-color', $p3_primary );
				chaplin_generate_css( '.fill-children-primary, .fill-children-primary *', 'fill', $p3_primary );
				echo $p3_supports_close;
			endif;

			// Secondary color
			if ( $secondary && $secondary !== $secondary_default ) : 
				chaplin_generate_css( '.color-secondary, .color-secondary-hover:hover, .has-secondary-color', 'color', $secondary );
				chaplin_generate_css( '.bg-secondary, .bg-secondary-hover:hover, .has-secondary-background-color', 'background-color', $secondary );
				chaplin_generate_css( '.border-color-secondary, .border-color-secondary-hover:hover', 'border-color', $secondary );
				chaplin_generate_css( '.fill-children-secondary, .fill-children-secondary *', 'fill', $secondary );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.color-secondary, .color-secondary-hover:hover, .has-secondary-color', 'color', $p3_secondary );
				chaplin_generate_css( '.bg-secondary, .bg-secondary-hover:hover, .has-secondary-background-color', 'background-color', $p3_secondary );
				chaplin_generate_css( '.border-color-secondary, .border-color-secondary-hover:hover', 'border-color', $p3_secondary );
				chaplin_generate_css( '.fill-children-secondary, .fill-children-secondary *', 'fill', $p3_secondary );
				echo $p3_supports_close;
			endif;

			// Accent color
			if ( $accent && $accent !== $accent_default ) : 
				chaplin_generate_css( '.color-accent, .color-accent-hover:hover, .has-accent-color', 'color', $accent );
				chaplin_generate_css( '.bg-accent, .bg-accent-hover:hover, .has-accent-background-color', 'background-color', $accent );
				chaplin_generate_css( '.border-color-accent, .border-color-accent-hover:hover', 'border-color', $accent );
				chaplin_generate_css( '.fill-children-accent, .fill-children-accent *', 'fill', $accent );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.color-accent, .color-accent-hover:hover, .has-accent-color', 'color', $p3_accent );
				chaplin_generate_css( '.bg-accent, .bg-accent-hover:hover, .has-accent-background-color', 'background-color', $p3_accent );
				chaplin_generate_css( '.border-color-accent, .border-color-accent-hover:hover', 'border-color', $p3_accent );
				chaplin_generate_css( '.fill-children-accent, .fill-children-accent *', 'fill', $p3_accent );
				echo $p3_supports_close;
			endif;
			
			// Border color
			if ( $border && $border !== $border_default ) : 
				chaplin_generate_css( '.color-border, .color-border-hover:hover, .has-border-color', 'color', $border );
				chaplin_generate_css( '.bg-border, .bg-border-hover:hover, .has-border-background-color', 'background-color', $border );
				chaplin_generate_css( '.border-color-border, .border-color-border-hover:hover', 'border-color', $border );
				chaplin_generate_css( '.fill-children-border, .fill-children-border *', 'fill', $border );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.color-border, .color-border-hover:hover, .has-border-color', 'color', $p3_border );
				chaplin_generate_css( '.bg-border, .bg-border-hover:hover, .has-border-background-color', 'background-color', $p3_border );
				chaplin_generate_css( '.border-color-border, .border-color-border-hover:hover', 'border-color', $p3_border );
				chaplin_generate_css( '.fill-children-border, .fill-children-border *', 'fill', $p3_border );
				echo $p3_supports_close;
			endif;

			// Light background color
			if ( $light_background && $light_background !== $light_background_default ) : 
				chaplin_generate_css( '.color-light-background, .color-light-background-hover:hover, .has-light-background-color', 'color', $light_background );
				chaplin_generate_css( '.bg-light-background, .bg-light-background-hover:hover, .has-light-background-background-color', 'background-color', $light_background );
				chaplin_generate_css( '.border-color-light-background, .border-color-light-background-hover:hover', 'border-color', $light_background );
				chaplin_generate_css( '.fill-children-light-background, .fill-children-light-background *', 'fill', $light_background );

				// P3 Colors
				echo $p3_supports_open;
				chaplin_generate_css( '.color-light-background, .color-light-background-hover:hover, .has-light-background-color', 'color', $p3_light_background );
				chaplin_generate_css( '.bg-light-background, .bg-light-background-hover:hover, .has-light-background-background-color', 'background-color', $p3_light_background );
				chaplin_generate_css( '.border-color-light-background, .border-color-light-background-hover:hover', 'border-color', $p3_light_background );
				chaplin_generate_css( '.fill-children-light-background, .fill-children-light-background *', 'fill', $p3_light_background );
				echo $p3_supports_close;
			endif;

		/* BLOCK EDITOR STYLES */

		} else if ( $type == 'block-editor' ) {

			/* Helper Variables -------------- */

			$headings_targets = apply_filters( 'chaplin_headings_targets_block_editor', '.editor-styles-wrapper .wp-block h1, .editor-styles-wrapper .wp-block h2, .editor-styles-wrapper .wp-block h3, .editor-styles-wrapper .wp-block h4, .editor-styles-wrapper .wp-block h5, .editor-styles-wrapper .wp-block h6, .editor-styles-wrapper .editor-post-title__block .editor-post-title__input' );
			$buttons_targets = apply_filters( 'chaplin_buttons_targets_block_editor', '.editor-styles-wrapper .faux-button, .editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link, .editor-styles-wrapper .wp-block-file .wp-block-file__button' );

			/* Fonts ------------------------- */

			// Body font
			if ( $body_font ) :
				chaplin_generate_css( '.editor-styles-wrapper > *, .editor-styles-wrapper .editor-post-title__block .editor-post-title__input', 'font-family', $body_font );
			endif;

			// Headings font
			if ( $headings_font ) :
				chaplin_generate_css( $headings_targets, 'font-family', $headings_font );
			endif;

			// Headings weight
			if ( $headings_weight ) {
				chaplin_generate_css( $headings_targets, 'font-weight', $headings_weight );
			}

			// Headings case
			if ( $headings_case !== 'normal' ) {
				chaplin_generate_css( $headings_targets, 'text-transform', $headings_case );
			}

			// Headings letterspacking
			if ( $headings_spacing !== 'normal' ) {
				chaplin_generate_css( $headings_targets, 'letter-spacing', $headings_spacing . 'em' );
			}

			/* Colors ------------------------ */

			/* ELEMENT SPECIFIC */

			// Background color
			if ( $background && $background !== $background_default ) : 
				chaplin_generate_css( $buttons_targets, 'color', $background );
				chaplin_generate_css( '.editor-styles-wrapper, .editor-styles-wrapper > .editor-writing-flow, .editor-styles-wrapper > .editor-writing-flow > div', 'background-color', $background );
			endif;

			// Primary color
			if ( $primary && $primary !== $primary_default ) : 
				chaplin_generate_css( '.editor-styles-wrapper > *', 'color', $primary );
				chaplin_generate_css( '.editor-styles-wrapper .editor-post-title__input', 'color', $primary );
				chaplin_generate_css( '.editor-styles-wrapper .is-style-outline .wp-block-button__link', 'color', $primary );
			endif;

			// Headings color (only if different than default and primary)
			if ( $headings && $headings !== $headings_default && $headings !== $primary ) : 
				chaplin_generate_css( $headings_targets, 'color', $headings );
			endif;

			// Buttons background color
			if ( $buttons_background && $buttons_background !== $buttons_background_default && $buttons_background !== $accent ) : 
				chaplin_generate_css( $buttons_targets, 'background-color', $buttons_background );
			endif;

			error_log( $buttons_text );

			// Buttons text color
			if ( $buttons_text && $buttons_text !== $buttons_text_default && $buttons_text !== $background ) : 
				chaplin_generate_css( $buttons_targets, 'color', $buttons_text );
			endif;

			// Secondary color
			if ( $secondary ) :
				chaplin_generate_css( '.editor-styles-wrapper .wp-block-latest-comments time, .editor-styles-wrapper .wp-block-latest-posts time', 'color', $secondary );
			endif;

			// Accent color
			if ( $accent && $accent !== $accent_default ) : 
				chaplin_generate_css( '.editor-styles-wrapper a', 'color', $accent );
				chaplin_generate_css( '.editor-styles-wrapper blockquote, .editor-styles-wrapper .wp-block-quote', 'border-color', $accent, '', ' !important' );
				chaplin_generate_css( '.editor-styles-wrapper .wp-block-file .wp-block-file__textlink', 'color', $accent );
				chaplin_generate_css( $buttons_targets, 'background', $accent );
				chaplin_generate_css( '.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link', 'border-color', $accent );
				chaplin_generate_css( '.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link', 'color', $accent );
			endif;
			
			// Border color
			if ( $border && $border !== $border_default ) : 
				chaplin_generate_css( '.editor-styles-wrapper hr, .editor-styles-wrapper pre, .editor-styles-wrapper th, .editor-styles-wrapper td, .editor-styles-wrapper fieldset', 'border-color', $border );
				chaplin_generate_css( '.editor-styles-wrapper caption', 'background', $border );
				chaplin_generate_css( '.editor-styles-wrapper .wp-block-latest-posts.is-grid li, .editor-styles-wrapper table.wp-block-table, .editor-styles-wrapper hr.wp-block-separator', 'border-color', $border );
			endif;

			// Light background color
			if ( $light_background && $light_background !== $light_background_default ) : 
				chaplin_generate_css( 'code, kbd, samp', 'background-color', $light_background );
				chaplin_generate_css( '.wp-block-table.is-style-stripes tbody tr:nth-child(odd)', 'background-color', $light_background );
				chaplin_generate_css( '.wp-block-shortcode', 'background-color', $light_background );
			endif;

			/* HELPER CLASSES */

			// Background color
			if ( $background && $background !== $background_default ) : 
				chaplin_generate_css( '.has-background-color', 'color', $background );
				chaplin_generate_css( '.has-background-background-color', 'background-color', $background );
			endif;

			// Primary color
			if ( $primary && $primary !== $primary_default ) : 
				chaplin_generate_css( '.has-primary-color', 'color', $primary );
				chaplin_generate_css( '.has-primary-background-color', 'background-color', $primary );
			endif;

			// Secondary color
			if ( $secondary ) :
				chaplin_generate_css( '.has-secondary-color', 'color', $secondary );
				chaplin_generate_css( '.has-secondary-background-color', 'background-color', $secondary );
			endif;

			// Accent color
			if ( $accent && $accent !== $accent_default ) : 
				chaplin_generate_css( '.has-accent-color', 'color', $accent );
				chaplin_generate_css( '.has-accent-background-color', 'background-color', $accent );
			endif;
			
			// Border color
			if ( $border && $border !== $border_default ) : 
				chaplin_generate_css( '.has-border-color', 'color', $border );
				chaplin_generate_css( '.has-border-background-color', 'background-color', $border );
			endif;

			// Light background color
			if ( $light_background && $light_background !== $light_background_default ) : 
				chaplin_generate_css( '.has-light-background-color', 'color', $light_background );
				chaplin_generate_css( '.has-light-background-background-color', 'background-color', $light_background );
			endif;

		} else if ( $type == 'classic-editor' ) {

			/* Helper Variables -------------- */

			$headings_targets = apply_filters( 'chaplin_headings_targets_classic_editor', 'body#tinymce.wp-editor h1, body#tinymce.wp-editor h2, body#tinymce.wp-editor h3, body#tinymce.wp-editor h4, body#tinymce.wp-editor h5, body#tinymce.wp-editor h6' );
			$buttons_targets = apply_filters( 'chaplin_buttons_targets_classic_editor', 'body#tinymce.wp-editor button, body#tinymce.wp-editor .button, body#tinymce.wp-editor .faux-button, body#tinymce.wp-editor input[type=\'button\'], body#tinymce.wp-editor input[type=\'reset\'], body#tinymce.wp-editor input[type=\'submit\']' );

			/* Fonts ------------------------- */

			// Body font
			if ( $body_font ) :
				chaplin_generate_css( 'body#tinymce.wp-editor', 'font-family', $body_font );
			endif;

			// Headings font
			if ( $headings_font ) :
				chaplin_generate_css( $headings_targets, 'font-family', $headings_font );
			endif;

			// Headings weight
			if ( $headings_weight ) {
				chaplin_generate_css( $headings_targets, 'font-weight', $headings_weight );
			}

			// Headings case
			if ( $headings_case !== 'normal' ) {
				chaplin_generate_css( $headings_targets, 'text-transform', $headings_case );
			}

			// Headings letterspacking
			if ( $headings_spacing !== 'normal' ) {
				chaplin_generate_css( $headings_targets, 'letter-spacing', $headings_spacing . 'em' );
			}

			/* Colors ------------------------ */

			/* ELEMENT SPECIFIC */

			// Background color
			if ( $background && $background !== $background_default ) : 
				chaplin_generate_css( 'body#tinymce.wp-editor', 'background-color', $background );
				chaplin_generate_css( $buttons_targets, 'color', $background );
			endif;

			// Primary color
			if ( $primary && $primary !== $primary_default ) : 
				chaplin_generate_css( 'body#tinymce.wp-editor', 'color', $primary );
			endif;

			// Headings color (only if different than default and primary)
			if ( $headings && $headings !== $headings_default && $headings !== $primary ) : 
				chaplin_generate_css( $headings_targets, 'color', $headings );
			endif;

			// Buttons background color
			if ( $buttons_background && $buttons_background !== $buttons_background_default && $buttons_background !== $accent ) : 
				chaplin_generate_css( $buttons_targets, 'background-color', $buttons_background );
			endif;

			// Buttons text color
			if ( $buttons_text && $buttons_text !== $buttons_text_default && $buttons_text !== $background ) : 
				chaplin_generate_css( $buttons_targets, 'color', $buttons_text );
			endif;

			// Accent color
			if ( $accent && $accent !== $accent_default ) : 
				chaplin_generate_css( 'body#tinymce.wp-editor a', 'color', $accent );
				chaplin_generate_css( 'body#tinymce.wp-editor blockquote, body#tinymce.wp-editor .wp-block-quote', 'border-color', $accent, '', ' !important' );
				chaplin_generate_css( $buttons_targets, 'background-color', $accent );
			endif;
			
			// Border color
			if ( $border && $border !== $border_default ) : 
				chaplin_generate_css( 'body#tinymce.wp-editor hr, body#tinymce.wp-editor pre, body#tinymce.wp-editor th, body#tinymce.wp-editor td, body#tinymce.wp-editor input, body#tinymce.wp-editor textarea, body#tinymce.wp-editor select, body#tinymce.wp-editor fieldset', 'border-color', $border );
				chaplin_generate_css( 'body#tinymce.wp-editor caption', 'background', $border );
			endif;

			// Light background color
			if ( $light_background && $light_background !== $light_background_default ) : 
				chaplin_generate_css( 'body#tinymce.wp-editor code, body#tinymce.wp-editor kbd, body#tinymce.wp-editor samp', 'background-color', $light_background );
				chaplin_generate_css( 'body#tinymce.wp-editor table tbody > tr:nth-child(odd)', 'background-color', $light_background );
			endif;

			/* HELPER CLASSES */
			// No helper classes in the classic editor

		}

		/* Minify before output -------------- */

		$css = chaplin_minify_css( ob_get_clean() );

		/* Return the results ---------------- */

		return $css;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT CUSTOMIZER CSS ON THE FRONT-END
	Get CSS built from the Customizer settings (fonts and colors), and output them on the front-end
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_customizer_css_output_front_end' ) ) :
	function chaplin_customizer_css_output_front_end() {

		wp_add_inline_style( 'chaplin_style', chaplin_get_customizer_css( 'front-end' ) );

	}
	add_action( 'wp_enqueue_scripts', 'chaplin_customizer_css_output_front_end' );
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


?>
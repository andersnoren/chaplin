<?php


/*	-----------------------------------------------------------------------------------------------
	THEME SUPPORTS
	Default setup, some features excluded
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_theme_support' ) ) :
	function chaplin_theme_support() {

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
		add_theme_support( 'custom-logo', 
			array(
				'height'      => 240,
				'width'       => 320,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

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

// Include custom template tags
require get_template_directory() . '/parts/template-tags.php';

// Handle Google Fonts
require get_template_directory() . '/parts/classes/class-google-fonts.php';

// Handle SVG icons
require get_template_directory() . '/parts/classes/class-svg-icons.php';

// Custom Customizer control for multiple checkboxes
require get_template_directory() . '/parts/classes/class-customize-control-checkbox-multiple.php';

// Handle Customizer settings
require get_template_directory() . '/parts/classes/class-theme-customizer.php';

// Custom comment walker
require get_template_directory() . '/parts/classes/class-comment-walker.php';

// Custom CSS class
require get_template_directory() . '/parts/classes/class-custom-css.php';

// Recent Comments widget
require get_template_directory() . '/parts/widgets/recent-comments.php';

// Recent Posts widget
require get_template_directory() . '/parts/widgets/recent-posts.php';


/* ------------------------------------------------------------------------------------------------
   REGISTER AND DEREGISTER THEME WIDGETS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_register_widgets' ) ) :
	function chaplin_register_widgets() {

		// Register custom widgets
		register_widget( 'Chaplin_Recent_Comments' );
		register_widget( 'Chaplin_Recent_Posts' );

		// Deregister default widgets replaced by our custom widgets
		unregister_widget( 'WP_Widget_Recent_Comments' );
		unregister_widget( 'WP_Widget_Recent_Posts' );

	}
	add_action( 'widgets_init', 'chaplin_register_widgets' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REGISTER STYLES
	Register and enqueue CSS
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_register_styles' ) ) :
	function chaplin_register_styles() {

		$theme_version = wp_get_theme( 'chaplin' )->get( 'Version' );
		$css_dependencies = array();

		// Retrieve and enqueue the URL for Google Fonts
		$google_fonts_url = Chaplin_Google_Fonts::get_google_fonts_url();

		if ( $google_fonts_url ) {
			wp_register_style( 'chaplin-google-fonts', $google_fonts_url, false, 1.0, 'all' );
			$css_dependencies[] = 'chaplin-google-fonts';
		}

		// By default, only load the Font Awesome fonts if the social menu is in use
		$load_font_awesome = apply_filters( 'chaplin_load_font_awesome', has_nav_menu( 'social-menu' ) );

		if ( $load_font_awesome ) {
			wp_register_style( 'chaplin-font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css', false, "5.15.1", 'all' );
			$css_dependencies[] = 'chaplin-font-awesome';
		}

		// Filter the list of dependencies used by the chaplin-style CSS enqueue
		$css_dependencies = apply_filters( 'chaplin_css_dependencies', $css_dependencies );

		wp_enqueue_style( 'chaplin-style', get_template_directory_uri() . '/style.css', $css_dependencies, $theme_version, 'all' );

		// Add output of Customizer settings as inline style
		wp_add_inline_style( 'chaplin-style', Chaplin_Custom_CSS::get_customizer_css( 'front-end' ) );

		// Enqueue the print styles stylesheet
		wp_enqueue_style( 'chaplin-print-styles', get_template_directory_uri() . '/assets/css/print.css', false, $theme_version, 'print' );

	}
	add_action( 'wp_enqueue_scripts', 'chaplin_register_styles' );
endif;


/*	-----------------------------------------------------------------------------------------------
	REGISTER SCRIPTS
	Register and enqueue JavaScript
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_register_scripts' ) ) :
	function chaplin_register_scripts() {

		$theme_version = wp_get_theme( 'chaplin' )->get( 'Version' );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Built-in JS assets
		$js_dependencies = array( 'jquery', 'imagesloaded' );

		// Register the Modernizr JS check for touchevents (used to determine whether background-attachment should be active)
		wp_register_script( 'chaplin-modernizr', get_template_directory_uri() . '/assets/js/modernizr-touchevents.min.js', array(), '3.6.0' );
		$js_dependencies[] = 'chaplin-modernizr';

		// Filter the list of dependencies used by the chaplin-construct JavaScript enqueue
		$js_dependencies = apply_filters( 'chaplin_js_dependencies', $js_dependencies );

		wp_enqueue_script( 'chaplin-construct', get_template_directory_uri() . '/assets/js/construct.js', $js_dependencies, $theme_version );

		// Setup AJAX
		$ajax_url = admin_url( 'admin-ajax.php' );

		// AJAX Load More
		wp_localize_script( 'chaplin-construct', 'chaplin_ajax_load_more', array(
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
			'main-menu'   => __( 'Main Menu', 'chaplin' ),
			'footer-menu' => __( 'Footer Menu', 'chaplin' ),
			'social-menu' => __( 'Social Menu', 'chaplin' ),
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
		$post_type = isset( $post ) ? $post->post_type : false;

		// Determine type of infinite scroll
		$pagination_type = get_theme_mod( 'chaplin_pagination_type', 'button' );
		
		switch ( $pagination_type ) {
			case 'button':
				$classes[] = 'pagination-type-button';
				break;
			case 'scroll':
				$classes[] = 'pagination-type-scroll';
				break;
			case 'links':
				$classes[] = 'pagination-type-links';
				break;
		}

		// Check whether the current page should have an overlay header
		if ( is_singular() && chaplin_is_cover_template( $post->ID ) ) {
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

		// Check whether the current page only has content
		if ( is_page_template( array( 'template-full-width-only-content.php', 'template-only-content.php' ) ) ) {
			$classes[] = 'has-only-content';
		}

		// Check whether the current page is full width
		if ( is_page_template( array( 'template-full-width-only-content.php', 'template-full-width.php', 'template-full-width-cover.php' ) ) ) {
			$classes[] = 'has-full-width-content';
		}

		// Check for sticky header
		if ( get_theme_mod( 'chaplin_sticky_header' ) ) {
			$classes[] = 'has-sticky-header';
		}

		// Check for disabled search
		if ( get_theme_mod( 'chaplin_disable_header_search', false ) ) {
			$classes[] = 'disable-search-modal';
		}

		// Check for disabled smooth scroll
		if ( get_theme_mod( 'chaplin_disable_smooth_scroll', false ) ) {
			$classes[] = 'disable-smooth-scroll';
		}

		// Check for disabled menu modal on desktop
		if ( get_theme_mod( 'chaplin_disable_menu_modal_on_desktop', false ) ) {
			$classes[] = 'disable-menu-modal-on-desktop';
		}

		// Check if we have an overlay logo
		if ( get_theme_mod( 'chaplin_overlay_logo' ) ) {
			$classes[] = 'has-overlay-logo';
		}

		// Check for post thumbnail
		if ( is_singular() && get_the_post_thumbnail_url() ) {
			$classes[] = 'has-post-thumbnail';
		} elseif ( is_singular() ) {
			$classes[] = 'missing-post-thumbnail';
		}

		// Check whether we're in the customizer preview
		if ( is_customize_preview() ) {
			$classes[] = 'customizer-preview';
		}

		// Check if posts have single pagination
		if ( is_single() && ( get_next_post() || get_previous_post() ) ) {
			$classes[] = 'has-single-pagination';
		} else {
			$classes[] = 'has-no-pagination';
		}

		// Check if we're showing comments
		if ( is_singular() && ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) ) {
			$classes[] = 'showing-comments';
		} else {
			$classes[] = 'not-showing-comments';
		}

		// Slim page template class names (class = name - file suffix)
		if ( is_page_template() ) {
			$classes[] = basename( get_page_template_slug(), '.php' );
		}

		return $classes;

	}
	add_action( 'body_class', 'chaplin_body_classes' );
endif;


/* ---------------------------------------------------------------------------------------------
   REGISTER SIDEBAR
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_sidebar_registration' ) ) :
	function chaplin_sidebar_registration() {

		// Arguments used in all register_sidebar() calls
		$shared_args = array(
			'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
			'after_title'   => '</h2>',
			'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
			'after_widget'  => '</div></div>',
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


/*	-----------------------------------------------------------------------------------------------
	ADD EXCERPT SUPPORT TO PAGES
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_add_excerpt_support_to_pages' ) ) :
	function chaplin_add_excerpt_support_to_pages() {

		add_post_type_support( 'page', 'excerpt' );

	}
	add_action( 'init', 'chaplin_add_excerpt_support_to_pages' );
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
	FILTER ARCHIVE TITLE

	@param	$title string		The initial title
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_archive_title' ) ) :
	function chaplin_filter_archive_title( $title ) {

		// On home, use title of the page for posts page.
		$blog_page_id = get_option( 'page_for_posts' );
		if ( is_home() && $blog_page_id && get_the_title( $blog_page_id ) ) {
			$title = get_the_title( $blog_page_id );
		} 

		// On search, show the search query.
		elseif ( is_search() ) {
			$title = sprintf( _x( 'Search: %s', '%s = The search query', 'chaplin' ), '&ldquo;' . get_search_query() . '&rdquo;' );
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

		// On the blog page, use the manual excerpt of the page for posts page.
		$blog_page_id = get_option( 'page_for_posts' );
		if ( is_home() && $blog_page_id && has_excerpt( $blog_page_id ) ) {
			$description = get_the_excerpt( $blog_page_id );
		}
		
		// On search, show a string describing the results of the search.
		elseif ( is_search() ) {
			global $wp_query;
			if ( $wp_query->found_posts ) {
				/* Translators: %s = Number of results */
				$description = sprintf( _nx( 'We found %s result for your search.', 'We found %s results for your search.',  $wp_query->found_posts, '%s = Number of results', 'chaplin' ), $wp_query->found_posts );
			} else {
				$description = __( 'We could not find any results for your search. You can give it another try through the search form below.', 'chaplin' );
			}
		}

		return $description;

	}
	add_filter( 'get_the_archive_description', 'chaplin_filter_archive_description' );
endif;


/*	-----------------------------------------------------------------------------------------------
	FILTER COMMENT REPLY LINK TO NOT JS SCROLL
	Filter the comment reply link to add a class indicating it should not use JS slow-scroll, as it
	makes it scroll to the wrong position on the page
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_comment_reply_link' ) ) :
	function chaplin_filter_comment_reply_link( $link ) {

		$link = str_replace( 'class=\'', 'class=\'do-not-smooth-scroll ', $link );
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


/* 	-----------------------------------------------------------------------------------------------
	FILTER NAV MENU ITEM ARGUMENTS
	Add a sub navigation toggle to the main menu
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_filter_nav_menu_item_args' ) ) :
	function chaplin_filter_nav_menu_item_args( $args, $item, $depth ) {

		// Add sub menu toggles to the main menu with toggles
		if ( $args->theme_location == 'main-menu' && isset( $args->show_toggles ) ) {

			// Wrap the menu item link contents in a div, used for positioning
			$args->before = '<div class="ancestor-wrapper">';
			$args->after  = '';

			// Add a toggle to items with children
			if ( in_array( 'menu-item-has-children', $item->classes ) ) {

				$toggle_target_string = '.menu-modal .menu-item-' . $item->ID . ' &gt; .sub-menu';

				// Add the sub menu toggle
				$args->after .= '<div class="sub-menu-toggle-wrapper"><a href="#" class="toggle sub-menu-toggle border-color-border fill-children-current-color" data-toggle-target="' . $toggle_target_string . '" data-toggle-type="slidetoggle" data-toggle-duration="250"><span class="screen-reader-text">' . __( 'Show sub menu', 'chaplin' ) . '</span>' . chaplin_get_theme_svg( 'chevron-down' ) . '</a></div>';

			}

			// Close the wrapper
			$args->after .= '</div><!-- .ancestor-wrapper -->';

			// Add sub menu icons to the main menu without toggles (the fallback menu)
		} elseif ( $args->theme_location == 'main-menu' ) {
			if ( in_array( 'menu-item-has-children', $item->classes ) ) {
				$args->before = '<div class="link-icon-wrapper fill-children-current-color">';
				$args->after  = chaplin_get_theme_svg( 'chevron-down' ) . '</div>';
			} else {
				$args->before = '';
				$args->after  = '';
			}
		}

		return $args;

	}
	add_filter( 'nav_menu_item_args', 'chaplin_filter_nav_menu_item_args', 10, 3 );
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

		// Calculate the current offset
		$iteration = intval( $ajax_query->query['posts_per_page'] ) * intval( $ajax_query->query['paged'] );

		if ( $ajax_query->have_posts() ) :
			while ( $ajax_query->have_posts() ) : $ajax_query->the_post();

				global $post;

				$iteration++;

				/**
				 * Fires before output of a grid item in the posts loop.
				 * 
				 * Allows output of custom elements within the posts loop, like banners.
				 * To add markup spanning the entire width of the posts grid, wrap it in the following element:
				 * <div class="grid-item col-1">[Your content]</div>
				 * @param int   $post_id 	Post ID.
				 * @param int   $iteration 	The current iteration of the loop.
				 */

				do_action( 'chaplin_posts_loop_before_grid_item', $post->ID, $iteration );
				?>

				<div class="grid-item">
					<?php get_template_part( 'parts/preview', $post_type ); ?>
				</div><!-- .grid-item -->

				<?php 

				/**
				 * Fires after output of a grid item in the posts loop.
				 */

				do_action( 'chaplin_posts_loop_after_grid_item', $post->ID, $iteration );

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
		wp_enqueue_style( 'chaplin_block_editor_styles', get_theme_file_uri( 'assets/css/chaplin-editor-style-block-editor.css' ), $css_dependencies, wp_get_theme( 'chaplin' )->get( 'Version' ), 'all' );

		// Add inline style from the Customizer
		wp_add_inline_style( 'chaplin_block_editor_styles', Chaplin_Custom_CSS::get_customizer_css( 'block-editor' ) );

	}
	add_action( 'enqueue_block_editor_assets', 'chaplin_block_editor_styles', 1, 1 );
endif;


/* ---------------------------------------------------------------------------------------------
   EDITOR STYLES FOR THE CLASSIC EDITOR
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_classic_editor_styles' ) ) :
	function chaplin_classic_editor_styles() {

		$classic_editor_styles = array(
			'assets/css/chaplin-editor-style-classic-editor.css',
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

		$styles = Chaplin_Custom_CSS::get_customizer_css( 'classic-editor' );

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
   FILTER NAV MENU WIDGET ARGUMENTS FOR SOCIAL MENU
   Adjust the styling of the nav menu widget when it's set to display the social menu.

   @param array $nav_menu_args	The arguments for wp_nav_menu.
   @param obj $nav_menu			The menu set to be displayed.
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_widget_nav_menu_args' ) ) :
	function chaplin_widget_nav_menu_args( $nav_menu_args, $nav_menu ) {

		// Get the social menu.
		$theme_locations = get_nav_menu_locations();

		// If there is no social menu set, return the nav menu args.
		if ( ! isset( $theme_locations['social-menu'] ) ) return $nav_menu_args;

		$social_menu 		= get_term( $theme_locations['social-menu'], 'nav_menu' );
		$social_menu_id 	= isset( $social_menu->term_id ) ? $social_menu->term_id : null;

		// If we're not outputting the social menu, return the existing args.
		if ( $social_menu_id !== $nav_menu->term_id ) return $nav_menu_args;

		// If we are outputting the social menu, modify the args to match the social menu.
		$nav_menu_args = wp_parse_args( $nav_menu_args, chaplin_get_social_menu_args() );

		return $nav_menu_args;

	}
	add_filter( 'widget_nav_menu_args', 'chaplin_widget_nav_menu_args', 10, 2 );
endif;


/*	-----------------------------------------------------------------------------------------------
	GET SOCIAL MENU WP_NAV_MENU ARGS
	Return the social menu arguments for wp_nav_menu().

	@param array $args		Arguments to use in conjunction with the default arguments.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_get_social_menu_args' ) ) :
	function chaplin_get_social_menu_args( $args = array() ) {

		return $args = wp_parse_args( $args, array(
			'theme_location'	=> 'social-menu',
			'container'			=> '',
			'container_class'	=> '',
			'menu_class'		=> 'social-menu reset-list-style social-icons s-icons',
			'depth'				=> 1,
			'link_before'		=> '<span class="screen-reader-text">',
			'link_after'		=> '</span>',
			'fallback_cb'		=> '',
		) );

	}
endif;


/* ---------------------------------------------------------------------------------------------
   BLOCK EDITOR SETTINGS
   Add custom colors and font sizes to the block editor
------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'chaplin_block_editor_settings' ) ) :
	function chaplin_block_editor_settings() {

		/* Block Editor Palette -------------- */

		$editor_color_palette = array();

		// Get the color options
		$chaplin_accent_color_options = Chaplin_Customize::chaplin_get_color_options();

		// Loop over them and construct an array for the editor-color-palette
		if ( $chaplin_accent_color_options ) {
			foreach ( $chaplin_accent_color_options as $color_option_name => $color_option ) {
				$editor_color_palette[] = array(
					'name'  => $color_option['label'],
					'slug'  => $color_option['slug'],
					'color' => get_theme_mod( $color_option_name, $color_option['default'] ),
				);
			}
		}

		// Add the background option
		$background_color = get_theme_mod( 'background_color' );
		if ( ! $background_color ) {
			$background_color_arr = get_theme_support( 'custom-background' );
			$background_color     = $background_color_arr[0]['default-color'];
		}
		$editor_color_palette[] = array(
			'name'  => __( 'Background Color', 'chaplin' ),
			'slug'  => 'background',
			'color' => '#' . $background_color,
		);

		// If we have accent colors, add them to the block editor palette
		if ( $editor_color_palette ) {
			add_theme_support( 'editor-color-palette', $editor_color_palette );
		}

		/* Block Editor Font Sizes ----------- */

		add_theme_support( 'editor-font-sizes',
			array(
				array(
					'name'      => _x( 'Small', 'Name of the small font size in Gutenberg', 'chaplin' ),
					'shortName' => _x( 'S', 'Short name of the small font size in the Gutenberg editor.', 'chaplin' ),
					'size'      => 16,
					'slug'      => 'small',
				),
				array(
					'name'      => _x( 'Regular', 'Name of the regular font size in Gutenberg', 'chaplin' ),
					'shortName' => _x( 'M', 'Short name of the regular font size in the Gutenberg editor.', 'chaplin' ),
					'size'      => 19,
					'slug'      => 'normal',
				),
				array(
					'name'      => _x( 'Large', 'Name of the large font size in Gutenberg', 'chaplin' ),
					'shortName' => _x( 'L', 'Short name of the large font size in the Gutenberg editor.', 'chaplin' ),
					'size'      => 24,
					'slug'      => 'large',
				),
				array(
					'name'      => _x( 'Larger', 'Name of the larger font size in Gutenberg', 'chaplin' ),
					'shortName' => _x( 'XL', 'Short name of the larger font size in the Gutenberg editor.', 'chaplin' ),
					'size'      => 32,
					'slug'      => 'larger',
				)
			)
		);

	}
	add_action( 'after_setup_theme', 'chaplin_block_editor_settings' );
endif;
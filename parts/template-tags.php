<?php


/* ------------------------------------------------------------------------------------------------
   CUSTOM LOGO OUTPUT
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_custom_logo' ) ) :
	function chaplin_the_custom_logo( $logo_theme_mod = 'custom_logo' ) {

		echo esc_html( chaplin_get_custom_logo( $logo_theme_mod ) );

	}
endif;

if ( ! function_exists( 'chaplin_get_custom_logo' ) ) :
	function chaplin_get_custom_logo( $logo_theme_mod = 'custom_logo' ) {

		// Get the attachment id for the specified logo
		$logo_id = get_theme_mod( $logo_theme_mod );
		
		if ( ! $logo_id ) return;

		$logo = wp_get_attachment_image_src( $logo_id, 'full' );

		if ( ! $logo ) return;

		// For clarity
		$logo_url = esc_url( $logo[0] );
		$logo_alt = get_post_meta( $logo_id, '_wp_attachment_image_alt', TRUE );
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

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="custom-logo-link <?php echo esc_attr( $logo_theme_mod_class ); ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" width="<?php echo esc_attr( $logo_width ); ?>" height="<?php echo esc_attr( $logo_height ); ?>" <?php if ( $logo_alt ) echo ' alt="' . esc_attr( $logo_alt ) . '"'; ?> />
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

	
/* ---------------------------------------------------------------------------------------------
   GET FALLBACK IMAGE
------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'chaplin_get_fallback_image_url' ) ) :
	function chaplin_get_fallback_image_url() {

		$disable_fallback_image = get_theme_mod( 'chaplin_disable_fallback_image', false );

		if ( $disable_fallback_image ) return '';

		$fallback_image_id = get_theme_mod( 'chaplin_fallback_image' );

		if ( $fallback_image_id ) {
			$fallback_image = wp_get_attachment_image_src( $fallback_image_id, 'full' );
		}

		$fallback_image_url = isset( $fallback_image ) ? esc_url( $fallback_image[0] ) : get_template_directory_uri() . '/assets/images/default-fallback-image.png';

		return $fallback_image_url;

	}
endif;


/* ---------------------------------------------------------------------------------------------
   OUTPUT AND RETURN FALLBACK IMAGE
   --------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_fallback_image' ) ) :
	function chaplin_the_fallback_image() {

		echo chaplin_get_fallback_image();

	}
endif;

if ( ! function_exists( 'chaplin_get_fallback_image' ) ) :
	function chaplin_get_fallback_image() {

		$fallback_image_url = chaplin_get_fallback_image_url();

		if ( ! $fallback_image_url ) return;

		return '<img src="' . esc_attr( $fallback_image_url ) . '" class="fallback-featured-image" />';

	}
endif;


/* ---------------------------------------------------------------------------------------------
   FILTER POST THUMBNAIL HTML TO INCLUDE FALLBACK IMAGE
   If a post thumbnail isn't set, filter the fallback image to be used instead.
------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'chaplin_filter_fallback_image' ) ) :
	function chaplin_filter_fallback_image( $html, $post_id, $post_thumbnail_id ) {

		// Make the disable fallback image variable filterable in child themes and plugins
		$disable_fallback_image = get_theme_mod( 'chaplin_disable_fallback_image', false );
		$disable_fallback_image = apply_filters( 'chaplin_disable_fallback_image_on_post', $disable_fallback_image, $post_id, $post_thumbnail_id );

		// If the post is password protected, return the fallback image (or an empty string, if the fallback image is disabled).
		if ( post_password_required( $post_id ) ) {
			return chaplin_get_fallback_image();

		// If there's an image already set, return it.
		} else if ( $html ) {
			return $html;

		// If not, and the fallback image is not disabled, return the fallback image.
		} else if ( ! $disable_fallback_image ) {
			return chaplin_get_fallback_image();

		// If not, and the fallback image is disabled, return nothing.
		} else {
			return '';
		}

	}
	add_filter( 'post_thumbnail_html', 'chaplin_filter_fallback_image', 10, 3 );
endif;


/* ---------------------------------------------------------------------------------------------
   FILTER HAS_POST_THUMBNAIL TO MATCH FALLBACK IMAGE VALUE
   If the fallback image is enabled, make sure the has_post_thumbnail() reflects that when a post 
   thumbnail isn't set.
------------------------------------------------------------------------------------------------ */

if ( ! function_exists( 'chaplin_filter_has_post_thumbnail' ) ) :
	function chaplin_filter_has_post_thumbnail( $has_thumbnail, $post_id ) {

		$disable_fallback_image = get_theme_mod( 'chaplin_disable_fallback_image', false );

		// If the fallback image is disabled, return the original $has_thumbnail value (true if there's a featured image set).
		if ( $disable_fallback_image ) {
			return $has_thumbnail;

		// If the fallback image is enabled, there's always a featured image, so return true.
		} else {
			return true;
		}

	}
	add_filter( 'has_post_thumbnail', 'chaplin_filter_has_post_thumbnail', 10, 2 );
endif;


/* ---------------------------------------------------------------------------------------------
   GET THE IMAGE SIZE OF PREVIEWS
------------------------------------------------------------------------------------------------ */

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
		$svg = wp_kses(
			Chaplin_SVG_Icons::get_svg( $svg_name, $color ),
			array(
				'svg'     => array(
					'class'       => true,
					'xmlns'       => true,
					'width'       => true,
					'height'      => true,
					'viewbox'     => true,
					'aria-hidden' => true,
					'role'        => true,
					'focusable'   => true,
				),
				'path'    => array(
					'fill'      => true,
					'fill-rule' => true,
					'd'         => true,
					'transform' => true,
				),
				'polygon' => array(
					'fill'      => true,
					'fill-rule' => true,
					'points'    => true,
					'transform' => true,
					'focusable' => true,
				),
			)
		);

		if ( ! $svg ) {
			return false;
		}

		return $svg;

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT SOCIAL MENU
	Output the social menu, if set.

	@param array $args		Arguments for wp_nav_menu().
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_social_menu' ) ) :
	function chaplin_the_social_menu( $args = array() ) {

		$social_args = chaplin_get_social_menu_args( $args );

		if ( has_nav_menu( $social_args['theme_location'] ) ) {
			wp_nav_menu( $social_args );
		}

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	IS COMMENT BY POST AUTHOR?
	Check if the specified comment is written by the author of the post commented on.

	@param obj $comment		The comment object.
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
	IS THE POST/PAGE SET TO A COVER TEMPLATE?
	Helper function for checking if the specified post is set to any of the cover templates.

	@param	$post mixed		Optional. Post ID or WP_Post object. Default is global $post.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_is_cover_template' ) ) :
	function chaplin_is_cover_template( $post = null ) {

		$post = get_post( $post );

		// Filterable list of cover templates to check for
		$cover_templates = apply_filters( 'chaplin_cover_templates', array( 'template-cover.php', 'template-full-width-cover.php' ) );

		return in_array( get_page_template_slug( $post ), $cover_templates );

	}
endif;


/* ------------------------------------------------------------------------------------------------
   OUTPUT & GET POST META
   If it's a single post, output the post meta values specified in the Customizer settings.

   @param	$post_id int		The ID of the post for which the post meta should be output.
   @param	$location string	Which post meta location to output.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_the_post_meta' ) ) :
	function chaplin_the_post_meta( $post_id, $location = 'single-top' ) {

		// Escaped in chaplin_get_post_meta()
		echo chaplin_get_post_meta( $post_id, $location );

	}
endif;

if ( ! function_exists( 'chaplin_get_post_meta' ) ) :
	function chaplin_get_post_meta( $post_id, $location = 'single-top' ) {

		/**
		 * Filter for modifying the post types supporting post meta output.
		 * 
		 * If you wish to enable a post type to display post meta, add it here.
		 * 
		 * @param array	$post_types		Post types with post meta support.
		 */

		$post_types = apply_filters( 'chaplin_allowed_post_types_for_meta_output', array( 'post', 'jetpack-portfolio' ) );

		if ( ! in_array( get_post_type( $post_id ), $post_types ) ) return;

		// Setup arrays with CSS classes for the post meta wrapper and list elements.
		$post_meta_wrapper_classes = array( 'post-meta-wrapper' );
		$post_meta_classes = array( 'post-meta' );

		// Get the post meta settings for the location passes as a parameter.
		switch ( $location ) {

			// In the single post header
			case 'single-top' :
				$post_meta = get_theme_mod( 'chaplin_post_meta_single_top' );
				$post_meta_wrapper_classes[] = 'post-meta-single';
				$post_meta_wrapper_classes[] = 'post-meta-single-top';

				// Empty = use a fallback
				if ( ! $post_meta ) {
					$post_meta = array(
						'post-date',
						'categories',
					);
				}
				break;

			// Below the single post content
			case 'single-bottom' :
				$post_meta = get_theme_mod( 'chaplin_post_meta_single_bottom' );
				$post_meta_wrapper_classes[] = 'post-meta-single';
				$post_meta_wrapper_classes[] = 'post-meta-single-bottom';

				// Empty = use a fallback
				if ( ! $post_meta ) {
					$post_meta = array(
						'tags',
					);
				}
				break;

			// In post previews
			case 'archive' :
				$post_meta = get_theme_mod( 'chaplin_post_meta_archive' );
				$post_meta_wrapper_classes[] = 'post-meta-archive';

				// Empty = use a fallback
				if ( ! $post_meta ) {
					$post_meta = array(
						'post-date',
					);
				}
				break;

		}

		// If we have post meta at this point, sort it.
		if ( $post_meta && ! in_array( 'empty', $post_meta ) ) {

			/**
			 * Filter for the order of the post meta.
			 * 
			 * Allows child themes to modify the order of the post meta.
			 * Note: Any post meta items added via the chaplin_post_meta_items filter will not be affected by this sorting.
			 * 
			 * @param array $post_meta_order 	Order of the post meta items.
			 */

			$post_meta_order = apply_filters( 'chaplin_post_meta_order', array( 'post-date', 'author', 'categories', 'jetpack-portfolio-type', 'tags', 'jetpack-portfolio-tag', 'comments', 'sticky', 'edit-link' ) );

			// Store any custom post meta items in a separate array, so we can append them after sorting.
			$post_meta_custom = array_diff( $post_meta, $post_meta_order );

			// Loop over the intended order, and sort $post_meta_reordered accordingly.
			$post_meta_reordered = array();
			foreach ( $post_meta_order as $i => $post_meta_name ) {
				$original_i = array_search( $post_meta_name, $post_meta );
				if ( $original_i === false ) continue;
				$post_meta_reordered[$i] = $post_meta[$original_i];
			}

			// Reassign the reordered post meta with custom post meta items appended, and update the indexes.
			$post_meta = array_values( array_merge( $post_meta_reordered, $post_meta_custom ) );

		}

		/**
		 * Filter for the post meta.
		 * 
		 * Allows child themes to add, remove and modify which post meta items to include.
		 * 
		 * @param array 	$post_meta 	Post meta items to include in the post meta.
		 * @param string 	$location 	Post meta location being output.
		 */

		$post_meta = apply_filters( 'chaplin_post_meta_items', $post_meta, $location );

		// If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output.
		if ( ! $post_meta || ( $post_meta && in_array( 'empty', $post_meta ) ) ) return;

		// Make sure the right color is used for the post meta.
		if ( chaplin_is_cover_template( $post_id ) && $location == 'single-top' ) {
			$post_meta_classes[] = 'color-inherit';
		} else {
			$post_meta_classes[] = 'color-accent';
		}

		/**
		 * Filter for the post meta CSS classes.
		 * 
		 * Allows child themes to filter the classes on the post meta wrapper element and list element.
		 * 
		 * @param array 	$classes 	CSS classes of the element.
		 * @param string	$location 	Post meta location being output.
		 * @param array		$post_meta 	Post meta items included in the location.
		 */

		$post_meta_wrapper_classes = apply_filters( 'chaplin_post_meta_wrapper_classes', $post_meta_wrapper_classes, $location, $post_meta );
		$post_meta_classes = apply_filters( 'chaplin_post_meta_classes', $post_meta_classes, $location, $post_meta );

		// Convert the class arrays to strings for output.
		$post_meta_wrapper_classes_str = implode( ' ', $post_meta_wrapper_classes );
		$post_meta_classes_str = implode( ' ', $post_meta_classes );

		// Enable the $has_meta variable to be modified in actions.
		global $has_meta;

		// Default it to false, to make sure we don't output an empty container.
		$has_meta = false;

		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		// Record output.
		ob_start();
		?>

		<div class="<?php echo esc_attr( $post_meta_wrapper_classes_str ); ?>">
			<ul class="<?php echo esc_attr( $post_meta_classes_str ); ?>">

				<?php

				/**
				 * Action run before output of post meta items.
				 * 
				 * If you add any output to this action, make sure you include $has_meta as a global variable 
				 * and set it to true.
				 * 
				 * @param array		$post_meta 	Post meta items included in the location.
				 * @param array 	$post_id 	ID of the post.
				 * @param string	$location 	Post meta location being output.
				 */

				do_action( 'chaplin_start_of_post_meta_list', $post_meta, $post_id, $location );

				foreach ( $post_meta as $post_meta_item ) :

					switch ( $post_meta_item ) {

						// Post date
						case 'post-date' : 
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
							<?php
							break;

						// Author
						case 'author' : 
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
									printf( esc_html_x( 'By %s', '%s = author name', 'chaplin' ), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author_meta( 'display_name' ) ) . '</a>' ); ?>
								</span>
							</li>
							<?php
							break;

						// Categories
						case 'categories' : 
							if ( ! has_category() ) break;
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
							break;

						// Jetpack Portfolio Type
						case 'jetpack-portfolio-type' : 
							if ( ! has_term( '', 'jetpack-portfolio-type', $post_id ) ) break;
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
							break;

						// Tags
						case 'tags' : 
							if ( ! has_tag( '', $post_id ) ) break;
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
							break;

						// Jetpack Portfolio Tags
						case 'jetpack-portfolio-tag' : 
							if ( ! has_term( '', 'jetpack-portfolio-tag', $post_id ) ) break;
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
							break;

						// Comments
						case 'comments' : 
							if ( post_password_required() || ! comments_open() || ! get_comments_number() ) break;
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
							break;

						// Sticky
						case 'sticky' : 
							if ( ! is_sticky() ) break;
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
							<?php
							break;

						// Edit link
						case 'edit-link' : 
							if ( ! current_user_can( 'edit_post', $post_id ) ) break;
							$has_meta = true;
							?>
							<li class="post-edit">

								<a href="<?php echo esc_url( get_edit_post_link() ); ?>" class="meta-wrapper">
									<span class="meta-icon">
										<?php chaplin_the_theme_svg( 'edit' ); ?>
									</span>
									<span class="meta-text">
										<?php esc_html_e( 'Edit', 'chaplin' ); ?>
									</span>
								</a>

							</li>
							<?php
							break;
						
						default : 

							/**
							 * Action for handling of custom post meta items.
							 * 
							 * This action gets called if the post meta looped over doesn't match any of the types supported
							 * out of the box in Chaplin. If you've added a custom post meta type in a child theme, you can
							 * output it here by hooking into chaplin_post_meta_[your-post-meta-key].
							 * 
							 *	Note: If you add any output to this action, make sure you include $has_meta as a global
							 *	variable and set it to true.
							 * 
							 * @param array 	$post_id 	ID of the post.
							 * @param string	$location 	Post meta location being output.
							 */

							do_action( 'chaplin_post_meta_' . $post_meta_item, $post_id, $location );
					}

				endforeach;

				/**
				 * Action run after output of post meta items.
				 * 
				 * If you add any output to this action, make sure you include $has_meta as a global variable 
				 * and set it to true.
				 * 
				 * @param array		$post_meta 	Post meta items included in the location.
				 * @param array 	$post_id 	ID of the post.
				 * @param string	$location 	Post meta location being output.
				 */

				do_action( 'chaplin_end_of_post_meta_list', $post_meta, $post_id, $location );

				?>

			</ul>
		</div>

		<?php

		wp_reset_postdata();

		// Get the recorded output.
		$meta_output = ob_get_clean();

		// If there is post meta, return it.
		return ( $has_meta && $meta_output ) ? $meta_output : '';

	}
endif;


/* ------------------------------------------------------------------------------------------------
   GET POST GRID COLUMN CLASSES
   Gets the number of columns set in the Customizer, and returns the classes that should be used to
   set the post grid to the number of columns specified
--------------------------------------------------------------------------------------------------- */

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


/*	-----------------------------------------------------------------------------------------------
	OUTPUT LOADING INDICATOR
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_loading_indicator' ) ) :
	function chaplin_loading_indicator() {

		$extra_loading_classes = '';

		// Check if the primary and border colors are the same
		$primary_color = 	get_theme_mod( 'chaplin_primary_text_color' );
		$border_color = 	get_theme_mod( 'chaplin_border_color' );

		$extra_loading_classes .= ( $primary_color == $border_color ) ? ' same-primary-border-color' : '';

		echo '<div class="loader border-color-border' . $extra_loading_classes . '"></div>';

	}
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT PREVIOUS POSTS LINK ON ARCHIVE PAGES
	On archive pages, when on at least page 2 and using the button or scroll load more type, output
	a link allowing visitor to go back to the previous page in the chronology.
	(When you're on page 2, output a link to go back to page one.)
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_output_previous_posts_link' ) ) :
	function chaplin_output_previous_posts_link() {

		global $paged;
		$pagination_type = get_theme_mod( 'chaplin_pagination_type', 'button' );
		
		if ( ( $pagination_type == 'button' || $pagination_type = 'scroll' ) && $paged && $paged > 1 ) : 
			?>

			<div class="previous-posts-link-wrapper color-secondary hide-no-js">
				<?php previous_posts_link( '<span class="arrow" aria-hidden="true">&larr; </span>' . __( 'To The Previous Page', 'chaplin' ) ); ?>
			</div><!-- .previous-posts-link-wrapper -->
			
			<?php
		endif;

	}
	add_action( 'chaplin_posts_start', 'chaplin_output_previous_posts_link' );
endif;


/*	-----------------------------------------------------------------------------------------------
	BREADCRUMBS
	Maybe output breadcrumbs, if enabled in the Customizer settings.

	@param array $args {
		@type int		$post_id				The ID of the post to output the breadcrumbs for
		@type string	$additional_classes		Extra classes for the breadcrumbs wrapper
	}
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_maybe_output_breadcrumbs' ) ) : 
	function chaplin_maybe_output_breadcrumbs( $args = array() ) {

		// Check if we're showing breadcrumbs
		$show_breadcrumbs = get_theme_mod( 'chaplin_show_breadcrumbs', false );
		if ( ! $show_breadcrumbs ) return;

		// No breadcrumbs on the front page
		if ( is_front_page() ) return;

		// Filter the arguments
		$args = apply_filters( 'chaplin_breadcrumbs_args', wp_parse_args( $args, array(
			'post_id'				=> null,
			'additional_classes'	=> '',
		) ) );

		// Get them as variables, to simplify
		$post_id 			= $args['post_id'];
		$additional_classes = $args['additional_classes'] ? ' ' . $args['additional_classes'] : '';

		// Get the post object
		if ( is_home() ) {
			$post_id = get_option( 'page_for_posts' );
			$post = get_post( $post_id );
		} elseif ( $post_id ) {
			$post = get_post( $post_id );
		} elseif ( is_single() || is_page() || is_attachment() ) {
			global $post;
			if ( $post ) {
				$post_id = $post->ID;
			}
		} 

		// Get the queried object
		$queried_object = get_queried_object() ? get_queried_object() : null;

		// Get the id of the page for posts, if one exists
		$page_for_posts_id = get_option( 'page_for_posts' );

		// Specify a seperator
		$sep = apply_filters( 'chaplin_breadcrumbs_separator', '<span class="sep fill-children-current-color chevron-icon">' . chaplin_get_theme_svg( 'chevron-right' ) . '</span>' );

		?>

		<div class="breadcrumbs-wrapper<?php echo esc_attr( $additional_classes ); ?>">

			<div class="breadcrumbs-inner-wrapper no-scrollbars">

				<ul class="breadcrumbs reset-list-style color-secondary">

					<?php

					// Record the output of the breadcrumbs list items
					ob_start();

					// No seperator before the first item
					echo '<li><a href="' . home_url() . '">' . __( 'Home', 'chaplin' ) . '</a></li>';

					if ( is_404() ) {
						echo '<li>' . $sep . __( 'Error 404', 'chaplin' ) . '</li>';
					} elseif ( is_tag() || is_category() || is_tax() ) {

						$taxonomy 			= get_taxonomy( $queried_object->taxonomy );
						$taxonomy_labels 	= get_taxonomy_labels( $taxonomy );

						// If we're showing post taxonomies, and a page for posts exists, link to it
						if ( ( is_tag() || is_category() ) && $page_for_posts_id ) {
							echo '<li>' . $sep . '<a href="' . get_permalink( $page_for_posts_id ) . '">' . get_the_title( $page_for_posts_id ) . '</a></li>';
						}
						// If we're showing a taxonomy, and that taxonomy has a single custom post type, and that custom post type is public and has an archive, link to it
						else {
							$tax_cpts = isset( $taxonomy->object_type ) ? $taxonomy->object_type : array();
							if ( count( $tax_cpts ) === 1 ) {
								$tax_cpt = get_post_type_object( $tax_cpts[0] );
								if ( $tax_cpt && $tax_cpt->public && $tax_cpt->has_archive ) {
									$tax_cpt_url = get_post_type_archive_link( $tax_cpt->name );
									echo '<li>' . $sep . '<a href="' . esc_url( $tax_cpt_url ) . '">' . $tax_cpt->labels->name . '</a></li>';
								}
							}
						}

						echo '<li>' . $sep . $taxonomy_labels->singular_name . '</li>';

						// Output all ancestors to the term
						$ancestors = get_ancestors( $queried_object->term_id, $queried_object->taxonomy, 'taxonomy' );
						
						if ( $ancestors ) {
							foreach ( $ancestors as $ancestor_id ) {
								$ancestor_term = get_term( $ancestor_id, $queried_object->taxonomy );
								echo '<li>' . $sep . '<a href="' . esc_url( get_term_link( $ancestor_term ) ) . '">' . $ancestor_term->name . '</a></li>';
							}
						}

						echo '<li>' . $sep . '<a href="' . esc_url( get_term_link( $queried_object ) ) . '">' . $queried_object->name . '</a></li>';
					} elseif ( is_day() ) {
						echo '<li>' . $sep . __( 'Day', 'chaplin' ) . '</li>';
						echo '<li>' . $sep . ''; the_time( get_option( 'date_format' ) ); echo'</li>';
					} elseif ( is_month() ) {
						echo '<li>' . $sep .  __( 'Month', 'chaplin' ) . '</li>';
						echo '<li>' . $sep . get_the_time( 'F Y' ) . '</li>';
					} elseif ( is_year() ) {
						echo '<li>' . $sep . __( 'Year', 'chaplin' ) . '</li>';
						echo '<li>' . $sep . get_the_time( 'Y' ) . '</li>';
					} elseif ( is_author() ) {
						echo '<li>' . $sep . __( 'Author', 'chaplin' ) . '</li>';
						echo '<li>' . $sep . '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></li>'; 
					} elseif ( isset( $_GET['paged'] ) && !empty( $_GET['paged'] ) ) {
						echo '<li>' . $sep . __( 'Archive', 'chaplin' ) . '</li>';
					} elseif ( is_search() ) {
						echo '<li>' . $sep . __( 'Search', 'chaplin' ) . '</li>';
						echo '<li>' . $sep . '"' . get_search_query() . '"</li>';
					} elseif ( is_post_type_archive() ) {
						echo '<li>' . $sep . $queried_object->labels->name .'</li>';
					} elseif ( is_archive() || is_home() ) {
						echo '<li>' . $sep . get_the_archive_title() .'</li>';
					} elseif ( is_singular() ) {

						// Get the post type data
						$post_type 		= get_post_type( $post_id );
						$post_type_obj 	= get_post_type_object( $post_type );

						// If the post type has a post type archive, output it
						if ( $post_type_obj->has_archive ) {
							echo '<li>' . $sep . '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '">' . $post_type_obj->labels->name . '</a></li>';
						} elseif ( $post_type == 'attachment' ) {
							echo '<li>' . $sep . __( 'Attachment', 'chaplin' ) . '</li>';
						} elseif ( $post_type == 'product' ) {
							$shop_id = get_option( 'woocommerce_shop_page_id' );
							if ( $shop_id ) {
								echo '<li>' . $sep . '<a href="' . esc_url( get_permalink( $shop_id ) ) . '">' . get_the_title( $shop_id ) . '</a></li>';
							}
						}

						// Display ancestors for post types that support it
						$ancestors = get_post_ancestors( $post_id );
						if ( $ancestors ) {
							$ancestors = array_reverse( $ancestors );
							foreach ( $ancestors as $ancestor_id ) {
								echo '<li>' . $sep . '<a href="' . esc_url( get_permalink( $ancestor_id ) ) . '">' . get_the_title( $ancestor_id ) . '</a></li>';
							}
						}

						// Output link to the blog page if we're on a blog post
						if ( $post_type == 'post' && $page_for_posts_id ) {
							echo '<li>' . $sep . '<a href="' . esc_url( get_permalink( $page_for_posts_id ) ) . '">' . get_the_title( $page_for_posts_id ) . '</a></li>';	
						}

						echo '<li>' . $sep . '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a></li>';

					} else {

						echo '<li>' . $sep . '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a></li>';

					}

					// Make the markup of the breadcrumbs list items filterable.
					// Child themes and plugins can set  the list items with their own logic here!
					echo apply_filters( 'chaplin_breadcrumbs_list_items_markup', ob_get_clean(), $args );

					?>

				</ul><!-- .breadcrumbs -->

			</div><!-- .no-scrollbars -->

		</div><!-- .breadcrumbs-wrapper -->

		<?php

	}
	add_action( 'chaplin_archive_header_start', 'chaplin_maybe_output_breadcrumbs' );
	add_action( 'chaplin_entry_header_start', 'chaplin_maybe_output_breadcrumbs' );
endif;


/*	-----------------------------------------------------------------------------------------------
	POST META SINGULAR BOTTOM
	Maybe output the post meta bottom on singular.

	@param	$post_id int	The ID of the post.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_maybe_output_single_post_meta_bottom' ) ) : 
	function chaplin_maybe_output_single_post_meta_bottom( $post_id ) {

		// Single bottom post meta
		chaplin_the_post_meta( $post_id, 'single-bottom' );

	}
	add_action( 'chaplin_entry_footer', 'chaplin_maybe_output_single_post_meta_bottom', 10 );
endif;


/*	-----------------------------------------------------------------------------------------------
	AUTHOR BIO
	Maybe output the author bio, if enabled in the Customizer settings.

	@param	$post_id int	The ID of the post.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_maybe_output_author_bio' ) ) : 
	function chaplin_maybe_output_author_bio( $post_id ) {

		// Check if we're set to show the author bio.
		$show_author_bio = get_theme_mod( 'chaplin_enable_author_bio', false );
		if ( ! $show_author_bio ) return;

		// Get the post we're showing the author bio for.
		$author_post = get_post( $post_id );
		if ( ! $author_post ) return;

		// Get data about the post post.
		$post_type 		= isset( $author_post->post_type ) ? $author_post->post_type : '';
		$post_author_id = isset( $author_post->post_author ) ? $author_post->post_author : null;

		if ( ! $post_author_id ) return;

		// Check if the post type should display an author bio (only post, by default).
		$author_bio_supported_post_types = apply_filters( 'chaplin_author_bio_supported_post_types', array( 'post' ) );
		if ( ! in_array( $post_type, $author_bio_supported_post_types ) ) return;

		// Get the author, and allow child themes and plugins to filter whether to display the author bio for specific users.
		$author_bio_show_for_user = apply_filters( 'chaplin_author_bio_enable_for_user', true, $post_author_id );
		if ( ! $author_bio_show_for_user ) return;

		// Get author information.
		$author_avatar 		= get_avatar( $post_author_id, 88 );
		$author_name 		= get_the_author_meta( 'display_name', $post_author_id ) ? get_the_author_meta( 'display_name', $post_author_id ) : get_the_author_meta( 'nickname', $post_author_id );
		$author_description = get_the_author_meta( 'description', $post_author_id );
		$author_posts_url 	= get_author_posts_url( $post_author_id );
		$author_url 		= get_the_author_meta( 'url', $post_author_id );

		// Output the author bio.
		?>

		<div class="author-bio section-inner thin bg-light-background color-light-background">

			<div class="author-bio-inner color-primary">

				<?php do_action( 'chaplin_author_bio_start' ); ?>

				<header class="author-bio-header">

					<?php if ( $author_avatar ) : ?>
						<a href="<?php echo esc_url( $author_posts_url ); ?>" class="author-avatar">
							<?php echo $author_avatar; ?>
						</a><!-- .author-avatar -->
					<?php endif; ?>

					<?php if ( $author_name ) : ?>
						<h2 class="author-bio-title">
							<?php printf( esc_html_x( 'By %s', '%s = author name', 'chaplin' ), '<a href="' . esc_url( $author_posts_url ) . '">' . $author_name . '</a>' ); ?>
						</h2>
					<?php endif; ?>

				</header><!-- .author-bio-header -->

				<?php if ( $author_description ) : ?>
					<div class="author-bio-description">
						<?php echo wpautop( $author_description ); ?>
					</div><!-- .author-bio-description -->
				<?php endif; ?>

				<footer class="author-bio-footer color-accent">

					<ul class="author-bio-meta-list post-meta">

						<?php do_action( 'chaplin_author_bio_meta_list_start' ); ?>

						<li class="author-bio-meta-archive-link icon-chevron-circled">
							<a class="meta-wrapper" href="<?php echo esc_url( $author_posts_url ); ?>">
								<span class="meta-icon">
									<?php chaplin_the_theme_svg( 'chevron-right-circled' ); ?>
								</span>
								<span class="meta-text"><?php _e( 'View Archive', 'chaplin' ); ?></span>
							</a>
						</li>

						<?php if ( $author_url ) : ?>

							<li class="author-bio-meta-website-link icon-chevron-circled">
								<a class="meta-wrapper" href="<?php echo esc_url( $author_url ); ?>">
									<span class="meta-icon">
										<?php chaplin_the_theme_svg( 'chevron-right-circled' ); ?>
									</span>
									<span class="meta-text"><?php _e( 'Visit Website', 'chaplin' ); ?></span>
								</a>
							</li>

						<?php endif; ?>

						<?php do_action( 'chaplin_author_bio_meta_list_end' ); ?>

					</ul><!-- .author-bio-meta-list -->

				</footer><!-- .author-bio-footer -->

				<?php do_action( 'chaplin_author_bio_end' ); ?>

			</div><!-- .author-bio-inner -->

		</div><!-- .author-bio -->

		<?php

	}
	add_action( 'chaplin_entry_footer', 'chaplin_maybe_output_author_bio', 20 );
endif;


/*	-----------------------------------------------------------------------------------------------
	SINGLE POST NAVIGATION
	Maybe output the single post navigation.
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_maybe_output_single_post_navigation' ) ) : 
	function chaplin_maybe_output_single_post_navigation() {

		// Only on posts
		if ( ! is_singular( apply_filters( 'chaplin_the_post_navigation_post_types', array( 'post' ) ) ) ) return;

		the_post_navigation( array(
			'prev_text' 	=> '<span class="arrow" aria-hidden="true">&larr;</span><span class="screen-reader-text">' . __( 'Previous post:', 'chaplin' ) . '</span><span class="post-title">%title</span>',
			'next_text' 	=> '<span class="arrow" aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . __( 'Next post:', 'chaplin' ) . '</span><span class="post-title">%title</span>',
		) );

	}
	add_action( 'chaplin_entry_footer', 'chaplin_maybe_output_single_post_navigation', 30 );
endif;


/*	-----------------------------------------------------------------------------------------------
	OUTPUT PREVIOUS POSTS LINK ON ARCHIVE PAGES
	On archive pages, when on at least page 2 and using the button or scroll load more type, output
	a link allowing visitor to go back to the previous page in the chronology.
	(When you're on page 2, output a link to go back to page one.)
--------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'chaplin_output_previous_posts_link' ) ) :
	function chaplin_output_previous_posts_link() {

		global $paged;
		$pagination_type = get_theme_mod( 'chaplin_pagination_type', 'button' );
		
		if ( ( $pagination_type == 'button' || $pagination_type = 'scroll' ) && $paged && $paged > 1 ) : 
			?>

			<div class="previous-posts-link-wrapper color-secondary hide-no-js">
				<?php previous_posts_link( '<span class="arrow" aria-hidden="true">&larr; </span>' . __( 'To The Previous Page', 'chaplin' ) ); ?>
			</div><!-- .previous-posts-link-wrapper -->
			
			<?php
		endif;

	}
	add_action( 'chaplin_posts_start', 'chaplin_output_previous_posts_link' );
endif;
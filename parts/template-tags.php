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

		$args = wp_parse_args( $args, array(
			'theme_location'	=> 'social-menu',
			'container'			=> '',
			'container_class'	=> '',
			'items_wrap'		=> '%3$s',
			'menu_id'			=> '',
			'menu_class'		=> '',
			'depth'				=> 1,
			'link_before'		=> '<span class="screen-reader-text">',
			'link_after'		=> '</span>',
			'fallback_cb'		=> '',
		) );

		if ( has_nav_menu( $args['theme_location'] ) ) : ?>

			<ul class="social-menu reset-list-style social-icons s-icons">

				<?php wp_nav_menu( $args ); ?>

			</ul><!-- .social-menu -->

			<?php 
		endif;

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

   @param	$post_id int		The ID of the post for which the post meta should be output
   @param	$location string	Which post meta location to output – single or preview
--------------------------------------------------------------------------------------------------- */

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
			if ( chaplin_is_cover_template( $post_id ) && $location == 'single-top' ) {
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
					do_action( 'chaplin_start_of_post_meta_list', $post_meta, $post_id, $location );

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
								printf( esc_html_x( 'By %s', '%s = author name', 'chaplin' ), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author_meta( 'display_name' ) ) . '</a>' ); ?>
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
					if ( in_array( 'comments', $post_meta ) && ! post_password_required() && ( comments_open() || get_comments_number() ) ) : 
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
					<?php endif; 

					// Allow output of additional post meta types to be added by child themes and plugins
					do_action( 'chaplin_end_of_post_meta_list', $post_meta, $post_id, $location );

					?>

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

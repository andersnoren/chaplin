<article <?php post_class( 'section-inner' ); ?> id="post-<?php the_ID(); ?>">

	<?php 

	$post_type = get_post_type();
	
	// On the cover page template, output the cover header
	if ( is_page_template( array( 'template-cover.php', 'template-full-width-cover.php' ) ) ) : 

		$cover_header_style = '';
		$cover_header_classes = '';

		$color_overlay_style = '';
		$color_overlay_classes = '';

		$section_inner_classes = '';

		$image_url = ! post_password_required() ? get_the_post_thumbnail_url( $post->ID, 'chaplin_fullscreen' ) : '';

		if ( $image_url ) {
			$cover_header_style 	= ' style="background-image: url( ' . esc_url( $image_url ) . ' );"';
			$cover_header_classes 	= ' bg-image';
		}

		// Get the color used for the color overlay
		$color_overlay_color = get_theme_mod( 'chaplin_cover_template_overlay_background_color' );
		if ( $color_overlay_color ) {
			$color_overlay_style = ' style="color: ' . esc_attr( $color_overlay_color ) . ';"';
		} else {
			$color_overlay_style = '';
		}

		// Note: The text color is applied by chaplin_get_customizer_css(), in functions.php

		// Get the fixed background attachment option
		if ( get_theme_mod( 'chaplin_cover_template_fixed_background', true ) ) {
			$cover_header_classes .= ' bg-attachment-fixed';
		}

		// Get the opacity of the color overlay
		$color_overlay_opacity = get_theme_mod( 'chaplin_cover_template_overlay_opacity' );
		$color_overlay_opacity = ( $color_overlay_opacity === false ) ? 80 : $color_overlay_opacity;
		$color_overlay_classes .= ' opacity-' . $color_overlay_opacity;

		// Get the blend mode of the color overlay (default = multiply)
		$color_overlay_opacity = get_theme_mod( 'chaplin_cover_template_overlay_blend_mode', 'multiply' );
		$color_overlay_classes .= ' blend-mode-' . $color_overlay_opacity;

		// Check whether we're fading the text
		$overlay_fade_text = get_theme_mod( 'chaplin_cover_template_fade_text', true );
		$section_inner_classes = $overlay_fade_text ? ' fade-block' : '';
	
		?>

		<div class="cover-header screen-height screen-width<?php echo esc_attr( $cover_header_classes ); ?>"<?php echo $cover_header_style; ?>>
			<div class="cover-header-inner-wrapper">
				<div class="cover-header-inner">
					<div class="cover-color-overlay color-accent<?php echo esc_attr( $color_overlay_classes ); ?>"<?php echo $color_overlay_style; ?>></div>
					<div class="section-inner<?php echo esc_attr( $section_inner_classes ); ?>">
						<?php get_template_part( 'parts/page-header' ); ?>
					</div><!-- .section-inner -->
				</div><!-- .cover-header-inner -->
			</div><!-- .cover-header-inner-wrapper -->
		</div><!-- .cover-header -->

	<?php 
	
	// On all other pages, output the regular page header
	else : 
	
		get_template_part( 'parts/page-header' );
		
		if ( has_post_thumbnail() && ! post_password_required() ) : ?>

			<figure class="featured-media">

				<?php 
				
				the_post_thumbnail();

				$caption = get_the_post_thumbnail_caption();
				
				if ( $caption ) : ?>

					<figcaption class="wp-caption-text"><?php echo esc_html( $caption ); ?></figcaption>

				<?php endif; ?>

			</figure><!-- .featured-media -->

		<?php endif; ?>

	<?php endif; ?>

	<div class="post-inner" id="post-inner">

		<div class="entry-content">

			<?php 
			the_content();
			wp_link_pages( array(
				'before'           => '<nav class="post-nav-links bg-light-background"><span class="label">' . __( 'Pages:', 'chaplin' ) . '</span>',
				'after'            => '</nav>',
			) );
			if ( $post_type !== 'post' ) {
				edit_post_link();
			}
			?>

		</div><!-- .entry-content -->

		<?php 

		// Single bottom post meta
		chaplin_the_post_meta( $post->ID, 'single-bottom' );

		// Single post navigation
		if ( is_single() ) {
			the_post_navigation( array(
				'prev_text' => '<span class="arrow" aria-hidden="true">&larr;</span><span class="screen-reader-text">' . __( 'Previous post:', 'chaplin' ) . '</span><span class="post-title">%title</span>',
				'next_text' => '<span class="arrow" aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . __( 'Next post:', 'chaplin' ) . '</span><span class="post-title">%title</span>',
			) );
		}

		// Output comments wrapper if it's a post, or if comments are open, or if there's a comment number – and check for password
		if ( ( $post_type == 'post' || comments_open() || get_comments_number() ) && ! post_password_required() ) : ?>

			<div class="comments-wrapper">

				<?php comments_template(); ?>

			</div><!-- .comments-wrapper -->

		<?php endif; ?>

	</div><!-- .post-inner -->

</article><!-- .post -->
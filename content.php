<article <?php post_class( 'section-inner' ); ?> id="post-<?php the_ID(); ?>">

	<?php 
	
	// On the cover page template, output the cover header
	if ( is_page_template( 'template-cover.php' ) ) : 

		$cover_header_style = '';
		$cover_header_classes = '';

		if ( has_post_thumbnail() ) {
			$image_url = get_the_post_thumbnail_url( $post->ID, 'chaplin_fullscreen' );

			$cover_header_style 	= ' style="background-image: url( ' . $image_url . ' );"';
			$cover_header_classes 	= ' bg-image';

			// Get the color used for the color overlay
			$color_overlay_color = get_theme_mod( 'chaplin_cover_template_overlay_color' );
			if ( $color_overlay_color ) {
				$color_overlay_style = ' style="color: ' . $color_overlay_color . ';"';
			} else {
				$color_overlay_style = '';
			}

			// Get the fixed background attachment option
			if ( get_theme_mod( 'chaplin_cover_template_fixed_background' ) ) {
				$cover_header_classes .= ' bg-attachment-fixed';
			}

			// Get the opacity of the color overlay
			$color_overlay_opacity = get_theme_mod( 'chaplin_cover_template_overlay_opacity' ) ?: '80';
			$color_overlay_classes = ' opacity-' . $color_overlay_opacity;
		}
	
		?>

		<div class="cover-header screen-height screen-width<?php echo $cover_header_classes; ?>"<?php echo $cover_header_style; ?>>
			<div class="cover-header-inner-wrapper">
				<div class="cover-header-inner">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="cover-color-overlay color-accent<?php echo $color_overlay_classes; ?>"<?php echo $color_overlay_style; ?>></div>
					<?php endif; ?>
					<div class="section-inner fade-block">
						<?php get_template_part( 'parts/page-header' ); ?>
					</div><!-- .section-inner -->
				</div><!-- .cover-header-inner -->
			</div><!-- .cover-header-inner-wrapper -->
		</div><!-- .cover-header -->

	<?php 
	
	// On all other pages, output the regular page header
	else : 
	
		get_template_part( 'parts/page-header' );
		
		if ( has_post_thumbnail() ) : ?>

			<figure class="featured-media">

				<?php 
				
				the_post_thumbnail();

				$caption = get_the_post_thumbnail_caption();
				
				if ( $caption ) : ?>

					<figcaption class="wp-caption-text"><?php echo $caption; ?></figcaption>

				<?php endif; ?>

			</figure><!-- .featured-media -->

		<?php endif; ?>

	<?php endif; ?>

	<div class="post-inner" id="post-inner">

		<div class="entry-content">

			<?php 
			the_content();
			wp_link_pages();
			edit_post_link();
			?>

		</div><!-- .entry-content -->

		<?php 

		// Single bottom post meta
		chaplin_the_post_meta( $post->ID, 'single-bottom' );

		if ( is_single() ) : 

			// Single pagination
			$next_post = get_next_post();
			$prev_post = get_previous_post();

			if ( $next_post || $prev_post ) :

				$pagination_classes = '';

				if ( ! $next_post ) {
					$pagination_classes = ' only-one only-prev';
				} elseif ( ! $prev_post ) {
					$pagination_classes = ' only-one only-next';
				}

				?>

				<nav class="pagination-single border-color-border<?php echo $pagination_classes; ?>">

					<?php if ( $prev_post ) : ?>

						<a class="previous-post" href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
							<span class="arrow">&larr;</span>
							<span class="title"><span class="title-inner"><?php echo wp_kses_post( get_the_title( $prev_post->ID ) ); ?></span></span>
						</a>

					<?php endif; ?>

					<?php if ( $next_post ) : ?>

						<a class="next-post" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
							<span class="arrow">&rarr;</span>
							<span class="title"><span class="title-inner"><?php echo wp_kses_post( get_the_title( $next_post->ID ) ); ?></span></span>
						</a>

					<?php endif; ?>

				</nav><!-- .single-pagination -->

				<?php

			endif;

		endif;
		
		?>

		<div class="comments-wrapper">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

	</div><!-- .post-inner -->

</article><!-- .post -->
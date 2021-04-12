<article <?php post_class( 'section-inner' ); ?> id="post-<?php the_ID(); ?>">

	<?php 

	do_action( 'chaplin_entry_article_start', $post->ID );

	$_post_type = get_post_type();
	
	// On the cover page template, output the cover header.
	if ( chaplin_is_cover_template() ) : 

		include( locate_template( 'parts/page-header-cover.php' ) );
	
	// On all other pages, output the regular page header.
	else : 
	
		get_template_part( 'parts/page-header' );
		
		if ( has_post_thumbnail() && ! post_password_required() ) : 
			?>

			<figure class="featured-media">

				<?php 

				do_action( 'chaplin_featured_media_start', $post->ID );
				
				the_post_thumbnail();

				$caption = get_the_post_thumbnail_caption();
				
				if ( $caption ) : 
					?>

					<figcaption class="wp-caption-text"><?php echo wp_kses_post( $caption ); ?></figcaption>

					<?php 
				endif; 

				do_action( 'chaplin_featured_media_end', $post->ID );
				
				?>

			</figure><!-- .featured-media -->

			<?php 
		endif; // has_post_thumbnail()
	endif; // chaplin_is_cover_template()
	?>

	<div class="post-inner" id="post-inner">

		<div class="entry-content">

			<?php 
			the_content();
			wp_link_pages( array(
				'before'           => '<nav class="post-nav-links bg-light-background"><span class="label">' . __( 'Pages:', 'chaplin' ) . '</span>',
				'after'            => '</nav>',
			) );
			if ( $_post_type !== 'post' ) {
				edit_post_link();
			}
			?>

		</div><!-- .entry-content -->

		<?php 

		/*
		 * @hooked chaplin_maybe_output_single_post_meta_bottom - 10
		 * @hooked chaplin_maybe_output_author_bio - 20
		 * @hooked chaplin_maybe_output_single_post_navigation - 30
		 */
		do_action( 'chaplin_entry_footer', $post->ID );

		// Output comments wrapper if comments are open or if there are comments, and check for password
		if ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) : 
			?>

			<div class="comments-wrapper">
				<?php comments_template(); ?>
			</div><!-- .comments-wrapper -->

			<?php 
		endif; 
		?>

	</div><!-- .post-inner -->

	<?php do_action( 'chaplin_entry_article_end', $post->ID ); ?>

</article><!-- .post -->

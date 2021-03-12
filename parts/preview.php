<article <?php post_class( 'preview preview-' . get_post_type() ); ?> id="post-<?php the_ID(); ?>">

	<?php 
	
	if ( has_post_thumbnail( $post->ID ) ) :

		$fallback_image_url = chaplin_get_fallback_image_url();

		$image_size 	= chaplin_get_preview_image_size();
		$image_url 		= get_the_post_thumbnail_url( $post->ID, $image_size ) ?: $fallback_image_url;

		// If the post is password protected, show the fallback image (or no image, if the fallback image option is disabled)
		if ( post_password_required( $post->ID ) ) {
			$image_url = $fallback_image_url;
		}

		if ( $image_url ) : 

			$aspect_ratio 			= get_theme_mod( 'chaplin_preview_image_aspect_ratio', '16x10' );
			$image_link_classes 	= '';
			$image_link_style_attr 	= '';

			if ( $aspect_ratio !== 'original' ) {
				$image_link_classes 	= ' faux-image aspect-ratio-' . $aspect_ratio;
				$image_link_style_attr 	= ' style="background-image: url( \'' . esc_url( $image_url ) . '\' );"';
			}

			?>

			<figure class="preview-media">

				<a href="<?php the_permalink(); ?>" class="preview-media-link<?php echo esc_attr( $image_link_classes ); ?>"<?php echo $image_link_style_attr; ?>>
					<?php the_post_thumbnail( $image_size ); ?>
				</a>

			</figure><!-- .preview-media -->

			<?php 
		endif;
	endif; 
	?>

	<header class="preview-header">

		<?php 
		the_title( '<h2 class="preview-title heading-size-3"><a href="' . get_the_permalink() . '">', '</a></h2>' );

		if ( get_theme_mod( 'chaplin_display_excerpts', false ) ) :

			$excerpt = get_the_excerpt();

			if ( $excerpt ) : 
				?>

				<div class="preview-excerpt">
					<?php echo apply_filters( 'the_excerpt', $excerpt ); ?>
				</div><!-- .preview-excerpt -->

				<?php 
			endif;
		endif;

		chaplin_the_post_meta( $post->ID, 'archive' );
		?>

	</header><!-- .preview-header -->

</article><!-- .preview -->

<article <?php post_class( 'preview preview-' . get_post_type() ); ?> id="post-<?php the_ID(); ?>">

	<?php

	$fallback_image_url = chaplin_get_fallback_image_url();

	if ( ( has_post_thumbnail() && ! post_password_required() ) || $fallback_image_url ) : ?>

		<figure class="preview-media">

			<?php

			$aspect_ratio = get_theme_mod( 'chaplin_preview_image_aspect_ratio', '16x10' );

			if ( has_post_thumbnail() && ! post_password_required() ) {
				$image_size = chaplin_get_preview_image_size();
				$image_url = get_the_post_thumbnail_url( $post->ID, $image_size );
			} else {
				$image_url = $fallback_image_url;
			}

			if ( $aspect_ratio !== 'original' ) : ?>

				<a href="<?php the_permalink(); ?>" class="faux-image aspect-ratio-<?php echo $aspect_ratio; ?>" style="background-image: url( <?php echo esc_attr( $image_url ); ?> );"></a>

			<?php else : ?>

				<a href="<?php the_permalink(); ?>">
					<?php 
					if ( has_post_thumbnail() && ! post_password_required() ) {
						the_post_thumbnail( $post->ID, $image_size ); 
					} else {
						echo '<img src="' . esc_url( $fallback_image_url ) . '" />';
					}
					?>
				</a>

			<?php endif; ?>
			
		</figure><!-- .preview-media -->

	<?php endif; ?>

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
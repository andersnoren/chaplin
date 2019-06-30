<article <?php post_class( 'preview preview-' . get_post_type() ); ?> id="post-<?php the_ID(); ?>">

	<?php

	$fallback_image_url = chaplin_get_fallback_image_url();

	if ( has_post_thumbnail() || $fallback_image_url ) : ?>

		<figure class="preview-media">

			<?php
			if ( has_post_thumbnail() ) {
				$image_size = chaplin_get_preview_image_size();
				$image_url = get_the_post_thumbnail_url( $post->ID, $image_size );
			} else {
				$image_url = $fallback_image_url;
			}
			?>

			<a href="<?php the_permalink(); ?>" class="faux-image" style="background-image: url( <?php echo esc_attr( $image_url ); ?> );"></a>
			
		</figure><!-- .preview-media -->

		<?php 
	endif;
	?>

	<header class="preview-header">

		<?php 
		the_title( '<h3 class="preview-title"><a href="' . get_the_permalink() . '">', '</a></h3>' ); 
		chaplin_the_post_meta( $post->ID, 'archive' );
		?>

	</header><!-- .preview-header -->

</article>

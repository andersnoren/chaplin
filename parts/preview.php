<article <?php post_class( 'preview preview-' . get_post_type() ); ?> id="post-<?php the_ID(); ?>">

	<a href="<?php the_permalink(); ?>" class="preview-wrapper">

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

				<div class="faux-image" style="background-image: url( <?php echo $image_url; ?> );"></div>
				
			</figure><!-- .preview-media -->

			<?php 
		endif;
		?>

		<header class="preview-header">

			<?php the_title( '<h3 class="preview-title">', '</h3>' ); ?>
			<time class="preview-timestamp"><?php the_time( get_option( 'date_format' ) ); ?></time>

		</header><!-- .preview-header -->

	</a><!-- .preview-wrapper -->

</article>

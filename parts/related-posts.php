<?php

$disable_related_posts = get_theme_mod( 'chaplin_disable_related_posts', false );

if ( is_single() && ! $disable_related_posts ) :

	$related_post_ids = array();

	// Exclude sticky posts and the current post
	$exclude = get_option( 'sticky_posts' );
	$exclude[] = $post->ID;

	// Sanitize the exclude values
	$exclude = array_map( 'esc_attr', $exclude );

	// Arguments used by all the queries below
	$base_args = array(
		'orderby' 			=> 'rand',
		'post__not_in' 		=> $exclude,
		'post_status' 		=> 'publish',
		'posts_per_page' 	=> 2,
	);

	// Check categories first
	$categories = wp_get_post_categories( $post->ID );

	if ( $categories ) {

		$categories_args = $base_args;
		$categories_args['category__in'] = $categories;

		$categories_posts = get_posts( $categories_args );

		foreach ( $categories_posts as $categories_post ) {
			$related_post_ids[] = $categories_post->ID;
		}
	}

	// If we don't get at least our posts_per_page number from that, fill up with posts selected at random
	if ( count( $related_post_ids ) < $base_args['posts_per_page'] ) {

		// Only with as many as we need though
		$random_post_args = $base_args;
		$random_post_args['posts_per_page'] = $base_args['posts_per_page'] - count( $related_post_ids );

		$random_posts = get_posts( $random_post_args );

		foreach ( $random_posts as $random_post ) {
			$related_post_ids[] = $random_post->ID;
		}
	}

	// Get the posts we've collected
	$related_posts_args = $base_args;
	$related_posts_args['include'] = $related_post_ids;

	$related_posts = get_posts( $related_posts_args );

	if ( $related_posts ) : ?>

		<div class="related-posts section-inner">

			<h3 class="related-posts-title"><?php _e( 'Related Posts', 'chaplin' ); ?></h3>

			<div class="posts">

				<div class="posts-grid grid tcols-2">

					<?php

					foreach ( $related_posts as $post ) {
						setup_postdata( $post );

						?>

						<div class="grid-item">
					
							<?php get_template_part( 'parts/preview', get_post_type() ); ?>

						</div>

						<?php

					}

					wp_reset_postdata();

					?>

				</div><!-- .posts-grid -->

			</div><!-- .posts -->

		</div><!-- .related-posts -->

	<?php endif; ?>

<?php endif; ?>
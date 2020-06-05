<?php

$enable_related_posts = get_theme_mod( 'chaplin_enable_related_posts', true );
$related_post_types = apply_filters( 'chaplin_post_types_with_related_posts', array( 'post' ) );

if ( is_singular( $related_post_types ) && $enable_related_posts ) :

	$related_posts_ids = array();

	$post_type = get_post_type();

	// Determine how many posts to load depending on the post grid columns setting
	$posts_per_page = absint( get_theme_mod( 'chaplin_post_grid_columns', 2 ) );

	// If we have more than two, we want to load 4 so the second row is full on portrait tablet
	$posts_per_page = $posts_per_page > 2 ? 4 : $posts_per_page;

	// Exclude sticky posts and the current post
	$exclude = get_option( 'sticky_posts' );
	$exclude[] = $post->ID;

	// Sanitize the exclude values
	$exclude = array_map( 'esc_attr', $exclude );

	// Arguments used by all the queries below
	$base_args = array(
		'orderby'			=> 'rand',
		'post__not_in'		=> $exclude,
		'post_status'		=> 'publish',
		'post_type'			=> $post_type,
		'posts_per_page'	=> $posts_per_page,
	);

	// Get posts from the same terms as the current post
	$post_type_taxonomies_obj = get_object_taxonomies( $post_type, 'object' );
	$post_type_taxonomies = array();

	// Only include publicly queryable taxonomies
	foreach ( $post_type_taxonomies_obj as $taxonomy_name => $taxonomy_args ) {
		if ( $taxonomy_args->publicly_queryable ) $post_type_taxonomies[] = $taxonomy_name;
	}

	if ( $post_type_taxonomies ) {

		// Exclude taxonomies we don't want to relate by
		$blacklisted_taxonomies = apply_filters( 'chaplin_related_posts_blacklisted_taxonomies', array( 'post_format' ) );
		$taxonomies = array_diff( $post_type_taxonomies, $blacklisted_taxonomies );

		if ( $taxonomies ) {

			$tax_query = array();

			// Loop through the taxomies for the current post
			foreach ( $taxonomies as $taxonomy ) {
				$terms = get_the_terms( $post->ID, $taxonomy );

				// If our post is set to a term in the taxonomy
				if ( $terms ) {

					// On the first loop, set the tax_query array
					if ( ! isset( $tax_query ) ) {
						$tax_query = array(
							'relation'  => 'OR',
						);
					}

					$term_ids = array();

					// Get all of the terms it has in the taxonomy
					foreach ( $terms as $term ) {
						$term_ids[] = $term->term_id;
					}

					// And append a tax query array for that taxonomy and those terms
					$tax_query[] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_ids
					);
				}
			}

			// Merge the base args with our tax query and get the resulting post IDs
			$taxonomy_posts_ids = get_posts( array_merge( $base_args, array(
				'fields'		=> 'ids',
				'tax_query'		=> $tax_query,
			) ) );

			if ( $taxonomy_posts_ids ) {
				$related_posts_ids = array_merge( $related_posts_ids, $taxonomy_posts_ids );
			}

		}

	}

	// If we don't get at least our posts_per_page number from that, fill up with posts selected at random
	if ( count( $related_posts_ids ) < $base_args['posts_per_page'] ) {

		$random_posts_ids = get_posts( array_merge( $base_args, array(
			'posts_per_page'	=> $base_args['posts_per_page'] - count( $related_posts_ids ),
			'exclude'			=> $related_posts_ids,
			'fields'			=> 'ids',
		) ) );

		if ( $random_posts_ids ) {
			$related_posts_ids = array_merge( $related_posts_ids, $random_posts_ids );
		}
	}

	// Get the posts we've collected
	$related_posts_args = array_merge( $base_args, array(
		'include'	=> $related_posts_ids,
	) );

	// Make the arguments filterable
	$related_posts_args = apply_filters( 'chaplin_related_posts_args', $related_posts_args );

	$related_posts = get_posts( $related_posts_args );

	if ( $related_posts ) : 
	
		$post_grid_column_classes = chaplin_get_post_grid_column_classes();
		
		?>

		<div class="related-posts section-inner">

			<h2 class="related-posts-title heading-size-3"><?php esc_html_e( 'Related Posts', 'chaplin' ); ?></h2>

			<div class="posts">

				<div class="posts-grid related-posts-grid grid <?php echo $post_grid_column_classes; ?>">

					<?php
					global $post;
					foreach ( $related_posts as $post ) :
						setup_postdata( $post );
						?>

						<div class="grid-item">
							<?php get_template_part( 'parts/preview', get_post_type() ); ?>
						</div><!-- .grid-item -->

						<?php
					endforeach;
					wp_reset_postdata();
					?>

				</div><!-- .posts-grid -->

			</div><!-- .posts -->

		</div><!-- .related-posts -->

	<?php endif; ?>

<?php endif; ?>

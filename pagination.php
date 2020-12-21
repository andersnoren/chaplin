<?php

// Set the type of pagination to use
// Available types: button/links/scroll
$pagination_type = get_theme_mod( 'chaplin_pagination_type', 'button' );

// Get the global $wp_query
global $wp_query;

// Combine the query with the query_vars into a single array
$query_args = array_merge( $wp_query->query, $wp_query->query_vars );

// If max_num_pages is not already set, add it
if ( ! array_key_exists( 'max_num_pages', $query_args ) ) {
	$query_args['max_num_pages'] = $wp_query->max_num_pages;
}

// If post_status is not already set, add it
if ( ! array_key_exists( 'post_status', $query_args ) ) {
	$query_args['post_status'] = 'publish';
}

// Make sure the paged value exists and is at least 1
if ( ! array_key_exists( 'paged', $query_args ) || 0 == $query_args['paged'] ) {
	// The page that will be loaded
	$query_args['paged'] = 1;
}

// Encode our modified query
$json_query_args = wp_json_encode( $query_args ); 

$wrapper_classes 	= '';
$pagination_classes = '';

$wrapper_classes 	.= ' pagination-type-' . $pagination_type;
$pagination_classes .= ' pagination-type-' . $pagination_type;

// Indicate when we're loading into the last page, so the pagination can be hidden for the button and scroll types
if ( $query_args['max_num_pages'] == $query_args['paged'] ) {
	$wrapper_classes .= ' loaded-last-page';
}

if ( ( $query_args['max_num_pages'] >= $query_args['paged'] ) ) : ?>

	<div class="pagination-wrapper section-inner<?php echo esc_attr( $wrapper_classes ); ?>">

		<div id="pagination" class="<?php echo esc_attr( $pagination_classes ); ?>" data-query-args="<?php echo esc_attr( $json_query_args ); ?>" data-pagination-type="<?php echo esc_attr( $pagination_type ); ?>" data-load-more-target=".load-more-target">

			<?php if ( $pagination_type == 'button' ) : ?>
				<button id="load-more"><?php esc_html_e( 'Load More', 'chaplin' ); ?></button>
			<?php endif; ?>

			<?php if ( in_array( $pagination_type, array( 'button', 'scroll' ) ) ) : ?>
				<p class="out-of-posts"><?php esc_html_e( 'Nothing left to load.', 'chaplin' ); ?></p>
				<div class="loading-icon"><?php chaplin_loading_indicator(); ?></div>
			<?php endif;

			// The pagination links also work as a no-js fallback, so they always need to be output
			$has_previous_link = get_previous_posts_link();
			$has_next_link = get_next_posts_link();

			if ( $has_previous_link || $has_next_link ) :

				$link_pagination_classes = '';

				if ( ! $has_previous_link ) {
					$link_pagination_classes = ' only-next';
				} elseif ( ! $has_next_link ) {
					$link_pagination_classes = ' only-previous';
				}

				?>

				<nav class="link-pagination<?php echo esc_attr( $link_pagination_classes ); ?>">

					<?php if ( get_previous_posts_link() ) : ?>
						<?php previous_posts_link( '<span class="arrow" aria-hidden="true">&larr;</span> ' . __( 'Previous page', 'chaplin' ) ); ?>
					<?php endif; ?>

					<?php if ( get_next_posts_link() ) : ?>
						<?php next_posts_link( __( 'Next page', 'chaplin' ) . ' <span class="arrow" aria-hidden="true">&rarr;</span>' ); ?>
					<?php endif; ?>

				</nav><!-- .posts-pagination -->

			<?php endif; ?>

		</div><!-- #pagination -->

	</div><!-- .pagination-wrapper -->

<?php endif; ?>

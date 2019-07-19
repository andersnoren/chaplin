<?php get_header(); ?>

<main id="site-content" role="main">

	<?php

	$archive_title = '';
	$archive_subtitle = '';
	
	if ( is_search() ) {
		global $wp_query;
		/* Translators: %s = The search query */
		$archive_title = sprintf( _x( 'Search: %s', '%s = The search query', 'chaplin' ), '&ldquo;' . get_search_query() . '&rdquo;' );
		if ( $wp_query->found_posts ) {
			/* Translators: %s = Number of results */
			$archive_subtitle = sprintf( _nx( 'We found %s result for your search.', 'We found %s results for your search.',  $wp_query->found_posts, '%s = Number of results', 'chaplin' ), $wp_query->found_posts );
		} else {
			$archive_subtitle = __( 'We could not find any results for your search. You can give it another try through the search form below.', 'chaplin' );
		}
	} else {
		$archive_title = get_the_archive_title();
		$archive_subtitle = get_the_archive_description( '<div>', '</div>' ); 
	}

	// Check if we're hiding the archive header on the blog page
	$show_home_header = get_theme_mod( 'chaplin_show_archive_header_on_home', true );
	
	if ( ( ! is_home() || is_home() && $show_home_header ) && ( $archive_title || $archive_subtitle ) ) : ?>
		
		<header class="archive-header section-inner">

			<?php if ( $archive_title ) : ?>
				<h1 class="archive-title"><?php echo wp_kses_post( $archive_title ); ?></h1>
			<?php endif; ?>

			<?php if ( $archive_subtitle ) : ?>
				<div class="archive-subtitle section-inner thin max-percentage intro-text"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></div>
			<?php endif; ?>
			
		</header><!-- .archive-header -->

	<?php endif; ?>

	<div class="posts section-inner">

		<?php if ( have_posts() ) : 

			$post_grid_column_classes = chaplin_get_post_grid_column_classes();
		
			?>

			<div class="posts-grid grid load-more-target <?php echo $post_grid_column_classes; ?>">
			
				<?php while ( have_posts() ) : the_post(); ?>

					<div class="grid-item">
				
						<?php get_template_part( 'parts/preview', get_post_type() ); ?>

					</div><!-- .grid-item -->

				<?php endwhile; ?>

			</div><!-- .posts-grid -->

		<?php elseif ( is_search() ) : ?>

			<div class="no-search-results-form">

				<?php get_search_form(); ?>

			</div><!-- .no-search-results -->

		<?php endif; ?>
	
	</div><!-- .posts -->

	<?php get_template_part( 'pagination' ); ?>

</main><!-- #site-content -->

<?php get_footer(); ?>
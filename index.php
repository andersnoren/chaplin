<?php get_header(); ?>

<main id="site-content" role="main">

	<?php

	$archive_title = '';
	$archive_subtitle = '';
	
	if ( is_search() ) {
		global $wp_query;
		$archive_title = sprintf( __( 'Search: %s', 'chaplin' ), '&ldquo;' . get_search_query() . '&rdquo;' );
		if ( $wp_query->found_posts ) {
			$archive_subtitle = sprintf( _n( 'We found %s result for your search.', 'We found %s results for your search.', $wp_query->found_posts, 'chaplin' ), $wp_query->found_posts );
		} else {
			$archive_subtitle = __( 'We could not find any results for your search. You can give it another try through the search form below.', 'chaplin' );
		}
	} else {
		$archive_title = get_the_archive_title();
		$archive_subtitle = get_the_archive_description( '<div>', '</div>' ); 
	}
	
	if ( $archive_title || $archive_subtitle ) : 
		?>
		
		<header class="archive-header section-inner">

			<div class="section-inner thin max-percentage no-margin">

				<?php if ( $archive_title ) : ?>
					<h1 class="archive-title"><?php echo $archive_title; ?></h1>
				<?php endif; ?>

				<?php if ( $archive_subtitle ) : ?>
					<div class="archive-subtitle intro-text"><?php echo wpautop( $archive_subtitle ); ?></div>
				<?php endif; ?>

			</div><!-- .section-inner -->
			
		</header><!-- .archive-header -->

	<?php endif; ?>

	<div class="posts section-inner">

		<?php if ( have_posts() ) : ?>

			<div class="posts-grid grid tcols-2 load-more-target">
			
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
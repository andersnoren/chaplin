<?php

/* 
Template Name: Only Content Template
Template Post Type: post, page
*/

get_header(); ?>

<main id="site-content">

	<?php

	if ( have_posts() ) :
		while ( have_posts() ) : 
		
			the_post(); 

			?>

			<article <?php post_class( 'section-inner' ); ?> id="post-<?php the_ID(); ?>">

				<?php do_action( 'chaplin_entry_article_start', $post->ID ); ?>

				<?php the_title( '<h1 class="screen-reader-text">', '</h1>' ); ?>

				<div class="post-inner" id="post-inner">

					<div class="entry-content">

						<?php 
						the_content();
						wp_link_pages();
						edit_post_link();
						?>

					</div><!-- .entry-content -->

				</div><!-- .post-inner -->

				<?php do_action( 'chaplin_entry_article_end', $post->ID ); ?>

			</article><!-- .post -->
			<?php

		endwhile;
	endif;

	?>

</main><!-- #site-content -->

<?php get_footer(); ?>

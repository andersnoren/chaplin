<?php get_header(); ?>

<main id="site-content" role="main">

	<div class="section-inner thin">

		<h1 class="entry-title"><?php _e( 'Page Not Found', 'chaplin' ); ?></h1>
			
		<p><?php _e( 'The page you were looking for could not be found. It might have been removed, renamed, or didn’t exist in the first place.', 'chaplin' ); ?></p>

		<?php get_search_form(); ?>

	</div><!-- .section-inner -->

</main><!-- #site-content -->

<?php get_footer(); ?>
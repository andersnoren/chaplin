<header class="entry-header">

	<div class="section-inner thin max-percentage no-margin">

		<?php 
		
		the_title( '<h1 class="entry-title">', '</h1>' );

		if ( has_excerpt() ) : ?>

			<div class="intro-text">
				<?php the_excerpt(); ?>
			</div>

			<?php 
		endif;

		// On pages with the cover template, display a "To the content" link
		if ( is_page() && is_page_template( 'template-cover.php' ) ) {
			?>

			<div class="to-the-content-wrapper">

				<a href="#post-inner" class="to-the-content">
					<div class="icon bg-body-background fill-children-primary"><?php chaplin_the_theme_svg( 'arrow-down' ); ?></div>
					<div class="text"><?php _e( 'Scroll Down', 'chaplin' ); ?></div>
				</a><!-- .to-the-content -->

			</div><!-- .to-the-content-wrapper -->

			<?php

		// Default to displaying the post meta
		} else {
			chaplin_the_post_meta( $post->ID, 'single-top' );
		}

		?>

	</div><!-- .section-inner -->

</header><!-- .entry-header -->
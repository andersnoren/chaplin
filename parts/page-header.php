<header class="entry-header">

	<?php 
	
	do_action( 'chaplin_entry_header_start', $post->ID );

	if ( is_front_page() ) {
		the_title( '<div class="entry-title faux-heading heading-size-1">', '</div>' );
	} else {
		the_title( '<h1 class="entry-title">', '</h1>' );
	}

	if ( has_excerpt() ) : ?>

		<div class="intro-text section-inner thin max-percentage">
			<?php the_excerpt(); ?>
		</div>

		<?php 
	endif;

	// On pages set to one of the cover templates, display a "To the content" link
	if ( is_page() && chaplin_is_cover_template() ) {
		?>

		<div class="to-the-content-wrapper">

			<a href="#post-inner" class="to-the-content">
				<div class="icon fill-children-current-color"><?php chaplin_the_theme_svg( 'arrow-down-circled' ); ?></div>
				<div class="text"><?php esc_html_e( 'Scroll Down', 'chaplin' ); ?></div>
			</a><!-- .to-the-content -->

		</div><!-- .to-the-content-wrapper -->

		<?php

	// Default to displaying the post meta
	} else {
		chaplin_the_post_meta( $post->ID, 'single-top' );
	}

	do_action( 'chaplin_entry_header_end', $post->ID );

	?>

</header><!-- .entry-header -->

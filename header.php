<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta http-equiv="content-type" content="<?php bloginfo( 'html_type' ); ?>" charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<?php 
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open(); 
		}

		// Add conditional sticky class to .header-inner
		$header_inner_classes = '';

		if ( get_theme_mod( 'chaplin_sticky_header' ) ) {
			$header_inner_classes .= ' stick-me bg-body-background';
		}
		?>

		<header id="site-header">

			<div class="header-inner<?php echo $header_inner_classes; ?>">

				<div class="section-inner">

					<div class="header-titles">

						<?php

						$logo = chaplin_get_custom_logo();
						$site_title = get_bloginfo( 'name' );
						$site_description = get_bloginfo( 'description' );

						if ( $logo ) {
							$home_link_contents = $logo . '<span class="screen-reader-text">' . $site_title . '</span>';
							$site_title_class = 'site-logo';
						} else {
							$site_title_class = 'site-title';
							$home_link_contents = '<a href="' . esc_url( home_url( '/' ) ) . '">' . $site_title . '</a>';
						}

						if ( is_front_page() ) : ?>
							<h1 class="<?php echo $site_title_class; ?>"><?php echo $home_link_contents; ?></h1>
						<?php else : ?>
							<div class="<?php echo $site_title_class; ?> faux-heading"><?php echo $home_link_contents; ?></div>
						<?php endif; 

						if ( $site_description ) : ?>

							<div class="site-description"><?php echo $site_description; ?></div><!-- .site-description -->

							<?php 
						endif; 
						?>

					</div><!-- .header-titles -->

					<div class="header-toggles">

						<?php 
						
						// Check whether the header search is deactivated in the customizer
						$disable_header_search = get_theme_mod( 'chaplin_header_search' ) ?: false; 
						
						if ( ! $disable_header_search ) : ?>
						
							<a href="#" class="toggle search-toggle hide-no-js" data-toggle-target=".search-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field">
								<div class="toggle-text">
									<?php _e( 'Search', 'chaplin' ); ?>
								</div>
								<?php chaplin_the_theme_svg( 'search' ); ?>
							</a><!-- .search-toggle -->

						<?php endif; ?>

						<a href="#" class="toggle nav-toggle" data-toggle-target=".menu-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-menu-modal">
							<div class="toggle-text">
								<span class="show"><?php _e( 'Menu', 'chaplin' ); ?></span>
								<span class="hide"><?php _e( 'Close', 'chaplin' ); ?></span>
							</div>
							<div class="bars">
								<div class="bar"></div>
								<div class="bar"></div>
								<div class="bar"></div>
							</div><!-- .bars -->
						</a><!-- .nav-toggle -->

					</div><!-- .header-toggles -->

				</div><!-- .section-inner -->

			</div><!-- .header-inner -->

			<?php 
			// Output the search modal (if it isn't deactivated in the customizer)
			if ( ! $disable_header_search ) {
				get_template_part( 'parts/modal-search' );
			}
			?>

		</header><!-- #site-header -->

		<?php 
		// Output the menu modal
		get_template_part( 'parts/modal-menu' ); 
		?>
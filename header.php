<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta http-equiv="content-type" content="<?php bloginfo( 'html_type' ); ?>" charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>

		<?php
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		}
		?>

		<a class="skip-link faux-button" href="#site-content"><?php esc_html_e( 'Skip to the content', 'chaplin' ); ?></a>

		<?php

		$only_content_templates = array( 'template-only-content.php', 'template-full-width-only-content.php' );
		$show_header = apply_filters( 'chaplin_show_header_footer_on_only_content_templates', false );

		// Don't output the markup of the header on the only content templates, unless filtered to do so
		if ( ! is_page_template( $only_content_templates ) || $show_header ) :

			// Add conditional sticky class to .header-inner
			$header_inner_classes = '';

			if ( get_theme_mod( 'chaplin_sticky_header', false ) ) {
				$header_inner_classes .= ' stick-me';
			}
			?>

			<header id="site-header">

				<?php do_action( 'chaplin_header_start' ); ?>

				<div class="header-inner<?php echo esc_attr( $header_inner_classes ); ?>">

					<div class="section-inner">

						<div class="header-titles">

							<?php

							$logo = chaplin_get_custom_logo();
							$site_title = get_bloginfo( 'name' );
							$site_description = get_bloginfo( 'description' );

							if ( $logo ) {

								// On cover templates, append the overlay logo if one is set
								if ( is_singular() && chaplin_is_cover_template() ) {
									$logo .= chaplin_get_custom_logo( 'chaplin_overlay_logo' );
								}

								$home_link_contents = $logo . '<span class="screen-reader-text">' . esc_html( $site_title ) . '</span>';
								$site_title_class = 'site-logo';
							} else {
								$site_title_class = 'site-title';
								$home_link_contents = '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . wp_kses_post( $site_title ) . '</a>';
							}

							if ( is_front_page() && is_home() ) : ?>
								<h1 class="<?php echo esc_attr( $site_title_class ); ?>"><?php echo $home_link_contents; ?></h1>
							<?php else : ?>
								<div class="<?php echo esc_attr( $site_title_class ); ?> faux-heading"><?php echo $home_link_contents; ?></div>
							<?php endif; ?>

							<?php if ( $site_description ) : ?>
								<div class="site-description"><?php echo wp_kses_post( $site_description ); ?></div>
							<?php endif; ?>

						</div><!-- .header-titles -->

						<div class="header-navigation-wrapper">

							<?php

							do_action( 'chaplin_header_navigation_start' );

							?>

							<div class="main-menu-alt-container hide-js">

								<ul class="main-menu-alt dropdown-menu reset-list-style">
									<?php
									if ( has_nav_menu( 'main-menu' ) ) {
										wp_nav_menu( array(
											'container' 		=> '',
											'items_wrap' 		=> '%3$s',
											'theme_location' 	=> 'main-menu',
										) );
									} else {
										wp_list_pages( array(
											'match_menu_classes' 	=> true,
											'title_li' 				=> false,
										) );
									}
									?>
								</ul><!-- .main-menu-alt -->

							</div><!-- .main-menu-alt-container -->

							<?php

							// Check whether to display the social menu in the header on desktop
							$show_desktop_header_social_menu = get_theme_mod( 'chaplin_show_desktop_header_social_menu', true );

							if ( $show_desktop_header_social_menu ) :
								?>
								<div class="main-menu-alt-social-container">
									<?php chaplin_the_social_menu(); ?>
								</div><!-- .main-menu-alt-social-container' -->
								<?php
							endif;
							?>

							<div class="header-toggles hide-no-js">

								<?php

								do_action( 'chaplin_header_toggles_start' );

								// Check whether the header search is deactivated in the customizer
								$disable_header_search = get_theme_mod( 'chaplin_disable_header_search', false );

								if ( ! $disable_header_search ) : 
									?>

									<a href="#" class="toggle search-toggle" data-toggle-target=".search-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-pressed="false" role="button">
										<div class="toggle-text">
											<?php esc_html_e( 'Search', 'chaplin' ); ?>
										</div>
										<?php chaplin_the_theme_svg( 'search' ); ?>
									</a><!-- .search-toggle -->

									<?php 
								endif;
								?>

								<a href="#" class="toggle nav-toggle" data-toggle-target=".menu-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-menu-modal" aria-pressed="false" data-set-focus=".menu-modal" role="button">
									<div class="toggle-text">
										<?php esc_html_e( 'Menu', 'chaplin' ); ?>
									</div>
									<div class="bars">
										<div class="bar"></div>
										<div class="bar"></div>
										<div class="bar"></div>
									</div><!-- .bars -->
								</a><!-- .nav-toggle -->

								<?php

								do_action( 'chaplin_header_toggles_end' );

								?>

							</div><!-- .header-toggles -->

							<?php

							do_action( 'chaplin_header_navigation_end' );

							?>

						</div><!-- .header-navigation-wrapper -->

					</div><!-- .section-inner -->

				</div><!-- .header-inner -->

				<?php
				// Output the search modal (if it isn't deactivated in the customizer)
				if ( ! $disable_header_search ) {
					get_template_part( 'parts/modal-search' );
				}
				?>

				<?php do_action( 'chaplin_header_end' ); ?>

			</header><!-- #site-header -->

			<?php
			// Output the menu modal
			get_template_part( 'parts/modal-menu' );

		endif; // is_page_template()
		?>
		
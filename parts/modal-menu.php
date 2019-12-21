<div class="menu-modal cover-modal" data-modal-target-string=".menu-modal" aria-expanded="false">

	<div class="menu-modal-inner modal-inner bg-body-background">

		<div class="menu-wrapper section-inner">

			<div class="menu-top">

				<div class="menu-modal-toggles header-toggles">

					<a href="#" class="toggle nav-toggle nav-untoggle" data-toggle-target=".menu-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-menu-modal" aria-pressed="false" data-set-focus="#site-header .nav-toggle">
						<div class="toggle-text">
							<?php esc_html_e( 'Close', 'chaplin' ); ?>
						</div>
						<div class="bars">
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
						</div><!-- .bars -->
					</a><!-- .nav-toggle -->

				</div><!-- .menu-modal-toggles -->

				<ul class="main-menu reset-list-style">
					<?php
					if ( has_nav_menu( 'main-menu' ) ) {
						wp_nav_menu( array(
							'container' 		=> '',
							'items_wrap' 		=> '%3$s',
							'show_toggles'		=> true,
							'theme_location' 	=> 'main-menu',
						) );
					} else {
						wp_list_pages( array( 
							'match_menu_classes' 	=> true,
							'title_li' 				=> false, 
						) );
					}
					?>
				</ul>

			</div><!-- .menu-top -->

			<div class="menu-bottom">

				<p class="menu-copyright">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <a href="<?php echo esc_url( home_url() ); ?>"><?php echo bloginfo( 'name' ); ?></a></p>

				<?php if ( has_nav_menu( 'social-menu' ) ) : ?>

					<ul class="social-menu reset-list-style social-icons s-icons">

						<?php
						wp_nav_menu( array(
							'theme_location'	=> 'social-menu',
							'container'			=> '',
							'container_class'	=> '',
							'items_wrap'		=> '%3$s',
							'menu_id'			=> '',
							'menu_class'		=> '',
							'depth'				=> 1,
							'link_before'		=> '<span class="screen-reader-text">',
							'link_after'		=> '</span>',
							'fallback_cb'		=> '',
						) );
						?>

					</ul><!-- .social-menu -->

				<?php endif; ?>

			</div><!-- .menu-bottom -->

		</div><!-- .menu-wrapper -->

	</div><!-- .menu-modal-inner -->

</div><!-- .menu-modal -->
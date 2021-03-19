<div class="menu-modal cover-modal" data-modal-target-string=".menu-modal" aria-expanded="false">

	<div class="menu-modal-inner modal-inner bg-body-background">

		<div class="menu-wrapper section-inner">

			<div class="menu-top">

				<div class="menu-modal-toggles header-toggles">

					<a href="#" class="toggle nav-toggle nav-untoggle" data-toggle-target=".menu-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-menu-modal" aria-pressed="false" data-set-focus="#site-header .nav-toggle" role="button"> 
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

				<?php 
				do_action( 'chaplin_menu_modal_before_menu' );
				?>

				<ul class="main-menu reset-list-style">
					<?php
					if ( has_nav_menu( 'main-menu' ) ) {
						wp_nav_menu(
							array(
								'container'      => '',
								'items_wrap'     => '%3$s',
								'show_toggles'   => true,
								'theme_location' => 'main-menu',
							)
						);
					} else {
						wp_list_pages( 
							array( 
								'match_menu_classes' => true,
								'title_li'           => false, 
							)
						);
					}
					?>
				</ul><!-- .main-menu -->

				<?php 
				do_action( 'chaplin_menu_modal_after_menu' );
				?>

			</div><!-- .menu-top -->

			<div class="menu-bottom">

				<?php
				do_action( 'chaplin_menu_modal_bottom_start' );
				?>

				<p class="menu-copyright">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <a href="<?php echo esc_url( home_url() ); ?>"><?php echo bloginfo( 'name' ); ?></a></p>

				<?php
				
				// Output the social menu, if set
				chaplin_the_social_menu();

				do_action( 'chaplin_menu_modal_bottom_end' );
				
				?>

			</div><!-- .menu-bottom -->

		</div><!-- .menu-wrapper -->

	</div><!-- .menu-modal-inner -->

</div><!-- .menu-modal -->

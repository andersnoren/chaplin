<div class="menu-modal cover-modal" data-modal-target-string=".menu-modal">

	<div class="menu-modal-inner modal-inner bg-body-background">

		<div class="menu-wrapper section-inner">

			<div class="menu-top">

				<ul class="main-menu reset-list-style">
					<?php
					if ( has_nav_menu( 'main-menu' ) ) {
						wp_nav_menu( array(
							'container' 		=> '',
							'items_wrap' 		=> '%3$s',
							'theme_location' 	=> 'main-menu',
						) );
					} else {
						wp_list_pages( array(
							'container' => '',
							'title_li' 	=> '',
						) );
					}
					?>
				</ul>

			</div><!-- .menu-top -->

			<div class="menu-bottom">

				<p class="menu-copyright">&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url() ); ?>"><?php echo bloginfo( 'name' ); ?></a></p>

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
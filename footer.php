        <footer class="" id="site-footer" role="contentinfo">

			<?php if ( is_active_sidebar( 'footer-one' ) || is_active_sidebar( 'footer-two' ) ) : ?>

				<div class="footer-widgets-outer-wrapper border-color-border section-inner">
				
					<div class="footer-widgets-wrapper grid tcols-2">

						<?php if ( is_active_sidebar( 'footer-one' ) ) : ?>
							<div class="footer-widgets column-one grid-item">
								<?php dynamic_sidebar( 'footer-one' ); ?>
							</div>
						<?php endif; ?>

						<?php if ( is_active_sidebar( 'footer-two' ) ) : ?>
							<div class="footer-widgets column-two grid-item">
								<?php dynamic_sidebar( 'footer-two' ); ?>
							</div>
						<?php endif; ?>

					</div><!-- .footer-widgets-wrapper -->
					
				</div><!-- .footer-widgets-outer-wrapper -->

			<?php endif; ?>

			<div class="footer-inner section-inner">

				<?php if ( has_nav_menu( 'footer-menu' ) ) : ?>

					<ul class="footer-menu reset-list-style">
						<?php
						wp_nav_menu( array(
							'container' 		=> '',
							'depth'				=> 1,
							'items_wrap' 		=> '%3$s',
							'theme_location' 	=> 'footer-menu',
						) );
						?>
					</ul><!-- .site-nav -->

				<?php endif; ?>

				<div class="footer-credits">

					<p class="footer-copyright">&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url() ); ?>"><?php echo bloginfo( 'name' ); ?></a></p>

					<p class="theme-credits color-secondary">
						<?php
						/* Translators: $s = name of the theme developer */
						printf( _x( 'Theme by %s', 'Translators: $s = name of the theme developer', 'chaplin' ), '<a href="https://www.andersnoren.se">' . __( 'Anders Norén', 'chaplin' ) . '</a>' ); ?>
					</p><!-- .theme-credits -->

				</div><!-- .footer-credits -->

			</div><!-- .footer-bottom -->

        </footer><!-- #site-footer -->

        <?php wp_footer(); ?>

    </body>
</html>
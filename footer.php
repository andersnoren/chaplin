        <footer class="mpad-v-20 tpad-v-60 dpad-v-80" id="site-footer" role="contentinfo">

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
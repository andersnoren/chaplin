        <?php

		$only_content_templates = array( 'template-only-content.php', 'template-full-width-only-content.php' );
		$show_footer = apply_filters( 'chaplin_show_header_footer_on_only_content_templates', false );

		// Don't output the markup of the footer on the only content templates, unless filtered to do so
		if ( ! is_page_template( $only_content_templates ) || $show_footer ) : ?>
		
			<footer id="site-footer" role="contentinfo">

				<?php do_action( 'chaplin_footer_start' ); ?>

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

				<?php endif; 

				$has_footer_menu = has_nav_menu( 'footer-menu' );

				$footer_inner_classes = '';

				if ( $has_footer_menu ) {
					$footer_inner_classes .= ' has-footer-menu';
				}
				
				?>

				<div class="footer-inner section-inner<?php echo esc_attr( $footer_inner_classes ); ?>">

					<?php if ( $has_footer_menu ) : ?>

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

						<p class="footer-copyright">&copy; <?php echo esc_html( date_i18n( __( 'Y', 'chaplin' ) ) ); ?> <a href="<?php echo esc_url( home_url() ); ?>" rel="home"><?php echo bloginfo( 'name' ); ?></a></p>

						<p class="theme-credits color-secondary">
							<?php
							/* Translators: $s = name of the theme developer */
							printf( esc_html_x( 'Theme by %s', 'Translators: $s = name of the theme developer', 'chaplin' ), '<a href="https://www.andersnoren.se">' . esc_html__( 'Anders Nor&eacute;n', 'chaplin' ) . '</a>' ); ?>
						</p><!-- .theme-credits -->

					</div><!-- .footer-credits -->

				</div><!-- .footer-bottom -->

				<?php do_action( 'chaplin_footer_end' ); ?>

			</footer><!-- #site-footer -->

			<?php 
		endif;
		
		wp_footer(); 
		
		?>

    </body>
</html>

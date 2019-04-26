<div class="search-modal cover-modal" data-modal-target-string=".search-modal">

	<div class="search-modal-inner modal-inner bg-body-background">

		<div class="section-inner">

			<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

			<form role="search" method="get" class="modal-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="<?php echo $unique_id; ?>">
					<?php echo _x( 'Search for:', 'Label', 'chaplin' ); ?>
				</label>
				<input type="search" id="<?php echo $unique_id; ?>" class="search-field" placeholder="<?php echo esc_attr_x( 'Search for&hellip;', 'Placeholder', 'chaplin' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
				<button type="submit" class="search-submit"><?php echo _x( 'Search', 'Submit button', 'chaplin' ); ?></button>
			</form><!-- .search-form -->

			<a href="#" class="toggle search-untoggle" data-toggle-target=".search-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field">
				<span class="screen-reader-text"><?php _e( 'Close search', 'chaplin' ); ?></span>
				<?php chaplin_the_theme_svg( 'cross' ); ?>
			</a><!-- .search-toggle -->

		</div><!-- .section-inner -->

	</div><!-- .search-modal-inner -->

</div><!-- .menu-modal -->
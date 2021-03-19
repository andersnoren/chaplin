<?php

class Chaplin_Recent_Comments extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname' 	=> 'widget_chaplin_recent_comments',
			'description' 	=> __( 'Displays recent comments with user avatars.', 'chaplin' ),
		);
		parent::__construct( 'widget_chaplin_recent_comments', __( 'Recent Comments', 'chaplin' ), $widget_ops );
	}

	function widget( $args, $instance ) {

		// Outputs the content of the widget
		extract( $args ); // Make before_widget, etc available.

		$widget_title = null;
		$number_of_comments = null;

		$widget_title = wp_kses_post( apply_filters( 'widget_title', $instance['widget_title'] ) );
		$number_of_comments = esc_attr( $instance['number_of_comments'] );

		echo $before_widget;

		if ( ! empty( $widget_title ) ) {

			echo $before_title . wp_kses_post( $widget_title ) . $after_title;

		} ?>

			<ul class="chaplin-widget-list">

				<?php

				if ( $number_of_comments == 0 ) {
					$number_of_comments = 3;
				}

				$args = array(
					'orderby'		=> 'date',
					'number'		=> $number_of_comments,
					'status'		=> 'approve',
				);

				global $comment;

				// The Query
				$comments_query = new WP_Comment_Query;
				$comments = $comments_query->query( $args );

				// Comment Loop
				if ( $comments ) {

					foreach ( $comments as $comment ) { ?>

						<li>

							<?php /* Translators: %1$s = post title, %2$s = post date */ ?>
							<a href="<?php echo esc_url( get_permalink( $comment->comment_post_ID ) ); ?>#comment-<?php echo esc_attr( $comment->comment_ID ); ?>">

								<div class="post-image" style="background-image: url( '<?php echo esc_url( get_avatar_url( get_comment_author_email( $comment->comment_ID ), array( 'size' => '160' ) ) ); ?>' );"></div>

								<div class="inner">

									<h3 class="title heading-size-5"><?php echo wp_kses_post( get_comment_author() ); ?></h5>
									<p class="meta color-secondary"><?php echo wp_kses_post( get_comment_date( get_option( 'date_format' ) ) ); ?></p>

								</div>

							</a>

						</li>

						<?php
					}
				}
				?>

			</ul>

		<?php echo $after_widget;
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );

		// Make sure we are getting a number
		$instance['number_of_comments'] = is_int( intval( $new_instance['number_of_comments'] ) ) ? intval( $new_instance['number_of_comments'] ) : 3;

		// Update and save the widget
		return $instance;

	}

	function form( $instance ) {

		// Set defaults
		if ( ! isset( $instance['widget_title'] ) ) {
			$instance['widget_title'] = '';
		}

		if ( ! isset( $instance['number_of_comments'] ) ) {
			$instance['number_of_comments'] = 3;
		}

		// Get the options into variables, escaping html characters on the way
		$widget_title = wp_kses_post( $instance['widget_title'] );
		$number_of_comments = esc_attr( $instance['number_of_comments'] );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php  esc_html_e( 'Title', 'chaplin' ); ?>:
			<input id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" class="widefat" value="<?php echo esc_attr( $widget_title ); ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number_of_comments' ); ?>"><?php esc_html_e( 'Number of comments to display', 'chaplin' ); ?>:
			<input id="<?php echo $this->get_field_id( 'number_of_comments' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_comments' ) ); ?>" type="number" class="widefat" value="<?php echo esc_attr( $number_of_comments ); ?>" /></label>
			<small>(<?php esc_html_e( 'Defaults to 3 if empty', 'chaplin' ); ?>)</small>
		</p>

		<?php
	}
}

?>

<?php if ( $comments ) : ?>

	<div class="comments section-inner thin max-percentage no-margin" id="comments">

		<?php

		$comments_number = absint( get_comments_number() );
		// Translators: %s = the number of comments
		$comments_title = sprintf( _nx( '%s Comment', '%s Comments', $comments_number, 'Translators: %s = the number of comments', 'chaplin' ), $comments_number ); ?>

		<div class="comments-header">

			<h3 class="comment-reply-title"><?php echo $comments_title; ?></h3>

		</div><!-- .comments-header -->

		<?php

		wp_list_comments( array(
			'walker'      	=> new Chaplin_Walker_Comment(),
			'avatar_size'	=> 120,
			'style' 		=> 'div',
		) );

		$comment_pagination = paginate_comments_links( array(
			'echo'		=> false,
			'prev_text' => '&larr; ' . __( 'Older Comments', 'chaplin' ),
			'next_text' => __( 'Newer Comments', 'chaplin' ) . ' &rarr;',
		) );

		if ( $comment_pagination ) :

			// If we're only showing the "Next" link, add a class indicating so
			if ( strpos( $comment_pagination, 'prev page-numbers' ) === false ) {
				$pagination_classes = ' only-next';
			} else {
				$pagination_classes = '';
			}
			?>

			<nav class="comments-pagination pagination<?php echo $pagination_classes; ?>">
				<?php echo $comment_pagination; ?>
			</nav>

		<?php endif; ?>

	</div><!-- comments -->

	<?php 
endif;

if ( comments_open() || pings_open() ) :

	comment_form( array(
		'class_form'			=> 'section-inner thin max-percentage no-margin',
		'comment_notes_before'	=> '',
		'comment_notes_after'	=> '',
	) );

elseif ( is_single() ) : ?>

	<div class="comment-respond" id="respond">

		<p class="comments-closed"><?php _e( 'Comments are closed.', 'chaplin' ); ?></p>

	</div><!-- #respond -->

<?php endif; ?>
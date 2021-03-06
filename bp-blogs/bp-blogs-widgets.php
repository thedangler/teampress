<?php

/***
 * The recent blogs widget is actually just the activity feed filtered on "new_blog_post".
 * Why not make some of your own widgets using a filtered activity stream?
 */

function bp_blogs_register_widgets() {
	global $current_blog, $bp;

	if ( bp_is_active( 'activity' ) && (int)$current_blog->blog_id == BP_ROOT_BLOG )
		add_action('widgets_init', create_function('', 'return register_widget("BP_Blogs_Recent_Posts_Widget");') );
}
add_action( 'bp_register_widgets', 'bp_blogs_register_widgets' );

class BP_Blogs_Recent_Posts_Widget extends WP_Widget {
	function bp_blogs_recent_posts_widget() {
		parent::WP_Widget( false, $name = __( 'Recent Site Wide Posts', 'buddypress' ) );
	}

	function widget($args, $instance) {
		global $bp;

		extract( $args );

		echo $before_widget;
		echo $before_title . $widget_name . $after_title;

		if ( empty( $instance['max_posts'] ) || !$instance['max_posts'] )
			$instance['max_posts'] = 10; ?>

		<?php if ( bp_has_activities( 'action=new_blog_post&max=' . $instance['max_posts'] . '&per_page=' . $instance['max_posts'] ) ) : ?>

			<ul id="blog-post-list" class="activity-list item-list">

				<?php while ( bp_activities() ) : bp_the_activity(); ?>

					<li>
						<div class="activity-content" style="margin: 0">

							<div class="activity-header">
								<?php bp_activity_action() ?>
							</div>

							<?php if ( bp_get_activity_content_body() ) : ?>
								<div class="activity-inner">
									<?php bp_activity_content_body() ?>
								</div>
							<?php endif; ?>

						</div>
					</li>

				<?php endwhile; ?>

			</ul>

		<?php else : ?>
			<div id="message" class="info">
				<p><?php _e( 'Sorry, there were no blog posts found. Why not write one?', 'buddypress' ) ?></p>
			</div>
		<?php endif; ?>

		<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['max_posts'] = strip_tags( $new_instance['max_posts'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'max_posts' => 10 ) );
		$max_posts = strip_tags( $instance['max_posts'] );
		?>

		<p><label for="bp-blogs-widget-posts-max"><?php _e('Max posts to show:', 'buddypress'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_posts' ); ?>" name="<?php echo $this->get_field_name( 'max_posts' ); ?>" type="text" value="<?php echo attribute_escape( $max_posts ); ?>" style="width: 30%" /></label></p>
	<?php
	}
}
?>
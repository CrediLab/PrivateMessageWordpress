<?php
// Add widget
add_action('widgets_init',
	create_function('', 'return register_widget("cl_pmw_widget");')
);

// Add widget to WP_Widget class
class cl_pmw_widget extends WP_Widget
{

	// Constructor
	public function __construct()
	{
		$widget_options = array( 'description' => __( 'Show notifications and new private messages on sidebar', 'cl_pmw' ) );
		$control_options = array();
		parent::__construct( 'cl_pmw_widget', __( 'Private Messages Widget', 'cl_pmw' ), $widget_options, $control_options );
	}

	// Display widget
	function widget( $args, $instance )
	{
		global $wpdb, $current_user;

		if ( !is_user_logged_in() )
		{
			return;
		}

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}

		// Get number of private messages
		$num_pm = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'pm WHERE `recipient` = "' . $current_user->ID . '" AND `deleted` != "2"' );
		$num_unread = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'pm WHERE `recipient` = "' . $current_user->ID . '" AND `read` = 0 AND `deleted` != "2"' );

		if ( empty( $num_pm ) )
		{
			$num_pm = 0;
		}
		if ( empty( $num_unread ) )
		{
			$num_unread = 0;
		}

		echo '<p><b>', sprintf( _n( 'You have %d private message (%d unread).', 'You have %d private messages (%d unread).', $num_pm, 'cl_pmw' ), $num_pm, $num_unread ), '</b></p>';

		if ( $instance['num_pm'] )
		{
			$msgs = $wpdb->get_results( 'SELECT `id`, `sender`, `subject`, `read`, `date` FROM ' . $wpdb->prefix . 'pm WHERE `recipient` = "' . $current_user->ID . '" AND `deleted` != "2" ORDER BY `date` DESC LIMIT ' . $instance['num_pm'] );
			if ( count( $msgs ) )
			{
				echo '<ol>';
				foreach ( $msgs as $msg )
				{
					$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->sender'" );
					echo '<li>';
					if ( !$msg->read )
					{
						echo '<b>';
					}
					echo $msg->subject;
					if ( !$msg->read )
					{
						echo '</b>';
					}
					printf( __( '<br />by <b>%s</b><br />at %s', 'cl_pmw' ), $msg->sender, $msg->date );
					echo '</li>';
				}
				echo '</ol>';
			}
		}

		echo '<p><a href="', get_bloginfo( 'wpurl' ), '/wp-admin/admin.php?page=inbox">', __( 'Click here to go to inbox', 'cl_pmw' ), ' &raquo;</a></p>';
	}

	// Update widget
	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		// Strip tags (if needed) and update the widget settings
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_pm'] = intval( $new_instance['num_pm'] );
		return $instance;
	}

	function form( $instance )
	{

		// Default widget settings
		$defaults = array( 'title' => __( 'Private Messages', 'cl_pmw' ), 'num_pm' => 5 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'num_pm' ); ?>"><?php _e( 'Number of messages:', 'cl_pmw' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'num_pm' ); ?>" name="<?php echo $this->get_field_name( 'num_pm' ); ?>" value="<?php echo $instance['num_pm']; ?>" style="width:100%;" />
	</p>
	<?php

	}
}
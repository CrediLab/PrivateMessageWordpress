<div class="wrap">
	<h2><?php _e( 'Inbox', 'pmw' ); ?></h2>
	<?php
	if ( !empty( $status ) )
	{
		echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
	}
	if ( empty( $msgs ) )
	{
		echo '<p>', __( 'You have no items in inbox.', 'pmw' ), '</p>';
	}
	else
	{
		$n = count( $msgs );
		$num_unread = 0;
		foreach ( $msgs as $msg )
		{
			if ( !( $msg->read ) )
			{
				$num_unread++;
			}
		}
		echo '<p>', sprintf( _n( 'You have %d private message (%d unread).', 'You have %d private messages (%d unread).', $n, 'pmw' ), $n, $num_unread ), '</p>';
		?>
		<form action="" method="get">
			<?php wp_nonce_field( 'pmw-bulk-action_inbox' ); ?>
			<input type="hidden" name="page" value="inbox" />

			<div class="tablenav">
				<select name="action">
					<option value="-1" selected="selected"><?php _e( 'Bulk Action', 'pmw' ); ?></option>
					<option value="delete"><?php _e( 'Delete', 'pmw' ); ?></option>
					<option value="mar"><?php _e( 'Mark As Read', 'pmw' ); ?></option>
				</select> <input type="submit" class="button-secondary" value="<?php _e( 'Apply', 'pmw' ); ?>" />
			</div>

			<table class="widefat fixed" cellspacing="0">
				<thead>
				<tr>
					<th class="manage-column check-column"><input type="checkbox" /></th>
					<th class="manage-column" width="10%"><?php _e( 'Sender', 'pmw' ); ?></th>
					<th class="manage-column"><?php _e( 'Subject', 'pmw' ); ?></th>
					<th class="manage-column" width="20%"><?php _e( 'Date', 'pmw' ); ?></th>
				</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $msgs as $msg )
					{
						$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
						?>
					<tr>
						<th class="check-column"><input type="checkbox" name="id[]" value="<?php echo $msg->id; ?>" />
						</th>
						<td><?php echo $msg->sender; ?></td>
						<td>
							<?php
							if ( $msg->read )
							{
								echo '<a href="', wp_nonce_url( "?page=inbox&action=view&id=$msg->id", 'pmw-view_inbox_msg_' . $msg->id ), '">', stripcslashes( $msg->subject ), '</a>';
							}
							else
							{
								echo '<a href="', wp_nonce_url( "?page=inbox&action=view&id=$msg->id", 'pmw-view_inbox_msg_' . $msg->id ), '"><b>', stripcslashes( $msg->subject ), '</b></a>';
							}
							?>
							<div class="row-actions">
							<span>
								<a href="<?php echo wp_nonce_url( "?page=inbox&action=view&id=$msg->id", 'pmw-view_inbox_msg_' . $msg->id ); ?>"><?php _e( 'View', 'pmw' ); ?></a>
							</span>
								<?php
								if ( !( $msg->read ) )
								{
									?>
									<span>
								| <a href="<?php echo wp_nonce_url( "?page=inbox&action=mar&id=$msg->id", 'pmw-mar_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Mark As Read', 'pmw' ); ?></a>
							</span>
									<?php

								}
								?>
								<span class="delete">
								| <a class="delete"
									href="<?php echo wp_nonce_url( "?page=inbox&action=delete&id=$msg->id", 'pmw-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Delete', 'pmw' ); ?></a>
							</span>
							<span class="reply">
								| <a class="reply"
								href="<?php echo wp_nonce_url( "?page=send&recipient=$msg->sender&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'pmw-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Reply', 'pmw' ); ?></a>
							</span>
							</div>
						</td>
						<td><?php echo $msg->date; ?></td>
					</tr>
						<?php

					}
					?>
				</tbody>
				<tfoot>
				<tr>
					<th class="manage-column check-column"><input type="checkbox" /></th>
					<th class="manage-column"><?php _e( 'Sender', 'pmw' ); ?></th>
					<th class="manage-column"><?php _e( 'Subject', 'pmw' ); ?></th>
					<th class="manage-column"><?php _e( 'Date', 'pmw' ); ?></th>
				</tr>
				</tfoot>
			</table>
		</form>
		<?php

	}
	?>
</div>
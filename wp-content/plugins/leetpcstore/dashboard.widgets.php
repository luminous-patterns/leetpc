<?php

add_action( 'wp_dashboard_setup', 'lpc_add_dashboard_widgets' );

function lpc_add_dashboard_widgets() {

	wp_add_dashboard_widget(
		'payment_history_dashboard_widget',
		'Payment History',
		'lpc_payment_history_dashboard'
	);

}

function lpc_payment_history_dashboard() {

	$logs = get_logs( array(
		'posts_per_page'   => 10,
		'log_entry_type'   => 'payment-received'
	) );

	?>
	<table class="form-table lpc-order-history">

		<thead>

			<tr valign="top">
				<th class="lpc-col-superslim" scope="column">REF#</th>
				<th class="lpc-col-slim" scope="column">Date/Time</th>
				<th class="" scope="column">Order</th>
				<th class="lpc-col-wide" scope="column">Method</th>
				<th class="lpc-col-slim" scope="column" style="text-align: right;">Amount</th>
			</tr>

		</thead>

		<tbody>

		<?php

		foreach ( $logs as $l ) {

			if ( !get_post( $l->extra['order_id'] ) ) continue;

			$extra = $l->getExtra();
			$order = get_order( $extra['order_id'] );

			?>
			<tr valign="top">
				<td><a href="<?php echo get_edit_post_link( $l->ID ); ?>" target="_blank"><?php echo $l->ID; ?></a></td>
				<td><?php echo $l->getDate( 'Y-m-d \@ H:i:s' ); ?></td>
				<td style="text-transform: capitalize;">
					<?php echo preg_replace( '/[-_]/', ' ', $order->status ); ?>
					<br /><a href="<?php echo get_edit_post_link( $order->ID ); ?>">Manage</a>
				</td>
				<td><?php echo $order->getPaymentMethod( true ); ?></td>
				<td style="text-align: right;">&dollar;<?php echo number_format( $order->payment['amount'], 2 ); ?></td>
			</tr>
			<?php

		}

		?>

		</tbody>

	</table>
	<?php

}
<?php

add_action( 'add_meta_boxes',      'order_add_meta' );
add_action( 'save_post',           'order_save_meta' );

function order_add_meta() {

	remove_meta_box( 'submitdiv', 'lpc_order', 'side' );
	
	add_meta_box(
		'order_history_metabox',
		'Event Log',
		'order_history_metabox',
		'lpc_order',
		'normal',
		'low'
	);
	
	add_meta_box(
		'order_actions_metabox',
		'Order Actions',
		'order_actions_metabox',
		'lpc_order',
		'normal',
		'high'
	);
	
	add_meta_box(
		'order_details_metabox',
		'Order Details',
		'order_details_metabox',
		'lpc_order',
		'normal',
		'high'
	);
	
	add_meta_box(
		'order_status_metabox',
		'Order Status',
		'order_status_metabox',
		'lpc_order',
		'side',
		'high'
	);
	
	add_meta_box(
		'order_summary_metabox',
		'Order Summary',
		'order_summary_metabox',
		'lpc_order',
		'side',
		'high'
	);
	
	add_meta_box(
		'order_account_details_metabox',
		'Customer Details',
		'order_account_details_metabox',
		'lpc_order',
		'side'
	);
	
}

function order_save_meta( $post_id ) {
	
	if ( !key_exists( 'order_meta', $_POST ) 
		|| ( !wp_verify_nonce( $_POST['order_meta'], 'order_meta_nonce' ) ) 
		|| ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		|| ( key_exists( 'post_type', $_POST ) && 'order' != $_POST['post_type'] ) 
		|| ( key_exists( 'post_type', $_POST ) && 'order' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) ) )
		return $post_id;

	return $post_id;

}

function order_details_metabox() {
	
	$order = get_order( get_the_ID() );

	?>

	<div class="order-details-tabs">
		<ul>
			<li class="selected" data-tab-name="details">Order Details</li>
			<li data-tab-name="components">Components</li>
			<li data-tab-name="payment">Payment</li>
		</ul>
	</div>

	<div class="tabs">

		<div class="tab-content details">

			<div class="form-table-container">

				<h4>Overview</h4>

				<table class="form-table">

					<tr valign="top">
						<th scope="row"><label>Order #</label></th>
						<td><?php echo $order->ID; ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Order Status</label></th>
						<td><?php echo $order->status; ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Order Age</label></th>
						<td><?php echo $order->getAge(); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Customer</label></th>
						<td><a href="#"><?php echo $order->account['firstname'] . ' ' . $order->account['lastname']; ?></a></td>
					</tr>

				</table>

			</div>

			<div class="form-table-container">

				<h4>Timeline</h4>

				<table class="form-table">

					<?php

					$dates = array(
						'created'             => 'Created',
						'payment_received'    => 'Payment Received',
						'stock_picked'        => 'Stock Picked',
						'ready'               => 'Assembled / Ready',
						'dispatched'          => 'Dispatched',
						'delivered'           => 'Delivered'
					);

					foreach ( $dates as $t => $label ) :

						$date = $order->getDate( $t, LPC_PRETTY_DATETIMES );

						if ( $date && $date != '-' ) : ?>

					<tr valign="top">
						<th scope="row"><label><?php echo $label; ?></label></th>
						<td><?php echo $date; ?></td>
					</tr>

						<?php else: ?>

					<tr valign="top">
						<th scope="row"><label><?php echo $label; ?></label></th>
						<td><button class="button set-date-<?php echo $date; ?>">Set <?php echo $label; ?></button></td>
					</tr>

						<?php break; endif; ?>

					<?php endforeach; ?>

				</table>

			</div>

		</div>

		<div class="tab-content components hidden">

		<?php foreach ( $order->items as $l ) : ?>

			<h4><?php echo $l['product_title']; ?></h4>

			<table class="line-items">

				<thead>

					<tr>
						<th class="qty-col">Qty</th>
						<th class="description-col">Description</th>
						<th class="price-col">Price</th>
					</tr>

				</thead>

				<tbody>

					<tr class="line-item">
						<td class="qty-col"><?php echo $l['qty']; ?>x</td>
						<td class="description-col" colspan="2">
							<h5><?php echo $l['product_title']; ?></h5>
							<div class="price">&dollar;<?php echo number_format( $l['total_price'], 2 ); ?></div>
						</td>
					</tr>

				<?php foreach ( $l['components'] as $c ) : ?>

					<tr class="line-item sub-item">
						<td class="qty-col">&nbsp;</td>
						<td class="description-col" colspan="3">
							<h5><strong><?php echo strtoupper( $c['type'] ); ?></strong> <?php echo $c['title']; ?></h5>
							<div class="model"><?php echo $c['model']; ?></div>
							<div class="price">&dollar;<?php echo number_format( $c['price'], 2 ); ?></div>
						</td>
					</tr>

				<?php endforeach; ?>
					
				</tbody>

			</table>

		<?php endforeach; ?>

		</div>

		<div class="tab-content payment hidden">

			<div class="form-table-container">

				<h4>General</h4>

				<table class="form-table">

					<tr valign="top">
						<th scope="row"><label>Amount Due</label></th>
						<td>&dollar;<?php echo number_format( $order->getTotal(), 2 ); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Method</label></th>
						<td><?php echo $order->getPaymentMethod( true ); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Status</label></th>
						<td><?php echo $order->getPaymentStatus( true ); ?></td>
					</tr>

				</table>

			</div>

	<?php if ( $order->payment['complete'] ) : ?>

			<div class="form-table-container">

				<h4>Payment Details</h4>

				<table class="form-table">

			<?php switch ( $order->getPaymentMethod() ) { 

				case 'cc': 
				?>

					<tr valign="top">
						<th scope="row"><label>Amount</label></th>
						<td>&dollar;<?php echo number_format( $order->payment['amount'], 2 ); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Date</label></th>
						<td><?php echo $order->getDate( 'payment_received', 'D jS \o\f F Y \@ H:i:s' ); ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Token</label></th>
						<td><?php echo $order->payment['token']; ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Message</label></th>
						<td><?php echo $order->payment['message']; ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>IP Address</label></th>
						<td><?php echo $order->payment['ipaddress']; ?></td>
					</tr>

				<?php break;

			} ?>

				</table>

			</div>

	<?php else: ?>

			<div class="form-table-container">

				<h4>Enter Manual Payment</h4>

				<table class="form-table">

					<tr valign="top">
						<th scope="row"><label>Date Received</label></th>
						<td><input type="text" class="widefat" /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Payment Amount</label></th>
						<td><input type="text" class="widefat" value="<?php echo $order->getTotal(); ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Reference #</label></th>
						<td><input type="text" class="widefat" /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Payment Type</label></th>
						<td><select class="widefat"><option>Bank Transfer</option></select></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label>Notes</label></th>
						<td><textarea class="widefat"></textarea></td>
					</tr>

				</table>

				<button class="button button-primary button-large">Process</button>

			</div>

	<?php endif; ?>

		</div>

	</div>

	<?php

}

function order_status_metabox() {
	
	$order = get_order( get_the_ID() );

	echo '<div class="indicator ' . $order->status . '">' . preg_replace( '/[-_]/', ' ', $order->status ) . '</div>';

}

function order_account_details_metabox() {
	
	$order = get_order( get_the_ID() );

	$phone = $order->account['phone'];
	$formatted_phone = substr( $phone, 0, 2 ) == '04' ? 
		preg_replace( '/^([0-9]{4})([0-9]{3})([0-9]{3})$/', '$1 $2 $3', $phone ) :
		preg_replace( '/^([0-9]{2})([0-9]{4})([0-9]{4})$/', '($1) $2 $3', $phone );

	$email = $order->user['email'];

	echo '<strong>' . $order->account['firstname'] . ' ' . $order->account['lastname'] . '</strong> <span class="small">(<a href="mailto:' . $email . '">' . $email . '</a>)</span>'
		. '<br />Ph ' . $formatted_phone
		. '<br />'
		. ( $order->account['company'] ? '<br />' . $order->account['company'] : '' )
		. '<br />' . $order->account['street']
		. '<br />' . $order->account['suburb'] . ' ' . $order->account['state'] . ' ' . $order->account['postcode']
		. '<br />AUSTRALIA'
		. '<button type="button" class="button">Manage</button>';

}

function lpc_get_mono_text( $amount, $pad = 10, $prepend = '' ) {
	return $prepend . preg_replace( '/ /', '&nbsp;', sprintf( "%' " . $pad . "s\n", number_format( $amount, 2 ) ) );
}

function order_summary_metabox() {
	
	$order = get_order( get_the_ID() );

	?>

	<table class="form-table">

		<tr valign="top">
			<th scope="row"><label>Items</label></th>
			<td><span>&nbsp;<?php echo lpc_get_mono_text( $order->items_count, 11 ); ?></span></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label>Items Total</label></th>
			<td><span>&nbsp;<?php echo lpc_get_mono_text( $order->items_total, 10, '&dollar;' ); ?></span></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label>Discount</label></th>
			<td><span>-<?php echo lpc_get_mono_text( $order->discount_total, 10, '&dollar;' ); ?></span></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label>Total</label></th>
			<td><span>&nbsp;<?php echo lpc_get_mono_text( $order->total, 10, '&dollar;' ); ?></span></td>
		</tr>

	</table>

	<?php

}

function order_actions_metabox() {
	
	$order = get_order( get_the_ID() );

	?>
	<ul>
		<li><a href="<?php echo $order->getLink( 'invoice' ); ?>"><span>View Invoice</span></a></li>
		<li><a href="<?php echo $order->getLink( 'service_order' ); ?>"><span>View Service Order</span></a></li>
		<li><a href=""><span>Add Log Entry</span></a></li>
		<li><a href=""><span>Set Status</span></a></li>
	</ul>
	<?php

}

function order_history_metabox() {
	
	$order = get_order( get_the_ID() );
	$logEntries = $order->getLog();

	?>

	<table class="form-table lpc-order-history">

		<thead>

			<tr valign="top">
				<th class="lpc-col-superslim" scope="column">#</th>
				<th class="lpc-col-slim" scope="column">Date/Time</th>
				<th class="lpc-col-slim" scope="column">Entry Type</th>
				<th class="lpc-col-wide" scope="column">Variables</th>
			</tr>

		</thead>

		<tbody>

	<?php foreach ( $logEntries as $entry ) : ?>

		<tr valign="top">
			<th class="lpc-col-superslim" scope="row"><a href="<?php echo get_edit_post_link( $entry[0] ); ?>" target="_blank"><?php echo $entry[0]; ?></a></th>
			<td class="lpc-col-slim"><?php echo $entry[1][0]->format( 'j-M-Y' ); ?> <strong>@<?php echo $entry[1][0]->format( 'h:i:s' ); ?></strong></td>
			<td class="lpc-col-slim"><?php echo $entry[3]; ?></td>
			<td class="lpc-col-wide"><ul><?php foreach ( $entry[count($entry)-1] as $k => $v ) : ?>
				<?php if ( in_array( $k, array( 'order_id', 'error', 'data', 'body' ) ) || substr( $k, 0, 1 ) == '_' ) continue; ?>
				<li><strong style="text-transform: capitalize;"><?php echo preg_replace( '/\_/', ' ', $k ); ?>:</strong> <?php echo $v; ?></li>
			<?php endforeach; ?></ul></td>
		</tr>

	<?php endforeach; ?>

		</tbody>

	</table>

	<?php

}
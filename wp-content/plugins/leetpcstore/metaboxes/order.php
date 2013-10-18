<?php

add_action( 'add_meta_boxes',      'order_add_meta' );
add_action( 'save_post',           'order_save_meta' );

function order_add_meta() {

	remove_meta_box( 'submitdiv', 'lpc_order', 'side' );
	
	add_meta_box(
		'order_history_metabox',
		'Order History',
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

function lpc_get_mono_price( $amount, $pad = 10 ) {
	return '&dollar;' . preg_replace( '/ /', '&nbsp;', sprintf( "%' " . $pad . "s\n", number_format( $amount, 2 ) ) );
}

function order_summary_metabox() {
	
	$order = get_order( get_the_ID() );

	?>

	<table class="form-table">

		<tr valign="top">
			<th scope="row"><label>Items</label></th>
			<td><?php echo $order->items_count; ?></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label>Items Total</label></th>
			<td><?php echo lpc_get_mono_price( $order->items_total ); ?></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label>Discount</label></th>
			<td>-<?php echo lpc_get_mono_price( $order->discount_total ); ?></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label>Total</label></th>
			<td><?php echo lpc_get_mono_price( $order->total ); ?></td>
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
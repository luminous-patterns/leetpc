<?php

	$cart = get_cart();

?>
<div class="modal-wrapper">

	<div class="modal checkout-modal">

		<div class="modal-header">
			<h3>Checkout</h3>
		</div>

		<div class="modal-body">
			<p>
				<strong>Sub-total</strong>
				<br /><?php echo $cart['sub_total']; ?>
			</p>
			<p>
				<strong>Items</strong>
				<br /><?php foreach( $cart['items'] as $k => $item ) { echo $item['qty'] . 'x Product ID: ' . $item['product_id'] . ' // Component IDs: ' . implode( ', ', $item['component_ids'] ); }; ?>
			</p>
		</div>

		<div class="modal-footer">
			<button class="secondary">Cancel</button>
			<button class="next-step">Continue</button>
		</div>

	</div>

</div>
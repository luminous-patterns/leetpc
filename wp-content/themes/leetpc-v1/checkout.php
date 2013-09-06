<?php

	$cart = get_cart();

?>
<div class="modal-wrapper">

	<div class="modal checkout-modal">

		<div class="modal-header">
			<h3>Checkout</h3>
		</div>

<!-- 		<div class="modal-body">
			<p>
				<strong>Sub-total</strong>
				<br /><?php echo $cart['sub_total']; ?>
			</p>
			<p>
				<strong>Items</strong>
				<br /><?php foreach( $cart['items'] as $k => $item ) { echo $item['qty'] . 'x Product ID: ' . $item['product_id'] . ' // Component IDs: ' . implode( ', ', $item['component_ids'] ); }; ?>
			</p>
		</div> -->

<!-- 		<div class="modal-body">

			<div>
				<label>First name</label>
				<input type="text" name="" />
			</div>

			<div>
				<label>Last name</label>
				<input type="text" name="" />
			</div>

			<div>
				<label>Email address</label>
				<input type="text" name="" />
			</div>

			<div>
				<label>Email address</label>
				<input type="text" name="" />
			</div>

			<div>
				<label>Email address</label>
				<input type="text" name="" />
			</div>

			<div>
				<label>Email address</label>
				<input type="text" name="" />
			</div>

		</div> -->

		<?php if ( $step == '1' ) : ?>

		<div class="modal-body">

			<div class="row checkout-field acct-firstname">
				<label>First name</label>
				<input type="text" name="acct-firstname" />
			</div>

			<div class="row checkout-field acct-lastname">
				<label>Last name</label>
				<input type="text" name="acct-lastname" />
			</div>

			<div class="row checkout-field acct-email">
				<label>Email</label>
				<input type="text" name="acct-email" />
			</div>

			<div class="row checkout-field acct-phone">
				<label>Phone</label>
				<input type="text" name="acct-phone" />
			</div>

			<div class="row checkout-field acct-street">
				<label>Street</label>
				<input type="text" name="acct-street" />
			</div>

			<div class="row checkout-field acct-postcode">
				<label>Postcode</label>
				<input type="text" name="acct-postcode" />
			</div>

			<div class="row checkout-field acct-suburb">
				<label>Suburb</label>
				<input type="text" name="acct-suburb" />
			</div>

			<div class="row checkout-field acct-state">
				<label>State</label>
				<select name="acct-state">
					<option>ACT</option>
					<option>NSW</option>
					<option>NT</option>
					<option>QLD</option>
					<option>TAS</option>
					<option>VIC</option>
					<option>WA</option>
				</select>
			</div>

		</div>

		<div class="modal-footer">
			<input type="hidden" name="current_step" value="1" />
			<button class="secondary close-modal">Cancel</button>
			<button class="next-step">Next</button>
		</div>

		<?php elseif ( $step == '2' ) : ?>

		<div class="modal-body">

			<?php echo var_dump( $_REQUEST ); ?>

			<div class="row checkout-field cc-name">
				<label>FULL name on card</label>
				<input type="text" name="" />
			</div>

			<div class="row checkout-field cc-number">
				<label>Card number</label>
				<input type="text" name="" />
			</div>

			<div class="row checkout-field cc-expiry">
				<label>Expiry</label>
				<select><option>9 - September</option></select>
				<select><option>2013</option></select>
			</div>

			<div class="row checkout-field cc-csc">
				<label>CSC/CVV</label>
				<input type="text" name="" />
			</div>

		</div>

		<div class="modal-footer">
			<input type="hidden" name="current_step" value="2" />
			<button class="secondary close-modal">Cancel</button>
			<button class="next-step">Place order</button>
		</div>

		<?php endif; ?>

	</div>

</div>
<?php

	$cart = get_cart();

	$next_step_btn_text = 'Continue';

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

		<div class="modal-body">

		<?php if ( count( $data['error'] ) > 0 ) : ?>

			<div class="error-details">
				<?php echo $data['error']['message']; ?>
				<input class="fields" type="hidden" value="<?php echo $data['error']['fields']; ?>" />
			</div>

		<?php endif; ?>

		<?php if ( $step == '1' ) : ?>

			<h4>Account details</h4>

			<div class="row checkout-field acct-email">
				<label>Email address</label>
				<input type="text" name="acct-email" value="<?php echo $_SESSION['checkout_data']['acct']['email']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-registered">
				<label>I am a ...</label>
				<div class="options">
					<label><input type="radio" name="acct-registered" value="0" <?php if ( !$_SESSION['checkout_data']['acct']['registered'] ) : ?>checked="checked"<?php endif; ?> /> New customer</label>
					<label><input type="radio" name="acct-registered" value="1" <?php if ( $_SESSION['checkout_data']['acct']['registered'] ) : ?>checked="checked"<?php endif; ?> /> Returning customer</label>
				</div>
			</div>

			<div class="row checkout-field acct-password <?php if ( !$_SESSION['checkout_data']['acct']['registered'] ) : ?>hidden<?php endif; ?>">
				<label>Password</label>
				<input type="password" name="acct-password" class="wide" />
			</div>

		<?php elseif ( $step == '2' ) : ?>

			<h4>Billing details</h4>

			<div class="row checkout-field acct-firstname">
				<label>First name</label>
				<input type="text" name="acct-firstname" value="<?php echo $_SESSION['checkout_data']['acct']['firstname']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-lastname">
				<label>Last name</label>
				<input type="text" name="acct-lastname" value="<?php echo $_SESSION['checkout_data']['acct']['lastname']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-company">
				<label>Company</label>
				<input type="text" name="acct-company" value="<?php echo $_SESSION['checkout_data']['acct']['company']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-phone">
				<label>Phone</label>
				<input type="text" name="acct-phone" value="<?php echo $_SESSION['checkout_data']['acct']['phone']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-street">
				<label>Street Address</label>
				<input type="text" name="acct-street" value="<?php echo $_SESSION['checkout_data']['acct']['street']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-suburb">
				<label>Suburb</label>
				<input type="text" name="acct-suburb" value="<?php echo $_SESSION['checkout_data']['acct']['suburb']; ?>" class="wide" />
			</div>

			<div class="row checkout-field acct-postcode">
				<label>Postcode</label>
				<input type="text" name="acct-postcode" value="<?php echo $_SESSION['checkout_data']['acct']['postcode']; ?>" />
			</div>

			<div class="row checkout-field acct-state">
				<label>State</label>
				<select name="acct-state" class="wide">
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'ACT' ) : ?>selected="selected"<?php endif; ?>>ACT</option>
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'NSW' ) : ?>selected="selected"<?php endif; ?>>NSW</option>
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'NT' ) : ?>selected="selected"<?php endif; ?>>NT</option>
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'QLD' ) : ?>selected="selected"<?php endif; ?>>QLD</option>
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'TAS' ) : ?>selected="selected"<?php endif; ?>>TAS</option>
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'VIC' ) : ?>selected="selected"<?php endif; ?>>VIC</option>
					<option <?php if ( $_SESSION['checkout_data']['acct']['state'] == 'WA' ) : ?>selected="selected"<?php endif; ?>>WA</option>
				</select>
			</div>

			<h4>Delivery details</h4>

			<div class="row checkout-field acct-registered">
				<label>Deliver to</label>
				<div class="options">
					<label><input type="radio" name="delivery-use_billing" value="0" <?php if ( !$_SESSION['checkout_data']['delivery']['use_billing'] ) : ?>checked="checked"<?php endif; ?> /> Use same as billing / account</label>
					<label><input type="radio" name="delivery-use_billing" value="1" <?php if ( $_SESSION['checkout_data']['delivery']['use_billing'] ) : ?>checked="checked"<?php endif; ?> /> Different address...</label>
				</div>
			</div>

		<?php elseif ( $step == '3' ) : ?>

			<!-- <h4>Order summary</h4>

			<div class="row checkout-field acct-address">
				<label>Deliver To</label>
				<div class="options">
					<?php echo $_SESSION['checkout_data']['acct']['firstname']; ?> 
					<?php echo $_SESSION['checkout_data']['acct']['lastname']; ?>
					<?php if ( $_SESSION['checkout_data']['acct']['company'] ) : ?>
						<br />
						<?php echo $_SESSION['checkout_data']['acct']['company']; ?>
					<?php endif; ?>
					<br /><br />
					<?php echo $_SESSION['checkout_data']['acct']['street']; ?>
					<br />
					<?php echo $_SESSION['checkout_data']['acct']['suburb']; ?>,
					<?php echo $_SESSION['checkout_data']['acct']['state']; ?>
					<?php echo $_SESSION['checkout_data']['acct']['postcode']; ?>
				</div>
			</div> -->

			<h4>Payment details</h4>

			<div class="row checkout-field total-amount">
				<label>Payment amount</label>
				<div class="options">
					&dollar; <?php echo number_format( get_cart_total(), 2 ); ?>
				</div>
			</div>

			<div class="row checkout-field cc-name">
				<label>FULL name on card</label>
				<input type="text" name="cc-name" class="wide" />
			</div>

			<div class="row checkout-field cc-number">
				<label>Card number</label>
				<input type="text" name="cc-number" class="wide" />
			</div>

			<div class="row checkout-field cc-expiry">
				<label>Expiry</label>
				<select name="cc-exp-month">
					<option value="01">1 - January</option>
					<option value="02">2 - February</option>
					<option value="03">3 - March</option>
					<option value="04">4 - April</option>
					<option value="05">5 - May</option>
					<option value="06">6 - June</option>
					<option value="07">7 - July</option>
					<option value="08">8 - August</option>
					<option value="09">9 - September</option>
					<option value="10">10 - October</option>
					<option value="11">11 - November</option>
					<option value="12">12 - December</option>
				</select>
				<select name="cc-exp-year"><?php for ( $i = 0; $i < 11; $i++ ) : ?>
					<option><?php echo date( "Y" ) + $i; ?></option>
				<?php endfor; ?></select>
			</div>

			<div class="row checkout-field cc-csc">
				<label>CSC/CVV</label>
				<input type="text" name="cc-csc" />
			</div>

			<?php $next_step_btn_text = 'Pay &amp; Finalise'; ?>

		<?php endif; ?>

		</div>

		<div class="modal-footer">
			<input type="hidden" name="current_step" value="<?php echo intval( $step ); ?>" />
			<a href="#" class="close-modal">Cancel</a>
			<?php if ( $step != '1' ) : ?><button class="secondary previous-step">&#9664;</button><?php endif; ?>
			<button class="next-step"><?php echo $next_step_btn_text; ?></button>
		</div>

	</div>

</div>
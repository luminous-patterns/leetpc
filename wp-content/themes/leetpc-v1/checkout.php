<?php

	$cart = get_cart();

	$next_step_btn_text = 'Continue';

?>
<div class="modal-wrapper">

	<div class="modal checkout-modal">

		<div class="modal-header">
			<h3>Checkout</h3>
		</div>

		<div class="modal-body">

		<?php if ( count( $data['error'] ) > 0 ) : ?>

			<div class="error-details">
				<?php echo $data['error']['message']; ?>
				<input class="fields" type="hidden" value="<?php echo $data['error']['fields']; ?>" />
			</div>

		<?php endif; ?>

		<?php if ( $step == '1' ) : ?>

			<h4>Account details</h4>

			<div class="row checkout-field user-email">
				<label>Email address</label>
				<input type="text" name="user-email" value="<?php echo $_SESSION['checkout_data']['user']['email']; ?>" class="wide" />
			</div>

			<div class="row checkout-field user-registered">
				<label>I am a ...</label>
				<div class="options">
					<label><input type="radio" name="user-registered" value="0" <?php if ( !$_SESSION['checkout_data']['user']['registered'] ) : ?>checked="checked"<?php endif; ?> /> New customer</label>
					<label><input type="radio" name="user-registered" value="1" <?php if ( $_SESSION['checkout_data']['user']['registered'] ) : ?>checked="checked"<?php endif; ?> /> Returning customer</label>
				</div>
			</div>

			<div class="row checkout-field user-password <?php if ( !$_SESSION['checkout_data']['user']['registered'] ) : ?>hidden<?php endif; ?>">
				<label>Password</label>
				<input type="password" name="user-password" class="wide" />
			</div>

		<?php elseif ( $step == '2' ) : ?>

			<h4>Billing details</h4>

			<?php 

			$fields = array(
				'acct-firstname' => array( 
					'type'      => 'input',
					'label'     => 'First Name',
					'value'     => $_SESSION['checkout_data']['acct']['firstname']
				),
				'acct-lastname' => array( 
					'type'      => 'input',
					'label'     => 'Last Name',
					'value'     => $_SESSION['checkout_data']['acct']['lastname']
				),
				'acct-company' => array( 
					'type'      => 'input',
					'label'     => 'Company',
					'value'     => $_SESSION['checkout_data']['acct']['company']
				),
				'acct-phone' => array( 
					'type'      => 'input',
					'label'     => 'Phone',
					'value'     => $_SESSION['checkout_data']['acct']['phone']
				),
				'acct-street' => array( 
					'type'      => 'input',
					'label'     => 'Street Address',
					'value'     => $_SESSION['checkout_data']['acct']['street']
				),
				'acct-suburb' => array( 
					'type'      => 'input',
					'label'     => 'Suburb',
					'value'     => $_SESSION['checkout_data']['acct']['suburb']
				),
				'acct-postcode' => array( 
					'type'      => 'input',
					'label'     => 'Postcode',
					'value'     => $_SESSION['checkout_data']['acct']['postcode']
				),
				'acct-state' => array( 
					'type'      => 'select',
					'label'     => 'State',
					'value'     => $_SESSION['checkout_data']['acct']['state'],
					'options'   => array( 'ACT','NSW','NT','QLD','TAS','VIC','WA' )
				)
			);

			?>

			<?php foreach ( $fields as $k => $f ) : ?>

			<div class="row checkout-field <?php echo $k; ?>">
				<label><?php echo $f['label']; ?></label>
				<?php switch ( $f['type'] ) { 

				case 'input' : ?>
				<input type="text" name="<?php echo $k; ?>" value="<?php echo $f['value']; ?>" class="wide" />
					<?php break; ?>

				<?php case 'select' : ?>
				<select name="<?php echo $k; ?>" class="wide">
					<?php foreach ( $f['options'] as $opt ) : ?>
					<option <?php if ( $f['value'] == $opt ) : ?>selected="selected"<?php endif; ?>><?php echo $opt; ?></option>
					<?php endforeach; ?>
				</select>
					<?php break; ?>

				<?php }; ?>
			</div>

			<?php endforeach; ?>

		<?php elseif ( $step == '3' ) : ?>

			<h4>Delivery details</h4>

			<div class="row checkout-field delivery-method">
				<label>Delivery method</label>
				<div class="options">
					FREE courier delivery to anywhere in Australia (order now for delivery before )
				</div>
			</div>

			<div class="row checkout-field delivery-use_different_addr">
				<label>Deliver to</label>
				<div class="options">
					<label><input type="radio" name="delivery-use_different_addr" value="0" <?php if ( !$_SESSION['checkout_data']['delivery']['use_different_addr'] ) : ?>checked="checked"<?php endif; ?> /> Same as billing address</label>
					<label><input type="radio" name="delivery-use_different_addr" value="1" <?php if ( $_SESSION['checkout_data']['delivery']['use_different_addr'] ) : ?>checked="checked"<?php endif; ?> /> Different address...</label>
				</div>
			</div>

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

		<?php elseif ( $step == '4' ) : ?>

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

		<?php elseif ( $step == '5' ) : ?>

			<pre style="font-size: 0.8em;"><?php var_export( $_SESSION['checkout_data'] ); ?></pre>

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
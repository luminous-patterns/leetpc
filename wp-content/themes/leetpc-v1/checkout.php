<?php

	if ( $step > 1 ) {
		$order = get_order( $_SESSION['order_id'] );
	}

	$next_step_btn_text = 'Continue';

	$can_go_back = true;
	$last_step = false;

?>
<div class="modal-wrapper">

	<div class="modal checkout-modal">

		<div class="modal-header">
			<h3>Checkout</h3>
		</div>

		<div class="modal-body">

		<?php if ( $step == '1' ) : ?>

			<h4>Account details</h4>

			<div class="row checkout-field user-registered">
				<label>I am a ...</label>
				<div class="options">
					<label><input type="radio" name="user-registered" value="0" checked="checked" /> New customer</label>
					<label><input type="radio" name="user-registered" value="1" /> Returning customer</label>
				</div>
			</div>

			<div class="row checkout-field user-email">
				<label>Email address</label>
				<input type="text" name="user-email" value="" class="wide" />
			</div>

			<div class="row checkout-field user-conf_email">
				<label>Confirm email address</label>
				<input type="text" name="user-conf_email" value="" class="wide" />
			</div>

			<div class="row checkout-field user-password hidden">
				<label>Password</label>
				<input type="password" name="user-password" class="wide" />
			</div>

		<?php elseif ( $step == '2' ) : ?>

			<?php $can_go_back = false; ?>

			<h4>Billing details</h4>

			<?php 

			$fields = array(
				'acct-firstname' => array( 
					'type'      => 'input',
					'label'     => 'First Name',
					'value'     => $order->account['firstname']
				),
				'acct-lastname' => array( 
					'type'      => 'input',
					'label'     => 'Last Name',
					'value'     => $order->account['lastname']
				),
				'acct-company' => array( 
					'type'      => 'input',
					'label'     => 'Company',
					'value'     => $order->account['company']
				),
				'acct-phone' => array( 
					'type'      => 'input',
					'label'     => 'Phone',
					'value'     => $order->account['phone']
				),
				'acct-street' => array( 
					'type'      => 'input',
					'label'     => 'Street Address',
					'value'     => $order->account['street']
				),
				'acct-suburb' => array( 
					'type'      => 'input',
					'label'     => 'Suburb',
					'value'     => $order->account['suburb']
				),
				'acct-postcode' => array( 
					'type'      => 'input',
					'label'     => 'Postcode',
					'value'     => $order->account['postcode']
				),
				'acct-state' => array( 
					'type'      => 'select',
					'label'     => 'State',
					'value'     => $order->account['state'],
					'options'   => array( 'Victoria','ACT','New South Wales','Northern Territory','Queensland','Tasmania','Western Australia' ),
					'disabled'  => array( 'ACT','New South Wales','Northern Territory','Queensland','Tasmania','Western Australia' )
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
					<option <?php if ( $f['value'] == $opt ) : ?>selected="selected"<?php endif; ?> <?php if ( in_array( $opt, $f['disabled'] ) ) : ?>disabled="disabled"<?php endif; ?>><?php echo $opt; ?></option>
					<?php endforeach; ?>
				</select>
					<?php break; ?>

				<?php }; ?>
			</div>

			<?php endforeach; ?>

			<div class="row checkout-field acct-country">
				<label>Country</label>
				<div class="options">
					Australia
				</div>
			</div>

		<?php elseif ( $step == '3' ) : ?>

			<h4>Delivery details</h4>

			<div class="row checkout-field delivery-method">
				<label>Delivery method</label>
				<div class="options">
					<label><input type="radio" name="delivery-method" value="free" checked="checked" /> FREE delivery to anywhere in Victoria</label>
				</div>
			</div>

			<div class="row checkout-field delivery-use_different_address">
				<label>Deliver to</label>
				<div class="options">
					<label><input type="radio" name="delivery-use_different_address" value="0" <?php if ( !$order->delivery['use_different_address'] ) : ?>checked="checked"<?php endif; ?> /> Same as billing address</label>
					<label><input type="radio" name="delivery-use_different_address" value="1" <?php if ( $order->delivery['use_different_address'] ) : ?>checked="checked"<?php endif; ?> /> Different address...</label>
				</div>
			</div>

			<div class="delivery-address <?php if ( !$order->delivery['use_different_address'] ) echo 'hidden'; ?>">

				<h4>Delivery address</h4>

				<?php 

				$fields = array(
					'delivery-firstname' => array( 
						'type'      => 'input',
						'label'     => 'First Name',
						'value'     => $order->delivery['firstname']
					),
					'delivery-lastname' => array( 
						'type'      => 'input',
						'label'     => 'Last Name',
						'value'     => $order->delivery['lastname']
					),
					'delivery-company' => array( 
						'type'      => 'input',
						'label'     => 'Company',
						'value'     => $order->delivery['company']
					),
					'delivery-street' => array( 
						'type'      => 'input',
						'label'     => 'Street Address',
						'value'     => $order->delivery['street']
					),
					'delivery-suburb' => array( 
						'type'      => 'input',
						'label'     => 'Suburb',
						'value'     => $order->delivery['suburb']
					),
					'delivery-postcode' => array( 
						'type'      => 'input',
						'label'     => 'Postcode',
						'value'     => $order->delivery['postcode']
					),
					'delivery-state' => array( 
						'type'      => 'select',
						'label'     => 'State',
						'value'     => $order->delivery['state'],
						'options'   => array( 'Victoria','ACT','New South Wales','Northern Territory','Queensland','Tasmania','Western Australia' ),
						'disabled'  => array( 'ACT','New South Wales','Northern Territory','Queensland','Tasmania','Western Australia' )
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
						<option <?php if ( $f['value'] == $opt ) : ?>selected="selected"<?php endif; ?> <?php if ( in_array( $opt, $f['disabled'] ) ) : ?>disabled="disabled"<?php endif; ?>><?php echo $opt; ?></option>
						<?php endforeach; ?>
					</select>
						<?php break; ?>

					<?php }; ?>
				</div>

				<?php endforeach; ?>

				<div class="row checkout-field delivery-country">
					<label>Country</label>
					<div class="options">
						Australia
					</div>
				</div>

			</div>

		<?php elseif ( $step == '4' ) : ?>

			<h4>Payment details</h4>

			<div class="row checkout-field payment-method">
				<label>Payment method</label>
				<div class="options">
					<label><input type="radio" name="payment-method" value="cc" checked="checked" /> VISA / MasterCard</label>
					<label><input type="radio" name="payment-method" value="bank" /> Bank deposit</label>
				</div>
			</div>

			<div class="row checkout-field total-amount">
				<label>Payment amount</label>
				<div class="options">
					&dollar;<?php echo number_format( $order->total, 2 ); ?>
				</div>
			</div>

			<div class="payment-section payment-method-cc">

				<h4>Credit card details</h4>

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

			</div>

			<div class="payment-section payment-method-bank hidden">

				<h4>Bank deposit instructions</h4>

				<div class="row checkout-field">
					<label>Bank name</label>
					<div class="options">
						Westpac
					</div>
				</div>

				<div class="row checkout-field">
					<label>Account name</label>
					<div class="options">
						LEETPC
					</div>
				</div>

				<div class="row checkout-field">
					<label>BSB</label>
					<div class="options">
						033-349
					</div>
				</div>

				<div class="row checkout-field">
					<label>Account #</label>
					<div class="options">
						383009
					</div>
				</div>

				<div class="row checkout-field">
					<label>Order #</label>
					<div class="options">
						<?php echo $order->ID; ?>
					</div>
				</div>

				<div class="row checkout-field">
					<div class="options">
						<span class="important"><strong>IMPORTANT!</strong> Remember to use your order number as the description for the payment.  Otherwise there may be delays in matching your order to your payment.</span>
						<br /><br />A copy of these deposit details, including your invoice/order number, will be sent to your email address (<?php echo $order->user['email']; ?>).
						<br /><br />Please click 'Pay &amp; Finalise' to place your order.
					</div>
				</div>

			</div>

			<?php $next_step_btn_text = 'Pay &amp; Finalise'; ?>

		<?php elseif ( $step == '5' ) : ?>

			<?php $can_go_back = false; ?>

			<div class="html-content">
				<h3><img src="<?php echo get_template_directory_uri(); ?>/img/icons/green-tick.png" width="24"> Checkout complete</h3>
				<p>Thanks <?php echo $order->account['firstname']; ?>,</p>
			<?php if ( $order->payment['method'] == 'cc' ) : ?>
				<p>Credit card payment of <strong>&dollar;<?php echo number_format( $order->payment['amount'], 2 ); ?></strong> for your order (#<?php echo $order->ID; ?>) was approved.</p>
				<p>A copy of your invoice will be sent to your email address (<?php echo $order->user['email']; ?>).</p>
			<?php elseif ( $order->payment['method'] == 'bank' ) : ?>
				<p>Please remember to send payment of <strong>&dollar;<?php echo number_format( $order->total, 2 ); ?></strong> for your order (#<?php echo $order->ID; ?>) as soon as possible.</p>
				<p>A copy of your order number, invoice, and our bank deposit details will be sent to your email address (<?php echo $order->user['email']; ?>).</p>
			<?php endif;?>
				<p>The expected delivery date for your order is <strong><?php echo $order->getDate( 'deliver_on', 'D jS \o\f M' ); ?></strong>.</p>
				<p>If you have any questions about your order please contact care@leetpc.com.au.</p>
			</div>

			<?php $next_step_btn_text = 'Complete'; ?>
			<?php $last_step = true; ?>

		<?php endif; ?>

		</div>

		<div class="modal-footer">
			<input type="hidden" name="current_step" value="<?php echo intval( $step ); ?>" />
			<?php if ( !$last_step ) : ?><a href="#" class="close-modal">Cancel</a><?php endif; ?>
			<?php if ( $step != '1' && $can_go_back ) : ?><button class="secondary previous-step">&#9664;</button><?php endif; ?>
			<button class="<?php echo $last_step ? 'close-modal' : 'next-step'; ?>"><?php echo $next_step_btn_text; ?></button>
		</div>

	</div>

</div>
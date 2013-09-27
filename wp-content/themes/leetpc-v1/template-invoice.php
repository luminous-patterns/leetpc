<?php /* Template Name: Invoice */

	if ( !$i = get_invoice( $_GET['invoice_id'] ) ) {

		die("no invoice");

	}

	get_header( 'invoice' );

	$acct = $i->getAccountDetails();

?>
	
	<!-- section -->
	<section role="main">
	
		<h1>TAX INVOICE</h1>

		<div class="section-group secondary">

			<div class="section total">
				<h2>Total</h2>
				<div class="amount">
					&dollar;<?php echo number_format( $i->getTotal(), 2 ); ?>
				</div>
			</div>

			<div class="section date">
				<h2>Date Invoiced</h2>
				<div class="invoiced">
					<?php echo $i->getDate(); ?>
				</div>
			</div>

		</div>

		<div class="section business-info">
			LEETPC PTY LTD
			<br />ABN 62 842 988 455
			<br />4 Holyrood Drive
			<br />Vermont VIC 3133
		</div>

		<div class="section bill-to">
			<h2>Bill To</h2>
			<div class="address">
				<?php echo $acct['firstname'] . ' ' . $acct['lastname']; ?>
				<br /><?php echo $acct['street']; ?>
				<br /><?php echo $acct['suburb']; ?> <?php echo $acct['state']; ?> <?php echo $acct['postcode']; ?>
			</div>
		</div>

		<div class="section line-items">

		<?php foreach ( $i->getLineItems() as $lid => $l ) : ?>

			<div class="line-item">
				<div class="quantity"><?php echo $l['qty']; ?>x</div>
				<div class="description"><?php echo $l['product_id']; ?></div>
				<div class="sub-total">&dollar;<?php echo number_format( $l['price'] * $l['qty'], 2 ); ?></div>
			</div>

		<?php endforeach; ?>

		</div>
	
	</section>
	<!-- /section -->

<?php get_footer( 'invoice' ); ?>
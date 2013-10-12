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

			<div class="section date">
				<h2>Date</h2>
				<div class="invoiced">
					<?php echo $i->getDate(); ?>
				</div>
			</div>

			<div class="section total">
				<h2>Total</h2>
				<div class="amount">
					&dollar;<?php echo number_format( $i->getTotal(), 2 ); ?>
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
			<h2>Customer</h2>
			<div class="address">
				<?php echo $acct['firstname'] . ' ' . $acct['lastname']; ?>
				<br /><?php echo $acct['street']; ?>
				<br /><?php echo $acct['suburb']; ?> <?php echo $acct['state']; ?> <?php echo $acct['postcode']; ?>
			</div>
		</div>

		<div class="section line-items">

			<table class="line-items">

				<thead>

					<tr>
						<th class="qty-col">Qty</th>
						<th class="description-col">Description</th>
						<th class="price-col">Price</th>
					</tr>

				</thead>

				<tbody>

				<?php foreach ( $i->getLineItems() as $l ) : ?>

					<tr class="line-item">
						<td class="qty-col"><?php echo $l['qty']; ?>x</td>
						<td class="description-col" colspan="2">
							<h3><?php echo $l['product_title']; ?></h3>
							<div class="price">&dollar;<?php echo number_format( $l['total_price'], 2 ); ?></div>
						</td>
					</tr>

				<?php endforeach; ?>

				<?php if ( $i->hasPromo() ) : ?>

					<?php
						$promo = $i->getPromo();
						$discount = $promo['type'] == '%' ? number_format( $promo['amount'] ) . '&#37;' : '&dollar;' . number_format( $promo['amount'] );
					?>

					<tr class="line-item discount-item">
						<td class="description-col" colspan="3">
							<div class="discount"><?php echo $discount; ?> Discount</div>
							<div class="details">code: <?php echo $promo['code']; ?></div>
							<div class="price">-&dollar;<?php echo number_format( $i->get( 'discount_total' ), 2 ); ?></div>
						</td>
					</tr>

				<?php endif; ?>
					
				</tbody>

			</table>

		</div>
	
	</section>
	<!-- /section -->

<?php get_footer( 'invoice' ); ?>
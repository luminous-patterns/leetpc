<?php /* Template Name: Invoice */

	if ( !$_GET['invoice_id'] || get_post_type( $_GET['invoice_id'] ) != 'invoice' ) {
		header( 'location: https://www.leetpc.com.au' );
		exit;
	}

	$i = get_invoice( $_GET['invoice_id'] );

	get_header( 'invoice' );

	$acct = $i->getAccountDetails();

?>
	
	<!-- section -->
	<section role="main">

		<div class="invoice-top">

			<img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="LEETPC" class="logo">
		
			<h1>TAX INVOICE</h1>

			<div class="section-group secondary">

				<div class="section date">
					<h2>Invoice Date</h2>
					<div class="invoiced">
						<?php echo $i->getDate(); ?>
					</div>
				</div>

				<div class="section total">
					<h2>Total Amount</h2>
					<div class="amount">
						&dollar;<?php echo number_format( $i->getTotal(), 2 ); ?>
					</div>
				</div>

			</div>

			<div class="section business-info">
				CALLAN MILNE
				<br />ABN 62 842 988 455
				<br />4 Holyrood Drive
				<br />Vermont VIC 3133
			</div>

			<div class="section bill-to">
				<h2>Invoice To</h2>
				<div class="address">
					<?php echo $acct['firstname'] . ' ' . $acct['lastname']; ?>
					<br /><?php echo $acct['street']; ?>
					<br /><?php echo $acct['suburb']; ?> <?php echo $acct['state']; ?> <?php echo $acct['postcode']; ?>
				</div>
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
							<div class="price">-&dollar;<?php echo number_format( $i->cart['discount_total'], 2 ); ?></div>
						</td>
					</tr>

				<?php endif; ?>
					
				</tbody>

			</table>

		</div>

		<div class="section deliver-to">
			<h2>Deliver To</h2>
			<div class="address">
				<?php $delivery = $i->getDeliveryAddress(); ?>
				<?php echo $delivery['firstname'] . ' ' . $delivery['lastname']; ?>
				<br /><?php echo $delivery['street']; ?>
				<br /><?php echo $delivery['suburb']; ?> <?php echo $delivery['state']; ?> <?php echo $delivery['postcode']; ?>
			</div>
		</div>
	
	</section>
	<!-- /section -->

<?php get_footer( 'invoice' ); ?>
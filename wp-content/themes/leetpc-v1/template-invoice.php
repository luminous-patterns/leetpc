<?php /* Template Name: Invoice */

	if ( !$_GET['order_id'] || get_post_type( $_GET['order_id'] ) != 'lpc_order' ) {
		header( 'location: https://www.leetpc.com.au' );
		exit;
	}

	$o = get_order( $_GET['order_id'] );

	get_header( 'invoice' );

	$acct = $o->account;

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
						<?php echo $o->getDate( 'created', 'jS \o\f F Y' ); ?>
					</div>
				</div>

				<div class="section total">
					<h2>Total Amount</h2>
					<div class="amount">
						&dollar;<?php echo number_format( $o->getTotal(), 2 ); ?>
					</div>
				</div>

			</div>

			<div class="section business-info">
				LEETPC
				<br />ABN 62 842 988 455
				<br />4 Holyrood Drive
				<br />Vermont VIC 3133
			</div>

			<div class="section bill-to">
				<h2>Invoice To</h2>
				<div class="address">
					<?php if ( $acct['company'] ) echo $acct['company'] . '<br />'; ?>
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

				<?php foreach ( $o->items as $l ) : ?>

					<tr class="line-item">
						<td class="qty-col"><?php echo $l['qty']; ?>x</td>
						<td class="description-col" colspan="2">
							<h3><?php echo $l['product_title']; ?></h3>
							<div class="price">&dollar;<?php echo number_format( $l['total_price'], 2 ); ?></div>
						</td>
					</tr>

				<?php endforeach; ?>

				<?php if ( $o->hasPromo() ) : ?>

					<?php
						$promo = $promo;
						$discount = $o->promo['type'] == '%' ? number_format( $o->promo['amount'] ) . '&#37;' : '&dollar;' . number_format( $o->promo['amount'] );
					?>

					<tr class="line-item discount-item">
						<td class="description-col" colspan="3">
							<div class="discount"><?php echo $discount; ?> Discount</div>
							<div class="details">code: <?php echo $o->promo['code']; ?></div>
							<div class="price">-&dollar;<?php echo number_format( $o->discount_total, 2 ); ?></div>
						</td>
					</tr>

				<?php endif; ?>
					
				</tbody>

			</table>

		</div>

		<div class="section deliver-to">
			<h2>Deliver To</h2>
			<div class="address">
				<?php $delivery = $o->getDeliveryAddress(); ?>
				<?php if ( $delivery['company'] ) echo $delivery['company'] . '<br />'; ?>
				<?php echo $delivery['firstname'] . ' ' . $delivery['lastname']; ?>
				<br /><?php echo $delivery['street']; ?>
				<br /><?php echo $delivery['suburb']; ?> <?php echo $delivery['state']; ?> <?php echo $delivery['postcode']; ?>
			</div>
		</div>
	
	</section>
	<!-- /section -->

<?php get_footer( 'invoice' ); ?>
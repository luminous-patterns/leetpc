<?php /* Template Name: Service Order */

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
	
		<h1>SERVICE ORDER</h1>

		<div class="section-group secondary">

			<div class="section date">
				<h2>Date</h2>
				<div class="invoiced">
					<?php echo $o->getDate( 'created', 'jS \o\f F Y' ); ?>
				</div>
			</div>

			<div class="section total">
				<h2>Total</h2>
				<div class="amount">
					&dollar;<?php echo number_format( $o->getTotal(), 2 ); ?>
				</div>
			</div>

		</div>

		<div class="section bill-to">
			<h2>Customer</h2>
			<div class="address">
				<?php if ( $acct['company'] ) echo $acct['company'] . '<br />'; ?>
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

				<?php foreach ( $o->items as $l ) : ?>

					<tr class="line-item">
						<td class="qty-col"><?php echo $l['qty']; ?>x</td>
						<td class="description-col" colspan="2">
							<h3><?php echo $l['product_title']; ?></h3>
							<div class="price">&dollar;<?php echo number_format( $l['total_price'], 2 ); ?></div>
						</td>
					</tr>

				<?php foreach ( $l['components'] as $c ) : ?>

					<tr class="line-item sub-item">
						<td class="qty-col">&nbsp;</td>
						<td class="description-col" colspan="3">
							<h3><strong><?php echo strtoupper( $c['type'] ); ?></strong> <?php echo $c['title']; ?></h3>
							<div class="model"><?php echo $c['model']; ?></div>
							<div class="price">&dollar;<?php echo number_format( $c['price'], 2 ); ?></div>
						</td>
					</tr>

				<?php endforeach; ?>

				<?php endforeach; ?>
					
				</tbody>

			</table>

		</div>
	
	</section>
	<!-- /section -->

<?php get_footer( 'invoice' ); ?>
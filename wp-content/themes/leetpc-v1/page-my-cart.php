<?php 

	get_header(); 

	$cart = get_cart();

?>
	
	<!-- section -->
	<section role="main">
	
	<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
		<!-- article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
			<table class="line-items">

				<thead>

					<tr>
						<th class="qty-col">Qty</th>
						<th class="description-col">Description</th>
						<th class="price-col">Price</th>
					</tr>

				</thead>

				<tbody>

				<?php if ( count( array_keys( $cart['items'] ) ) > 0 ) : ?>

					<?php foreach( $cart['items'] as $k => $item ) : 

						$product = get_product( $item['product_id'] );

						?>

						<tr class="line-item line-item-<?php echo $k; ?>" data-line-item-id="<?php echo $k; ?>">
							<td class="qty-col">
								<select class="item-qty"><?php

									for ( $i = 1; $i < 6; $i++ ) {

										$s = $item['qty'] == $i ? 'selected="selected"' : '';

										?><option <?php echo $s; ?>><?php echo $i; ?></option><?php

									}

								?></select>
							</td>
							<td class="description-col" colspan="2">
								<?php echo get_the_post_thumbnail( $product->post->ID, 'thumbnail', array( 'class' => 'product-thumbnail' ) ); ?>
								<h3><a href="<?php echo get_permalink( $product->post->ID ); ?>"><?php echo $product->post->post_title; ?></a></h3>
								<div class="actions">
									<a href="#" class="toggle-details">View details</a> |
									<a href="#" class="remove-line-item">Remove</a>
								</div>
								<div class="details hidden">
									<ul><?php foreach( $item['component_ids'] as $cid ) :
										$c = get_post( str_replace( 'component-', '', $cid ) );
										?>
										<li>
											<?php echo $c->post_title; ?>
											<?php if ( $product->comPriceDiffs[$c->ID] != '0' ) : ?><span class="price-diff">
												<?php echo $product->comPriceDiffs[$c->ID] > 0 ? '+' : '-'; ?>
												&dollar;<?php echo abs( $product->comPriceDiffs[$c->ID] ); ?>
											</span><?php endif; ?>
										</li>
									<?php endforeach; ?></ul>
								</div>
								<div class="price">&dollar;<?php echo $product->calcPrice( $item['component_ids'] ); ?></div>
							</td>
						</tr>

					<?php endforeach; ?>

					<?php if ( $cart['promo']['code'] ) : ?>

						<?php
							$discount = $cart['promo']['type'] == '%' ? number_format( $cart['promo']['amount'] ) . '&#37;' : '&dollar;' . number_format( $cart['promo']['amount'] );
							$discount_amount = $cart['promo']['type'] == '%' ? number_format( $cart['sub_total'] * ( $cart['promo']['amount'] / 100 ), 2 ) : number_format( $cart['promo']['amount'] );
						?>

						<tr class="line-item discount-item">
							<td class="description-col" colspan="3">
								<div class="discount"><?php echo $discount; ?> Discount</div>
								<div class="details">code: <?php echo $cart['promo']['code']; ?></div>
								<div class="price">-&dollar;<?php echo number_format( $cart['discount_total'], 2 ); ?></div>
							</td>
						</tr>

					<?php endif; ?>

				<?php else : ?>

						<tr class="line-item no-line-items">
							<td class="description-col" colspan="3">
								<strong>Your shopping cart is empty</strong>
								Find things to fill it with on the <a href="/products/">products page</a>.
							</td>
						</tr>

				<?php endif; ?>
					
				</tbody>

			</table>

		<?php if ( count( array_keys( $cart['items'] ) ) > 0 ) : ?>

			<div class="promo-code">
				<a href="#" class="promo-entry-toggle">Add discount or promo code</a>
				<div class="promo-entry hidden">
					<h4>Enter code</h4>
					<input type="text" name="promo-code" value="" />
					<button class="small apply-promo-code">Apply</button>
				</div>
			</div>

			<div class="sub-total">
				<h3>Sub-total</h3>
				<div class="amount">
					&dollar;<?php echo number_format( $cart['sub_total'], 2 ); ?>
				</div>
			</div>

			<div class="checkout-btn-container">
				<button class="checkout">Checkout &amp; Complete Order</button>
				<div class="icons">
					<img src="<?php echo get_template_directory_uri(); ?>/img/accepted-cards.png" />
				</div>
			</div>

		<?php endif; ?>

			<h2>Delivery information</h2>

			<p>We are currently only accepting customers from <strong>Victoria, Australia.</strong>  Delivery to any valid business or residential address in the state of Victoria is free.</p>

			<h3>Expected delivery times</h3>

			<ul>
				<li>&lt; 40km from Melbourne - 1 to 4 working days</li>
				<li>40km+ from Melbourne - 3 to 7 working days</li>
			</ul>
			
		</article>
		<!-- /article -->
		
	<?php endwhile; ?>
	
	<?php endif; ?>
	
	</section>
	<!-- /section -->

<?php get_footer(); ?>
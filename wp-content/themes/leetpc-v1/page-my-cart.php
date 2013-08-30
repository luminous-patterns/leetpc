<?php 

	get_header(); 

	$cart = get_cart();

?>
	
	<!-- section -->
	<section role="main">
	
		<h1><?php the_title(); ?></h1>
	
	<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
		<!-- article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
			<table>

				<thead>

					<tr>
						<th class="qty-col">Qty</th>
						<th class="description-col">Description</th>
						<th class="price-col">Price</th>
					</tr>

				</thead>

				<tbody>

					<?php foreach( $cart['items'] as $k => $item ) : 

						$product = get_post( $item['product_id'] );
						$meta = get_post_custom( $item['product_id'] );
						// echo $item['qty'] . 'x Product ID: ' . $item['product_id'] . ' // Component IDs: ' . implode( ', ', $item['component_ids'] ); }; 

						// $components = array();

						// foreach ( $item['component_ids'] as $cid ) {
						// 	$c = get_post( str_replace( 'component-', '', $cid ) );
						// 	$c = get_post( $cid );
						// 	$components[] = $c->post_title;
						// }

						?>

						<tr class="line-item line-item-<?php echo $k; ?>">
							<td class="qty-col"><input type="text" size="2" value="1" /></td>
							<td class="description-col">
								<h3><a href="<?php echo get_permalink( $product->ID ); ?>"><?php echo $product->post_title; ?></a></h3>
								<ul><?php foreach( $item['component_ids'] as $cid ) :
									$c = get_post( str_replace( 'component-', '', $cid ) );
									?>
									<li><?php echo $c->post_title; ?></li>
								<?php endforeach; ?></ul>
							</td>
							<td class="price-col">&dollar;<?php echo number_format( $meta['price'][0], 2 ); ?></td>
						</tr>

					<?php endforeach; ?>
					
				</tbody>

			</table>

			<div class="promo-code">
				<a href="#">Add promo code / voucher</a>
			</div>

			<div class="sub-total">
				<h3>Sub-total</h3>
				<div class="amount">
					&dollar;<?php echo number_format( $cart['sub_total'], 2 ); ?>
				</div>
			</div>

			<div class="checkout-btn-container">
				<button class="checkout">Checkout &amp; Finalize Order</button>
			</div>

			<h2>Postage estimator</h2>

			<p>blah</p>
			
		</article>
		<!-- /article -->
		
	<?php endwhile; ?>
	
	<?php else: ?>
	
		<!-- article -->
		<article>
			
			<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
			
		</article>
		<!-- /article -->
	
	<?php endif; ?>
	
	</section>
	<!-- /section -->

<?php get_footer(); ?>
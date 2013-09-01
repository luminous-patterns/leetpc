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

						$product = get_product( $item['product_id'] );

						?>

						<tr class="line-item line-item-<?php echo $k; ?>" data-line-item-id="<?php echo $k; ?>">
							<td class="qty-col"><input type="text" size="2" value="1" /></td>
							<td class="description-col">
								<!-- <?php echo get_the_post_thumbnail( $product->post->ID, 'medium', array( 'class' => 'product-thumbnail' ) ); ?> -->
								<h3><a href="<?php echo get_permalink( $product->post->ID ); ?>"><?php echo $product->post->post_title; ?></a></h3>
								<div class="actions">
									<a href="#">Details</a> |
									<a href="#" class="remove-line-item">Remove</a>
								</div>
								<!--<ul><?php foreach( $item['component_ids'] as $cid ) :
									$c = get_post( str_replace( 'component-', '', $cid ) );
									?>
									<li><?php echo $c->post_title; ?></li>
								<?php endforeach; ?></ul>-->
							</td>
							<td class="price-col">&dollar;<?php echo $product->calcPrice( $item['component_ids'] ); ?></td>
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
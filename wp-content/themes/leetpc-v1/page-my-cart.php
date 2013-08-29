<?php get_header(); ?>
	
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

					<tr class="line-item line-item-308387">
						<td class="qty-col"><input type="text" size="2" value="1" /></td>
						<td class="description-col">
							<h3>Blah de blah</h3>
							<p>Some random information about the product that is being sold</p>
						</td>
						<td class="price-col">&dollar;2,499.00</td>
					</tr>

					<tr class="line-item line-item-308387">
						<td class="qty-col"><input type="text" size="2" value="1" /></td>
						<td class="description-col">
							<h3>Blah de blah</h3>
							<p>Some random information about the product that is being sold</p>
						</td>
						<td class="price-col">&dollar;799.00</td>
					</tr>
					
				</tbody>

			</table>

			<div class="sub-total">
				<h3>Sub-total</h3>
				<div class="amount">
					$3,298.00
				</div>
			</div>

			<div class="checkout-btn-container">
				<button class="customize">Checkout &amp; Finalize Order</button>
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
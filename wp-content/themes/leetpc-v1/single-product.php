<?php get_header( 'single-product' ); ?>

	<?php

	$p = get_product( get_the_ID() );

	$product = get_post_custom();

	$component_ids = explode( ',', $product['components'][0] );

	$components = array();
	$defaults = array();

	foreach ( $component_ids as $id ) {

		$def = preg_match( '/\*$/', $id );
		$id = $def ? substr( $id, 10, -1 ) : substr( $id, 10 );

		$c = get_post( $id );

		$terms = get_the_terms( $c->ID, 'component_group' );
		$terms_keys = array_keys( $terms );
		list( $type, $sub_type ) = explode( '-', $terms[$terms_keys[0]]->slug );

		$components[$type][] = $c;

		if ( $def ) {
			$defaults[$type] = $c;
		}

	}

	?>
	
	<section role="main">
	
	<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php if ( has_post_thumbnail()) : ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php the_post_thumbnail(); ?>
				</a>
			<?php endif; ?>
			
			<h1>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			</h1>

			<div class="product-price">
				<div class="amount">
					$<?php echo number_format( $product['price'][0] ); ?>
				</div>
				<button class="customize">Add to cart</button>
			</div>
			
			<div class="product-type"><?php $cat = wp_get_post_terms( get_the_ID(), 'product_type' ); echo $cat[0]->name; ?> PC</div>

			<div class="editor-content">

				<?php the_content(); ?>

			</div>

			<h2>What's inside</h2>

			<div class="product-config">

				<table>

					<tr>
						<th>CPU</th>
						<td>
							<?php echo $defaults['cpu']->post_title; ?>
						</td>
					</tr>

					<tr>
						<th>Memory (RAM)</th>
						<td>
							<?php echo $defaults['ram']->post_title; ?>
						</td>
					</tr>

				<?php if ( array_key_exists( 'videocard', $defaults ) ) : ?>
					<tr>
						<th>Video (GFX)</th>
						<td>
							<?php echo $defaults['videocard']->post_title; ?>
						</td>
					</tr>
				<?php endif; ?>

					<tr>
						<th>Primary HDD</th>
						<td>
							<?php echo $defaults['hdd']->post_title; ?>
						</td>
					</tr>

				<?php if ( array_key_exists( 'wifi', $defaults ) ) : ?>
					<tr>
						<th>WIFI</th>
						<td>
							<?php echo $defaults['wifi']->post_title; ?>
						</td>
					</tr>
				<?php endif; ?>

					<tr>
						<th>Optical</th>
						<td>
							<?php echo $defaults['optical']->post_title; ?>
						</td>
					</tr>

				<?php if ( array_key_exists( 'sound', $defaults ) ) : ?>
					<tr>
						<th>Sound</th>
						<td>
							<?php echo $defaults['sound']->post_title; ?>
						</td>
					</tr>
				<?php endif; ?>

					<tr>
						<th>Operating System</th>
						<td>
							<?php echo $defaults['os']->post_title; ?>
						</td>
					</tr>

				<?php if ( array_key_exists( 'service', $defaults ) ) : ?>
					<tr>
						<th>Warranty</th>
						<td>
							<?php echo $defaults['service']->post_title; ?>
						</td>
					</tr>
				<?php endif; ?>

				</table>

			</div>

			<div class="product-footer">
				<p><button class="customize">Customise PC &amp; add to cart</button></p>
				<p><a href="/products/?product_type=<?php echo $p->type->slug; ?>" class="button secondary">View more <?php echo $p->type->name; ?> PCs</a></p>
				<p><a href="<?php echo home_url(); ?>">Return to the home page</a></p>
			</div>
			
			<?php edit_post_link(); ?>
			
		</article>
		
	<?php endwhile; ?>
	
	<?php else: ?>
	
		<article>
			
			<h1><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h1>
			
		</article>
	
	<?php endif; ?>
	
	</section>

<?php get_footer(); ?>
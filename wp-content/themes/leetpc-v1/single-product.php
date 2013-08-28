<?php get_header(); ?>

	<?php

	$product = get_post_custom();

	$component_ids = explode( ',', $product['components'][0] );

	$components = array();

	foreach ( $component_ids as $id ) {

		$def = preg_match( '/\*$/', $id );
		$id = $def ? substr( $id, 10, -1 ) : substr( $id, 10 );

		$c = get_post( $id );

		$terms = get_the_terms( $c->ID, 'component_group' );
		$terms_keys = array_keys( $terms );
		list( $type, $sub_type ) = explode( '-', $terms[$terms_keys[0]]->slug );

		$components[$type][] = $c;

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
				<button class="customize">Customize &amp; Buy</button>
			</div>
			
			<div class="product-type"><?php $cat = get_terms( 'product_type' ); echo $cat[1]->name; ?> Series</div>

			<div class="editor-content">

				<?php the_content(); ?>

			</div>

			<h2>Default specifications</h2>

			<div class="product-config">

				<table>

					<tr>
						<th>CPU</th>
						<td>
							Intel Core i7 3930K (3.20Ghz / Six Core)
							<!-- <a href="#">downgrade</a> -->
						</td>
					</tr>

					<tr>
						<th>Motherboard</th>
						<td>ASUS SABERTOOTH-X79</td>
					</tr>

					<tr>
						<th>Memory (RAM)</th>
						<td>
							16GB DDR3 1866MHz (G.Skill 4x4GB)
							<!-- <a href="#">upgrade/downgrade</a> -->
						</td>
					</tr>

					<tr>
						<th>Primary HDD</th>
						<td>
							2x 1TB Seagate SATAIII
							<!-- <a href="#">upgrade/downgrade</a> -->
						</td>
					</tr>

					<tr>
						<th>Video (GFX)</th>
						<td>
							ASUS ROG Matrix 7970 Platinum
							<!-- <a href="#">upgrade/downgrade</a> -->
						</td>
					</tr>

					<tr>
						<th>Optical</th>
						<td>
							Pioneer 15x Blu-Ray Writer
							<!-- <a href="#">upgrade</a> -->
						</td>
					</tr>

					<tr>
						<th>Sound</th>
						<td>
							Realtek® ALC892 8-Channel HD Audio
							<!-- <a href="#">upgrade</a> -->
						</td>
					</tr>

					<tr>
						<th>Operating System</th>
						<td>
							Windows 8 Ultimate
							<!-- <a href="#">upgrade/downgrade</a> -->
						</td>
					</tr>

				</table>

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
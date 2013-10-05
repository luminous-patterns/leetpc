<?php include( 'header.common.php' ); ?>

		<div class="product-sampler">

			<?php

			$products = get_products( array( 'posts_per_page' => 4 ) );

			?>

			<div class="mini-products">

				<?php $count = 0; ?>
				<?php foreach ( $products as $p ) : ?>

				<div class="mini-featured-product <?php $count++; if ( $count == 2 ) echo 'active'; ?>">
					<h2><?php $cat = wp_get_post_terms( $p->post->ID, 'product_type' ); echo $cat[0]->name; ?> PCs</h2>
				</div>

				<?php endforeach; ?>

			</div>

			<?php $count = 0; ?>
			<?php foreach ( $products as $p ) : ?>

			<div class="featured-product <?php $count++; if ( $count != 2 ) echo 'hidden'; ?>">

				<h1><a href="<?php echo get_permalink( $p->post->ID ); ?>"><?php echo $p->post->post_title; ?></a></h1>

				<ul><?php foreach( $p->comDefaults as $type => $c ) : ?>
					<li><?php echo $c->post_title; ?></li>
				<?php endforeach; ?></ul>

				<?php echo get_the_post_thumbnail( $p->post->ID, 'large' ); ?>

				<div class="product-price">
					<div class="amount">
						$<?php echo number_format( $p->get( 'price' ) ); ?>
					</div>
				</div>

				<!-- <a href=""></a> -->
				<a href="<?php echo get_permalink( $p->post->ID ); ?>" class="button">View Product</a>

			</div>

			<?php endforeach; ?>

		</div>
	
		<!-- wrapper -->
		<div class="wrapper">
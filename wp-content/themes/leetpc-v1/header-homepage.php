<?php include( 'header.common.php' ); ?>

		<div class="product-sampler">

			<?php

			$products = get_products();

			$p = $products[0];

			?>

			<div class="featured-product">

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

			<div class="controls">

				<div class="control-item">

				</div>

			</div>

		</div>
	
		<!-- wrapper -->
		<div class="wrapper">
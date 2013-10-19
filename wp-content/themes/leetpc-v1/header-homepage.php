<?php include( 'header.common.php' ); ?>

		<div class="product-sampler">

			<?php

			$products = get_products( array( 'posts_per_page' => 4 ) );

			$p = $products[1];

			?>

			<div class="featured-product">
				<div class="inside-container"></div>
			</div>

			<?php $count = 0; ?>
			<?php foreach ( $products as $p ) : ?>
			<?php $count++; ?>

			<div class="featured-product display-order-<?php echo $count; ?> hidden">

				<h2><a href="<?php echo get_permalink( $p->post->ID ); ?>"><?php echo $p->post->post_title; ?></a></h2>

				<div class="components"><?php 

					$com_lines = array(); 
					foreach( $p->comDefaults as $type => $c ) { 
						if ( $type == 'case' ) continue;
						$com_lines[] = '<strong>' . strtoupper( $type ) . '</strong> ' . $c->post_title; 
					}

					echo implode( '<br />', $com_lines );

				?></div>

				<?php echo get_the_post_thumbnail( $p->post->ID, 'large' ); ?>

				<div class="product-price">
					<div class="amount">
						$<?php echo number_format( $p->get( 'price' ) ); ?>
					</div>
				</div>

				<div class="actions">
					<a href="/products/?product_type=<?php echo $p->type->slug; ?>" class="button secondary">More <?php echo $p->type->name; ?> PCs</a>
					<a href="<?php echo get_permalink( $p->post->ID ); ?>" class="button">View or purchase PC</a>
				</div>

			</div>

			<?php endforeach; ?>

		</div>
	
		<!-- wrapper -->
		<div class="wrapper">
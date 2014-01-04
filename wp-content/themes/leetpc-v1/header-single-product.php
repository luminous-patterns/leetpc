<?php include( 'header.common.php' ); 

	$p = get_product( get_the_ID() );

	?>

		<div class="product-header">

			<?php if ( has_post_thumbnail() ) : ?>
			<div class="featured-image"><?php the_post_thumbnail(); ?></div>
			<?php endif; ?>
			
			<div class="product-summary">

				<h1><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
				<span class="product-category"><?php echo $p->getTypeName(); ?></span>

				<div class="product-price">$<?php echo number_format( $p->getPrice() ); ?></div>
				
			</div>

			<button class="customize">Add to cart</button>

			<div class="product-tabs">
				<ul>
					<li class="tab-details active">Details</li>
					<li class="tab-specs">Specifications</li>
					<li class="tab-photos">Photos (3)</li>
					<li class="tab-reviews">Reviews (0)</li>
				</ul>
			</div>

		</div>
	
		<!-- wrapper -->
		<div class="wrapper">
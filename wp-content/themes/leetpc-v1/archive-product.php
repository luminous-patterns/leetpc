<?php get_header(); ?>

	<?php 

	$type = $_GET['product_type'] ? $_GET['product_type'] : 'all-products';
	$type_titles = array(
		'all-products' => 'All Products',
		'home-and-student' => 'Home &amp; Student PCs',
		'professional' => 'Professional PCs',
		'enterprise' => 'Enterprise PCs',
		'gaming-and-multimedia' => 'Gaming &amp; Multimedia PCs'
	);

	?>
	
	<button class="sidebar-toggle-container secondary"><div class="toggle-sidebar layer-1"></div><div class="toggle-sidebar layer-2"></div></button>
	<h1><?php echo $type_titles[$type]; ?></h1>
	
	<!-- section -->
	<section role="main">

		<div class="product-list">
		
		<?php if ( have_posts() ): while ( have_posts() ) : the_post(); $p = get_product( get_the_ID() ); ?>
			
			<!-- article -->
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'compact' ); ?>>
			
				<!-- post thumbnail -->
				<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" style="float: left;">
						<?php the_post_thumbnail(array(120,120)); // Declare pixel size you need inside the array ?>
					</a>
				<?php endif; ?>
				<!-- /post thumbnail -->
				
				<!-- post title -->
				<h2>
					<a href="<?php the_permalink(); ?>" title="<?php echo get_the_title() . ' ' . $p->type->name; ?> PC"><?php the_title(); ?></a>
				</h2>
				<!-- /post title -->

				<div class="product-type"><?php $cat = wp_get_post_terms( get_the_ID(), 'product_type' ); echo $cat[0]->name; ?> PC</div>

				<div class="components"><?php 

					$types = array( 'cpu', 'ram', 'videocard', 'hdd', 'wifi', 'optical', 'os', 'service' );
					$com_lines = array(); 
					foreach( $types as $type ) {
						if ( !array_key_exists( $type, $p->comDefaults ) ) continue;
						$c = $p->comDefaults[$type];
						$label = $type;
						if ( $type == 'service' ) $label = 'warranty';
						$com_lines[] = '<strong>' . strtoupper( $label ) . '</strong> ' . $c->post_title; 
					}

					echo implode( '<br />', $com_lines );

				?></div>

				<div class="product-price">
					<div class="amount">
						&dollar;<?php echo number_format( $p->get( 'price' ) ); ?>
					</div>
				</div>
				
				<div class="actions">
					<button class="customize">Add to cart</button>
					<a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title() . ' ' . $p->type->name; ?> PC">Product information</a>
					<?php edit_post_link( 'Edit product' ); ?>
				</div>
				
			</article>
			<!-- /article -->
			
		<?php endwhile; ?>

		<?php else: ?>

			<!-- article -->
			<article>
				<h2>No products to display</h2>
			</article>
			<!-- /article -->

		<?php endif; ?>

		</div>
		
		<?php get_template_part('pagination'); ?>
		
		<p class="asterisk">* All prices are quoted in Australian Dollars and include GST</p>
	
	</section>
	<!-- /section -->

	<!-- sidebar -->
	<aside class="sidebar" role="complementary">
	    		
		<div class="sidebar-widget product-type-filter">
			<ul>
				<li class="<?php if ( !$_GET['product_type'] ) echo 'current'; ?>"><a href="/products/">All Products (5)</a></li>
				<li class="<?php if ( $_GET['product_type'] == 'home-and-student' ) echo 'current'; ?>"><a href="/products/?product_type=home-and-student">Home &amp; Student PCs (2)</a></li>
				<li class="<?php if ( $_GET['product_type'] == 'professional' ) echo 'current'; ?>"><a href="/products/?product_type=professional">Professional PCs (1)</a></li>
				<li class="<?php if ( $_GET['product_type'] == 'gaming-and-multimedia' ) echo 'current'; ?>"><a href="/products/?product_type=gaming-and-multimedia">Gaming &amp; Multimedia PCs (2)</a></li>
			</ul>
		</div>
	    		
		<div class="sidebar-widget">
			<?php

				$deliver_by = new DateTime( null, new DateTimeZone( 'Australia/Melbourne' ) );
				$deliver_by->add( new DateInterval( 'P9D' ) );
				if ( $deliver_by->format( 'N' ) > 5 ) {
					$period = 9 - $deliver_by->format( 'N' );
					$deliver_by->add( new DateInterval( 'P' . $period . 'D' ) );
				}

			?>
			<h3>FREE Delivery</h3>
			<p>Customers in <strong>Victoria, Australia</strong> receive free delivery on all PC orders.  Order today to receive your new PC by <?php echo $deliver_by->format( 'D jS \o\f M' ); ?>.</p>
		</div>
			
	</aside>
	<!-- /sidebar -->

<?php get_footer(); ?>
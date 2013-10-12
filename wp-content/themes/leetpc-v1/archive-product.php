<?php get_header(); ?>
	
	<!-- section -->
	<section role="main">
	
		<h1>Products</h1>

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

					$com_lines = array(); 
					foreach( $p->comDefaults as $type => $c ) { 
						if ( $type == 'case' ) continue;
						$com_lines[] = $c->post_title . ' <strong>' . strtoupper( $type ) . '</strong>'; 
					}

					echo implode( ', ', $com_lines );

				?></div>

				<div class="product-price">
					<div class="amount">
						&dollar;<?php echo number_format( $p->get( 'price' ) ); ?>
					</div>
				</div>
				
				<div class="actions">
					<button class="small customize">Add to cart</button>
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
	
	</section>
	<!-- /section -->

<?php get_footer(); ?>
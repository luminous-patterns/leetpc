<?php /* Template Name: Homepage Template */ get_header( 'homepage' ); ?>
	
	<button class="sidebar-toggle-container secondary"><div class="toggle-sidebar layer-1"></div><div class="toggle-sidebar layer-2"></div></button>
	
	<!-- section -->
	<section role="main">
	
	<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
		<!-- article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="editor-content">
		
				<?php the_content(); ?>

			</div>

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

	<!-- sidebar -->
	<aside class="sidebar" role="complementary">
	    		
		<div class="sidebar-widget product-type-filter">
			<ul>
				<li class="current"><a href="/">LEETPC Home</a></li>
				<li><a href="/products/">Products</a></li>
				<li><a href="/why-us/">Why choose us</a></li>
				<li><a href="/contact-us/">Contact us</a></li>
				<li><a href="/my-cart/">My shopping cart</a></li>
			</ul>
		</div>
	    		
		<div class="sidebar-widget">
			<h3>Need help?</h3>
			<p>Contact us by email <a href="mailto:care@leetpc.com.au">care@leetpc.com.au</a> or phone <strong>(03) 9872 4837</strong>.</p>
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
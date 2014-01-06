<?php get_header(); $post = get_post(); ?>

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
			
		<?php edit_post_link(); ?>
		
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
				<li><a href="/">LEETPC Home</a></li>
				<li><a href="/products/">Products</a></li>
				<li <?php if ( $post->post_name == 'why-us' ) echo 'class="current"'; ?>><a href="/why-us/">Why choose us</a></li>
				<li><a href="/contact-us/">Contact us</a></li>
				<li><a href="/my-cart/">My shopping cart</a></li>
			</ul>
		</div>
			
	</aside>
	<!-- /sidebar -->

<?php get_footer(); ?>
<?php /* Template Name: Contact Page */ get_header(); ?>
	
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
				<li><a href="/">LEETPC Home</a></li>
				<li><a href="/products/">Products</a></li>
				<li><a href="/service-and-repair/">Service and repair</a></li>
				<li><a href="/why-us/">Why choose us</a></li>
				<li class="current"><a href="/contact-us/">Contact us</a></li>
				<li><a href="/my-cart/">My shopping cart</a></li>
			</ul>
		</div>
	    		
		<div class="sidebar-widget">
			<h3>By e-mail</h3>
			<p>
				<strong>Customer care</strong>
				<br /><a href="mailto:care@leetpc.com.au">care@leetpc.com.au</a>
			</p>
			<p>
				<strong>Technical support</strong>
				<br /><a href="mailto:care@leetpc.com.au">support@leetpc.com.au</a>
			</p>
			<p>
				<strong>Sales</strong>
				<br /><a href="mailto:care@leetpc.com.au">sales@leetpc.com.au</a>
			</p>
			<p>
				<strong>Feedback</strong>
				<br /><a href="mailto:care@leetpc.com.au">feedback@leetpc.com.au</a>
			</p>
		</div>
	    		
		<div class="sidebar-widget">
			<h3>By phone</h3>
			<p>
				<strong>All enquiries</strong>
				<br />0400 935 853
			</p>
		</div>
	    		
		<div class="sidebar-widget">
			<h3>By mail</h3>
			<p>
				<strong>All enquiries</strong>
				<br />4 Holyrood Dr
				<br />Vermont VIC 3133
				<br />AUSTRALIA
			</p>
		</div>
			
	</aside>
	<!-- /sidebar -->

<?php get_footer(); ?>
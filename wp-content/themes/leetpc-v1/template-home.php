<?php /* Template Name: Homepage Template */ get_header( 'homepage' ); ?>
	
	<!-- section -->
	<section role="main">
	
		<h1><?php the_title(); ?></h1>
	
	<?php if (have_posts()): while (have_posts()) : the_post(); ?>
	
		<!-- article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="editor-content">
		
				<?php the_content(); ?>

			</div>
				
			<?php

			$product_types = get_terms( 'product_type', array( 'hide_empty' => false ) );

			foreach ( $product_types as $p ) : ?>

			<div class="product-type">

				<h3><?php echo $p->name; ?></h3>

			</div>

			<?php endforeach; ?>
			
			<br class="clear">
			
			<?php edit_post_link(); ?>
			
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

<?php get_footer(); ?>
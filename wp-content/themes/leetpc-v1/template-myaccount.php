<?php /* Template Name: My Account */ get_header(); ?>
	
	<!-- section -->
	<section role="main">

		<h1>My Account</h1>
	
		<!-- article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="editor-content">

			<?php if ( is_user_logged_in() ) : ?>

				<p><strong>Welcome back, Cal</strong></p>

			<?php else : ?>

			<?php endif; ?>

			</div>

		</article>

	</section>
	<!-- /section -->

	<!-- sidebar -->
	<aside class="sidebar" role="complementary">

	<?php if ( is_user_logged_in() ) : ?>
	    		
		<div class="sidebar-widget product-type-filter">
			<ul>
				<li class="current"><a href="/my-account/">Dashboard</a></li>
				<li><a href="/my-account/orders/">My order history</a></li>
				<li><a href="/my-account/">Edit account details</a></li>
			</ul>
			<ul>
				<li><a href="/my-account/edit/">Change email address</a></li>
				<li><a href="/my-account/edit/">Change password</a></li>
				<li><a href="/my-account/logout/">Log out</a></li>
			</ul>
		</div>

	<?php else : ?>

		<div class="sidebar-widget product-type-filter">
			<ul>
				<li class="current"><a href="/my-account/login/">Customer login</a></li>
				<li><a href="/my-account/register/">Account registration</a></li>
				<li><a href="/my-account/recover-password/">Lost password recovery</a></li>
			</ul>
		</div>

	<?php endif; ?>

	</div>

<?php get_footer(); ?>
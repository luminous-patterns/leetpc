<?php /* Template Name: My Account */ get_header(); ?>
	
	<!-- section -->
	<section role="main">

		<h1>My Account</h1>

	<?php if ( is_user_logged_in() ) : ?>

		<div class="">

			<ul>
				<li><a href="#"></a></li>
				<li><a href="#"></a></li>
				<li><a href="#"></a></li>
			</ul>

		</div>

		<div class="">

			<ul>
				<li><a href="#">Change email address</a></li>
				<li><a href="#">Change password</a></li>
				<li><a href="#">Logout</a></li>
			</ul>

		</div>

	<?php else : ?>

	<?php endif; ?>

	</section>
	<!-- /section -->

<?php get_footer(); ?>
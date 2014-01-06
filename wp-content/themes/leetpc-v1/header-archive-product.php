<?php include( 'header.common.php' ); ?>
	
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
	
		<div class="page-header">
			<h1><?php echo $type_titles[$type]; ?></h1>
		</div>

		<!-- wrapper -->
		<div class="wrapper">
			
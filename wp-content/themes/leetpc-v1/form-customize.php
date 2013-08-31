<?php

if ( get_post_type() == 'product' ) :

	$product = get_post_custom();

	$component_ids = explode( ',', $product['components'][0] );

	$components = array();
	$default_ids = array();
	$defaults = array();
	$attrs = array();

	foreach ( $component_ids as $id ) {

		$def = preg_match( '/\*$/', $id );
		$id = $def ? substr( $id, 10, -1 ) : substr( $id, 10 );

		$c = get_post( $id );

		$terms = get_the_terms( $c->ID, 'component_group' );
		$terms_keys = array_keys( $terms );
		list( $type, $sub_type ) = explode( '-', $terms[$terms_keys[0]]->slug );

		$components[$type][] = $c;

		if ( $def ) {
			$defaults[$type] = $c;
			$default_ids[] = 'component-' . $c->ID;
		}

		$attrs[$c->ID] = get_post_custom( $c->ID );

	}

	?>

	<div class="modal-wrapper">

		<div class="modal customize-form-modal customize-form product-attrs">

			<input type="hidden" name="product_id" value="<?php the_ID(); ?>" />
			<input type="hidden" name="static_component_ids" value="<?php echo implode( ',', $default_ids ); ?>" />
			<input type="hidden" name="component_ids" value="<?php echo implode( ',', $default_ids ); ?>" />
			<input type="hidden" name="final_selection" value="" />

			<input type="hidden" name="product_base_price" value="<?php echo $product['price'][0]; ?>" />
			<input type="hidden" name="product_price_diffs" value="0.00" />

			<div class="modal-header">
				<h3 class="product-name">Customize PC</h3>
				<div class="sub-total">
					<div class="amount">
						&dollar;<?php echo number_format( $product['price'][0] ); ?>
					</div>
				</div>
			</div>

			<div class="modal-body">

				<div class="component-list">

					<div class="component component-cpu">
						<h4>CPU</h4>
						<div class="component-options">
							<?php print_component_options( 'cpu', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-motherboard">
						<h4>Motherboard</h4>
						<div class="component-options">
							<?php print_component_options( 'motherboard', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-ram">
						<h4>Memory (RAM)</h4>
						<div class="component-options">
							<?php print_component_options( 'ram', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-hdd">
						<h4>Primary HDDs</h4>
						<div class="component-options">
							<?php print_component_options( 'hdd', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-videocard">
						<h4>Video (GFX)</h4>
						<div class="component-options">
							<?php print_component_options( 'videocard', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-optical">
						<h4>Optical</h4>
						<div class="component-options">
							<?php print_component_options( 'optical', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-sound">
						<h4>Sound</h4>
						<div class="component-options">
							**** Realtek® ALC892 8-Channel HD Audio
						</div>
					</div>

					<div class="component component-os">
						<h4>Operating System</h4>
						<div class="component-options">
							<?php print_component_options( 'os', $components, $defaults, $attrs ); ?>
						</div>
					</div>

					<div class="component component-case">
						<h4>Case</h4>
						<div class="component-options">
							<?php print_component_options( 'case', $components, $defaults, $attrs ); ?>
						</div>
					</div>

				</div>
			
			</div>

			<div class="modal-footer">
				<button class="secondary close-modal">Cancel</button>
				<button class="add-to-cart">Add to cart</button>
			</div>

		</div>

	</div>

<?php endif; ?>
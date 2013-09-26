<?php
	
	add_action( 'add_meta_boxes',      'product_add_meta' );
	add_action( 'save_post',           'product_save_meta' );
	
	function product_add_meta() {
		
		add_meta_box(
			'product_metabox', // HTML 'id' attribute of the edit screen section
			'Product Meta', // Title of the edit screen section, visible to user
			'product_metabox', // Function that prints out the HTML for the edit screen section.
			'product', // The type of Write screen on which to show the edit screen section
			'normal', // The part of the page where the edit screen section should be shown
			'high' // The priority within the context where the boxes should show
		);
		
	}
	
	function product_save_meta( $post_id ) {
		
		if ( !key_exists( 'product_meta', $_POST ) 
			|| ( !wp_verify_nonce( $_POST['product_meta'], 'product_meta_nonce' ) ) 
			|| ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			|| ( key_exists( 'post_type', $_POST ) && 'product' != $_POST['post_type'] ) 
			|| ( key_exists( 'post_type', $_POST ) && 'product' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			|| !key_exists( 'prod_attr', $_POST ) )
			return $post_id;

		// Automatic update attributes
		$autoUpdateAttrs = array( 'price', 'components' );

		foreach ( $autoUpdateAttrs as $a ) {
			if ( key_exists( $a, $_POST['prod_attr'] ) ) {
				update_post_meta( $post_id, $a, $_POST['prod_attr'][$a] );
			}
		}
		
	}

	function product_metabox() {
		
		$current = array();
		$custom = get_post_custom();
		
		$d = array(
			
			'price'          => '0.00',
			'components'     => ''
			
		);
		
		foreach ( $d as $k => $v ) {
			$key = $k;
			$current[$k] = ( key_exists( $key, $custom ) ) ? $custom[$key][0] : $d[$k];
		}

		wp_nonce_field( 'product_meta_nonce', 'product_meta' );

		?>

		<div class="loading-overlay"></div>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="prod_attr[components]">Components</label></th>
				<td>
					<div class="components-list-container">
						<ul class="components-list"><?php 

							$component_groups = get_terms( 'component_group' );

							$tg = array();

							foreach ( $component_groups as $g ) {

								list( $o, $h ) = explode( '-', $g->slug );

								if ( !array_key_exists( $o, $tg ) ) {
									$tg[$o] = array();
								}

								$tg[$o][$h] = $g;

							}

							ksort( $tg );

							foreach ( $tg as $o => $og ) {

								echo '<li class="group group-' . $o . '"><div class="title">' . strtoupper( $o ) . ' <div class="values"></div></div><ul>';

								foreach ( $og as $h => $g ) :

									echo '<li class="group sub-group group-' . $o . '"><div class="title">' . strtoupper( $h ) . ' <div class="values"></div></div><ul>';

									?>

										<li>
											<input type="radio" class="clear-value" name="<?php echo $o; ?>" value="" />
											<label>
												<input type="checkbox" class="clear-value" />
												None
											</label>
										</li>

									<?php
									
									$components = get_posts( array( 
										'post_type' => 'component',
										'component_group' => $g->slug
									) );

									foreach ( $components as $c ) : 

										$meta = get_post_custom( $c->ID );

										?>

										<li class="component component-<?php echo $c->ID; ?>">
											<input type="radio" name="<?php echo $o; ?>" value="<?php echo $c->ID; ?>" />
											<label>
												<input type="checkbox" name="component-<?php echo $c->ID; ?>" />
												<?php echo $c->post_title; ?>
												<span class="cost">&dollar;<?php echo number_format( $meta['cost'][0] ) . ( $meta['cost'][0] < $meta['price'][0] ? ' <strong>/ $' . number_format( $meta['price'][0] ) . '</strong>' : '' ); ?></span>
											</label>
										</li>

									<?php endforeach;

									echo '</ul></li>';

								endforeach;

								echo '</ul></li>';

							}

						?></ul>
					</div>
					<input type="hidden" name="prod_attr[components]" id="prod_attr[components]" class="components-list-input" value="<?php echo $current['components']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="prod_attr[min_price]">Cost $</label></th>
				<td>
					<input type="text" name="prod_attr[min_price]" id="prod_attr[min_price]" class="regular-text internal-cost" value="<?php echo $current['min_price']; ?>" readonly="readonly" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="prod_attr[calc_price]">Auto Price $</label></th>
				<td>
					<input type="text" name="prod_attr[calc_price]" id="prod_attr[calc_price]" class="regular-text calculated-price" value="<?php echo $current['calc_price']; ?>" readonly="readonly" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="prod_attr[price]">Final Price $</label></th>
				<td>
					<input type="text" name="prod_attr[price]" id="prod_attr[price]" class="regular-text" value="<?php echo $current['price']; ?>" />
				</td>
			</tr>

		</table>

		<?php

	}

	/*










	*/

	add_action( 'add_meta_boxes',      'component_add_meta' );
	add_action( 'save_post',           'component_save_meta' );
	
	function component_add_meta() {
		
		add_meta_box(
			'component_metabox',
			'Component Meta',
			'component_metabox',
			'component',
			'normal',
			'high'
		);
		
	}
	
	function component_save_meta( $post_id ) {
		
		if ( !key_exists( 'component_meta', $_POST ) 
			|| ( !wp_verify_nonce( $_POST['component_meta'], 'component_meta_nonce' ) ) 
			|| ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			|| ( key_exists( 'post_type', $_POST ) && 'component' != $_POST['post_type'] ) 
			|| ( key_exists( 'post_type', $_POST ) && 'component' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			|| !key_exists( 'comp_attr', $_POST ) )
			return $post_id;

		// Automatic update attributes
		$autoUpdateAttrs = array( 'price', 'cost', 'long_name', 'model_number', 'manufacturer_link', 'wholesale_link' );

		foreach ( $autoUpdateAttrs as $a ) {
			if ( key_exists( $a, $_POST['comp_attr'] ) ) {
				update_post_meta( $post_id, $a, $_POST['comp_attr'][$a] );
			}
		}
		
	}

	function component_metabox() {
		
		$current = array();
		$custom = get_post_custom();
		
		$d = array(
			
			'price'               => '0.00',
			'cost'                => '0.00',

			'long_name'           => '',
			'model_number'        => '',
			'manufacturer_link'   => '',
			'wholesale_link'      => ''
			
		);
		
		foreach ( $d as $k => $v ) {
			$key = $k;
			$current[$k] = ( key_exists( $key, $custom ) ) ? $custom[$key][0] : $d[$k];
		}

		wp_nonce_field( 'component_meta_nonce', 'component_meta' );

		?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="comp_attr[cost]">Cost $</label></th>
				<td>
					<input type="text" name="comp_attr[cost]" id="comp_attr[cost]" class="regular-text" value="<?php echo $current['cost']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="comp_attr[price]">Price $</label></th>
				<td>
					<input type="text" name="comp_attr[price]" id="comp_attr[price]" class="regular-text" value="<?php echo $current['price']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="comp_attr[long_name]">Long Name</label></th>
				<td>
					<input type="text" name="comp_attr[long_name]" id="comp_attr[long_name]" class="regular-text" value="<?php echo $current['long_name']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="comp_attr[model_number]">Model Number</label></th>
				<td>
					<input type="text" name="comp_attr[model_number]" id="comp_attr[model_number]" class="regular-text" value="<?php echo $current['model_number']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="comp_attr[manufacturer_link]">Manufacturer Link</label></th>
				<td>
					<input type="text" name="comp_attr[manufacturer_link]" id="comp_attr[manufacturer_link]" class="regular-text" value="<?php echo $current['manufacturer_link']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="comp_attr[wholesale_link]">Wholesale Link</label></th>
				<td>
					<input type="text" name="comp_attr[wholesale_link]" id="comp_attr[wholesale_link]" class="regular-text" value="<?php echo $current['wholesale_link']; ?>" />
				</td>
			</tr>

		</table>

		<?php

	}

	/*










	*/

	add_action( 'add_meta_boxes',      'invoice_add_meta' );
	add_action( 'save_post',           'invoice_save_meta' );
	
	function invoice_add_meta() {
		
		add_meta_box(
			'invoice_metabox',
			'Invoice Meta',
			'invoice_metabox',
			'invoice',
			'normal',
			'high'
		);
		
	}
	
	function invoice_save_meta( $post_id ) {
		
		if ( !key_exists( 'invoice_meta', $_POST ) 
			|| ( !wp_verify_nonce( $_POST['invoice_meta'], 'invoice_meta_nonce' ) ) 
			|| ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			|| ( key_exists( 'post_type', $_POST ) && 'invoice' != $_POST['post_type'] ) 
			|| ( key_exists( 'post_type', $_POST ) && 'invoice' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) ) )
			return $post_id;

		return $post_id;

	}

	function invoice_metabox() {
		
		// $current = array();
		$custom = get_post_custom();

		$keys = array(
			'_cart'        => 'Order Items',
			'_user'        => 'User',
			'_acct'        => 'Account',
			'_delivery'    => 'Delivery',
			'_cc'          => 'Credit Card'
		);
		
		// $d = array(
			
		// 	'price'               => '0.00',
		// 	'cost'                => '0.00',

		// 	'long_name'           => '',
		// 	'model_number'        => '',
		// 	'manufacturer_link'   => '',
		// 	'wholesale_link'      => ''
			
		// );
		
		// foreach ( $d as $k => $v ) {
		// 	$key = $k;
		// 	$current[$k] = ( key_exists( $key, $custom ) ) ? $custom[$key][0] : $d[$k];
		// }

		// wp_nonce_field( 'component_meta_nonce', 'component_meta' );

		?>

		<table class="form-table">

		<?php foreach ( $keys as $k => $label ) : ?>

			<tr valign="top">
				<th scope="row"><label for="<?php echo $k; ?>"><?php echo $label; ?></label></th>
				<td>
					<?php echo $custom[$k][0]; ?>
				</td>
			</tr>

		<?php endforeach; ?>

		</table>

		<?php

	}
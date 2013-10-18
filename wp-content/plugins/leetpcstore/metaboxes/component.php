<?php

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
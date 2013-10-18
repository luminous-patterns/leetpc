<?php

	add_action( 'add_meta_boxes',      'coupon_add_meta' );
	add_action( 'save_post',           'coupon_save_meta' );
	
	function coupon_add_meta() {
		
		add_meta_box(
			'coupon_metabox',
			'Coupon Meta',
			'coupon_metabox',
			'coupon',
			'normal',
			'high'
		);
		
	}
	
	function coupon_save_meta( $post_id ) {
		
		if ( !key_exists( 'coupon_meta', $_POST ) 
			|| ( !wp_verify_nonce( $_POST['coupon_meta'], 'coupon_meta_nonce' ) ) 
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			|| ( key_exists( 'post_type', $_POST ) && 'coupon' != $_POST['post_type'] ) 
			|| ( key_exists( 'post_type', $_POST ) && 'coupon' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			|| !key_exists( 'coupon_attr', $_POST ) )
			return $post_id;

		// Automatic update attributes
		$autoUpdateAttrs = array( 'code', 'expires', 'discount_type', 'discount_amount' );

		foreach ( $autoUpdateAttrs as $a ) {
			if ( key_exists( $a, $_POST['coupon_attr'] ) ) {
				update_post_meta( $post_id, $a, $_POST['coupon_attr'][$a] );
			}
		}
		
	}

	function coupon_metabox() {
		
		$current = array();
		$custom = get_post_custom();
		
		$def_date = new DateTime( null, new DateTimeZone( 'Australia/Melbourne' ) );
		$def_date->add( new DateInterval( 'P90D' ) );

		$d = array(

			'code'                => strtoupper( substr( md5( uniqid( '', true ) ), -8 ) ),
			'expires'             => $def_date->format( 'Y-m-d' ),
			
			'discount_type'       => '%',
			'discount_amount'     => '0.00'
			
		);
		
		foreach ( $d as $k => $v ) {
			$key = $k;
			$current[$k] = ( key_exists( $key, $custom ) ) ? $custom[$key][0] : $d[$k];
		}

		wp_nonce_field( 'coupon_meta_nonce', 'coupon_meta' );

		?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="coupon_attr[code]">Code</label></th>
				<td>
					<input type="text" name="coupon_attr[code]" id="coupon_attr[code]" class="regular-text" value="<?php echo $current['code']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="coupon_attr[expires]">Expires</label></th>
				<td>
					<input type="text" name="coupon_attr[expires]" id="coupon_attr[expires]" class="regular-text" value="<?php echo $current['expires']; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="coupon_attr[discount_amount]">Discount</label></th>
				<td>
					<select name="coupon_attr[discount_type]" id="coupon_attr[discount_type]">
						<option value="$" <?php if ( $current['discount_type'] == '$' ) echo 'checked="checked"'; ?>>&dollar;</option>
						<option value="%" <?php if ( $current['discount_type'] == '%' ) echo 'checked="checked"'; ?>>&#37;</option>
					</select>
					<input type="text" name="coupon_attr[discount_amount]" id="coupon_attr[discount_amount]" class="small-text" value="<?php echo $current['discount_amount']; ?>" />
				</td>
			</tr>

		</table>

		<?php

	}
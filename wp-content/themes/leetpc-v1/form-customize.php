<?php

if ( get_post_type() == 'product' ) {

	$product = get_post_custom();

	$component_ids = explode( ',', $product['components'][0] );

	$components = array();
	$defaults = array();

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
		}

	}

}

?>

<div class="customize-form-wrapper">

	<div class="customize-form">

		<input type="hidden" name="product-id" value="" />

		<div class="form-header">
			<h3 class="product-name">PC10813-XG9</h3>
			<div class="sub-total">
				<div class="amount">
					&dollar;2,499.00
				</div>
			</div>
		</div>

		<div class="component-table">

			<table>

				<tr>
					<th>CPU</th>
					<td>
						<select><option><?php echo $defaults['cpu']->post_title; ?></option></select>
					</td>
				</tr>

				<tr>
					<th>Motherboard</th>
					<td><?php echo $defaults['motherboard']->post_title; ?></td>
				</tr>

				<tr>
					<th>Memory (RAM)</th>
					<td>
						<select><option><?php echo $defaults['ram']->post_title; ?></option></select>
					</td>
				</tr>

				<tr>
					<th>Primary HDD</th>
					<td>
						<select><option><?php echo $defaults['hdd']->post_title; ?></option></select>
					</td>
				</tr>

				<tr>
					<th>Video (GFX)</th>
					<td>
						<select><option><?php echo $defaults['videocard']->post_title; ?></option></select>
					</td>
				</tr>

				<tr>
					<th>Optical</th>
					<td>
						<select><option><?php echo $defaults['optical']->post_title; ?></option></select>
					</td>
				</tr>

				<tr>
					<th>Sound</th>
					<td>
						Realtek® ALC892 8-Channel HD Audio
					</td>
				</tr>

				<tr>
					<th>Operating System</th>
					<td>
						<select><option><?php echo $defaults['os']->post_title; ?></option></select>
					</td>
				</tr>

			</table>

		</div>

		<div class="form-footer">
			<button class="secondary">Cancel</button>
			<button>Add to cart</button>
		</div>

	</div>

</div>
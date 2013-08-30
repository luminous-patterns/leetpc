<?php

function get_cart() {
	init_cart();
	return $GLOBALS['wp_session']['shopping_cart'];
}

function get_cart_key( $product_id, $component_ids ) {
	sort( $component_ids );
	return md5( $product_id . str_replace( ',', '', implode( ',', $component_ids ) ) );
}

function add_product_to_cart( $product_id, $component_ids = array(), $qty = 1 ) {

	global $wp_session;

	init_cart();

	$key = get_cart_key( $product_id, $component_ids );

	if ( !array_key_exists( $key, $wp_session['shopping_cart']['items'] ) ) {
		$wp_session['shopping_cart']['items_count'] += $qty;
		$wp_session['shopping_cart']['items'][$key] = array(
			'product_id' => $product_id,
			'component_ids' => $component_ids,
			'qty' => $qty//,
			// 'price' => calcProductPrice( $product_id, $component_ids )
		);
	}

	return $line_item;

}

function empty_cart() {
	$GLOBALS['wp_session']['shopping_cart']['items'] = array();
	$GLOBALS['wp_session']['shopping_cart']['items_count'] = 0;
	return true;
}

function init_cart() {
	if ( !$GLOBALS['wp_session'] || !array_key_exists( 'shopping_cart', $GLOBALS['wp_session'] ) ) {
		$GLOBALS['wp_session']['shopping_cart'] = array(
			'sub_total'     => 0.00,
			'created'       => time(),
			'items'         => array(),
			'items_count'   => 0
		);
	}
	return true;
}

function update_line_item_qty( $line_item_key, $qty ) {

	// global $wp_session;

	// if ( !$wp_session['shopping_cart'] || !array_key_exists( $line_item_key, $wp_session['shopping_cart']['items'] ) ) {
	// 	return false;
	// }

	// if ( $qty < 1 ) {
	// 	return remove_line_item( $line_item_key );
	// }

	// $wp_session['shopping_cart']['items_count'] = $wp_session['shopping_cart']['items_count']

	// $wp_session['shopping_cart']['items'][$line_item_key]['qty'] = $qty;
	// return true;

}

// function calcProductPrice( $product_id, $component_ids = array() ) {

// 	$sub_total = 0;

// 	$product = get_post( $product_id );
// 	$meta = get_post_custom( $product_id );

// 	$selected_components = array();
// 	$all_prod_components = explode( ',', $meta['components'][0] );

// 	foreach ( $component_ids as $id ) {

// 		$id = str_replace( 'component-', '', $id );

// 		$component = get_post( $id );
// 		$com_meta = get_post_custom( $id );

// 		$selected_components[$id] = array(
// 			'component' => $component,
// 			'meta' => $com_meta,
// 			'price' => max( $com_meta['price'][0], $com_meta['cost'][0] )
// 		);

// 	}

// 	foreach ( $all_prod_components as $id ) {

// 		$id = str_replace( 'component-', '', $id );
// 		$def = preg_match( '/\*$/', $id );

// 		if ( $def ) {
// 			$id = substr( $id, 0, -1 );
// 			$price = max( $selected_components[$id]['meta']['price'][0], $selected_components[$id]['meta']['cost'][0] );
// 			$def_price = 
// 			$price_diff = 
// 		}


// 	}

// }
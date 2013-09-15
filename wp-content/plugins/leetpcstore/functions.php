<?php

// Create cart class

function get_products( $args = array() ) {

	$d = array(
		'post_type' => 'product',
		'sort_order' => 'DESC',
		'sort_column' => 'post_title',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_status' => 'publish'
	);

	$posts = get_pages( array_merge( $d, $args ) );

	$products = array();

	foreach ( $posts as $p ) {
		$products[] = get_product( $p->ID );
	}

	return $products;

}

// function &create_invoice( $params ) {
// 	return $GLOBALS['leetpc']->createInvoice( $params );
// }

function &get_product( $product_id ) {
	return $GLOBALS['leetpc']->getProduct( $product_id );
}

function get_cart() {
	init_cart();
	return $_SESSION['shopping_cart'];
}

function get_cart_total() {
	init_cart();
	return $_SESSION['shopping_cart']['sub_total'];
}

function get_cart_key( $product_id, $component_ids ) {
	sort( $component_ids );
	return md5( $product_id . str_replace( ',', '', implode( ',', $component_ids ) ) );
}

function add_product_to_cart( $product_id, $component_ids = array(), $qty = 1 ) {

	init_cart();

	$key = get_cart_key( $product_id, $component_ids );

	if ( !array_key_exists( $key, $_SESSION['shopping_cart']['items'] ) ) {

		$price = calc_product_price( $product_id, $component_ids );

		$_SESSION['shopping_cart']['items'][$key] = array(
			'product_id'       => $product_id,
			'component_ids'    => $component_ids,
			'qty'              => $qty,
			'price'            => $price
		);

	}

	return $line_item;

}

function calc_product_price( $product_id, $component_ids ) {
	$product = get_product( $product_id );
	return $product->calcPrice( $component_ids );
}

function empty_cart() {
	init_cart( true );
	return true;
}

function init_cart( $empty_cart = false ) {

	if ( !array_key_exists( 'shopping_cart', $_SESSION ) || $empty_cart ) {

		$_SESSION['shopping_cart'] = array(
			'sub_total'     => 0.00,
			'created'       => time(),
			'items'         => array(),
			'items_count'   => 0
		);

		return true;

	}

	if ( count( $_SESSION['shopping_cart']['items'] ) > 0 ) {
		calc_cart_totals();
	}

	// var_dump($_SESSION['shopping_cart']);

	return true;

}

function calc_cart_totals() {

	$_SESSION['shopping_cart']['sub_total'] = 0.00;
	$_SESSION['shopping_cart']['items_count'] = 0;

	foreach ( $_SESSION['shopping_cart']['items'] as $k => $item ) {

		$_SESSION['shopping_cart']['items'][$k]['price'] = calc_product_price( $item['product_id'], $item['component_ids'] );
		$_SESSION['shopping_cart']['sub_total'] += calc_product_price( $item['product_id'], $item['component_ids'] ) * $item['qty'];
		$_SESSION['shopping_cart']['items_count'] += $item['qty'];

	}

}

function remove_line_item( $line_item_key ) {

	init_cart();

	if ( array_key_exists( $line_item_key, $_SESSION['shopping_cart']['items'] ) ) {
		unset( $_SESSION['shopping_cart']['items'][$line_item_key] );
	}

	return true;

}

// function update_line_item_qty( $line_item_key, $qty ) {

// 	$cart = get_cart();

// 	if ( $qty < 1 ) {
// 		return remove_line_item( $line_item_key );
// 	}

// 	$_SESSION['shopping_cart']['items_count'] = $_SESSION['shopping_cart']['items_count']

// 	$_SESSION['shopping_cart']['items'][$line_item_key]['qty'] = $qty;
// 	return true;

// }

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
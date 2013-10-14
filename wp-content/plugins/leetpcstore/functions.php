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

function &get_product( $product_id ) {
	return $GLOBALS['leetpc']->getProduct( $product_id );
}

function &get_invoice( $invoice_id ) {
	return $GLOBALS['leetpc']->getInvoice( $invoice_id );
}

function &get_coupon( $coupon_id ) {
	return $GLOBALS['leetpc']->getCoupon( $coupon_id );
}

function calc_product_price( $product_id, $component_ids ) {
	$product = get_product( $product_id );
	return $product->calcPrice( $component_ids );
}

/*
	Depreciated cart functions
*/
function get_cart() {
	return $GLOBALS['lpcCart']->toArray();
}

function get_cart_total() {
	return $GLOBALS['lpcCart']->getTotal();
}

function add_product_to_cart( $product_id, $component_ids = array(), $qty = 1 ) {
	return $GLOBALS['lpcCart']->addItem( $product_id, $component_ids, $qty );
}

function empty_cart() {
	return $GLOBALS['lpcCart']->emptyCart();
}

function apply_coupon( $coupon ) {
	return $GLOBALS['lpcCart']->addPromo( $coupon );
}

function clear_coupon() {
	return $GLOBALS['lpcCart']->removePromo();
}

function remove_line_item( $line_item_key ) {
	return $GLOBALS['lpcCart']->removeItem( $line_item_key );
}

function set_line_item_qty( $line_item_key, $qty ) {
	return $GLOBALS['lpcCart']->setItemQty( $line_item_key, $qty );
}
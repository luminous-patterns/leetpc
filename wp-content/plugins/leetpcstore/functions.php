<?php

function lpc_log( $type, $note = '', $meta = array() ) {

	$i = array(
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
		'post_author'    => 3,
		'post_title'     => 'LOG ENTRY',
		'post_type'      => 'log_entry',
		'post_status'    => 'publish',
		'post_content'   => $note,
	);

	$log_id = wp_insert_post( $i );

	if ( !$entry_type = get_term_by( 'slug', $type, 'log_entry_type' ) ) {
		$term = wp_insert_term( preg_replace( '/-/', ' ', $type ), 'log_entry_type', array( 'slug' => $type ) );
		$entry_type = get_term( $term['term_id'], 'log_entry_type' );
	}

	wp_set_post_terms( $log_id, array( $entry_type->term_id ), 'log_entry_type' );

	$always_log = array(
		'ip_address' => $_SERVER['REMOTE_ADDR'],
		'user_agent' => $_SERVER['HTTP_USER_AGENT'],
		'session_id' => session_id(),
		'user_id'    => get_current_user_id()
	);

	$meta = array_merge( $meta, $always_log );

	foreach ( $meta as $k => $v ) update_post_meta( $log_id, $k, $v );

	return get_log( $log_id );

}

function &get_coupon( $coupon_id ) {
	return $GLOBALS['leetpc']->getCoupon( $coupon_id );
}

function &get_order( $order_id ) {
	return $GLOBALS['leetpc']->getOrder( $order_id );
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
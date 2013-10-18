<?php

add_action( 'init',                                     'leetpcstore_order_taxonomy_init' );
add_filter( 'manage_lpc_order_posts_columns',           'leetpcstore_order_columns_filter' );
add_action( 'manage_lpc_order_posts_custom_column',     'leetpcstore_order_columns_action', 20, 2 );

function leetpcstore_order_columns_filter( $columns ) {

	$title_column = $columns['title'];

	unset( $columns['title'], $columns['date'], $columns['comments'] );

	$new_columns = array(
		'order_number'      => 'ID',
		'order_payment'     => 'Payment',
		'order_status'      => 'Status',
		'order_customer'    => 'Customer',
		'order_deliver_on'  => 'Deliver on',
		'order_items_total' => 'Items',
		'order_discount'    => 'Discount',
		'order_total'       => '$ Total'
	);

    return array_merge( $columns, $new_columns );

}

function leetpcstore_order_columns_action( $column, $post_id ) {

	$o = get_order( $post_id );

    switch ( $column ) {

    	case 'order_number':
    		edit_post_link( $post_id, '', '', $post_id );
    		break;

    	case 'order_customer':
    		echo $o->account['firstname'] . ' ' . $o->account['lastname']
    			. '<br />' . $o->account['phone']
    			. '<br />' . $o->account['street'] . ', ' . $o->account['suburb'] . ' ' . $o->account['postcode'];
    		break;

    	case 'order_payment':
    		$methods = array(
    			'cc' => 'Credit card',
    			'bank' => 'Bank deposit'
			);
			$status_colors = array(
				'success' => '#1b2',
				'pending' => '#12b',
				'' => '#12b'
			);
    		echo '<strong style="color: ' . $status_colors[$o->payment['status']] . '">' . $o->payment['status'] . '</strong><br />' . $methods[$o->payment['method']];
    		break;

    	case 'order_status':
    		echo $o->status;
    		break;

    	case 'order_deliver_on':
    		echo $o->delivery['deliver_on'];
    		break;

    	case 'order_items_total':
		    echo '$' . number_format( $o->items_total, 2 )
		    	. '<br />' . $o->items_count . ' Item' . ( $o->items_count > 1 ? 's' : '' );
    		break;

    	case 'order_discount':
    		if ( $o->discount_total > 0.01 ) {
				$promo = $promo;
				$discount = $o->promo['type'] == '%' ? number_format( $o->promo['amount'] ) . '&#37;' : '&dollar;' . number_format( $o->promo['amount'] );
    			echo '$' . number_format( $o->discount_total, 2 ) . '<br />code <em>' . $o->promo['code'] . '</em> ' . $discount;
    		}
    		else {
    			echo '$0.00';
    		}
    		break;

    	case 'order_total':
		    echo '$' . number_format( $o->total, 2 );
    		break;

    }

}

function leetpcstore_order_taxonomy_init() {

    register_post_type( 'lpc_order',
        array(
            'labels' => array(
                'menu_name' => 'Orders',
                'all_items' => 'All Orders',
                'name' => 'Orders',
                'singular_name' => 'Order',
                'add_new' => 'Add Order',
                'add_new_item' => 'Add New Order',
                'edit' => 'Edit',
                'edit_item' => 'Edit Order',
                'new_item' => 'New Order',
                'view' => 'View Order',
                'view_item' => 'View Order',
                'search_items' => 'Search Orders',
                'not_found' => 'No Orders found',
                'not_found_in_trash' => 'No Orders found in trash'
            ),
            'description' => '',
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => true,
            'supports' => array( 'title', 'comments', 'custom-fields' ),
            'has_archive' => false,
            'show_in_nav_menus' => true,
            'menu_position' => 25
        )
    );

}

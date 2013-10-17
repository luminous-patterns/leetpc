<?php

add_filter( 'manage_component_posts_columns',           'leetpcstore_component_columns_filter' );
add_action( 'manage_component_posts_custom_column',     'leetpcstore_component_columns_action', 10, 2 );

function leetpcstore_component_columns_filter( $columns ) {

	$title_column = $columns['title'];

	unset( $columns['title'], $columns['date'], $columns['comments'] );

	$new_columns = array(
		'type'        => 'Type',
		'title'       => $title_column,
		'long_name'   => 'Long name',
		'cost'        => '$ Cost',
		'price'       => '$ Price'
	);

	unset( $columns['date'], $columns['comments'] );

    return array_merge( $columns, $new_columns );

}

function leetpcstore_component_columns_action( $column, $post_id ) {

	$meta = get_post_custom( $post_id );

	$types = wp_get_post_terms( $post_id, 'component_group' );
	$type = $types[0];

    switch ( $column ) {

    	case 'type':
    		echo '<a href="edit.php?post_type=component&component_group=' . $type->slug . '">' . $type->name . '</a>';
    		break;

    	case 'long_name':
    		echo $meta['long_name'][0];
    		break;

		case 'cost' :
		    echo '$' . number_format( $meta['cost'][0], 2 );
			break;

		case 'price' :
		    echo '$' . number_format( $meta['price'][0], 2 );
		    break;

    }

}

add_filter( 'manage_product_posts_columns',           'leetpcstore_product_columns_filter' );
add_action( 'manage_product_posts_custom_column',     'leetpcstore_product_columns_action', 10, 2 );

function leetpcstore_product_columns_filter( $columns ) {

	$title_column = $columns['title'];

	unset( $columns['title'], $columns['date'], $columns['comments'] );

	$new_columns = array(
		'type'        => 'Type',
		'title'       => $title_column,
		'components'  => 'Components',
		'cost'        => '$ Cost',
		'price'       => '$ Price',
		'margin'      => '$ Margin'
	);

    return array_merge( $columns, $new_columns );

}

function leetpcstore_product_columns_action( $column, $post_id ) {

	$p = get_product( $post_id );

    switch ( $column ) {

    	case 'type':
    		echo $p->type->name;
    		break;

    	case 'components':
			$com_lines = array(); 
			foreach( $p->comDefaults as $type => $c ) { 
				if ( $type == 'case' ) continue;
				$com_lines[] = '<strong>' . strtoupper( $type ) . ':</strong> <a href="' . get_edit_post_link( $post_id, '' ) . '" title="Edit component">' . $c->post_title . '</a>';
			}
			echo implode( ' / ', $com_lines );
    		break;

		case 'cost' :
		    echo '$' . number_format( $p->getCost(), 2 );
			break;

		case 'price' :
		    echo '$' . number_format( $p->getPrice(), 2 );
		    break;

		case 'margin' :
			$margin = $p->getPrice() - $p->getCost();
		    echo '$' . number_format( $margin, 2 ) . ' (' . number_format( $margin / $p->getPrice() * 100 ) . '%)';
		    break;

    }

}

add_filter( 'manage_invoice_posts_columns',           'leetpcstore_invoice_columns_filter' );
add_action( 'manage_invoice_posts_custom_column',     'leetpcstore_invoice_columns_action', 20, 2 );

function leetpcstore_invoice_columns_filter( $columns ) {

	$title_column = $columns['title'];

	unset( $columns['title'], $columns['date'], $columns['comments'] );

	$new_columns = array(
		'invoice_number'      => 'ID',
		'invoice_payment'     => 'Payment',
		'invoice_status'      => 'Status',
		'invoice_customer'    => 'Customer',
		'invoice_deliver_on'  => 'Deliver on',
		'invoice_items_total' => 'Items',
		'invoice_discount'    => 'Discount',
		'invoice_total'       => '$ Total'
	);

    return array_merge( $columns, $new_columns );

}

function leetpcstore_invoice_columns_action( $column, $post_id ) {

	$i = get_invoice( $post_id );

	$payment = $i->getPaymentDetails();
	$delivery = $i->getDeliveryDetails();
	$account = $i->getAccountDetails();
	$cart = $i->getCart();

    switch ( $column ) {

    	case 'invoice_number':
    		edit_post_link( $post_id, '', '', $post_id );
    		break;

    	case 'invoice_customer':
    		echo $account['firstname'] . ' ' . $account['lastname']
    			. '<br />' . $account['phone']
    			. '<br />' . $account['street'] . ', ' . $account['suburb'] . ' ' . $account['postcode'];
    		break;

    	case 'invoice_payment':
    		$methods = array(
    			'cc' => 'Credit card',
    			'bank' => 'Bank deposit'
			);
			$status_colors = array(
				'success' => '#1b2',
				'pending' => '#12b'
			);
    		echo '<strong style="color: ' . $status_colors[$payment['status']] . '">' . $payment['status'] . '</strong><br />' . $methods[$payment['method']];
    		break;

    	case 'invoice_status':
    		echo $i->getStatus();
    		break;

    	case 'invoice_deliver_on':
    		echo $delivery['deliver_on'];
    		break;

    	case 'invoice_items_total':
		    echo '$' . number_format( $i->getItemsTotal(), 2 )
		    	. '<br />' . $i->cart['items_count'] . ' Item' . ( $i->cart['items_count'] > 1 ? 's' : '' );
    		break;

    	case 'invoice_discount':
    		if ( $cart['discount_total'] > 0.01 ) {
				$promo = $i->getPromo();
				$discount = $promo['type'] == '%' ? number_format( $promo['amount'] ) . '&#37;' : '&dollar;' . number_format( $promo['amount'] );
    			echo '$' . number_format( $cart['discount_total'], 2 ) . '<br />code <em>' . $cart['promo']['code'] . '</em> ' . $discount;
    		}
    		else {
    			echo '$0.00';
    		}
    		break;

    	case 'invoice_total':
		    echo '$' . number_format( $i->getTotal(), 2 );
    		break;

    }

}

add_action( 'init', 'leetpcstore_taxonomy_init' );

function leetpcstore_taxonomy_init() {

	// Product Types
	register_taxonomy( 'product_type',
		array( 'product' ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => 'Product Types',
				'singular_name' => 'Product Type',
				'search_items' =>  'Search Product Types',
				'all_items' => 'All Product Types',
				'parent_item' => 'Parent Product Type',
				'parent_item_colon' => 'Parent Product Type:',
				'edit_item' => 'Edit Type',
				'update_item' => 'Update Type',
				'add_new_item' => 'Add New Type',
				'new_item_name' => 'New Type Name'
			),
			'show_ui' => true,
			'query_var' => true,
			// 'rewrite' => false,
			'rewrite' => 'products'
		)
	);

	// Products
	register_post_type( 'product',
		array(
			'labels' => array(
				'menu_name' => 'Products',
				'all_items' => 'All Products',
				'name' => 'Products',
				'singular_name' => 'Product',
				'add_new' => 'Add Product',
				'add_new_item' => 'Add New Product',
				'edit' => 'Edit',
				'edit_item' => 'Edit Product',
				'new_item' => 'New Product',
				'view' => 'View Product',
				'view_item' => 'View Product',
				'search_items' => 'Search Products',
				'not_found' => 'No Products found',
				'not_found_in_trash' => 'No Products found in trash',
				'parent' => 'Parent Product'
			),
			'description' => '',
			'public' => true,
			'show_ui' => true,
			'capability_type' => 'post',
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'hierarchical' => true,
			// 'rewrite' => true,
			'rewrite' => array( 'slug' => 'product', 'with_front' => false ),
			'query_var' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions', 'page-attributes' ),
			// 'has_archive' => false,
			'has_archive' => 'products',
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

	// Component Groups
	register_taxonomy( 'component_group',
		array( 'component' ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => 'Component Groups',
				'singular_name' => 'Component Group',
				'search_items' =>  'Search Component Groups',
				'all_items' => 'All Component Groups',
				'parent_item' => 'Parent Component Group',
				'parent_item_colon' => 'Parent Component Group:',
				'edit_item' => 'Edit Group',
				'update_item' => 'Update Group',
				'add_new_item' => 'Add New Group',
				'new_item_name' => 'New Group Name'
			),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => false
		)
	);

	// Components
	register_post_type( 'component',
		array(
			'labels' => array(
				'menu_name' => 'Components',
				'all_items' => 'All Components',
				'name' => 'Components',
				'singular_name' => 'Component',
				'add_new' => 'Add Component',
				'add_new_item' => 'Add New Component',
				'edit' => 'Edit',
				'edit_item' => 'Edit Component',
				'new_item' => 'New Component',
				'view' => 'View Component',
				'view_item' => 'View Component',
				'search_items' => 'Search Components',
				'not_found' => 'No Components found',
				'not_found_in_trash' => 'No Components found in trash'
			),
			'description' => '',
			'public' => true,
			'show_ui' => true,
			'capability_type' => 'post',
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'hierarchical' => false,
			'rewrite' => false,
			'query_var' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions', 'page-attributes' ),
			'has_archive' => false,
			//'has_archive' => 'component',
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

	// Carts
	// register_post_type( 'cart',
	// 	array(
	// 		'labels' => array(
	// 			'menu_name' => 'Carts',
	// 			'all_items' => 'All Carts',
	// 			'name' => 'Carts',
	// 			'singular_name' => 'Cart',
	// 			'add_new' => 'Add Cart',
	// 			'add_new_item' => 'Add New Cart',
	// 			'edit' => 'Edit',
	// 			'edit_item' => 'Edit Cart',
	// 			'new_item' => 'New Cart',
	// 			'view' => 'View Cart',
	// 			'view_item' => 'View Cart',
	// 			'search_items' => 'Search Carts',
	// 			'not_found' => 'No Carts found',
	// 			'not_found_in_trash' => 'No Carts found in trash'
	// 		),
	// 		'description' => '',
	// 		'public' => true,
	// 		'show_ui' => true,
	// 		'capability_type' => 'post',
	// 		'publicly_queryable' => true,
	// 		'exclude_from_search' => false,
	// 		'hierarchical' => false,
	// 		'rewrite' => false,
	// 		'query_var' => true,
	// 		'supports' => array( 'title', 'editor', 'comments', 'revisions', 'page-attributes' ),
	// 		'has_archive' => false,
	// 		//'has_archive' => 'cart',
	// 		'show_in_nav_menus' => true,
	// 		'menu_position' => 25
	// 	)
	// );

	// Line items
	// register_post_type( 'lineitem',
	// 	array(
	// 		'labels' => array(
	// 			'menu_name' => 'Line items',
	// 			'all_items' => 'All Line items',
	// 			'name' => 'Line items',
	// 			'singular_name' => 'Line item',
	// 			'add_new' => 'Add Line item',
	// 			'add_new_item' => 'Add New Line item',
	// 			'edit' => 'Edit',
	// 			'edit_item' => 'Edit Line item',
	// 			'new_item' => 'New Line item',
	// 			'view' => 'View Line item',
	// 			'view_item' => 'View Line item',
	// 			'search_items' => 'Search Line items',
	// 			'not_found' => 'No Line items found',
	// 			'not_found_in_trash' => 'No Line items found in trash',
	// 			'parent' => 'Parent Line item'
	// 		),
	// 		'description' => '',
	// 		'public' => true,
	// 		'show_ui' => true,
	// 		'capability_type' => 'post',
	// 		'publicly_queryable' => true,
	// 		'exclude_from_search' => false,
	// 		'hierarchical' => true,
	// 		'rewrite' => false,
	// 		'query_var' => true,
	// 		'supports' => array( 'title', 'editor', 'comments' ),
	// 		'has_archive' => false,
	// 		'show_in_nav_menus' => true,
	// 		'menu_position' => 25
	// 	)
	// );

	// Invoices
	register_post_type( 'invoice',
		array(
			'labels' => array(
				'menu_name' => 'Invoices',
				'all_items' => 'All Invoices',
				'name' => 'Invoices',
				'singular_name' => 'Invoice',
				'add_new' => 'Add Invoice',
				'add_new_item' => 'Add New Invoice',
				'edit' => 'Edit',
				'edit_item' => 'Edit Invoice',
				'new_item' => 'New Invoice',
				'view' => 'View Invoice',
				'view_item' => 'View Invoice',
				'search_items' => 'Search Invoices',
				'not_found' => 'No Invoices found',
				'not_found_in_trash' => 'No Invoices found in trash'
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
			'supports' => array( 'title', 'editor', 'comments', 'revisions' ),
			'has_archive' => false,
			//'has_archive' => 'invoice',
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

	// Orders
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
			'supports' => array( 'title', 'comments' ),
			'has_archive' => false,
			//'has_archive' => 'invoice',
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

	// Log Entries
	register_post_type( 'log_entry',
		array(
			'labels' => array(
				'menu_name' => 'Logs',
				'all_items' => 'All Log Entries',
				'name' => 'Entries',
				'singular_name' => 'Log Entry',
				'add_new' => 'Add Entry',
				'add_new_item' => 'Add New Entry',
				'edit' => 'Edit',
				'edit_item' => 'Edit Entry',
				'new_item' => 'New Entry',
				'view' => 'View Entry',
				'view_item' => 'View Entry',
				'search_items' => 'Search Logs',
				'not_found' => 'No Entries found',
				'not_found_in_trash' => 'No Entries found in trash'
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
			'supports' => array( 'title', 'editor', 'comments' ),
			'has_archive' => false,
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);
	register_taxonomy( 'log_entry_type',
		array( 'log_entry' ),
		array(
			'hierarchical' => true,
			'labels' => array(
				'name' => 'Log Entry Types',
				'singular_name' => 'Log Entry Type',
				'search_items' =>  'Search Log Entry Types',
				'all_items' => 'All Log Entry Types',
				'parent_item' => 'Parent Entry Type',
				'parent_item_colon' => 'Parent Type:',
				'edit_item' => 'Edit Type',
				'update_item' => 'Update Type',
				'add_new_item' => 'Add New Type',
				'new_item_name' => 'New Type Name'
			),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => false
		)
	);

	// Coupons
	register_post_type( 'coupon',
		array(
			'labels' => array(
				'menu_name' => 'Coupons',
				'all_items' => 'All Coupons',
				'name' => 'Coupons',
				'singular_name' => 'Coupon',
				'add_new' => 'Add Coupon',
				'add_new_item' => 'Add New Coupon',
				'edit' => 'Edit',
				'edit_item' => 'Edit Coupon',
				'new_item' => 'New Coupon',
				'view' => 'View Coupon',
				'view_item' => 'View Coupon',
				'search_items' => 'Search Coupons',
				'not_found' => 'No Coupons found',
				'not_found_in_trash' => 'No Coupons found in trash'
			),
			'description' => '',
			'public' => false,
			'show_ui' => true,
			'capability_type' => 'post',
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'hierarchical' => false,
			'rewrite' => false,
			'query_var' => true,
			'supports' => array( 'title', 'editor' ),
			'has_archive' => false,
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

	register_post_status( 'waiting', array(
		'label'          => 'Waiting',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'    => _n_noop( 'Waiting <span class="count">(%s)</span>', 'Waiting <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'processing', array(
		'label'          => 'Processing',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'    => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'building', array(
		'label'          => 'Building',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'    => _n_noop( 'Building <span class="count">(%s)</span>', 'Building <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'in-transit', array(
		'label'          => 'In Transit',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'    => _n_noop( 'In Transit <span class="count">(%s)</span>', 'In Transit <span class="count">(%s)</span>' ),
	) );

	// Add service order post type

	// Add 

}

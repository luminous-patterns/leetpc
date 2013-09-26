<?php

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
			'rewrite' => false,
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
			'has_archive' => false,
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
			'rewrite' => false,
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
	register_post_type( 'cart',
		array(
			'labels' => array(
				'menu_name' => 'Carts',
				'all_items' => 'All Carts',
				'name' => 'Carts',
				'singular_name' => 'Cart',
				'add_new' => 'Add Cart',
				'add_new_item' => 'Add New Cart',
				'edit' => 'Edit',
				'edit_item' => 'Edit Cart',
				'new_item' => 'New Cart',
				'view' => 'View Cart',
				'view_item' => 'View Cart',
				'search_items' => 'Search Carts',
				'not_found' => 'No Carts found',
				'not_found_in_trash' => 'No Carts found in trash'
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
			'supports' => array( 'title', 'editor', 'comments', 'revisions', 'page-attributes' ),
			'has_archive' => false,
			//'has_archive' => 'cart',
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

	// Line items
	register_post_type( 'lineitem',
		array(
			'labels' => array(
				'menu_name' => 'Line items',
				'all_items' => 'All Line items',
				'name' => 'Line items',
				'singular_name' => 'Line item',
				'add_new' => 'Add Line item',
				'add_new_item' => 'Add New Line item',
				'edit' => 'Edit',
				'edit_item' => 'Edit Line item',
				'new_item' => 'New Line item',
				'view' => 'View Line item',
				'view_item' => 'View Line item',
				'search_items' => 'Search Line items',
				'not_found' => 'No Line items found',
				'not_found_in_trash' => 'No Line items found in trash',
				'parent' => 'Parent Line item'
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
			'supports' => array( 'title', 'editor', 'comments', 'revisions', 'page-attributes' ),
			'has_archive' => false,
			//'has_archive' => 'invoice',
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

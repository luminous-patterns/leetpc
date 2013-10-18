<?php

add_action( 'init',                                     'leetpcstore_invoice_taxonomy_init' );

function leetpcstore_invoice_taxonomy_init() {

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
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

}

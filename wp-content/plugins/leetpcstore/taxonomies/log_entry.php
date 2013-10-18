<?php

add_action( 'init',                                     'leetpcstore_log_entry_taxonomy_init' );

function leetpcstore_log_entry_taxonomy_init() {

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
			'supports' => array( 'title', 'editor', 'comments', 'custom-fields' ),
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

}

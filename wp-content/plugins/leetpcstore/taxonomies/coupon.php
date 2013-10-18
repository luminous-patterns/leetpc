<?php

add_action( 'init',                                     'leetpcstore_coupon_taxonomy_init' );

function leetpcstore_coupon_taxonomy_init() {

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

}

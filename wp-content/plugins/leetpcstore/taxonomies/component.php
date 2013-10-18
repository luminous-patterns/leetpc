<?php

add_action( 'init',                                     'leetpcstore_component_taxonomy_init' );
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

function leetpcstore_component_taxonomy_init() {

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
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

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

}

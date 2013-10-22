<?php

add_action( 'init',                                     'leetpcstore_log_entry_taxonomy_init' );
add_filter( 'manage_log_entry_posts_columns',           'leetpcstore_log_entry_columns_filter' );
add_action( 'manage_log_entry_posts_custom_column',     'leetpcstore_log_entry_columns_action', 20, 2 );
add_filter( 'pre_get_posts',                            'log_entry_get_posts' );

function log_entry_get_posts( $query ) {
	if ( get_post_type( get_the_ID() ) == 'log_entry' ) { 
		$query->set( 'orderby', 'ID' );
		$query->set( 'order', 'DESC' );
	}
}

function &get_log( $log_entry_id ) {
	return $GLOBALS['leetpc']->getLogEntry( $log_entry_id );
}

function get_logs( $args = array() ) {

	$d = array(
		'posts_per_page'   => -1,
		'orderby'          => 'ID',
		'order'            => 'DESC',
		'post_type'        => 'log_entry',
		'post_status'      => 'publish',
		'suppress_filters' => true
	);

	$posts = get_posts( array_merge( $d, $args ) );

	$log_entries = array();

	foreach ( $posts as $p ) {
		$log_entries[] = get_log( $p->ID );
	}

	return $log_entries;

}

function leetpcstore_log_entry_columns_filter( $columns ) {

	unset( $columns['title'], $columns['date'], $columns['comments'] );

	$new_columns = array(
		'log_entry_id'             => 'ID',
		'log_entry_date'           => 'Date',
		'log_entry_ip'             => 'IP address',
		'log_entry_session_id'     => 'Session ID',
		'log_entry_type'           => 'Entry type',
		'log_entry_meta'           => 'Meta data'
	);

    return array_merge( $columns, $new_columns );

}

function leetpcstore_log_entry_columns_action( $column, $post_id ) {

	$l = get_log( $post_id );

    switch ( $column ) {

    	case 'log_entry_id':
    		echo '<a href="' . get_edit_post_link( $post_id ) . '">' . $post_id . '</a>';
    		break;

    	case 'log_entry_session_id':
    		echo '<a href="edit.php?post_type=log_entry&meta_key=session_id&meta_value=' . $l->get( 'session_id' ) . '">' . $l->get( 'session_id' ) . '</a>';
    		break;

    	case 'log_entry_date':
    		echo $l->getDate( 'Y-m-d H:i:s' );
    		break;

    	case 'log_entry_ip':
    		$rgba_vals = explode( '.', $l->get( 'ip_address' ) );
    		array_pop( $rgba_vals );
    		echo '<span style="background-color: rgba(' . implode( ',', $rgba_vals  ) . ',0.3);">' . ( $l->get( 'ip_address' ) ? $l->get( 'ip_address' ) : '-' ) . '</span>';
    		break;

    	case 'log_entry_type':
    		echo '<a href="edit.php?post_type=log_entry&log_entry_type=' . $l->getTypeID() . '">' . $l->getTypeName() . '</a>';
    		break;

    	case 'log_entry_meta':
    		$ignore_fields = array( 'ip_address', 'session_id', 'extra', 'data', '_edit_lock', 'component_ids', 'user_agent', 'request_data' );
    		$field_rows = array();
    		foreach ( $l->extra as $k => $v ) {
    			if ( in_array( $k, $ignore_fields ) ) continue;
    			$field_rows[] = "<strong>$k:</strong> $v";
    		}
    		echo implode( '<br />', $field_rows );
    		break;

    }

}

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

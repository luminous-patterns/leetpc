<?php

add_action( 'init',                                     'leetpcstore_product_taxonomy_init' );
add_filter( 'manage_product_posts_columns',             'leetpcstore_product_columns_filter' );
add_action( 'manage_product_posts_custom_column',       'leetpcstore_product_columns_action', 10, 2 );

function &get_product( $product_id ) {
	return $GLOBALS['leetpc']->getProduct( $product_id );
}

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

function leetpcstore_product_taxonomy_init() {

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
			'rewrite' => array( 'slug' => 'product', 'with_front' => false ),
			'query_var' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions', 'page-attributes' ),
			'has_archive' => 'products',
			'show_in_nav_menus' => true,
			'menu_position' => 25
		)
	);

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
			'rewrite' => 'products'
		)
	);

}
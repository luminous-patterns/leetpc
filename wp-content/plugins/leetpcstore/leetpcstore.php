<?php
/*
Plugin Name: LEETPC Store
Plugin URI: http://www.leetpc.com.au
Description: LEETPC Store functionality for WordPress
Author: Cal Milne
Version: 1.0.0
Author URI: http://www.leetpc.com.au
Copyright 2013 Cal Milne
*/

session_start();

// Are we using making real payments here?
define( 'LIVE_PAYMENTS', false );

// Book keeping time zone
define( 'BOOK_KEEPING_TZ', 'Australia/Melbourne' );

/**
 * Include our core functions
 **/
require_once( 'functions.php' );
require_once( 'taxonomies.php' );
require_once( 'metaboxes.php' );

require_once( 'cart.class.php' );
// require_once( 'order.class.php' );
require_once( 'coupon.class.php' );
require_once( 'product.class.php' );
require_once( 'invoice.class.php' );

/**
 * Include the main library
 **/
require_once( 'leetpcstore.class.php' );

/**
 * Get started!
 **/
new leetPcStore();
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

/**
 * Include our core functions
 **/
require_once( 'functions.php' );
require_once( 'taxonomies.php' );
require_once( 'metaboxes.php' );

/**
 * Include the main library
 **/
require_once( 'leetpcstore.class.php' );

/**
 * Get started!
 **/
new leetPcStore();
<?php
/*
Plugin Name: LEETPC Store
Plugin URI: http://www.leetpc.com.au
Description: LEETPC Store functionality for WordPress
Author: Cal Milne
Version: 1.0.0
Author URI: http://www.leetpc.com.au
Copyright 2012 Integrated Web Services
*/

/**
 * Functions, taxonomies, rewrites, metaboxes, etc.
 **/

require_once( 'registry.php' );
require_once( 'functions.php' );
require_once( 'taxonomies.php' );
require_once( 'rewrites.php' );
require_once( 'metaboxes.php' );

/**
 * Main class
 **/

require_once( 'leetpcstore.class.php' );

/**
 * Initialize
 **/

$leetPcStore = new leetPcStore();

$leetPcStore->init();

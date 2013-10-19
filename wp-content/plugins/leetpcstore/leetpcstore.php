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

/* Live payments enabled */
define( 'LIVE_PAYMENTS',               false );

/* Book keeping time zone */
define( 'BOOK_KEEPING_TZ',             'Australia/Melbourne' );

/* Common date formats */
define( 'LPC_INVOICE_DATES',           'jS \o\f F Y' );
define( 'LPC_PRETTY_DATES',            'D jS \o\f F Y' );
define( 'LPC_PRETTY_DATETIMES',        'D jS \o\f F Y \@ H:i:s' );
define( 'LPC_LOGENTRY_DATETIMES',      DateTime::ATOM );

/* Core functions */
require_once( 'functions.php' );

/* Custom post types and taxonomies */
require_once( 'taxonomies.php' );

/* Edit post screen metaboxes */
require_once( 'metaboxes.php' );

/* Admin dashboard widgets */
require_once( 'dashboard.widgets.php' );

/* LEETPC object classes */
require_once( 'classes.php' );

/* LEETPC plugin main router/controller  */
require_once( 'leetpcstore.class.php' );

/* Get started! */
new leetPcStore();
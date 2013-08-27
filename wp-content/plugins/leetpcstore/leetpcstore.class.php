<?php
/**
 * LEETPC Store Main Class
 * 
 * @copyright Copyright (c) Cal Milne
 * @author Cal Milne <cal@leetpc.com.au>
 **/
class leetPcStore {

	/**
	 * Plugin name
	 * @var string
	 */
	private $_name = 'leetpcstore';

	/**
	 * Source version
	 * @var string
	 */
	private $_version = '1.0.0';

	/**
	 * JavaScript revision
	 * @var string
	 */
	private $_js_revision = '0';

	/**
	 * JavaScript dependencies
	 * @var array
	 */
	private $_js_dependencies = array( 'jquery' );

	/**
	 * JavaScript revision (admin)
	 * @var string
	 */
	private $_admin_js_revision = '0';

	/**
	 * JavaScript dependencies (admin)
	 * @var array
	 */
	private $_admin_js_dependencies = array( 'jquery' );

	/**
	 * Options
	 * @var array
	 */
	private $_options = array();

	/**
	 * Plugin url
	 * @var string
	 */
	private $_url;

	/**
	 * Plugin dir
	 * @var string
	 */
	private $_dir;

	/**
	 * Plugin path
	 * @var string
	 */
	private $_path;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->_dir                     = WP_PLUGIN_DIR . '/' . $this->_name;
		$this->_path                    = $this->_dir . '/' . $this->_name . '.php';
		$this->_url                     = plugin_dir_url( __FILE__ );
		$this->_js_revision             = $this->_version . '.' . $this->_js_revision;
		$this->_admin_js_revision       = $this->_version . '.' . $this->_admin_js_revision;

		register_activation_hook( $this->_path,                    array( &$this, 'install' ) );
		register_deactivation_hook( $this->_path,                  array( &$this, 'uninstall' ) );

		$this->_actionsAndFilters();

	}

	/**
	 * Add actions to WordPress
	 * @since 1.0.0
	 * @return void
	 */
	private function _actionsAndFilters() {

		add_action( 'init',                                        array( &$this, 'init' ) );
		add_action( 'admin_init',                                  array( &$this, 'initAdmin' ) );

		/*

		add_action( 'init',                                        array( &$this, 'init' ) );
		add_action( 'admin_init',                                  array( &$this, 'initAdmin' ) );
		
		add_action( 'wp_ajax_METHOD_NAME',                         array( &$this, 'METHOD' ) );
		add_action( 'wp_ajax_nopriv_METHOD_NAME',                  array( &$this, 'METHOD_NOPRIV' ) );

		*/

	}

	/**
	 * Plugin install (activation)
	 * @since 1.0.1
	 * @return void
	 */
	public function install() {

	}

	/**
	 * Plugin un-install (deactivation)
	 * @since 1.0.1
	 * @return void
	 */
	public function uninstall() {
		
	}

	/**
	 * Init
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {

		wp_register_style( $this->_name . '-css', $this->_url . 'css/style.css' );
		wp_register_script( $this->_name . '-js', $this->_url . 'js/functions.js', $this->_js_dependencies, $this->_js_revision );
		
		wp_enqueue_style( $this->_name . '-css' );
		wp_enqueue_script( $this->_name . '-js' );

	}

	/**
	 * Init (admin)
	 * @since 1.0.0
	 * @return void
	 */
	public function initAdmin() {

		wp_register_style( $this->_name . '-css', $this->_url . 'css/admin.css' );
		wp_register_script( $this->_name . '-js', $this->_url . 'js/admin.js', $this->_admin_js_dependencies, $this->_admin_js_revision );
		
		wp_enqueue_style( $this->_name . '-css' );
		wp_enqueue_script( $this->_name . '-js' );

	}

}

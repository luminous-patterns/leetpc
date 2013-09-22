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

	private $_productsCache = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		$GLOBALS['leetpc'] = &$this;

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

		add_action( 'wp_ajax_add_to_cart',                         array( &$this, 'addProductToCart' ) );
		add_action( 'wp_ajax_nopriv_add_to_cart',                  array( &$this, 'addProductToCart' ) );

		add_action( 'wp_ajax_remove_from_cart',                    array( &$this, 'removeProductFromCart' ) );
		add_action( 'wp_ajax_nopriv_remove_from_cart',             array( &$this, 'removeProductFromCart' ) );

		add_action( 'wp_ajax_empty_cart',                          array( &$this, 'emptyCart' ) );
		add_action( 'wp_ajax_nopriv_empty_cart',                   array( &$this, 'emptyCart' ) );

		add_action( 'wp_ajax_get_customize_form',                  array( &$this, 'getCustomizeForm' ) );
		add_action( 'wp_ajax_nopriv_get_customize_form',           array( &$this, 'getCustomizeForm' ) );

		add_action( 'wp_ajax_get_checkout_step',                   array( &$this, 'getCheckoutStep' ) );
		add_action( 'wp_ajax_nopriv_get_checkout_step',            array( &$this, 'getCheckoutStep' ) );

		add_action( 'after_setup_theme',                           array( &$this, 'afterSetupTheme' ) );

		/*

		add_action( 'init',                                        array( &$this, 'init' ) );
		add_action( 'admin_init',                                  array( &$this, 'initAdmin' ) );
		
		add_action( 'wp_ajax_METHOD_NAME',                         array( &$this, 'METHOD' ) );
		add_action( 'wp_ajax_nopriv_METHOD_NAME',                  array( &$this, 'METHOD_NOPRIV' ) );

		*/

	}

	private function echoJsonExit( $x ) {
		header( 'Content-type: application/json' );
		echo json_encode( $x );
		exit;
	}

	public function afterSetupTheme() {

	}

	// public function createInvoice( $params ) {

	// 	// create invoice
	// 	$post_id = wp_insert_post( array(
	// 		'post_author'    => [ <user ID> ] //The user ID number of the author.
	// 		'post_content'   => [ <the text of the post> ] //The full text of the post.
	// 		'post_name'      => [ <the name> ] // The name (slug) for your post
	// 		'post_status'    => 'publish' //Set the status of the new post.
	// 		'post_title'     => [ <the title> ] //The title of your post.
	// 		'post_type'      => 'invoice',
	// 	) );

	// 	// add line items

	// 	// return invoice id

	// }

	public function &getProduct( $id ) {
		if ( !array_key_exists( $id, $this->_productsCache ) ) {
			$this->_productsCache[$id] = new lpcProduct( $id );
		}
		return $this->_productsCache[$id];
	}

	public function getCustomizeForm() {
		customize_product_form( $_POST['product_id'] );
		exit;
	}

	public function processCheckoutData( $reload = false ) {

	    $data = $reload ? array() : $_SESSION['checkout_data'];

	    foreach ( $_POST['submitted'] as $k => $v ) {

	        $c = explode( '-', $k );
	        $x = count( $c );

	        switch ( $x ) {

	            case 3:
	                $data[$c[0]][$c[1]][$c[2]] = $v;
	                break;

	            case 2:
	                $data[$c[0]][$c[1]] = $v;
	                break;

	            case 1:
	                $data[$c[0]] = $v;
	                break;

	        }

	    }

	    $_SESSION['checkout_data'] = $data;

	}

	public function checkoutStep( $step, $data ) {
	    include( get_template_directory() . DIRECTORY_SEPARATOR . 'checkout.php' );
	}

	public function getCheckoutStep() {

		$error = array();
		$step = $_POST['step'];

		switch ( $step ) {

			case '1':

				break;

			case '2':

				$login = $_POST['submitted']['acct-registered'];
				$user = get_user_by( 'email', $_POST['submitted']['acct-email'] );
				$valid_password = !$login ? false : wp_check_password( $_POST['submitted']['acct-password'], $user->data->user_pass, $user->ID );

				if ( $login ) {

					if ( $user ) {

						if ( !$valid_password ) {
							// Wrong password
							$error['message'] = 'Invalid username or password';
							$error['fields'] = 'acct-email,acct-password';
							break;
						}

						// Valid user, save ID
						$_SESSION['checkout_data']['user_id'] = $user->ID;
						break;

					}

					// No such user
					$error['message'] = 'User does not exist';
					$error['fields'] = 'acct-email';
					break;

				}

				if ( $user ) {

					// User exists but no login was sent
					$error['message'] = 'You have an account with us already';

				}

				break;

		}

	    $this->processCheckoutData( $step < 2 );

		$this->checkoutStep( count( $error ) > 0 ? $step - 1 : $step, array( 'error' => $error ) );

		exit;

	}

	public function emptyCart() {
		empty_cart();
		$this->echoJsonExit( array( 'code' => 200, 'cart' => get_cart() ) );
	}

	public function removeProductFromCart() {
		remove_line_item( $_POST['line_item_id'] );
		$this->echoJsonExit( array( 'code' => 200, 'cart' => get_cart() ) );
	}

	public function addProductToCart() {

		if ( !array_key_exists( 'product_id', $_POST ) ) {
			$this->echoJsonExit( array( 'code' => 400, 'message' => 'Product ID not specified' ) );
		}

		$product_id = $_POST['product_id'];
		$component_ids = array_key_exists( 'component_ids', $_POST ) ? explode( ',', $_POST['component_ids'] ) : array();

		add_product_to_cart( $product_id, $component_ids );

		$this->echoJsonExit( array( 'code' => 200, 'cart' => get_cart() ) );

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

		wp_register_style( $this->_name . '-admin-css', $this->_url . 'css/admin.css' );
		wp_register_script( $this->_name . '-admin-js', $this->_url . 'js/admin.js', $this->_admin_js_dependencies, $this->_admin_js_revision );
		
		wp_enqueue_style( $this->_name . '-admin-css' );
		wp_enqueue_script( $this->_name . '-admin-js' );

	}

}

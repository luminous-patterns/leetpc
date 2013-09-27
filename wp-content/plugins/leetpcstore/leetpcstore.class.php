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
	private $_invoicesCache = array();

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

	public function &getInvoice( $id ) {
		if ( !array_key_exists( $id, $this->_invoicesCache ) ) {
			$this->_invoicesCache[$id] = new lpcInvoice( $id );
		}
		return $this->_invoicesCache[$id];
	}

	public function getCustomizeForm() {
		customize_product_form( $_POST['product_id'] );
		exit;
	}

	public function processCheckoutData( $reload = false ) {

	    $data = $reload ? array( 'cart' => get_cart() ) : $_SESSION['checkout_data'];

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

	public function showCheckoutStep( $step, $data ) {
	    include( get_template_directory() . DIRECTORY_SEPARATOR . 'checkout.php' );
	}

	public function getCheckoutStep() {

		$p = false;
		$e = array();
		$s = $_POST['step'] - 1;

		$password = $_POST['submitted']['user-password'] ? $_POST['submitted']['user-password'] : '';
		$_POST['submitted']['user-password'] = '******';

	    $this->processCheckoutData( $s < 1 );

		switch ( $s ) {

			case 1: // Process account info

				$login = $_POST['submitted']['user-registered'];
				$user = get_user_by( 'email', $_POST['submitted']['user-email'] );
				$valid_password = !$login ? false : wp_check_password( $password, $user->data->user_pass, $user->ID );

				if ( $login ) {

					if ( $user ) {

						if ( !$valid_password ) {
							// Wrong password
							$e['message'] = 'Invalid username or password';
							$e['fields'] = 'user-email,user-password';
							break;
						}

						// Valid user, save ID
						$_SESSION['checkout_data']['user_id'] = $user->ID;
						break;

					}

					// No such user
					$e['message'] = 'User does not exist';
					$e['fields'] = 'user-email';
					break;

				}

				if ( $user ) {

					// User exists but no login was sent
					$e['message'] = 'You have an account with us already';

				}

				break;

			case 2: // Process billing info

				break;

			case 3: // Process shipping info

				break;

			case 4: // Process credit card info and create order

				if ( $_SESSION['checkout_data']['cc']['number'] == '51631000000000' ) {
					// test transaction
				}

				$p = true;

				break;

		}

		if ( $p ) {
			
			$e = array();

			foreach ( $_SESSION['checkout_data']['cart']['items'] as $lid => $l ) {

				$x = array(
					'qty'              => $l['qty'],
					'product_id'       => $l['product_id'],
					'product_title'    => get_the_title( $l['product_id'] ),
					'single_price'     => $l['price'],
					'total_price'      => $l['price'] * $l['qty'],
					'components'       => array()
				);

				foreach ( $l['component_ids'] as $cid ) {

					$def = preg_match( '/\*$/', $cid );
					$cid = $def ? substr( $cid, 10, -1 ) : substr( $cid, 10 );

					$c = get_post( $cid );

					$terms = get_the_terms( $c->ID, 'component_group' );
					$terms_keys = array_keys( $terms );
					list( $type, $sub_type ) = explode( '-', $terms[$terms_keys[0]]->slug );

					$components[$type][] = $c;

					if ( $def ) {
						$defaults[$type] = $c;
						$default_ids[] = 'component-' . $c->ID;
					}

					$attrs = get_post_custom( $c->ID );

					$x['components'][] = array(
						'id'       => $c->ID,
						'title'    => $c->post_title,
						'type'     => $type,
						'price'    => $attrs['price'][0],
						'model'    => $attrs['model_number'][0]
					);

				}

				$e[] = $x;

			}

			$_SESSION['checkout_data']['line_items'] = $e;

			$invoice_id = $this->createInvoice( $_SESSION['checkout_data'] );

			if ( $invoice_id ) {
				empty_cart();
			}

		}

		$this->showCheckoutStep( count( $e ) > 0 ? $s : $s + 1, array( 'error' => $e ) );

		exit;

	}

	private function createInvoice( $data ) {

		$i = array(
			'comment_status' => 'open',
			'ping_status'    => 'closed',
			'post_author'    => 2,
			'post_status'    => 'waiting',
			'post_title'     => 'NEW INVOICE',
			'post_type'      => 'invoice'
			// 'tags_input'     => [ '<tag>, <tag>, <...>' ] //For tags.
			// 'to_ping'        => [ ? ] //?
			// 'tax_input'      => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
		);

		$id = wp_insert_post( $i );

		foreach ( $data as $k => $v ) {
			add_post_meta( $id, "_{$k}", is_array( $v ) ? json_encode( $v ) : $v, true );
		}

		$u = array(
			'ID'         => $id,
			'post_title' => "INVOICE ID#{$id}",
			'post_name'  => "invoice-{$id}"
		);

		wp_update_post( $u );

		return $id; 

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

<?php
require_once( 'pin.php' );
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
	 * Object cache
	 * @var string
	 */
	private $_couponsCache = array();
	private $_productsCache = array();
	private $_ordersCache = array();
	private $_logEntriesCache = array();

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

		// Initialise cart
		$GLOBALS['lpcCart'] = new lpcCart();
		$this->cart = &$GLOBALS['lpcCart'];

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

		add_action( 'wp_ajax_update_line_item_qty',                array( &$this, 'updateCartItemQty' ) );
		add_action( 'wp_ajax_nopriv_update_line_item_qty',         array( &$this, 'updateCartItemQty' ) );

		add_action( 'wp_ajax_remove_from_cart',                    array( &$this, 'removeProductFromCart' ) );
		add_action( 'wp_ajax_nopriv_remove_from_cart',             array( &$this, 'removeProductFromCart' ) );

		add_action( 'wp_ajax_empty_cart',                          array( &$this, 'emptyCart' ) );
		add_action( 'wp_ajax_nopriv_empty_cart',                   array( &$this, 'emptyCart' ) );

		add_action( 'wp_ajax_get_customize_form',                  array( &$this, 'getCustomizeForm' ) );
		add_action( 'wp_ajax_nopriv_get_customize_form',           array( &$this, 'getCustomizeForm' ) );

		add_action( 'wp_ajax_get_checkout_step',                   array( &$this, 'getCheckoutStep' ) );
		add_action( 'wp_ajax_nopriv_get_checkout_step',            array( &$this, 'getCheckoutStep' ) );

		add_action( 'wp_ajax_apply_coupon_code',                   array( &$this, 'applyCouponCode' ) );
		add_action( 'wp_ajax_nopriv_apply_coupon_code',            array( &$this, 'applyCouponCode' ) );

		add_action( 'after_setup_theme',                           array( &$this, 'afterSetupTheme' ) );

	}

	public function afterSetupTheme() {

	}

	public function applyCouponCode() {

		$code = strtoupper( $_POST['coupon_code'] );

		$d = array(
			'post_type' => 'coupon',
			'meta_key' => 'code',
			'meta_value' => $code
		);

		$c = get_posts( $d );

		if ( count( $c ) < 1 ) {
			$this->returnAjaxError( array( 'message' => 'Invalid discount code' ) );
		}

		$this->cart->addPromo( $this->getCoupon( $c[0]->ID ) );

		$this->exitWithJSON( array( 'cart' => $this->cart->toArray() ) );

	}

	public function &getProduct( $id ) {
		if ( !array_key_exists( $id, $this->_productsCache ) ) {
			$this->_productsCache[$id] = new lpcProduct( $id );
		}
		return $this->_productsCache[$id];
	}

	public function &getOrder( $id ) {
		if ( !array_key_exists( $id, $this->_ordersCache ) ) {
			$this->_ordersCache[$id] = new lpcOrder( $id );
		}
		return $this->_ordersCache[$id];
	}

	public function &getCoupon( $id ) {
		if ( !array_key_exists( $id, $this->_couponsCache ) ) {
			$this->_couponsCache[$id] = new lpcCoupon( $id );
		}
		return $this->_couponsCache[$id];
	}

	public function &getLogEntry( $id ) {
		if ( !array_key_exists( $id, $this->_logEntriesCache ) ) {
			$this->_logEntriesCache[$id] = new lpcLogEntry( $id );
		}
		return $this->_logEntriesCache[$id];
	}

	public function &createNewOrder() {
		$order = new lpcOrder();
		$this->_ordersCache[$order->ID] = &$order;
		return $this->_ordersCache[$order->ID];
	}

	public function getCustomizeForm() {
		customize_product_form( $_POST['product_id'] );
		exit;
	}

	public function processCheckoutData( $reload = false ) {

	    if ( $reload ) {
    		$order = $this->createNewOrder();
    		$_SESSION['order_id'] = $order->ID;
    		$order->importlpcCart( $this->cart );
	    }
	    else {
	    	$order = $this->getOrder( $_SESSION['order_id'] );
	    }

	    if ( array_key_exists( 'submitted', $_POST ) && is_array( $_POST['submitted'] ) ) {

			$s = array();

			foreach ( $_POST['submitted'] as $k => $v ) {

				$c = explode( '-', $k );

				switch ( count( $c ) ) {
					case 3:
						$s[$c[0]][$c[1]][$c[2]] = $v;
						break;
					case 2:
						$s[$c[0]][$c[1]] = $v;
						break;
					case 1:
						$s[$c[0]] = $v;
						break;
				}

			}

			$order->update( $s );

	    }

	    return $order;

	}

	public function showCheckoutStep( $step, $data ) {
	    include( get_template_directory() . DIRECTORY_SEPARATOR . 'checkout.php' );
	}

	public function getCheckoutStep() {

		$p = false;
		$e = array( 'message' => null, 'fields' => array() );
		$s = $_POST['step'] - 1;
		$submitted = is_array( $_POST['submitted'] ) ? $_POST['submitted'] : array();

		$skip_process_account = false;
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			if ( $s == 0 ) {
				$s = 1;
				$skip_process_account = true;
			}
		}

	    $order = $this->processCheckoutData( $s < 1 || ( $s == 1 && $skip_process_account ) );

		if ( $_POST['direction'] != -1 ) {

			// Check required fields
			$requiredFields = array(
				'step-1'  => array( 'user-email' ),
				'step-2'  => array( 'acct-firstname', 'acct-lastname', 'acct-phone', 'acct-street', 'acct-suburb', 'acct-postcode', 'acct-state' ),
				'step-3'  => array( 'delivery-use_different_address' ),
				'step-4'  => array( 'payment-method' )
			);

			if ( $skip_process_account ) {
				$requiredFields['step-1'] = array();
			}

			if ( $submitted['delivery-use_different_address'] ) {
				$requiredFields['step-3'] = array_merge( $requiredFields['step-3'], array( 'delivery-firstname', 'delivery-lastname', 'delivery-street', 'delivery-suburb', 'delivery-postcode', 'delivery-state' ) );
			}

			if ( array_key_exists( 'payment-method', $submitted ) && $submitted['payment-method'] == 'cc' ) {
				$requiredFields['step-4'] = array_merge( $requiredFields['step-4'], array( 'cc-name', 'cc-number', 'cc-exp-month', 'cc-exp-year', 'cc-csc' ) );
			}

			foreach ( $requiredFields['step-'.$s] as $field ) {
				if ( !array_key_exists( $field, $submitted ) || ( !$submitted[$field] && $submitted[$field] !== 0  && $submitted[$field] !== '0' ) ) {
					$e['message'] = 'Please enter a value for all required fields';
					$e['fields'][] = array( 'name' => $field, 'message' => 'Required field cannot be left empty' );
				}
			}

			if ( !$this->isError( $e ) ) {

				switch ( $s ) {

					case 1: // Process account info

						if ( $skip_process_account ) {
							$order->update( array( 'user' => array(
								'id'            => $current_user->ID,
								'email'         => $current_user->user_email,
								'password'      => '*********',
								'registered'    => true
							) ) );
							break;
						}

						if ( filter_var( $submitted['user-email'], FILTER_VALIDATE_EMAIL ) === false ) {
							$e['message'] = 'Invalid email address format';
							$e['fields'][] = array( 'name' => 'user-email', 'message' => 'Please enter a valid email address' );
							break;
						}

						$login = $submitted['user-registered'];
						$user = get_user_by( 'email', $submitted['user-email'] );
						$valid_password = !$login ? false : wp_check_password( $submitted['user-password'], $user->data->user_pass, $user->ID );

						if ( $login ) {

							if ( $user ) {

								if ( !$valid_password ) { // Wrong password
									$e['message'] = 'Incorrect password';
									$e['fields'][] = array( 'name' => 'user-password', 'message' => 'Incorrect password' );
									break;
								}

								// Valid user, save details
								$order->update( array( 'user' => array(
									'id'            => $user->ID,
									'email'         => $user->user_email,
									'password'      => '*********',
									'registered'    => true
								) ) );
								break;

							}

							// No such user
							$e['message'] = 'User does not exist';
							$e['fields'][] = array( 'name' => 'user-email', 'message' => 'User does not exist' );
							break;

						}

						if ( $user ) { // User exists but no login was provided
							$e['message'] = 'You have an account with us already';
							$e['fields'][] = array( 'name' => 'user-email', 'message' => 'Email address already registered' );
						}

						if ( strtolower( $submitted['user-email'] ) != strtolower( $submitted['user-conf_email'] ) ) {
							$e['message'] = 'Email address does not match';
							$e['fields'][] = array( 'name' => 'user-conf_email', 'message' => 'Email address does not match' );
						}

						$order->update( array( 'user' => array( 'email' => strtolower( $submitted['user-email'] ) ) ) );

						break;

					case 2: // Process billing info

						break;

					case 3: // Process shipping info

						break;

					case 4: // Process credit card info and create order

						if ( $submitted['payment-method'] == 'cc' ) {

							$cards = array(
								'visa'          => '(4\d{12}(?:\d{3})?)',
								'mastercard'    => '(5[1-5]\d{14})'
							);

							$cc_number = preg_replace( '/\D/', '', $submitted['cc-number'] );
							$pattern = '#^(?:' . implode( '|', array_values( $cards ) ) . ')$#';
							$valid_cc = preg_match( $pattern, $cc_number );

							if ( !$valid_cc ) {
								$e['message'] = 'Invalid credit card number';
								$e['fields'][] = array( 'name' => 'cc-number', 'message' => 'Invalid credit card number' );
								break;
							}

						}

						try {
							$order->process();
							empty_cart();
						}
						catch ( lpcOrderException $x ) {
							$e = $x->e;
						}

						break;

				}

			}

			if ( $this->isError( $e ) ) {
				$this->returnAjaxError( $e );
			}

		}

		$this->showCheckoutStep( $s + 1, array( 'error' => $e ) );

		exit;

	}

	private function sendEmail( $to, $subject, $body, $attach = '' ) {
		$headers = array(
			'Bcc: LEETPC Customer Care <care@leetpc.com.au>',
			'Bcc: Callan Milne <cal@leetpc.com.au>',
		);
		return wp_mail( $to, $subject, $body, $headers, $attach );
	}

	public function emptyCart() {
		$this->cart->emptyCart();
		$this->exitWithJSON( array( 'cart' => $this->cart->toArray() ) );
	}

	public function removeProductFromCart() {
		$this->cart->removeItem( $_POST['line_item_id'] );
		$this->exitWithJSON( array( 'cart' => $this->cart->toArray() ) );
	}

	public function addProductToCart() {

		if ( !array_key_exists( 'product_id', $_POST ) ) {
			$this->returnAjaxError( array( 'message' => 'Product ID not specified' ) );
		}

		$product_id = $_POST['product_id'];
		$component_ids = array_key_exists( 'component_ids', $_POST ) ? explode( ',', $_POST['component_ids'] ) : array();

		$this->cart->addItem( $product_id, $component_ids );

		$this->exitWithJSON( array( 'cart' => $this->cart->toArray() ) );

	}

	public function updateCartItemQty() {

		if ( !array_key_exists( 'line_item_id', $_POST ) ) {
			$this->returnAjaxError( array( 'message' => 'Line Item ID not specified' ) );
		}

		$line_item_id = $_POST['line_item_id'];
		$qty = $_POST['qty'];

		$this->cart->setItemQty( $line_item_id, $qty );

		$this->exitWithJSON( array( 'cart' => $this->cart->toArray() ) );

	}

	private function isError( $e ) {
		return $e['message'] !== null;
	}

	private function returnAjaxError( $e ) {
		return $this->exitWithJSON( array( 'error' => $e ), 400 );
	}

	private function exitWithJSON( $array, $code = 200 ) {

		switch ( $code ) {

			case 400:
				header( 'HTTP/1.1 400 Bad Request' );
				break;

		}
		
		header( 'Content-type: application/json' );
		echo json_encode( $array );
		exit;

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

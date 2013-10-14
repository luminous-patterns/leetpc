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

	private $_couponsCache = array();
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

		/*

		add_action( 'init',                                        array( &$this, 'init' ) );
		add_action( 'admin_init',                                  array( &$this, 'initAdmin' ) );
		
		add_action( 'wp_ajax_METHOD_NAME',                         array( &$this, 'METHOD' ) );
		add_action( 'wp_ajax_nopriv_METHOD_NAME',                  array( &$this, 'METHOD_NOPRIV' ) );

		*/

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

		apply_coupon( $this->getCoupon( $c[0]->ID ) );

		$this->exitWithJSON( array( 'cart' => get_cart() ) );

	}

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

	public function &getCoupon( $id ) {
		if ( !array_key_exists( $id, $this->_couponsCache ) ) {
			$this->_couponsCache[$id] = new lpcCoupon( $id );
		}
		return $this->_couponsCache[$id];
	}

	public function getCustomizeForm() {
		customize_product_form( $_POST['product_id'] );
		exit;
	}

	public function processCheckoutData( $reload = false ) {

	    $data = $reload ? array( 'cart' => get_cart(), 'order_id' => substr( uniqid(), -8 ) ) : $_SESSION['checkout_data'];

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
		$e = array( 'message' => null, 'fields' => array() );
		$s = $_POST['step'] - 1;

		$payment_complete = false;

		$skip_process_account = false;
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			if ( $s == 0 ) {
				$s = 1;
				$skip_process_account = true;
			}
		}

		$submitted = $_POST['submitted'];

		$password = $submitted['user-password'] ? $submitted['user-password'] : '';
		$_POST['submitted']['user-password'] = '******';

	    $this->processCheckoutData( $s < 1 || ( $s == 1 && $skip_process_account ) );

		if ( $_POST['direction'] != -1 ) {

		    $required_fields = array();

			switch ( $s ) {

				case 1: // Process account info

					if ( $skip_process_account ) {
						$_SESSION['checkout_data']['user_id'] = $current_user->ID;
						$_SESSION['checkout_data']['user'] = array(
							'id'            => $current_user->ID,
							'email'         => $current_user->user_email,
							'registered'    => '1'
						);
						break;
					}

					if ( filter_var( $submitted['user-email'], FILTER_VALIDATE_EMAIL ) === false ) {
						$e['message'] = 'Invalid email address format';
						$e['fields'][] = array( 'name' => 'user-email', 'message' => 'Invalid email address format' );
						break;
					}

					$login = $submitted['user-registered'];
					$user = get_user_by( 'email', $submitted['user-email'] );
					$valid_password = !$login ? false : wp_check_password( $password, $user->data->user_pass, $user->ID );

					if ( $login ) {

						if ( $user ) {

							if ( !$valid_password ) {
								// Wrong password
								$e['message'] = 'Incorrect password';
								$e['fields'][] = array( 'name' => 'user-password', 'message' => 'Incorrect password' );
								break;
							}

							// Valid user, save ID
							$_SESSION['checkout_data']['user_id'] = $user->ID;
							$_SESSION['checkout_data']['user']['id'] = $user->ID;
							break;

						}

						// No such user
						$e['message'] = 'User does not exist';
						$e['fields'][] = array( 'name' => 'user-email', 'message' => 'User does not exist' );
						break;

					}

					if ( $user ) {

						// User exists but no login was sent
						$e['message'] = 'You have an account with us already';
						$e['fields'][] = array( 'name' => 'user-email', 'message' => 'Email address already registered' );

					}

					if ( strtolower( $submitted['user-email'] ) != strtolower( $submitted['user-conf_email'] ) ) {
						$e['message'] = 'Email address does not match';
						$e['fields'][] = array( 'name' => 'user-conf_email', 'message' => 'Email address does not match' );
					}

					break;

				case 2: // Process billing info

					$required_fields = array( 'acct-firstname', 'acct-lastname', 'acct-phone', 'acct-street', 'acct-suburb', 'acct-postcode', 'acct-state' );

					break;

				case 3: // Process shipping info

					if ( $submitted['delivery-use_different_addr'] == 1 ) {
						$required_fields = array( 'delivery-firstname', 'delivery-lastname', 'delivery-street', 'delivery-suburb', 'delivery-postcode', 'delivery-state' );
					}

					break;

				case 4: // Process credit card info and create order

					if ( $_SESSION['checkout_data']['payment']['method'] == 'cc' ) { // Process credit card

						$required_fields = array( 'cc-name', 'cc-number', 'cc-exp-month', 'cc-exp-year', 'cc-csc' );

						if ( $_SESSION['checkout_data']['cc']['number'] == '51631000000000' ) { // test transaction
							$_SESSION['checkout_data']['payment']['status'] = 'success';
							$p = true;
						}

						$this->pin = new Pin();

						try {

							$gateway_response = $this->pin->postCharge( array(
								
								'email'         => $_SESSION['checkout_data']['user']['email'],
								'description'   => 'PC order ' . $_SESSION['checkout_data']['order_id'],
								'amount'        => intval( $_SESSION['checkout_data']['cart']['total'] * 100 ),
								'ip_address'    => $_SERVER['REMOTE_ADDR'],
								'currency'      => 'AUD',

								'card[number]'             => preg_replace( '/\s+/', '', $_SESSION['checkout_data']['cc']['number'] ),
								'card[expiry_month]'       => $_SESSION['checkout_data']['cc']['exp']['month'],
								'card[expiry_year]'        => $_SESSION['checkout_data']['cc']['exp']['year'],
								'card[cvc]'                => $_SESSION['checkout_data']['cc']['csc'],
								'card[name]'               => $_SESSION['checkout_data']['cc']['name'],
								'card[address_line1]'      => $_SESSION['checkout_data']['acct']['street'],
								'card[address_city]'       => $_SESSION['checkout_data']['acct']['suburb'],
								'card[address_postcode]'   => $_SESSION['checkout_data']['acct']['postcode'],
								'card[address_state]'      => 'VIC',
								'card[address_country]'    => 'Australia'

							) );

							$_SESSION['checkout_data']['cc']['number'] = substr( $_SESSION['checkout_data']['cc']['number'], 0, 4 ) . '-####-####-'  . substr( $_SESSION['checkout_data']['cc']['number'], -4 );
							unset( $_SESSION['checkout_data']['cc']['csc'] );

							if ( $gateway_response->success ) {
								$_SESSION['checkout_data']['payment']['status'] = 'success';
								$_SESSION['checkout_data']['payment']['gateway'] = 'pin.net.au';
								$_SESSION['checkout_data']['payment']['token'] = $gateway_response->token;
								$_SESSION['checkout_data']['payment']['message'] = $gateway_response->status_message;
								$_SESSION['checkout_data']['payment']['amount'] = $gateway_response->amount / 100;
								$_SESSION['checkout_data']['payment']['ipaddress'] = $gateway_response->ip_address;
								$_SESSION['checkout_data']['payment']['date'] = $gateway_response->created_at;
								$p = true;
							}

						}
						catch ( PIN_Exception $exception ) {
							$e['message'] = $exception->getDescription();
							$e['fields'][] = array( 'name' => 'cc-number', 'message' => $exception->getDescription(), 'extra' => $exception->getErrors() );
						}

					}
					elseif ( $_SESSION['checkout_data']['payment']['method'] == 'bank' ) { // Process bank deposit
						unset( $_SESSION['checkout_data']['cc'] );
						$_SESSION['checkout_data']['payment']['status'] = 'pending';
						$p = true;
					}

					

					break;

			}

			foreach ( $required_fields as $field ) {

				if ( !array_key_exists( $field, $submitted ) || !$submitted[$field] ) {
					$e['message'] = 'Please enter a value for all required fields';
					$e['fields'][] = array( 'name' => $field, 'message' => 'Required field cannot be left empty' );
				}

			}

			if ( $p ) {

				// Estimate delivery based on 9 days from today (or first business day after) 
				$deliver_by = new DateTime( null, new DateTimeZone( 'Australia/Melbourne' ) );
				$deliver_by->add( new DateInterval( 'P9D' ) );
				if ( $deliver_by->format( 'N' ) > 5 ) {
					$period = 9 - $deliver_by->format( 'N' );
					$deliver_by->add( new DateInterval( 'P' . $period . 'D' ) );
				}
				$_SESSION['checkout_data']['delivery']['deliver_on'] = $deliver_by->format( 'D jS \o\f M' );
				
				$y = array();

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

					$y[] = $x;

				}

				$_SESSION['checkout_data']['line_items'] = $y;

				$invoice_id = $this->createInvoice( $_SESSION['checkout_data'] );
				$_SESSION['checkout_data']['invoice_id'] = $invoice_id;

				// Send emails
				$this->sendEmailFromCheckoutData( 'order-success', $_SESSION['checkout_data'] );
				if ( $_SESSION['checkout_data']['payment']['method'] == 'cc' ) {
					$this->sendEmailFromCheckoutData( 'cc-payment', $_SESSION['checkout_data'] );
				}
				else {
					$this->sendEmailFromCheckoutData( 'payment-request', $_SESSION['checkout_data'] );
				}

				if ( $invoice_id ) {
					empty_cart();
				}

			}

			if ( $this->isError( $e ) ) {
				$this->returnAjaxError( $e );
			}

		}

		$this->showCheckoutStep( $s + 1, array( 'error' => $e ) );

		exit;

	}

	private function sendEmailFromCheckoutData( $type, $d ) {

		$to = $d['user']['email'];
		$invoice_id = $d['invoice_id'];
		$firstname = $d['acct']['firstname'];

		switch ( $type ) {

			case 'order-success':
				$subject = "Order confirmation";
				$body = "Thanks $firstname,\n\n"
					. "Your PC order has been received and your expected delivery date is " . $d['delivery']['deliver_on'] . ".\n\n"
					. "You can view and print your copy of your invoice (#$invoice_id) via the following link:\n"
					. "https://www.leetpc.com.au/invoice/?invoice_id=$invoice_id";
					// . "You may review your invoice history at any time via the LEETPC customer area:\n"
					// . "https://www.leetpc.com.au/my-account/";
				break;

			case 'cc-payment':
				$subject = 'Credit card payment confirmation';
				$body = "Hi $firstname,\n\n"
					. "This is a payment receipt for Invoice #$invoice_id generated on " . date( 'd-m-Y' ) . ".\n\n"
					. "Amount: $" . number_format( $d['cart']['total'], 2 ) . " AUD\n"
					. "Status: " . $d['payment']['status'] . "\n\n"
					. "You can view and print your copy of your invoice (#$invoice_id) via the following link:\n"
					. "https://www.leetpc.com.au/invoice/?invoice_id=$invoice_id";
					// . "You may review your invoice history at any time via the LEETPC customer area:\n"
					// . "https://www.leetpc.com.au/my-account/";
				break;

			case 'payment-request':
				$subject = 'Payment required';
				$body = "Hi $firstname,\n\n"
					. "This is an automated reminder that payment is due for Invoice #$invoice_id, generated on " . date( 'd-m-Y' ) . ".  Please make payment as soon as possible using the following account details.\n\n"
					. "Bank name: WESTPAC\n"
					. "Account name: INTEGRATED WEB SERVICES\n"
					. "BSB: 033-349\n"
					. "Acct #: 383009\n"
					. "Amount: $" . number_format( $d['payment']['amount'], 2 ) . " AUD\n\n"
					. "** IMPORTANT ** Please remember to include your invoice number ($invoice_id) as the description for your payment.\n\n"
					. "You can view and print your copy of your invoice (#$invoice_id) via the following link:\n"
					. "https://www.leetpc.com.au/invoice/?invoice_id=$invoice_id";
					// . "You may review your invoice history at any time via the LEETPC customer area:\n"
					// . "https://www.leetpc.com.au/my-account/";
				break;

		}

		$body .= "\n\nSincerely,\nCustomer care\nLEETPC.com.au";

		return $this->sendEmail( $to, $subject, $body );

	}

	private function sendWelcomeEmail( $user_id, $password ) {

		$subject = 'Your account information';
		$body = "Hi $firstname,\n\n"
			. "We have created an account for you.\n\n"
			. "Username (email): $email\n"
			. "Password: $password\n\n"
			. "You may review your invoice history at any time via the LEETPC customer area:\n"
			. "https://www.leetpc.com.au/my-account/";

		$body .= "\n\nSincerely,\nCustomer care\nLEETPC.com.au";

		return $this->sendEmail( $to, $subject, $body );

	}

	// private function sendServiceOrder( $user_id, $password ) {

	// 	$subject = 'Your account information';
	// 	$body = "Hi $firstname,\n\n"
	// 		. "We have created an account for you.\n\n"
	// 		. "Username (email): $email\n"
	// 		. "Password: $password\n\n"
	// 		. "You may review your invoice history at any time via the LEETPC customer area:\n"
	// 		. "https://www.leetpc.com.au/my-account/";

	// 	$body .= "\n\nSincerely,\nCustomer care\nLEETPC.com.au";

	// 	return $this->sendEmail( $to, $subject, $body );

	// }

	private function sendEmail( $to, $subject, $body, $attach = '' ) {
		$headers = array(
			'Bcc: LEETPC Customer Care <care@leetpc.com.au>',
			'Bcc: Callan Milne <cal@leetpc.com.au>',
		);
		return wp_mail( $to, $subject, $body, $headers, $attach );
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
		$this->exitWithJSON( array( 'cart' => get_cart() ) );
	}

	public function removeProductFromCart() {
		remove_line_item( $_POST['line_item_id'] );
		$this->exitWithJSON( array( 'cart' => get_cart() ) );
	}

	public function addProductToCart() {

		if ( !array_key_exists( 'product_id', $_POST ) ) {
			$this->returnAjaxError( array( 'message' => 'Product ID not specified' ) );
		}

		$product_id = $_POST['product_id'];
		$component_ids = array_key_exists( 'component_ids', $_POST ) ? explode( ',', $_POST['component_ids'] ) : array();

		add_product_to_cart( $product_id, $component_ids );

		$this->exitWithJSON( array( 'cart' => get_cart() ) );

	}

	public function updateCartItemQty() {

		if ( !array_key_exists( 'line_item_id', $_POST ) ) {
			$this->returnAjaxError( array( 'message' => 'Line Item ID not specified' ) );
		}

		$line_item_id = $_POST['line_item_id'];
		$qty = $_POST['qty'];

		set_line_item_qty( $line_item_id, $qty );

		$this->exitWithJSON( array( 'cart' => get_cart() ) );

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

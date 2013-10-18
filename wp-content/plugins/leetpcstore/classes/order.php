<?php

require_once( 'cart.php' );

class lpcOrder extends lpcCart {

	static $public_methods = array( 'process', 'update', 'importlpcCart', 'addItem', 'setItemQty', 'removeItem', 'addPromo', 'removePromo', 'emptyCart' );
	static $excl_from_save = array( 'ID', 'log', 'cc' );

	public $ID;

	public $user_id;

	public $user = array(
		'email'                   => null,
		'registered'              => false
	);

	public $account = array(
		'firstname'               => null,
		'lastname'                => null,
		'company'                 => null,
		'street'                  => null,
		'suburb'                  => null,
		'postcode'                => null,
		'state'                   => 'VIC',
		'country'                 => 'AU'
	);

	public $delivery = array(
		'method'                  => null,
		'use_different_address'   => false,
		'firstname'               => null,
		'lastname'                => null,
		'company'                 => null,
		'street'                  => null,
		'suburb'                  => null,
		'postcode'                => null,
		'state'                   => 'VIC',
		'country'                 => 'AU',
		'deliver_on'              => null
	);

	public $payment = array(
		'complete'                => false,
		'method'                  => null,
		'status'                  => null
	);

	public $cc = array(
		'card[number]'            => null,
		'card[expiry_month]'      => null,
		'card[expiry_year]'       => null,
		'card[cvc]'               => null,
		'card[name]'              => null
	);

	public $status = 'incomplete';

	private $log = array();

	function __construct( $id = null ) {

		if ( $id ) {
			if ( get_post_type( $id ) == 'lpc_order' ) $this->load( $id );
			else trigger_error( 'Trying to load non-order type as order type', E_USER_ERROR );
		}
		else $this->create();

	}

	function __destruct() {

	}

    public function __call( $method, $arguments ) {
        if ( method_exists( $this, $method ) && in_array( $method, self::$public_methods ) ) {
            $result = call_user_func_array( array( $this, $method ), $arguments );
            $this->save();
            return $result;
        }
    }

	protected function load( $id ) {

		$this->ID = $id;

		$p = get_post( $id );
		$m = get_post_custom( $id );

		foreach ( array_keys( get_object_vars( $this ) ) as $var ) {
			if ( in_array( $var, self::$excl_from_save ) ) continue;
			$this->$var = json_decode( $m["$var"][0], true );
		}

		$logs = get_posts( array(
			'posts_per_page'   => 20,
			'post_type'        => 'log_entry',
			'meta_key'         => 'order_id',
			'meta_value'       => $this->ID,
			'order_by'         => 'ID',
			'order'            => 'ASC'
		) );

		foreach ( $logs as $log ) $this->appendLogEntry( $log );

	}

	public function save() {
		foreach ( get_object_vars( $this ) as $var => $value ) {
			if ( in_array( $var, self::$excl_from_save ) ) continue;
			update_post_meta( $this->ID, "$var", json_encode( $value ) );
		}
	}

	public function getLog( $order = 'ID_DESC' ) {
		return $order == 'ID_DESC' ? array_reverse( $this->log ) : $this->log;
	}

	protected function create() {

		$i = array(
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_author'    => 2,
			'post_title'     => 'NEW ORDER',
			'post_type'      => 'lpc_order',
			// 'post_status'    => 'published'
		);

		$this->ID = wp_insert_post( $i, true );

		wp_publish_post( $this->ID );

		$u = array(
			'ID'         => $this->ID,
			'post_title' => "ORDER ID#{$this->ID}",
			'post_name'  => "order-{$this->ID}",
		);

		wp_update_post( $u );

		$this->log( 'order-created' );

	}

	protected function update( $data ) {
		foreach ( $data as $g => $v ) $this->updateVar( $g, $v );
	}

	protected function updateVar( $g, $v ) {

		switch ( $g ) {

			case 'user':
				$this->user = array_replace_recursive( $this->user, $v );
				if ( array_key_exists( 'id', $this->user ) ) $this->user_id = $this->user['id'];
				break;

			case 'acct':
			case 'account':
				$this->account = array_replace_recursive( $this->account, $v );
				break;

			case 'delivery':
				$this->delivery = array_replace_recursive( $this->delivery, $v );
				break;

			case 'payment':
				$this->payment = array_replace_recursive( $this->payment, $v );
				break;

			case 'cc':
				$this->cc = array_replace_recursive( $this->cc, $v );
				break;

			case 'status':
				$this->setStatus( $v );
				break;

		}

	}

	protected function log( $t, $note = '', $m = array() ) {
		return $this->appendLogEntry( lpc_log( $t, $note, array_merge( $m, array( 'order_id' => $this->ID ) ) ) );
	}

	protected function appendLogEntry( $log ) {

		$date = new DateTime( $log->post_date_gmt, new DateTimeZone( 'UTC' ) );
		$date->setTimezone( new DateTimeZone( BOOK_KEEPING_TZ ) );

		$type = wp_get_post_terms( $log->ID, 'log_entry_type' );

		$log_date = $date->format( DateTime::ATOM );
		$log_note = $log->post_content;
		$log_extra = array();

		foreach ( get_post_custom( $log->ID ) as $k => $v ) $log_extra[$k] = $v[0];

		$this->log[] = array( $log->ID, array( $date, $log_date ), $type[0]->slug, $type[0]->name, $log_note, $log_extra );

		return true;

	}

	protected function importlpcCart( lpcCart $cart ) {
		foreach ( array_keys( get_class_vars( 'lpcCart' ) ) as $var ) {
			if ( in_array( $var, self::$excl_from_save ) ) continue;
			$this->$var = $cart->$var;
		}
		return true;
	}

	protected function sendEmailNotification( $type ) {

		$headers = array(
			'Bcc: LEETPC Customer Care <care@leetpc.com.au>',
			'Bcc: Callan Milne <cal@leetpc.com.au>',
		);

		$to = $this->user['email'];
		$firstname = $this->account['firstname'];
		$attach = '';
		$subject = '';
		$body = '';
		$signature = "\n\nSincerely,\nCustomer care\nLEETPC.com.au";

		switch ( $type ) {

			case 'order-placed':
				$subject = "Order confirmation";
				$body = "Thanks $firstname,\n\n"
					. "\n\nYour PC order has been received and your expected delivery date is " . $this->getDate( 'deliver_on', 'D jS \o\f M' ) . "."
					. "\n\nYou can view and print your copy of your invoice (#{$this->ID}) via the following link:"
					. "\n" . $this->getLink( 'invoice' );
				break;

			case 'cc-payment':
				$subject = 'Credit card payment confirmation';
				$body = "Hi $firstname,"
					. "\n\nThis is a payment receipt for Invoice #{$this->ID} generated on " . $this->getDate( 'created' ) . "."
					. "\n\nAmount: $" . number_format( $this->payment['amount'], 2 ) . " AUD\n"
					. "\nStatus: " . $this->payment['status']
					. "\n\nYou can view and print your copy of your invoice (#{$this->ID}) via the following link:"
					. "\n" . $this->getLink( 'invoice' );
				break;

			case 'payment-request':
				$subject = 'Payment required';
				$body = "Hi $firstname,"
					. "\n\nThis is an automated reminder that payment is due for Invoice #{$this->ID}, generated on " . $this->getDate( 'created' ) . ".  Please make payment as soon as possible using the following account details."
					. "\n\nBank name: WESTPAC"
					. "\nAccount name: INTEGRATED WEB SERVICES"
					. "\nBSB: 033-349"
					. "\nAcct #: 383009"
					. "\nAmount: $" . number_format( $this->total, 2 ) . " AUD"
					. "\n\n** IMPORTANT ** Please remember to include your invoice number ({$this->ID}) as the description for your payment."
					. "\n\nYou can view and print your copy of your invoice (#{$this->ID}) via the following link:"
					. "\n" . $this->getLink( 'invoice' );
					// . "\n\nYou may review your invoice history at any time via the LEETPC customer area:"
					// . "\nhttps://www.leetpc.com.au/my-account/";
				break;

		}

		$body .= $signature;

		if ( !$subject || !wp_mail( $to, $subject, $body, $headers, $attach ) ) return false;

		$this->log( 'customer-contact', '', array( 
			'type'       => 'outbound',
			'medium'     => 'email',
			'recipient'  => $to,
			'subject'    => $subject,
			'body'       => $body
		) );

		return true;

	}

	protected function processPayment() {

		switch ( $this->getPaymentMethod() ) {

			case 'cc':
				return $this->chargeCard();
				break;

			case 'bank':
				$this->sendEmailNotification( 'payment-request' );
				return true;
				break;

		}

		return false;

	}

	private function chargeCard() {

		if ( $this->status != 'incomplete' ) return;

		$this->pin = new Pin();

		try {

			$card_number = preg_replace( '/\s+/', '', trim( $this->cc['number'] ) );
			$amount = intval( $this->total * 100 );

			$this->log( 'payment-attempt', '', array( 
				'method'         => 'cc',
				'card_number'    => 'XXXX-XXXX-XXXX-' . substr( $card_number, -4 ), 
				'card_name'      => $this->cc['name'],
				'amount'         => $amount
			) );

			$gateway_response = $this->pin->postCharge( array(
				
				'email'         => $this->user['email'],
				'description'   => "PC order $this->ID",
				'amount'        => $amount,
				'ip_address'    => $_SERVER['REMOTE_ADDR'],
				'currency'      => 'AUD',

				'card[number]'             => $card_number,
				'card[expiry_month]'       => $this->cc['exp']['month'],
				'card[expiry_year]'        => $this->cc['exp']['year'],
				'card[cvc]'                => $this->cc['csc'],
				'card[name]'               => $this->cc['name'],
				'card[address_line1]'      => $this->account['street'],
				'card[address_city]'       => $this->account['suburb'],
				'card[address_postcode]'   => $this->account['postcode'],
				'card[address_state]'      => 'VIC',
				'card[address_country]'    => 'Australia'

			) );

		}
		catch ( PIN_Exception $e ) {
			$error = array( 
				'message'   => $e->getDescription(),
				'fields'    => array( 
					array( 
						'name'       => 'cc-number', 
						'message'    => $e->getDescription(), 
						'extra'      => $e->getErrors() 
					) 
				)
			);
			$this->log( 'payment-failed', '', array( 'method' => 'cc', 'error' => json_encode( $error ) ) );
			throw new lpcOrderException( $error );
		}

		if ( $gateway_response->success ) {

			$this->payment['complete']       = true;
			$this->payment['status']         = 'success';
			$this->payment['gateway']        = 'pin.net.au';
			$this->payment['token']          = $gateway_response->token;
			$this->payment['message']        = $gateway_response->status_message;
			$this->payment['ipaddress']      = $gateway_response->ip_address;
			$this->payment['created_at']     = $gateway_response->created_at;
			$this->payment['amount']         = $gateway_response->amount / 100;
			$this->payment['gateway_data']   = $gateway_response;

			$this->log( 'payment-received', $gateway_response->token, array( 'data' => json_encode( $gateway_response ) ) );

			$this->sendEmailNotification( 'cc-payment' );

			return true;

		}

		return false;

	}

	protected function estimateDeliveryDate() {
		// Estimate delivery based on 9 days from today (or first business day after) 
		$deliver_on = new DateTime( null, new DateTimeZone( BOOK_KEEPING_TZ ) );
		$deliver_on->add( new DateInterval( 'P9D' ) );
		if ( $deliver_on->format( 'N' ) > 5 ) {
			$period = 9 - $deliver_on->format( 'N' );
			$deliver_on->add( new DateInterval( 'P' . $period . 'D' ) );
		}
		$this->delivery['deliver_on'] = $deliver_on->format( DateTime::ATOM );
		return $this->delivery['deliver_on'];
	}

	protected function process() {

		if ( $this->status != 'incomplete' ) return true;

		if ( $this->processPayment() ) {
			$this->estimateDeliveryDate();
			$this->sendEmailNotification( 'order-placed' );
			$status = $this->payment['complete'] ? 'processed' : 'pending-payment';
			$this->setStatus( $status );
			return true;
		}

		return false;

	}

	protected function setStatus( $status ) {
		
		if ( $status == $this->status ) return true;

		$previous_status = $this->status;
		$this->status = $status;

		$this->log( 'status-changed', '', array( 
			'status' => $status, 
			'previous_status' => $previous_status 
		) );

	}

	public function getDeliveryAddress() {
		return $this->delivery['use_different_address'] ? $this->delivery : array_merge( $this->delivery, $this->account );
	}

	public function needsAttention() {
		$a = array(
			// 'incomplete',
			// 'pending-payment',
			'processed',
			// 'building',
			'ready',
			// 'in-transit',
			// 'complete',
		);
		return in_array( $this->status, $a ) && $this->status != 'complete';
	}

	public function getPaymentMethod( $formatted = false ) {
		$f = array(
			'cc'      => 'VISA / MasterCard (online)',
			'bank'    => 'Bank Transfer'
		);
		return !$formatted ? $this->payment['method'] : $f[$this->payment['method']];
	}

	public function getPaymentStatus( $formatted = false ) {
		$f = array(
			'success'      => 'Payment received',
			'incomplete'   => 'Pending customer payment'
		);
		$r = $this->payment['status'] ? $this->payment['status'] : 'incomplete';
		return !$formatted ? $r : $f[$r];
	}

	public function getLink( $t ) {

		$path = '';

		switch ( $t ) {

			case 'invoice':
				$path = "/invoice/?order_id={$this->ID}";
				break;

			case 'service_order':
				$path = "/serviceorder/?order_id={$this->ID}";
				break;

		}

		return home_url( $path );

	}

	public function getDate( $t, $f = 'Y-m-d', $utc = false ) {

		$date = null;

		switch ( $t ) {

			case 'created':
				$date = get_post_time( DateTime::ATOM, true, $this->ID );
				break;

			case 'deliver_on':
				$date = $this->delivery['deliver_on'];
				break;

			case 'payment_received':
				$date = $this->payment['created_at'];
				break;

		};

		$d = new DateTime( $date, new DateTimeZone( 'UTC' ) );
		if ( !$utc ) $d->setTimezone( new DateTimeZone( BOOK_KEEPING_TZ ) );

		return $d->format( $f );

	}

}

class lpcOrderException extends Exception {

	function __construct( $e ) {
		$this->e = $e;
	}

}
<?php

// cURL Library
require_once( 'curl.php' );

if ( !LIVE_PAYMENTS ) {
	define( 'PIN_API_ENDPOINT_HOST',         'https://test-api.pin.net.au' );
	define( 'PIN_API_SECRET_KEY',            'oQIvh3BSb6aYknvc_xYJzw' );
}
else {
	define( 'PIN_API_ENDPOINT_HOST',         'https://api.pin.net.au' );
	define( 'PIN_API_SECRET_KEY',            '0g8BWNsJZtHOJeoLRBZgEg' );
}

class Pin {

	private $curl; 

	function __construct() {

		$this->curl = new Curl();

		// Curl options
		$this->curl->options['CURLOPT_USERPWD'] = PIN_API_SECRET_KEY . ':';
		$this->curl->options['CURLOPT_SSL_VERIFYPEER'] = false;

	}

	public function postCustomer( $data ) {

		$defaults = array(
			'email'                      => '',
			'card[number]'               => '',
			'card[expiry_month]'         => '',
			'card[expiry_year]'          => '',
			'card[cvc]'                  => '',
			'card[name]'                 => '',
			'card[address_line1]'        => '',
			'card[address_line2]'        => '',
			'card[address_city]'         => '',
			'card[address_postcode]'     => '',
			'card[address_state]'        => '',
			'card[address_country]'      => 'AU',
		);

		$vars = array_merge( $defaults, $data );

		return $this->parseResponse( $this->post( 'customers', $vars ) );

	}

	public function putCustomer( $customer_token, $data ) {

		$defaults = array(
			'email'                      => '',
			'card[number]'               => '',
			'card[expiry_month]'         => '',
			'card[expiry_year]'          => '',
			'card[cvc]'                  => '',
			'card[name]'                 => '',
			'card[address_line1]'        => '',
			'card[address_line2]'        => '',
			'card[address_city]'         => '',
			'card[address_postcode]'     => '',
			'card[address_state]'        => '',
			'card[address_country]'      => 'AU',
		);

		$vars = array_merge( $defaults, $data );

		return $this->parseResponse( $this->put( 'customers/' . $customer_token, $vars ) );

	}

	public function postCharge( $data ) {

		$defaults = array(
			'email'                      => '',
			'description'                => 'LEETPC.com.au',
			'amount'                     => 100,
			'currency'                   => 'AUD',
			'ip_address'                 => '127.0.0.1'
		);

		$vars = array_merge( $defaults, $data );

		return $this->parseResponse( $this->post( 'charges', $vars ) );

	}

	private function parseResponse( $response ) {

		if ( $response->headers['Status-Code'] >= 300 ) {
			throw new PIN_Exception( $response );
		}

		$json = json_decode( $response->body );

		return $json->response;

	}

	private function get( $path = 'customers', $vars = array() ) {
		return $this->curlExec( $path, 'get', $vars );
	}

	private function post( $path = 'customers', $vars = array() ) {
		return $this->curlExec( $path, 'post', $vars );
	}

	private function put( $path = 'customers', $vars = array() ) {
		return $this->curlExec( $path, 'put', $vars );
	}

	private function curlExec( $path = 'customers', $request = 'get', $vars = array() ) {
		return $this->curl->{$request}( PIN_API_ENDPOINT_HOST . '/1/' . $path, $vars );
	}

}

class PIN_Exception extends Exception {

	private      $response;
	private      $responseBody;

	/**
	 * Constructor
	 * 
	 * @param integer $code 
	 * @param string|array $replacements The replacement(s) - if the error message has any
	 * @param Exception $previous Previous exception
	 */
	public function __construct( $pin_response ) {

		$this->response = $pin_response;

		$this->httpStatus = $this->response->headers['Status-Code'];
		$this->responseBody = json_decode( $this->response->body );

		parent::__construct( 'HTTP Error ' . $this->response->headers['Status-Code'] . "\n" . $this->response->error_description . "\n" . $this->response->body, 11700 );

	}

	public function getType() {
		return $this->responseBody->error;
	}

	public function getDescription() {
		return $this->responseBody->error_description;
	}

	public function getErrors() {
		return $this->responseBody->messages;
	}

	/**
	 * Get error message
	 * 
	 * @param integer $code 
	 * @return string The error definition
	 */
	private function _getErrorDefinition( $code, $replacements = array() ) {
		return vsprintf( $this->_definitions[$code][1], $replacements );
	}

}

?>
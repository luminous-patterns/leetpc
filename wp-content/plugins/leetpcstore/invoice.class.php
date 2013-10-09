<?php

class lpcInvoice {

	function __construct( $invoice_id ) {

		$this->post = get_post( $invoice_id );
		$this->meta = get_post_custom( $invoice_id );

		$this->cart = $this->getCart();

	}

	public function get( $k ) {
		return array_key_exists( $k, $this->meta ) ? $this->meta[$k][0] : null;
	}

	public function getUserDetails() {
		return json_decode( $this->get( '_user' ), true );
	}

	public function getAccountDetails() {
		return json_decode( $this->get( '_acct' ), true );
	}

	public function getDeliveryDetails() {
		return json_decode( $this->get( '_delivery' ), true );
	}

	public function getCart() {
		return json_decode( $this->get( '_cart' ), true );
	}

	public function getItemsTotal() {
		return $this->cart['items_total'];
	}

	public function getTotal() {
		return $this->cart['total'];
	}

	public function hasPromo() {
		return $this->cart['promo']['code'] !== null;
	}

	public function getPromo() {
		return $this->cart['promo'];
	}

	public function getDate() {
		return date( 'jS \o\f F Y', strtotime( $this->post->post_date ) );
	}

	public function getLineItems() {
		return json_decode( $this->meta['_line_items'][0], true );
	}

	public function getStatus() {
		return $this->post->post_status;
	}
	
}
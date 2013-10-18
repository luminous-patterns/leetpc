<?php

class lpcCoupon {

	function __construct( $coupon_id ) {

		$this->post = get_post( $coupon_id );
		$this->meta = get_post_custom( $coupon_id );

	}

	public function get( $k ) {
		return array_key_exists( $k, $this->meta ) ? $this->meta[$k][0] : null;
	}

}
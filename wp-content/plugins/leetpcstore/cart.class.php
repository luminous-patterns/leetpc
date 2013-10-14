<?php

class lpcCart {

	static $public_methods    = array( 'addItem', 'setItemQty', 'removeItem', 'addPromo', 'removePromo', 'emptyCart' );

	public $items             = array();
	public $items_count       = 0;
	public $items_total       = 0.00;

	public $promo = array(
		'code'    => null,
		'type'    => null,
		'amount'  => null
	);

	public $discount_total    = 0.00;
	public $sub_total         = 0.00;
	public $total             = 0.00;

	public $created           = null;

	function __construct() {
		$this->loadCart();
	}

	function __destruct() {
		$this->saveCart();
	}

    public function __call( $method, $arguments ) {
        if ( method_exists( $this, $method ) && in_array( $method, self::$public_methods ) ) {
            $result = call_user_func_array( array( $this, $method ), $arguments );
            $this->calculateTotals();
            return $result;
        }
    }

    public function getTotal() {
    	return $this->total;
    }

    public function toArray() {
		$s = array();
		foreach ( get_object_vars( $this ) as $var => $value ) 
			$s[$var] = $value;
		return $s;
    }

    private function loadCart() {
		if ( !array_key_exists( 'shopping_cart', $_SESSION ) ) {
			$this->created = time();
			return;
		}
		foreach ( $_SESSION['shopping_cart'] as $var => $value ) $this->$var = $value;
    }

    private function saveCart() {
		$s = array();
		foreach ( get_object_vars( $this ) as $var => $value ) 
			$s[$var] = $value;
		$_SESSION['shopping_cart'] = $s;
    }

    private function calculateTotals() {

		$items_count = 0;
		$items_total = 0.00;
		$discount_total = 0.00;
		$sub_total = 0.00;
		$total = 0.00;

		foreach ( $this->items as $k => $item ) {
			$this->items[$k]['price'] = calc_product_price( $item['product_id'], $item['component_ids'] );
			$items_total += calc_product_price( $item['product_id'], $item['component_ids'] ) * $item['qty'];
			$items_count += $item['qty'];
		}

		$sub_total = $items_total;

		if ( $this->promo['code'] ) {
			$discount_total = $this->promo['type'] == '%' ? $items_total * ( $this->promo['amount'] / 100 ) : $this->promo['amount'];
			$sub_total = max( 0, $sub_total - $discount_total );
		}

		$total = $sub_total;

		$this->items_count = $items_count;
		$this->items_total = $items_total;
		$this->discount_total = $discount_total;
		$this->sub_total = $sub_total;
		$this->total = $total;

    }

	private function addItem( $product_id, $component_ids = array(), $qty = 1 ) {

		$k = $this->generateKey( $product_id, $component_ids );

		if ( !array_key_exists( $k, $this->items ) ) {
			$this->items[$k] = array(
				'product_id'       => $product_id,
				'component_ids'    => $component_ids,
				'qty'              => $qty,
				'price'            => calc_product_price( $product_id, $component_ids )
			);
		}
		else $this->items[$k]['qty'] += $qty;

		return true;

	}

	private function setItemQty( $k, $qty ) {

		if ( $qty == 0 ) {
			return remove_line_item( $k );
		}

		if ( array_key_exists( $k, $this->items ) ) {
			$this->items[$k]['qty'] = $qty;
		}

		return true;
		
	}

	private function removeItem( $k ) {

		if ( array_key_exists( $k, $this->items ) ) {
			unset( $this->items[$k] );
		}

		return true;

	}

	private function addPromo( $p ) {
		$this->promo = array(
			'code'    => $p->get( 'code' ),
			'type'    => $p->get( 'discount_type' ),
			'amount'  => $p->get( 'discount_amount' )
		);
		return true;
	}

	private function removePromo() {
		$vars = get_class_vars( $this );
		$this->promo = $vars['promo'];
		return true;
	}

    private function emptyCart() {
    	$this->removePromo();
    	$this->items = array();
    	return true;
    }

	private function generateKey( $product_id, $component_ids ) {
		sort( $component_ids );
		return md5( $product_id . str_replace( ',', '', implode( ',', $component_ids ) ) );
	}

}
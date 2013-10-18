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
		$this->load();
	}

	function __destruct() {
		$this->save();
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

	public function hasPromo() {
		return $this->promo['code'] !== null;
	}

    public function toArray() {
		$s = array();
		foreach ( get_object_vars( $this ) as $var => $value ) 
			$s[$var] = $value;
		return $s;
    }

    protected function load() {
		if ( !array_key_exists( 'shopping_cart', $_SESSION ) ) {
			$this->created = time();
			return;
		}
		foreach ( $_SESSION['shopping_cart'] as $var => $value ) $this->$var = $value;
    }

    protected function save() {
		$s = array();
		foreach ( get_object_vars( $this ) as $var => $value ) 
			$s[$var] = $value;
		$_SESSION['shopping_cart'] = $s;
    }

    protected function calculateTotals() {

		$items_count = 0;
		$items_total = 0.00;
		$discount_total = 0.00;
		$sub_total = 0.00;
		$total = 0.00;

		foreach ( array_keys( $this->items ) as $k ) {
			$this->items[$k]['single_price'] = calc_product_price( $this->items[$k]['product_id'], $this->items[$k]['component_ids'] );
			$this->items[$k]['total_price'] = $this->items[$k]['single_price'] * $this->items[$k]['qty'];
			$items_total += $this->items[$k]['total_price'];
			$items_count += $this->items[$k]['qty'];
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

	protected function addItem( $product_id, $component_ids = array(), $qty = 1 ) {

		$k = $this->generateKey( $product_id, $component_ids );

		if ( !array_key_exists( $k, $this->items ) ) {

			$product = get_product( $product_id );
			$component_ids = array_unique( array_merge( $component_ids, $product->comFixedIDs ) );
			$price = $product->calcPrice( $component_ids );

			$item = array(

				'qty'              => $qty,
				'product_id'       => $product_id,
				'product_title'    => get_the_title( $product_id ),
				'single_price'     => $price,
				'total_price'      => $price * $qty,
				'components'       => array(),

				'component_ids'    => $component_ids,
				'price'            => $price,

			);

			foreach ( $component_ids as $cid ) {

				$c = get_post( preg_replace( '/\*/', '', substr( $cid, 10 ) ) );

				$terms = get_the_terms( $c->ID, 'component_group' );
				$terms_keys = array_keys( $terms );
				list( $type, $sub_type ) = explode( '-', $terms[$terms_keys[0]]->slug );

				$attrs = get_post_custom( $c->ID );

				$item['components'][] = array(

					'id'                  => $c->ID,
					'title'               => $c->post_title,
					'type'                => $type,
					'sub_type'            => $sub_type,

					'price'               => $attrs['price'][0],
					'cost'                => $attrs['cost'][0],
					'model'               => $attrs['model_number'][0],
					'long_name'           => $attrs['long_name'][0],
					'model_number'        => $attrs['model_number'][0],
					'manufacturer_link'   => $attrs['manufacturer_link'][0],
					'wholesale_link'      => $attrs['wholesale_link'][0]

				);

			}
			
			$this->items[$k] = $item;

		}
		else $this->items[$k]['qty'] += $qty;

		return true;

	}

	protected function setItemQty( $k, $qty ) {

		if ( $qty == 0 ) {
			return $this->removeItem( $k );
		}

		if ( array_key_exists( $k, $this->items ) ) {
			$this->items[$k]['qty'] = $qty;
		}

		return true;
		
	}

	protected function removeItem( $k ) {

		if ( array_key_exists( $k, $this->items ) ) {
			unset( $this->items[$k] );
		}

		return true;

	}

	protected function addPromo( $p ) {
		$this->promo = array(
			'code'    => $p->get( 'code' ),
			'type'    => $p->get( 'discount_type' ),
			'amount'  => $p->get( 'discount_amount' )
		);
		return true;
	}

	protected function removePromo() {
		$vars = get_class_vars( $this );
		$this->promo = $vars['promo'];
		return true;
	}

    protected function emptyCart() {
    	$this->removePromo();
    	$this->items = array();
    	return true;
    }

	protected function generateKey( $product_id, $component_ids ) {
		sort( $component_ids );
		return md5( $product_id . str_replace( ',', '', implode( ',', $component_ids ) ) );
	}

}
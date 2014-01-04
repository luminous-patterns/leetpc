<?php

class lpcProduct {

	function __construct( $product_id ) {

		$this->post = get_post( $product_id );
		$this->meta = get_post_custom( $product_id );

		$this->types = wp_get_post_terms( $product_id, 'product_type' );
		$this->type = $this->types[0];

		$this->initComponents();

	}

	private function initComponents() {

		$component_ids = explode( ',', $this->meta['components'][0] );

		$components = array();
		$default_ids = array();
		$fixed_ids = array();
		$defaults = array();
		$attrs = array();

		foreach ( $component_ids as $id ) {

			$fixed = preg_match( '/\*\*$/', $id );
			$id = substr( $id, 10 );

			if ( $fixed ) {
				$def = true;
				$id = substr( $id, 0, -2 );
			}
			else {
				$def = preg_match( '/\*$/', $id );
				$id = $def ? substr( $id, 0, -1 ) : $id;
			}

			$c = get_post( $id );

			$terms = get_the_terms( $c->ID, 'component_group' );
			$terms_keys = array_keys( $terms );
			list( $type, $sub_type ) = explode( '-', $terms[$terms_keys[0]]->slug );

			$components[$type][] = $c;

			if ( $def || $fixed ) {
				$defaults[$type] = $c;
				$default_ids[] = 'component-' . $c->ID;
			}

			if ( $fixed ) {
				$fixed_ids[] = 'component-' . $c->ID;
			}

			$attrs[$c->ID] = get_post_custom( $c->ID );

		}

		$price_diffs = array();

		foreach ( $components as $type => $coms ) {

			$d = $defaults[$type];
	
			foreach ( $coms as $c ) {
				$price_diffs[$c->ID] = floatval( $attrs[$c->ID]['price'][0] - $attrs[$d->ID]['price'][0] );
			}

		}

		$this->componentIDs = $component_ids;
		$this->components = $components;
		$this->comFixedIDs = $fixed_ids;
		$this->comDefaultIDs = $default_ids;
		$this->comDefaults = $defaults;
		$this->comAttrs = $attrs;
		$this->comPriceDiffs = $price_diffs;

	}

	public function get( $k ) {
		return $this->meta[$k][0];
	}

	public function getType() {
		return $this->type;
	}

	public function getTypeName() {
		return $this->type->name;
	}

	public function printComponentOptions( $type ) {
		return print_component_options( $type, $this->components, $this->comDefaults, $this->comAttrs );
	}

	public function getPrice() {
		return $this->meta['price'][0];
	}

	public function getCost( $component_ids = null ) {

		$cost = 0.00;

		if ( $component_ids === null ) {
			$component_ids = $this->comDefaultIDs;
		}

		foreach ( $component_ids as $i ) {
			$cost += $this->comAttrs[preg_replace( '/^component-/', '', $i )]['cost'][0];
		}

		return $cost;

	}

	public function calcPrice( $component_ids = null ) {

		$sub_total = $this->getPrice();

		foreach ( $component_ids as $i ) {
			$sub_total += $this->comPriceDiffs[preg_replace( '/^component-/', '', $i )];
		}

		return $sub_total;

	}
	
}
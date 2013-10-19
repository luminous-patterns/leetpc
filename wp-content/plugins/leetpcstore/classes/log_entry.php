<?php

class lpcLogEntry {

	public $ID;

	public $extra = array();

	function __construct( $log_entry_id ) {

		if ( !get_post_type( $log_entry_id ) == 'log_entry' ) trigger_error( 'Invalid Log Entry ID', E_USER_ERROR );

		$this->post = get_post( $log_entry_id );
		$this->ID = $this->post->ID;

		$this->type = wp_get_post_terms( $this->ID, 'log_entry_type' );
		$this->date = new DateTime( $this->post->post_date_gmt, new DateTimeZone( 'UTC' ) );
		$this->date->setTimezone( new DateTimeZone( BOOK_KEEPING_TZ ) );

		foreach ( get_post_custom( $this->ID ) as $k => $v ) $this->extra[$k] = $v[0];

	}

	public function get( $k ) {
		return array_key_exists( $k, $this->extra ) ? $this->extra[$k] : null;
	}

	public function getDate( $f = LPC_LOGENTRY_DATETIMES ) {
		return $this->date->format( $f );
	}

	public function getTypeID() {
		return $this->type[0]->slug;
	}

	public function getTypeName() {
		return $this->type[0]->name;
	}

	public function getNote() {
		return $this->post_content;
	}

	public function getType() {
		return $this->type[0];
	}

	public function getExtra() {
		return $this->extra;
	}

	public function toArray() {
		return array( 
			$this->ID, 
			array( $this->date, $this->getDate() ), 
			$this->getTypeID(), 
			$this->getTypeName(), 
			$this->getNote(), 
			$this->getExtra()
		);
	}

}
<?php
namespace Jet_Engine\Relations\Types;

/**
 * Type_Query class, which provides a standardized way to communicate with results of the query()
 * method from Jet_Engine\Relations\Types object.
 */
class Type_Query {

	protected $items = array();
	protected $total_count = 0;
	protected $final_query = array();

	public function __construct( $items = array(), $total_count = 0, $final_query = array() ) {
		$this->items       = $items;
		$this->total_count = $total_count;
		$this->final_query = $final_query;
	}

	public function get_items() {
		return $this->items;
	}

	public function get_total_count() {
		return $this->total_count;
	}

	public function get_final_query() {
		return $this->final_query;
	}
}

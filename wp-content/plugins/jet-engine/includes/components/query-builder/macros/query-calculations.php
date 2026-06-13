<?php
namespace Jet_Engine\Query_Builder\Macros;

class Query_Calculations_Macro extends \Jet_Engine_Base_Macros {

	use \Jet_Engine\Query_Builder\Traits\Query_Calculations_Trait;

	public function macros_tag() {
		return 'query_calculations';
	}

	public function macros_name() {
		return $this->get_title();
	}

	public function macros_args() {
		return $this->get_args();
	}

	public function macros_callback( $args = array() ) {
		return $this->get_result( $args );
	}
}
